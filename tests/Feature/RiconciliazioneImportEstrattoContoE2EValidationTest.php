<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\OdsImportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * R5 · Controllo finale — Validazione pagamenti avanzati / riconciliazione (attività #8549).
 *
 * Test E2E che esercita SOLO ciò che esiste in codice e che il design R5 dichiara di
 * RIUSARE come backend della riconciliazione:
 *   - InvoiceService::recordPayment (è ciò che "Abbina e registra pagamento" chiamerebbe),
 *   - InvoiceService::createCreditNote (è la base dello "storno"),
 *   - Payment.reference_number / Payment.notes (i campi precompilati dal movimento §5),
 *   - il pattern import dry-run + report anomalie di OdsImportService (§3/§10: il CSV banca
 *     deve ricalcarlo).
 * E documenta come BLOCCO ciò che il design R5 richiede ma che NON è ancora implementato:
 * staging movimenti, eventi di riconciliazione, servizio di import CSV banca, collegamento
 * pagamento↔riga estratto conto, schermata di match e reversibilità (Disfa).
 *
 * Riferimenti design: docs/36_UX_RICONCILIAZIONE_MANUALE_E_IMPORT_CSV.md
 *   §1 (gap rilevati: la "destra" — scadenze R4 — esiste; mancano ingresso movimenti,
 *   schermata di match e memoria abbinamenti), §3 (import CSV dry-run+report),
 *   §4-§5 (match split + modal "Abbina e registra pagamento"), §6 (eventi + [Disfa]),
 *   §7 (storni come eventi), §10 (impatti tecnici: 2 tabelle nuove + BankStatementImportService).
 *
 * Trascrizioni parte 1:
 *   r.424-474 — import dell'estratto conto, riconoscere i bonifici/POS in arrivo e
 *               collegarli alle rate attese; riconciliazione manuale "a vista".
 *   r.508-534 — copertura delle rate, verificare se l'allievo è in regola, parziali e
 *               storni da gestire senza cancellazioni distruttive.
 */
class RiconciliazioneImportEstrattoContoE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $currentYear;
    private User $user;
    private int $invoiceSeq = 0;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('acl:sync', ['--reset-defaults' => true]);

        $this->currentYear = AcademicYear::create([
            'name' => '2025/26',
            'slug' => '2025-26',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    private function makeStudent(string $first = 'Mario', string $last = 'Rossi'): Student
    {
        return Student::create([
            'first_name' => $first,
            'last_name' => $last,
            'birth_date' => '2012-01-01',
            'tax_code' => 'RSSMRA12A01H501Z',
        ]);
    }

    private function makeInvoice(Student $student, float $total = 810.0, string $status = 'sent', string $dueDate = '2026-05-31'): Invoice
    {
        return Invoice::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->currentYear->id,
            'invoice_number' => 'FATT-2025-' . str_pad((string) (++$this->invoiceSeq), 4, '0', STR_PAD_LEFT),
            'invoice_date' => '2025-10-01',
            'due_date' => $dueDate,
            'subtotal' => $total,
            'total_amount' => $total,
            'status' => $status,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CIÒ CHE FUNZIONA (E2E sul codice reale che R5 riusa — deve passare)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * MATCH 1:1 — BACKEND (§4-§5, trascr. p1 r.424-474) — confermare un abbinamento
     * "movimento ↔ scadenza" chiama il recordPayment di R4: importo della rata → la rata
     * più vecchia diventa "paid" con payment_id e paid_date, saldo ridotto. Questa è la
     * scrittura reale che il bottone "Abbina e registra pagamento" eseguirebbe.
     */
    public function test_match_1a1_backend_recordPayment_salda_la_rata(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);
        app(InvoiceService::class)->createPaymentPlan($invoice, 3, Carbon::parse('2025-11-01')); // rate da 270

        // Movimento banca +270,00 abbinato alla rata 1/3.
        app(InvoiceService::class)->recordPayment($invoice, 270.0, 'bank_transfer', Carbon::parse('2025-11-05'));

        $prima = $invoice->paymentPlans()->orderBy('due_date')->first();
        $this->assertSame('paid', $prima->status);
        $this->assertNotNull($prima->payment_id);
        $this->assertEquals('2025-11-05', $prima->paid_date->format('Y-m-d'));

        $invoice->refresh();
        $this->assertEqualsWithDelta(540.0, (float) $invoice->remaining_amount, 0.001);
    }

    /**
     * MOVIMENTO → PAGAMENTO: RIFERIMENTO + NOTE (§5 "precompilati dal movimento") — la
     * tabella payments porta già reference_number e notes: il design vi parcheggia la
     * causale/IBAN/ordinante del bonifico. Verifichiamo che siano colonne reali e
     * persistano (è il minimo perché un Payment "ricordi" da quale movimento nasce).
     */
    public function test_payment_porta_riferimento_e_note_dal_movimento(): void
    {
        $this->assertTrue(Schema::hasColumn('payments', 'reference_number'));
        $this->assertTrue(Schema::hasColumn('payments', 'notes'));

        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 270.0);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => 270.0,
            'payment_date' => '2025-11-05',
            'payment_method' => 'bank_transfer',
            'reference_number' => 'BONIF. ROSSI MARIO PIANOFORTE',
            'notes' => 'Import estratto conto 05/05 · IT60X0542811101000000123456',
        ]);

        $payment->refresh();
        $this->assertStringContainsString('ROSSI MARIO', $payment->reference_number);
        $this->assertStringContainsString('IT60X0542811101000000123456', $payment->notes);
    }

    /**
     * CUMULATIVO (§5/§8, trascr. p1 r.508-534) — un solo movimento che copre più rate
     * consecutive le marca a cascata fino a capienza. È il backend del caso "cumulativo".
     */
    public function test_cumulativo_un_movimento_copre_piu_rate(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);
        app(InvoiceService::class)->createPaymentPlan($invoice, 3, Carbon::parse('2025-11-01')); // 3 × 270

        // Movimento unico +540,00 → copre le prime due rate.
        app(InvoiceService::class)->recordPayment($invoice, 540.0, 'bank_transfer', Carbon::parse('2025-11-05'));

        $this->assertSame(2, $invoice->paymentPlans()->where('status', 'paid')->count());
        $this->assertSame(1, $invoice->paymentPlans()->where('status', 'pending')->count());
    }

    /**
     * STORNO — BACKEND (§7, trascr. p1 r.508-534) — un movimento negativo (reso/rimborso)
     * si traduce in una nota di credito collegata alla fattura: createCreditNote crea la
     * CreditNote senza generare un Payment positivo. È la base dello "storno come evento".
     */
    public function test_storno_crea_nota_credito_collegata_alla_fattura(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);

        $nota = app(InvoiceService::class)->createCreditNote($invoice, 45.0, 'reso cauzione');

        $this->assertInstanceOf(CreditNote::class, $nota);
        $this->assertEqualsWithDelta(45.0, (float) $nota->amount, 0.001);
        $this->assertSame('reso cauzione', $nota->reason);
        $this->assertSame(1, $invoice->creditNotes()->count());
        // Nessun pagamento positivo generato dallo storno.
        $this->assertSame(0, $invoice->payments()->count());
    }

    /**
     * PATTERN IMPORT DRY-RUN RIUSABILE (§3/§10) — il design impone che il CSV banca
     * ricalchi OdsImportService: dry-run obbligatorio + report anomalie per categoria.
     * Verifichiamo che il pattern esista già (parametro $dryRun) ed è quindi disponibile
     * come modello per BankStatementImportService (che invece NON esiste, vedi BLOCCO).
     */
    public function test_pattern_import_dryrun_riusabile_esiste(): void
    {
        $ref = new \ReflectionMethod(OdsImportService::class, 'importStudents');
        $dryRunParam = collect($ref->getParameters())->first(fn ($p) => $p->getName() === 'dryRun');
        $this->assertNotNull($dryRunParam, 'Il pattern import dry-run (riusabile per il CSV banca) esiste in OdsImportService.');
        $this->assertTrue($dryRunParam->isDefaultValueAvailable());
        $this->assertFalse($dryRunParam->getDefaultValue(), 'dry-run è opt-in, coerente con "anteprima prima della scrittura".');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BLOCCO — implementazione R5 assente (documentata, non implementata qui)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * BLOCCO §1/§10 (INGRESSO MOVIMENTI) — manca la tabella di staging
     * `bank_statement_lines`: senza di essa non c'è la colonna sinistra "estratto conto",
     * né l'idempotenza su row_hash, né gli stati da_abbinare/abbinato/parziale/ignorato/storno.
     */
    public function test_blocco_tabella_bank_statement_lines_assente(): void
    {
        $this->assertFalse(Schema::hasTable('bank_statement_lines'),
            'BLOCCO §1/§10: manca lo staging dei movimenti bancari (colonna sinistra della riconciliazione).');
        $this->assertFalse(class_exists(\App\Models\BankStatementLine::class),
            'BLOCCO §10: manca il model BankStatementLine.');
    }

    /**
     * BLOCCO §6/§10 (MEMORIA ABBINAMENTI) — manca la tabella `reconciliation_events`:
     * è il log degli eventi (ABBINATO/PARZIALE/CUMULATIVO/SPLIT/STORNO/IGNORATO/ANNULLATO)
     * che rende parziali e storni rappresentabili e — soprattutto — reversibili con [Disfa].
     * Senza eventi, recordPayment è una mutazione cieca senza audit né annullamento.
     */
    public function test_blocco_tabella_reconciliation_events_assente(): void
    {
        $this->assertFalse(Schema::hasTable('reconciliation_events'),
            'BLOCCO §6/§10: manca la memoria degli abbinamenti come eventi (no audit, no [Disfa]).');
        $this->assertFalse(class_exists(\App\Models\ReconciliationEvent::class),
            'BLOCCO §10: manca il model ReconciliationEvent.');
    }

    /**
     * BLOCCO §3/§10 (PARSER CSV BANCA) — manca BankStatementImportService che ricalchi
     * OdsImportService (PASS-1 lettura+anomalie su row_hash duplicati, PASS-2 normalizza+scrive
     * solo movimenti, mai Payment; flag $dryRun). L'import del CSV estratto conto non esiste.
     */
    public function test_blocco_bank_statement_import_service_assente(): void
    {
        $this->assertFalse(class_exists(\App\Services\BankStatementImportService::class),
            'BLOCCO §3/§10: manca il servizio di import CSV estratto conto (dry-run + report anomalie).');
    }

    /**
     * BLOCCO §1 (PAYMENT ↔ RIGA ESTRATTO CONTO) — "Payment non sa da quale riga di estratto
     * conto nasce": la tabella payments non ha bank_statement_line_id. Senza questo ponte
     * non si può tornare dal pagamento al movimento (né disfare in modo tracciato).
     * (reference_number/notes sono testo libero, non un collegamento referenziale.)
     */
    public function test_blocco_payment_non_collegato_a_riga_estratto_conto(): void
    {
        $this->assertFalse(Schema::hasColumn('payments', 'bank_statement_line_id'),
            'BLOCCO §1: il Payment non è collegato referenzialmente alla riga di estratto conto.');
        $this->assertFalse(method_exists(Payment::class, 'bankStatementLine'),
            'BLOCCO §1: manca la relazione Payment→BankStatementLine.');
    }

    /**
     * BLOCCO §7 (STORNO ↔ RIGA ESTRATTO CONTO) — la CreditNote esiste (storno backend) ma
     * non è collegabile a una riga di estratto conto: nessun bank_statement_line_id su
     * credit_notes. Lo storno-come-evento (§7) non è quindi tracciabile fino al movimento.
     */
    public function test_blocco_storno_non_collegabile_a_riga_estratto_conto(): void
    {
        $this->assertFalse(Schema::hasColumn('credit_notes', 'bank_statement_line_id'),
            'BLOCCO §7: la nota di credito (storno) non è collegabile alla riga di estratto conto.');
    }

    /**
     * BLOCCO §4-§5 (SCHERMATA DI MATCH + AZIONI) — non esiste alcuna route di riconciliazione:
     * né import CSV, né schermata split estratto-conto↔scadenze, né abbina/disfa/ignora.
     * La "destra" (scadenze R4) esiste, ma il ponte UX e le sue azioni AJAX sono assenti.
     */
    public function test_blocco_schermata_match_e_route_riconciliazione_assenti(): void
    {
        $routes = $this->routeNames();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'reconcil')
                || str_contains((string) $n, 'riconcil')
                || str_contains((string) $n, 'bank-statement')
                || str_contains((string) $n, 'estratto-conto')),
            'BLOCCO §4-§5: nessuna route per import CSV / schermata di match / abbina-disfa.'
        );
        $this->assertFalse(class_exists(\App\Http\Controllers\Admin\ReconciliationController::class),
            'BLOCCO §4-§5: manca il controller della riconciliazione.');
    }

    /**
     * BLOCCO §5/§8 (PARZIALE) — anche il backend riusato (recordPayment) NON gestisce il
     * parziale richiesto da R5: un movimento < importo rata riduce il saldo ma lascia la
     * rata "pending" (nessuna nozione di residuo sulla rata). Il design vuole una rata che
     * resti aperta col residuo dichiarato: serve logica nuova, non solo la UI.
     */
    public function test_blocco_pagamento_parziale_non_lascia_residuo_sulla_rata(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);
        app(InvoiceService::class)->createPaymentPlan($invoice, 3, Carbon::parse('2025-11-01')); // rate da 270

        // Movimento parziale +100,00 < 270,00.
        app(InvoiceService::class)->recordPayment($invoice, 100.0, 'bank_transfer', Carbon::parse('2025-11-05'));

        $invoice->refresh();
        $this->assertEqualsWithDelta(710.0, (float) $invoice->remaining_amount, 0.001);
        // Nessuna rata pagata e nessun campo residuo sulla rata: il parziale §5 non è rappresentabile.
        $this->assertSame(0, $invoice->paymentPlans()->where('status', 'paid')->count(),
            'BLOCCO §5: il parziale non aggiorna la rata né registra un residuo.');
        $this->assertFalse(Schema::hasColumn('payment_plans', 'paid_amount'),
            'BLOCCO §5: la rata non ha un campo per l\'acconto/residuo (parziale non rappresentabile).');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────────

    private function routeNames()
    {
        return collect(app('router')->getRoutes())->map->getName()->filter()->values();
    }
}
