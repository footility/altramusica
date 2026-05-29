<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Contract;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Document;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use App\Services\ContractService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * R3 · Controllo finale — Validazione contratti + firma mock (attività #8546).
 *
 * Test E2E che esercita SOLO ciò che esiste in codice (Contract, ContractService,
 * ContractController + route admin) e documenta esplicitamente i gap/bug rispetto
 * al design R3. Riferimenti: docs/30_UX_FLUSSO_CONTRATTO_E_FIRMA.md
 * §2 (mappa stati), §3 (timeline), §5 (firma mock = upload scansione),
 * §6 (passi indietro), §9 (webhook), §10 (impatti tecnici).
 *
 * Trascrizioni parte 1 r.322-328 (modello/link precompilato per corso), r.392-398
 * (contratto regolare con rate/scadenze), r.546-550 (contratto fondamentale prima
 * del resto); parte 2 r.142-156 (due+ tipi di contratto: regolare/breve/estivo).
 */
class ContrattiFirmaE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $currentYear;
    private User $user;

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

    // ─────────────────────────────────────────────────────────────────────────
    // CIÒ CHE FUNZIONA (E2E sul codice reale — deve passare)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * FLUSSO §2/§7 — Crea contratto "da modello": lo store genera numero contratto
     * e token (link precompilato, trascr. p1 r.322-328) e parte in "Proposta" (draft).
     * I "termini" fanno da modello del contratto.
     */
    public function test_crea_contratto_da_modello_genera_numero_e_token(): void
    {
        $student = $this->makeStudent();

        $response = $this->actingAs($this->user)->post(route('admin.contracts.store'), [
            'student_id' => $student->id,
            'academic_year_id' => $this->currentYear->id,
            'type' => 'regular',
            'start_date' => '2025-10-01',
            'end_date' => '2026-06-30',
            'status' => 'draft',
            'terms' => 'Quota annuale Pianoforte, 3 rate con scadenze. Regolamento e privacy.',
            'notes' => null,
        ]);

        $response->assertRedirect(route('admin.contracts.index'));
        $response->assertSessionHas('success');

        $contract = Contract::first();
        $this->assertNotNull($contract);
        $this->assertSame('draft', $contract->status);
        $this->assertMatchesRegularExpression('/^CONTR-\d{4}-\d{4}$/', $contract->contract_number);
        $this->assertNotEmpty($contract->token, 'Token link precompilato generato alla creazione.');
        $this->assertSame(64, strlen($contract->token));
        $this->assertNull($contract->sent_date);
        $this->assertNull($contract->signed_date);
    }

    /**
     * FLUSSO parte 2 r.142-156 — i tre tipi di contratto reali (regolare/breve/estivo)
     * + noleggio strumento sono accettati dalla validazione.
     */
    public function test_tutti_i_tipi_contratto_sono_accettati(): void
    {
        $student = $this->makeStudent();

        foreach (['regular', 'short', 'summer', 'instrument_rental'] as $i => $type) {
            $this->actingAs($this->user)->post(route('admin.contracts.store'), [
                'student_id' => $student->id,
                'academic_year_id' => $this->currentYear->id,
                'type' => $type,
                'start_date' => '2025-10-01',
                'end_date' => '2026-06-30',
                'status' => 'draft',
            ])->assertSessionHasNoErrors();
        }

        $this->assertSame(4, Contract::count());
        $this->assertEqualsCanonicalizing(
            ['regular', 'short', 'summer', 'instrument_rental'],
            Contract::pluck('type')->all()
        );
    }

    /**
     * FLUSSO §3/§5b — Timeline proposta→inviato→firmato via le route reali
     * (send poi sign). Verifica avanzamento stato + popolamento date.
     */
    public function test_timeline_proposta_inviato_firmato_via_route(): void
    {
        $contract = $this->makeContract('draft');

        // Proposta → Inviato
        $this->actingAs($this->user)
            ->post(route('admin.contracts.send', $contract))
            ->assertRedirect(route('admin.contracts.show', $contract));

        $contract->refresh();
        $this->assertSame('sent', $contract->status);
        $this->assertNotNull($contract->sent_date);

        // Inviato → Firmato
        $this->actingAs($this->user)
            ->post(route('admin.contracts.sign', $contract))
            ->assertRedirect(route('admin.contracts.show', $contract));

        $contract->refresh();
        $this->assertSame('signed', $contract->status);
        $this->assertNotNull($contract->signed_date);
    }

    /**
     * FLUSSO §7 — Proposta da iscrizione: createFromEnrollment crea il contratto
     * in "Proposta" (draft) con periodo precompilato dall'iscrizione (riuso R2).
     */
    public function test_crea_contratto_da_iscrizione(): void
    {
        $student = $this->makeStudent();
        $enrollment = $this->makeEnrollment($student);

        $service = app(ContractService::class);
        $contract = $service->createFromEnrollment($enrollment, 'regular');

        $this->assertSame('draft', $contract->status);
        $this->assertSame($student->id, $contract->student_id);
        $this->assertSame($this->currentYear->id, $contract->academic_year_id);
        $this->assertEquals('2025-10-01', $contract->start_date->format('Y-m-d'));
        $this->assertEquals('2026-06-30', $contract->end_date->format('Y-m-d'));
    }

    /**
     * FLUSSO §5 — FIX applicato: signContract() ora accetta una data firma opzionale
     * (doc 30 §10: "la firma su carta può essere di ieri"). Senza data usa now().
     */
    public function test_fix_signContract_accetta_data_opzionale(): void
    {
        $service = app(ContractService::class);

        // Con data esplicita (data sulla copia cartacea)
        $c1 = $this->makeContract('sent');
        $service->signContract($c1, '2026-05-24');
        $c1->refresh();
        $this->assertSame('signed', $c1->status);
        $this->assertEquals('2026-05-24', $c1->signed_date->format('Y-m-d'));

        // Senza data → fallback now() (retrocompatibile)
        $c2 = $this->makeContract('sent');
        $service->signContract($c2);
        $c2->refresh();
        $this->assertSame('signed', $c2->status);
        $this->assertEquals(now()->format('Y-m-d'), $c2->signed_date->format('Y-m-d'));
    }

    /**
     * §2 — Numero contratto progressivo nell'anno (CONTR-YYYY-000N).
     */
    public function test_numero_contratto_progressivo(): void
    {
        $service = app(ContractService::class);
        $student = $this->makeStudent();

        $n1 = $service->generateContractNumber();
        Contract::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->currentYear->id,
            'contract_number' => $n1,
            'type' => 'regular',
            'start_date' => '2025-10-01',
            'end_date' => '2026-06-30',
            'status' => 'draft',
        ]);
        $n2 = $service->generateContractNumber();

        $this->assertSame((int) substr($n1, -4) + 1, (int) substr($n2, -4));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GAP / BUG rispetto al design R3 (documentati, non implementati qui)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GAP §5 (centrale) — Firma mock = upload scansione ASSENTE.
     * Il design vuole che "firmato" allegi una scansione (Document type=signed_contract)
     * in modo atomico. Oggi sign() marca solo lo stato e NON crea/collega alcun Document,
     * e non esiste un'action/route registerSignature con upload.
     */
    public function test_gap_firma_non_allega_scansione(): void
    {
        $contract = $this->makeContract('sent');

        $this->actingAs($this->user)->post(route('admin.contracts.sign', $contract));
        $contract->refresh();

        $this->assertSame('signed', $contract->status);
        // GAP: firmato ma nessuna prova documentale collegata.
        $this->assertSame(0, Document::where('contract_id', $contract->id)->count(),
            'GAP §5: sign() non allega la scansione firmata (Document mancante).');

        // Nessuna action/route per registrare la firma con upload.
        $this->assertFalse(method_exists(\App\Http\Controllers\Admin\ContractController::class, 'registerSignature'),
            'GAP §5: manca ContractController@registerSignature (modal upload scansione).');
        $routes = $this->routeNames();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'register-signature')
                || str_contains((string) $n, 'registra-firma')),
            'GAP §5: nessuna route per "Registra firma (carica scansione)".'
        );

        // Il building block però esiste: Document è collegabile a un contratto (riuso R10).
        $doc = Document::create([
            'contract_id' => $contract->id,
            'student_id' => $contract->student_id,
            'type' => 'contract', // valore valido nell'enum attuale
            'file_path' => 'contracts/firma-mock.pdf',
            'file_name' => 'firma-mock.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1234,
            'uploaded_by_user_id' => $this->user->id,
        ]);
        $this->assertTrue($contract->documents()->whereKey($doc->id)->exists(),
            'Document è collegabile al contratto: l\'innesto per la firma mock esiste già.');
    }

    /**
     * GAP §5/§10 — Il tipo documento `signed_contract` previsto dal design (per
     * distinguere la SCANSIONE FIRMATA dalla bozza) NON è nell'enum di documents.type
     * {contract,privacy,photo_consent,other}. La firma mock dovrebbe ripiegare su
     * 'contract'/'other', perdendo la distinzione "bozza" vs "firmato".
     */
    public function test_gap_tipo_documento_signed_contract_non_in_enum(): void
    {
        $contract = $this->makeContract('signed');

        $threw = false;
        try {
            Document::create([
                'contract_id' => $contract->id,
                'student_id' => $contract->student_id,
                'type' => 'signed_contract',
                'file_path' => 'contracts/firmato.pdf',
                'file_name' => 'firmato.pdf',
                'mime_type' => 'application/pdf',
                'size' => 1000,
                'uploaded_by_user_id' => $this->user->id,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            $threw = true;
        }
        $this->assertTrue($threw,
            'GAP §5/§10: documents.type non ammette "signed_contract" (enum da estendere per la firma mock).');
    }

    /**
     * GAP §5/§10 — Il livello HTTP non passa una data firma: ContractController@sign
     * chiama signContract() senza data → usa sempre now(). La data sulla copia cartacea
     * non è impostabile dalla UI (manca il form/modal). Il servizio ora la supporta (fix),
     * ma il controller non la inoltra.
     */
    public function test_gap_controller_sign_non_passa_data_firma(): void
    {
        $contract = $this->makeContract('sent');

        // Anche passando una data nel POST, il controller la ignora (non la valida né la usa).
        $this->actingAs($this->user)->post(route('admin.contracts.sign', $contract), [
            'signed_date' => '2026-01-15',
        ]);
        $contract->refresh();

        $this->assertSame('signed', $contract->status);
        $this->assertEquals(now()->format('Y-m-d'), $contract->signed_date->format('Y-m-d'),
            'GAP §5/§10: il controller forza now(); la data firma dal form non è gestita.');
    }

    /**
     * GAP §6 — Passi indietro/correzioni assenti: nessuna action/route per
     * Annulla invio (sent→draft), Sblocca firma (signed→sent), Annulla, Riapri.
     */
    public function test_gap_passi_indietro_assenti(): void
    {
        $controller = \App\Http\Controllers\Admin\ContractController::class;
        foreach (['unsend', 'unsign', 'cancel', 'reopen'] as $action) {
            $this->assertFalse(method_exists($controller, $action),
                "GAP §6: manca ContractController@{$action} (passo indietro confermato).");
        }

        $routes = $this->routeNames();
        foreach (['contracts.unsend', 'contracts.unsign', 'contracts.cancel', 'contracts.reopen'] as $name) {
            $this->assertFalse($routes->contains('admin.' . $name),
                "GAP §6: nessuna route admin.{$name}.");
        }
    }

    /**
     * GAP §9 — Innesto webhook firma digitale assente (atteso in Fase 2):
     * nessuna route pubblica POST /contracts/webhook/sign.
     */
    public function test_gap_webhook_firma_digitale_assente(): void
    {
        $routes = $this->routeNames();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'contracts.webhook')
                || str_contains((string) $n, 'contracts.sign-webhook')),
            'GAP §9: nessun endpoint webhook firma digitale (corretto: è Fase 2).'
        );
    }

    /**
     * BUG — ContractService::generatePrecompiledLinkToken() è rotto:
     *  1) referenzia la route nominata `contracts.accept` che NON esiste
     *     → RouteNotFoundException quando invocato;
     *  2) genera un token NUOVO casuale invece di usare $contract->token già salvato.
     * Il design §4 dà per scontato un link precompilato pubblico funzionante: oggi non lo è.
     */
    public function test_bug_generatePrecompiledLinkToken_route_inesistente(): void
    {
        // La route pubblica non esiste.
        $this->assertFalse($this->routeNames()->contains('contracts.accept'),
            'La route nominata contracts.accept non è registrata.');

        $contract = $this->makeContract('sent');
        $service = app(ContractService::class);

        $threw = false;
        try {
            $service->generatePrecompiledLinkToken($contract);
        } catch (\Throwable $e) {
            $threw = true;
            $this->assertStringContainsString('contracts.accept', $e->getMessage());
        }
        $this->assertTrue($threw,
            'BUG: generatePrecompiledLinkToken() lancia eccezione (route contracts.accept inesistente).');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────────

    private function makeContract(string $status): Contract
    {
        $student = $this->makeStudent();

        return Contract::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->currentYear->id,
            'contract_number' => app(ContractService::class)->generateContractNumber(),
            'type' => 'regular',
            'start_date' => '2025-10-01',
            'end_date' => '2026-06-30',
            'status' => $status,
            'token' => \Illuminate\Support\Str::random(64),
        ]);
    }

    private function makeEnrollment(Student $student): Enrollment
    {
        $type = CourseType::firstOrCreate(
            ['code' => 'IND'],
            ['name' => 'Individuale', 'duration_minutes' => 30, 'max_students' => 1,
             'price_full' => 90, 'price_reduced' => 70, 'price_annual' => 810,
             'price_monthly' => 90, 'active' => true]
        );
        $course = Course::firstOrCreate(['code' => 'PF'], ['course_type_id' => $type->id, 'name' => 'Pianoforte']);
        $offering = CourseOffering::create([
            'course_id' => $course->id,
            'academic_year_id' => $this->currentYear->id,
            'start_date' => $this->currentYear->start_date,
            'end_date' => $this->currentYear->end_date,
            'max_students' => 8,
            'current_students' => 0,
            'status' => 'active',
        ]);

        return Enrollment::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->currentYear->id,
            'course_offering_id' => $offering->id,
            'enrollment_date' => '2025-09-15',
            'start_date' => '2025-10-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
    }

    private function routeNames()
    {
        return collect(app('router')->getRoutes())->map->getName()->filter()->values();
    }
}
