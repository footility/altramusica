<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Models\Student;
use App\Models\User;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * R4 · Controllo finale — Validazione contabilità per studente (attività #8543).
 *
 * Test E2E che esercita SOLO ciò che esiste in codice (Invoice + CRUD,
 * InvoiceService::createPaymentPlan/recordPayment/createCreditNote,
 * PaymentPlan con scope overdue/pending, AccountingReportController@balances)
 * e documenta esplicitamente i gap/bug rispetto al design R4.
 *
 * Riferimenti design: docs/25_UX_VISTA_CONTABILITA_PER_STUDENTE.md
 *   §2-§3 (vista per studente: striscia sintesi dovuto/pagato/da-saldare/scadenza),
 *   §4 (fattura espansa: righe/rate/pagamenti), §5 (registra pagamento contestuale),
 *   §6 (note di credito + credito disponibile), §7 (parziale/cumulativo/roll-up famiglia).
 *
 * Trascrizioni parte 1:
 *   r.76   — "vedere riassunte tutte le informazioni contabili, quanto ha pagato,
 *            quanto non ha pagato … il recupero crediti è terribilmente dispersivo".
 *   r.392-404 — contratto con valore corso × settimane + le tre scadenze delle rate;
 *            numero rate concordato e variabile (anche mensile).
 *   r.508-534 — corrispondenza contratto↔rate, "copertura delle rate" a corso iniziato,
 *            verificare se l'allievo è in regola coi pagamenti (recupero crediti / solleciti),
 *            numero rate variabile e temporalmente scandito.
 */
class ContabilitaPerStudenteE2EValidationTest extends TestCase
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
    // CIÒ CHE FUNZIONA (E2E sul codice reale — deve passare)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * SCADENZARIO (§3, trascr. p1 r.392-404) — il piano rate divide il totale in N
     * rate con scadenze mensili progressive, stato iniziale "pending". Numero rate
     * variabile (qui 3, ma il flusso accetta 1..12). Via la route reale per fattura.
     */
    public function test_scadenzario_piano_rate_genera_rate_con_scadenze(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);

        $this->actingAs($this->user)
            ->post(route('admin.invoices.payment-plan', $invoice), [
                'number_of_installments' => 3,
                'first_due_date' => '2025-11-01',
            ])
            ->assertRedirect(route('admin.invoices.show', $invoice));

        $rate = $invoice->paymentPlans()->orderBy('installment_number')->get();
        $this->assertCount(3, $rate);
        $this->assertSame([1, 2, 3], $rate->pluck('installment_number')->all());
        // Scadenze mensili progressive a partire dalla prima.
        $this->assertEquals('2025-11-01', $rate[0]->due_date->format('Y-m-d'));
        $this->assertEquals('2025-12-01', $rate[1]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-01-01', $rate[2]->due_date->format('Y-m-d'));
        // Somma rate = totale fattura (ultima rata assorbe il resto).
        $this->assertEqualsWithDelta(810.0, (float) $rate->sum('amount'), 0.001);
        $this->assertSame(['pending', 'pending', 'pending'], $rate->pluck('status')->all());
    }

    /**
     * REGISTRA PAGAMENTO + VEDI SALDO (§3/§5) — pagando l'importo di una rata via la
     * route reale: Payment creato, rata più vecchia marcata "paid" con paid_date e
     * payment_id, saldo residuo (remaining_amount) ridotto del pagato.
     */
    public function test_registra_pagamento_aggiorna_rata_e_saldo(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);
        app(InvoiceService::class)->createPaymentPlan($invoice, 3, Carbon::parse('2025-11-01'));

        $rataImporto = (float) $invoice->paymentPlans()->orderBy('due_date')->first()->amount; // 270

        $this->actingAs($this->user)
            ->post(route('admin.invoices.payment', $invoice), [
                'amount' => $rataImporto,
                'payment_method' => 'cash',
                'payment_date' => '2025-11-03',
            ])
            ->assertRedirect(route('admin.invoices.show', $invoice));

        $this->assertSame(1, Payment::where('invoice_id', $invoice->id)->count());

        $prima = $invoice->paymentPlans()->orderBy('due_date')->first();
        $this->assertSame('paid', $prima->status);
        $this->assertEquals('2025-11-03', $prima->paid_date->format('Y-m-d'));
        $this->assertNotNull($prima->payment_id);

        // Saldo: dovuto 810 − pagato 270 = 540 da saldare.
        $invoice->refresh();
        $this->assertEqualsWithDelta(270.0, (float) $invoice->paid_amount, 0.001);
        $this->assertEqualsWithDelta(540.0, (float) $invoice->remaining_amount, 0.001);
    }

    /**
     * PAGAMENTO CUMULATIVO (§7) — un pagamento pari alla somma di più rate consecutive
     * le marca a cascata e, saldando l'intero dovuto, porta la fattura a "paid".
     */
    public function test_pagamento_totale_marca_tutte_le_rate_e_fattura_paid(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);
        app(InvoiceService::class)->createPaymentPlan($invoice, 3, Carbon::parse('2025-11-01'));

        app(InvoiceService::class)->recordPayment($invoice, 810.0, 'bank_transfer', Carbon::parse('2025-11-03'));

        $invoice->refresh();
        $this->assertSame('paid', $invoice->status);
        $this->assertEqualsWithDelta(0.0, (float) $invoice->remaining_amount, 0.001);
        $this->assertSame(3, $invoice->paymentPlans()->where('status', 'paid')->count());
    }

    /**
     * VEDI SALDO AGGREGATO (§2, trascr. p1 r.76 "quanto ha pagato/non ha pagato") —
     * il report saldi per studente espone dovuto/pagato e isola chi ha saldo aperto.
     */
    public function test_report_saldi_per_studente_mostra_dovuto_e_pagato(): void
    {
        $aperto = $this->makeStudent('Mario', 'Rossi');
        $saldato = $this->makeStudent('Giulia', 'Bianchi');

        $invAperto = $this->makeInvoice($aperto, 810.0);
        app(InvoiceService::class)->recordPayment($invAperto, 540.0, 'cash', Carbon::parse('2025-11-03'));

        $invSaldato = $this->makeInvoice($saldato, 300.0);
        app(InvoiceService::class)->recordPayment($invSaldato, 300.0, 'cash', Carbon::parse('2025-11-03'));

        // only_open=0: evita la HAVING su colonna non aggregata (rifiutata da SQLite,
        // accettata in prod MySQL) e mostra tutti gli studenti con dovuto/pagato.
        $response = $this->actingAs($this->user)
            ->get(route('admin.accounting.balances', [
                'academic_year_id' => $this->currentYear->id,
                'only_open' => 0,
            ]));

        $response->assertOk();
        $response->assertSee('Rossi');
        $response->assertSee('Bianchi');
    }

    /**
     * RECUPERO CREDITI (§3 semaforo 🔴, trascr. p1 r.508-534 "in regola coi pagamenti") —
     * lo scope PaymentPlan::overdue() rileva le rate scadute (pending + due_date passata),
     * il sostrato per evidenziare cosa scade ora.
     */
    public function test_scope_overdue_rileva_rate_scadute(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);

        PaymentPlan::create([
            'invoice_id' => $invoice->id,
            'installment_number' => 1,
            'amount' => 270,
            'due_date' => now()->subWeek(),   // scaduta
            'status' => 'pending',
        ]);
        PaymentPlan::create([
            'invoice_id' => $invoice->id,
            'installment_number' => 2,
            'amount' => 270,
            'due_date' => now()->addMonth(),   // futura
            'status' => 'pending',
        ]);

        $scadute = PaymentPlan::overdue()->get();
        $this->assertCount(1, $scadute);
        $this->assertSame(1, $scadute->first()->installment_number);
    }

    /**
     * NOTA DI CREDITO (§6) — il servizio crea una CreditNote collegata alla fattura
     * con numero, importo e motivo (storno/rimborso/abbuono).
     */
    public function test_nota_credito_creata_via_service(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);

        $nota = app(InvoiceService::class)->createCreditNote($invoice, 45.0, 'abbuono');

        $this->assertInstanceOf(CreditNote::class, $nota);
        $this->assertEqualsWithDelta(45.0, (float) $nota->amount, 0.001);
        $this->assertSame('abbuono', $nota->reason);
        $this->assertSame(1, $invoice->creditNotes()->count());
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GAP / BUG rispetto al design R4 (documentati, non implementati qui)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GAP §2-§3 (CENTRALE) — la "vista contabilità centrata sullo studente" non esiste:
     * niente tab/route Contabilità sulla scheda studente, niente striscia sintesi.
     * Oggi students/show ha solo il bottone "Nuova Fattura" e la lista globale
     * invoices.index?student_id= (lista filtrata, NON una scheda contabile per persona).
     */
    public function test_gap_vista_contabilita_per_studente_assente(): void
    {
        $controller = \App\Http\Controllers\Admin\StudentController::class;
        foreach (['accounting', 'contabilita', 'ledger', 'billing'] as $action) {
            $this->assertFalse(method_exists($controller, $action),
                "GAP §2-§3: manca StudentController@{$action} (tab Contabilità per studente).");
        }

        $routes = $this->routeNames();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'students.accounting')
                || str_contains((string) $n, 'students.contabilita')
                || str_contains((string) $n, 'students.balance')),
            'GAP §2-§3: nessuna route per la vista contabilità per studente.'
        );

        // L'unica scorciatoia dalla scheda studente è "Nuova Fattura" (lista CRUD globale).
        $this->assertTrue($routes->contains('admin.invoices.index'),
            'Oggi la contabilità per studente passa solo dalla lista fatture filtrata.');
    }

    /**
     * GAP §5 — "Registra pagamento" è SOLO per fattura: recordPayment(Invoice) e la
     * route invoices/{invoice}/payment. Non esiste l'azione rapida contestuale dalla
     * rata/scadenza (nessuna route che prenda un PaymentPlan, nessun importo
     * precompilato dalla rata a livello applicativo).
     */
    public function test_gap_registra_pagamento_solo_per_fattura(): void
    {
        // Il servizio aggancia il pagamento alla FATTURA, non alla rata.
        $ref = new \ReflectionMethod(InvoiceService::class, 'recordPayment');
        $this->assertSame(Invoice::class, $ref->getParameters()[0]->getType()?->getName(),
            'GAP §5: recordPayment parte dalla fattura, non dalla rata/scadenza.');

        $routes = $this->routeNames();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'payment-plan')
                && str_contains((string) $n, 'payment')
                && str_contains((string) $n, 'installment')),
            'GAP §5: nessuna route "registra pagamento" agganciata alla singola rata.'
        );

        // Il controller valida solo amount/method/date: i campi del modal §5
        // (riferimento, note) non sono gestiti dall'azione HTTP.
        $body = file_get_contents(app_path('Http/Controllers/Admin/InvoiceController.php'));
        $this->assertStringNotContainsString("'reference_number'", $body,
            'GAP §5: il modal prevede "Riferimento"/"Note", il controller recordPayment non li accetta.');
    }

    /**
     * GAP §7 "pagamento parziale" — recordPayment marca una rata SOLO se il residuo
     * copre l'intero importo della rata (MVP). Un pagamento parziale sotto l'importo
     * rata lascia la rata "pending" pur avendo ridotto il saldo: la scadenza resta
     * 🔴 anche se un acconto è arrivato → incoerenza scadenzario vs saldo.
     */
    public function test_gap_pagamento_parziale_non_intacca_la_rata(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);
        app(InvoiceService::class)->createPaymentPlan($invoice, 3, Carbon::parse('2025-11-01')); // rate da 270

        // Acconto di 100 € < 270 € (importo rata).
        app(InvoiceService::class)->recordPayment($invoice, 100.0, 'cash', Carbon::parse('2025-11-03'));

        $invoice->refresh();
        // Il saldo scende…
        $this->assertEqualsWithDelta(710.0, (float) $invoice->remaining_amount, 0.001);
        // …ma NESSUNA rata risulta pagata (resta tutta pending).
        $this->assertSame(0, $invoice->paymentPlans()->where('status', 'paid')->count(),
            'GAP §7: il pagamento parziale non aggiorna la rata (resta pending nonostante l\'acconto).');
    }

    /**
     * GAP §6 — Le note di credito NON sono esposte da una route/azione (createCreditNote
     * è solo nel servizio) E non riducono il "da saldare": remaining_amount = totale −
     * pagato, ignora del tutto le CreditNote. Il "credito disponibile" del design e la
     * detrazione [Usa] non esistono lato dato calcolato.
     */
    public function test_gap_nota_credito_senza_route_e_non_scala_il_dovuto(): void
    {
        $routes = $this->routeNames();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'credit-note')
                || str_contains((string) $n, 'nota-credito')
                || str_contains((string) $n, 'creditNote')),
            'GAP §6: nessuna route per creare/usare la nota di credito dalla vista.'
        );
        $this->assertFalse(method_exists(\App\Http\Controllers\Admin\InvoiceController::class, 'createCreditNote'),
            'GAP §6: createCreditNote esiste solo nel servizio, non come azione del controller.');

        // Il credito non scala il dovuto: 810 totale, 0 pagato, NC 45 → da saldare
        // resta 810 (non 765). Manca "Da saldare = dovuto − pagato − note di credito".
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, 810.0);
        app(InvoiceService::class)->createCreditNote($invoice, 45.0, 'abbuono');

        $invoice->refresh();
        $this->assertEqualsWithDelta(810.0, (float) $invoice->remaining_amount, 0.001,
            'GAP §6: remaining_amount ignora le note di credito (da-saldare sovrastimato).');
    }

    /**
     * GAP §7 — Roll-up famiglia assente: nessuna aggregazione dovuto/pagato/da-saldare
     * sul nucleo (fratelli). Né route né metodo dedicato; il report saldi è per singolo
     * studente, non per famiglia.
     */
    public function test_gap_rollup_famiglia_assente(): void
    {
        $routes = $this->routeNames();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'family')
                && (str_contains((string) $n, 'balance') || str_contains((string) $n, 'accounting'))),
            'GAP §7: nessuna route per il roll-up contabile della famiglia.'
        );
        $this->assertFalse(method_exists(\App\Http\Controllers\Admin\AccountingReportController::class, 'familyBalances'),
            'GAP §7: manca l\'aggregazione contabile per nucleo familiare.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────────

    private function routeNames()
    {
        return collect(app('router')->getRoutes())->map->getName()->filter()->values();
    }
}
