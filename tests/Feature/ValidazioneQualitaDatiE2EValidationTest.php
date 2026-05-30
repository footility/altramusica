<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\User;
use App\Services\OdsImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use Tests\TestCase;

/**
 * R12 · Controllo finale OMBRELLO — Validazione qualità dati (attività #8551).
 *
 * CONTESTO: R12 (#8532, doc 39) è SOLO DESIGN. Due sotto-controlli finali sono già
 * stati eseguiti e archiviati:
 *   - #8533 (doc 40, QualitaDatiAnomalieE2EValidationTest): regole di RILEVAMENTO
 *     anomalie sul dato vivo (duplicati CF/nome, omonimi, campi critici, orfani);
 *   - #8534 (doc 42, MergeGuidatoReversibileE2EValidationTest): semantica del MERGE
 *     guidato reversibile (ribalta FK, dedup pivot, archivia, log+revert).
 *
 * Questo controllo OMBRELLA i due e aggiunge l'angolo non ancora coperto richiesto
 * dall'attività #8551 — le **VALIDAZIONI WARNING** del principio cardine §0.1 del
 * design ("Segnalare, NON bloccare"): un dato sporco non deve MAI impedire di
 * iscrivere un allievo o registrare un pagamento oggi (warning inline, mai errore
 * bloccante). Lo verifica sui FLUSSI OPERATIVI REALI (POST ai controller store):
 *
 *   1) PERMISSIVITÀ — i salvataggi accettano dato anomalo SENZA 422:
 *      CF assente/non valido, CF duplicato, minore senza tutore, tutore senza alcun
 *      contatto e senza consenso, fattura senza righe → tutti persistiti, non bloccati.
 *   2) LOOP "segnala→rileva" — lo stesso dato sporco salvato via HTTP viene poi
 *      RILEVATO come anomalia dalla spec di rilevamento (#8533) → il debito di qualità
 *      è raccolto, non perso (§0.1: "raccoglie il debito e lo rende lavorabile dopo").
 *   3) BLOCCO — il livello di SURFACING del warning (avviso inline al salvataggio +
 *      badge "Qualità dati") è ASSENTE: oggi il save è permissivo ma SILENZIOSO
 *      (flash solo 'success', mai 'warning'); manca lo strato §0.1/§2 che mostra
 *      l'avviso. Più la chiusura ombrello: le due sotto-validazioni esistono.
 *
 * Nessuna implementazione effettuata: registro vivo del gap, coerente con doc 39 §10.
 * Trascrizioni: parte 1 r.116-122 (dati sporchi/CF ripetuti), 454-474 (pulizia anagrafiche).
 */
class ValidazioneQualitaDatiE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $year;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('acl:sync', ['--reset-defaults' => true]);

        $this->year = AcademicYear::create([
            'name' => '2025/26',
            'slug' => '2025-26',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────────

    /** Riusa ESATTAMENTE la validità CF dell'import (cleanTaxCode + TAX_CODE_REGEX). */
    private function taxCodeIsValid(?string $cf): bool
    {
        if (empty($cf)) {
            return false;
        }
        $ref = new ReflectionClass(OdsImportService::class);
        $regex = $ref->getConstant('TAX_CODE_REGEX');
        $clean = $ref->getMethod('cleanTaxCode');
        $clean->setAccessible(true);

        return (bool) preg_match($regex, $clean->invoke(new OdsImportService, $cf));
    }

    /** Spec di rilevamento duplicati per CF normalizzato (specchio di $duplicateCfs, #8533). */
    private function taxCodeIsDuplicated(string $cf): bool
    {
        $ref = new ReflectionClass(OdsImportService::class);
        $clean = $ref->getMethod('cleanTaxCode');
        $clean->setAccessible(true);
        $svc = new OdsImportService;
        $target = $clean->invoke($svc, $cf);

        return Student::whereNotNull('tax_code')->get()
            ->filter(fn ($s) => $clean->invoke($svc, $s->tax_code) === $target)
            ->count() > 1;
    }

    private function postStudent(array $over = [])
    {
        return $this->actingAs($this->user)->post(route('admin.students.store'), array_merge([
            'academic_year_id' => $this->year->id, // hidden del form reale
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'status' => 'interested',
        ], $over));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 0. OMBRELLO — le due sotto-validazioni del controllo finale R12 esistono
    // ═════════════════════════════════════════════════════════════════════════

    public function test_ombrello_sotto_validazioni_8533_e_8534_presenti(): void
    {
        // #8533 — rilevamento anomalie; #8534 — merge guidato reversibile.
        $this->assertTrue(
            class_exists(\Tests\Feature\QualitaDatiAnomalieE2EValidationTest::class),
            'Manca la sotto-validazione #8533 (rilevamento anomalie).'
        );
        $this->assertTrue(
            class_exists(\Tests\Feature\MergeGuidatoReversibileE2EValidationTest::class),
            'Manca la sotto-validazione #8534 (merge guidato).'
        );
        $this->assertFileExists(base_path('docs/40_R12_CONTROLLO_FINALE_VALIDAZIONE_QUALITA_DATI_ANOMALIE.md'));
        $this->assertFileExists(base_path('docs/42_R12_CONTROLLO_FINALE_VALIDAZIONE_MERGE_GUIDATO.md'));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 1. VALIDAZIONI WARNING — §0.1 "Segnalare, non bloccare": il save è PERMISSIVO
    // ═════════════════════════════════════════════════════════════════════════

    /** §0.1: CF assente e data nascita mancante non bloccano l'inserimento. */
    public function test_store_studente_accetta_cf_assente_e_data_mancante(): void
    {
        $response = $this->postStudent(['tax_code' => null, 'birth_date' => null]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', ['first_name' => 'Mario', 'last_name' => 'Rossi', 'tax_code' => null]);
    }

    /** §0.1: un CF palesemente non valido entra lo stesso (warning, non errore). */
    public function test_store_studente_accetta_cf_non_valido(): void
    {
        $cfSporco = 'ABC';
        $this->assertFalse($this->taxCodeIsValid($cfSporco), 'Premessa: il CF dev’essere non valido per l’import.');

        $this->postStudent(['tax_code' => $cfSporco])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', ['tax_code' => $cfSporco]);
    }

    /** §0.1: due allievi con lo STESSO CF si salvano entrambi (nessun unique che blocca). */
    public function test_store_studente_non_blocca_cf_duplicato(): void
    {
        $cf = 'RSSMRA12A01H501Z';
        $this->postStudent(['first_name' => 'Mario', 'tax_code' => $cf])->assertSessionHasNoErrors();
        $this->postStudent(['first_name' => 'Maria', 'tax_code' => $cf])->assertSessionHasNoErrors();

        $this->assertSame(2, Student::where('tax_code', $cf)->count());
        // …e il duplicato resta RILEVABILE per la pulizia successiva (#8533).
        $this->assertTrue($this->taxCodeIsDuplicated($cf));
    }

    /** §0.1 (vincolo §0.1 del design): un MINORE si iscrive senza tutore, oggi. */
    public function test_store_minore_senza_tutore_non_e_bloccato(): void
    {
        $this->postStudent(['birth_date' => '2015-01-01'])->assertSessionHasNoErrors();

        $minore = Student::firstWhere('first_name', 'Mario');
        $this->assertNotNull($minore);
        $this->assertCount(0, $minore->guardians, 'Nessun tutore richiesto al salvataggio.');
    }

    /** §0.1/§5: un tutore SENZA contatti e SENZA consenso privacy entra (anomalia, non blocco). */
    public function test_store_tutore_accetta_nessun_contatto_e_senza_consenso(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.guardians.store'), [
            'first_name' => 'Anna',
            'last_name' => 'Bianchi',
            'privacy_consent' => false,
        ]);

        $response->assertSessionHasNoErrors();
        $g = Guardian::firstWhere('last_name', 'Bianchi');
        $this->assertNotNull($g);
        $this->assertNull($g->cell_1);
        $this->assertNull($g->email_1);
        $this->assertFalse((bool) $g->privacy_consent);
    }

    /** §0.1/§6: una fattura si registra SENZA righe (orfano possibile, non bloccato). */
    public function test_store_fattura_non_richiede_righe(): void
    {
        $studentId = $this->postStudent()->isRedirect() ? Student::first()->id : Student::first()->id;

        $response = $this->actingAs($this->user)->post(route('admin.invoices.store'), [
            'academic_year_id' => $this->year->id,
            'student_id' => $studentId,
            'invoice_number' => '', // hidden del form: vuoto → generato dal service
            'invoice_date' => '2025-09-01',
            'due_date' => '2025-09-30',
            'subtotal' => 100,
            'total_amount' => 100,
            'status' => 'sent',
        ]);

        $response->assertSessionHasNoErrors();
        $inv = Invoice::firstWhere('student_id', $studentId);
        $this->assertNotNull($inv);
        $this->assertCount(0, $inv->items, 'Fattura senza righe = orfano §6, ma il salvataggio non lo impedisce.');
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 2. LOOP "segnala → rileva": il dato sporco salvato via HTTP è poi RILEVABILE
    // ═════════════════════════════════════════════════════════════════════════

    public function test_dato_sporco_salvato_via_http_resta_rilevabile_come_anomalia(): void
    {
        // Salvataggio PERMISSIVO di due schede con CF non valido e duplicato.
        $cfSporco = 'XX';
        $this->postStudent(['first_name' => 'Luca', 'tax_code' => $cfSporco])->assertSessionHasNoErrors();
        $this->postStudent(['first_name' => 'Lucia', 'tax_code' => $cfSporco])->assertSessionHasNoErrors();

        // Il debito NON è perso: lo scanner (#8533) lo ritrova sul dato vivo.
        $this->assertFalse($this->taxCodeIsValid($cfSporco), 'CF non valido → anomalia 🟠 "CF assente/invalido".');
        $this->assertTrue($this->taxCodeIsDuplicated($cfSporco), 'Stesso CF su 2 schede → anomalia "duplicato".');
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 3. BLOCCO — manca lo STRATO DI SURFACING del warning (§0.1 "warning inline", §2 badge)
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Oggi il save è permissivo ma SILENZIOSO: con dato sporco il flash è solo
     * 'success', mai un 'warning'/'data_quality_warnings'. Il design §0.1 vuole un
     * "warning inline" e §2 un badge: quel livello è da implementare (fuori scope R12).
     */
    public function test_blocco_warning_inline_al_salvataggio_assente(): void
    {
        $response = $this->postStudent(['tax_code' => 'ABC', 'birth_date' => null]);

        $response->assertSessionHas('success'); // permissivo
        $response->assertSessionMissing('warning');
        $response->assertSessionMissing('data_quality_warnings');
    }

    /** §2: la voce/badge "Qualità dati" e lo scanner non sono ancora montati. */
    public function test_blocco_rotte_qualita_dati_assenti(): void
    {
        $this->assertFalse(Route::has('admin.data-quality.index'), 'Rotta pannello qualità dati non attesa in R12.');
        $this->assertFalse(class_exists(\App\Services\DataQualityScanner::class), 'Scanner non atteso in R12.');
        // L'ancora operativa esiste (la dashboard), il pannello no.
        $this->assertTrue(Route::has('admin.dashboard'));
    }
}
