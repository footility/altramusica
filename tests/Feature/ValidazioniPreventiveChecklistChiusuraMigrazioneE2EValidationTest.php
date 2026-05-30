<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Contract;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\StudentYear;
use App\Models\User;
use App\Services\OdsImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use Tests\TestCase;

/**
 * R12 · Validazioni preventive (warning) + checklist chiusura migrazione (attività #8535).
 *
 * CONTESTO — coerente con i fratelli R12 (#8532/#8533/#8534/#8551):
 * R12 è SOLO DESIGN. Non esistono `DataQualityScanner`, comando di chiusura migrazione,
 * report di certificazione persistito (`migration_closure_reports`/Setting), né rotte/voce
 * di menu. Quindi qui NON si testa un controller/UI di certificazione (= "blocco"), ma:
 *
 *   1) VALIDAZIONI PREVENTIVE = WARNING (non bloccanti): si verifica sul FLUSSO REALE
 *      (POST `store`) che le regole minime preventive del design NON blocchino il salvataggio
 *      — restano segnalazioni, mai errori 422. (Angolo "preventivo al salvataggio";
 *      complementare a #8551 che fotografa la mancanza del *surfacing* del warning.)
 *
 *   2) CHECKLIST CHIUSURA MIGRAZIONE: si specifica la checklist come INSIEME DI GATE
 *      calcolabili sul DATO VIVO (riusando il motore CF di `OdsImportService`), si semina
 *      un dataset sporco realistico e si dimostra che OGNI gate rileva correttamente e che
 *      il VERDETTO di certificabilità è esatto (gate STRUTTURALI puliti ⇒ certificabile;
 *      i WARNING annotano debito residuo senza bloccare). Questa è la logica che lo scanner
 *      mancante dovrà implementare: la checklist qui è eseguibile, non illustrativa.
 *
 *   3) FOTOGRAFA IL BLOCCO: scanner/comando/report persistito/rotte ASSENTI — registro vivo.
 *
 * Trascrizioni: parte 1 r.116-122 (dati sporchi/CF ripetuti), 454-474 (pulizia anagrafiche).
 * Doc: docs/45_R12_VALIDAZIONI_PREVENTIVE_E_CHECKLIST_CHIUSURA_MIGRAZIONE.md
 */
class ValidazioniPreventiveChecklistChiusuraMigrazioneE2EValidationTest extends TestCase
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
    // Helper — riuso ESATTO del motore CF dell'import (cleanTaxCode + TAX_CODE_REGEX)
    // ─────────────────────────────────────────────────────────────────────────

    private function cleanCf(?string $cf): string
    {
        $ref = new ReflectionClass(OdsImportService::class);
        $m = $ref->getMethod('cleanTaxCode');
        $m->setAccessible(true);

        return $m->invoke(new OdsImportService, (string) $cf);
    }

    private function cfIsValid(?string $cf): bool
    {
        if (empty($cf)) {
            return false;
        }
        $regex = (new ReflectionClass(OdsImportService::class))->getConstant('TAX_CODE_REGEX');

        return (bool) preg_match($regex, $this->cleanCf($cf));
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

    private function makeStudent(array $attr = [], bool $withYear = true): Student
    {
        $s = Student::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'Allievo',
            'birth_date' => '2000-01-01',
            'tax_code' => null,
        ], $attr));

        if ($withYear) {
            StudentYear::create([
                'student_id' => $s->id,
                'academic_year_id' => $this->year->id,
                'status' => 'enrolled',
                'privacy_consent' => true,
            ]);
        }

        return $s;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // CHECKLIST CHIUSURA MIGRAZIONE — motore eseguibile (specchio dello scanner mancante)
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Calcola la checklist di chiusura migrazione sul DATO VIVO.
     * Ogni gate: ['count' => n, 'severity' => 'structural'|'warning', 'sample' => mixed].
     * I gate STRUTTURALI, se != 0, impediscono la certificazione (debito che rompe
     * l'integrità). I WARNING annotano qualità residua ma NON bloccano (§0.1 del design).
     */
    private function closureChecklist(): array
    {
        $activeYearId = $this->year->id;

        // CF normalizzato → set di "cognome|nome" distinti (specchio di $duplicateCfs, #8533)
        $cfOwners = [];
        $nameRows = [];
        foreach (Student::all() as $s) {
            $cf = $this->cleanCf($s->tax_code);
            $nameKey = mb_strtolower(trim($s->last_name . '|' . $s->first_name));
            if ($cf !== '') {
                $cfOwners[$cf][$nameKey] = true;
            }
            $nameRows[$nameKey][] = $s->id;
        }
        $dupCf = array_filter($cfOwners, fn ($o) => count($o) > 1);
        $homonyms = array_filter($nameRows, fn ($ids) => count($ids) > 1);

        $today = Carbon::parse($this->year->end_date);

        return [
            // ---- STRUTTURALI (rompono l'integrità → bloccano la certificazione) ----
            'G1_allievi_senza_anno' => $this->gate('structural',
                Student::doesntHave('years')->pluck('id')->all()),

            'G9_fatture_senza_righe' => $this->gate('structural',
                Invoice::doesntHave('items')->pluck('id')->all()),

            'G10_contratti_orfani' => $this->gate('structural',
                Contract::whereNull('student_id')
                    ->orWhereNotIn('student_id', Student::pluck('id'))
                    ->pluck('id')->all()),

            // ---- WARNING (qualità residua, NON bloccano — §0.1 "segnalare, non bloccare") ----
            'G2_cf_assente' => $this->gate('warning',
                Student::where(fn ($q) => $q->whereNull('tax_code')->orWhere('tax_code', ''))
                    ->pluck('id')->all()),

            'G3_cf_non_valido' => $this->gate('warning',
                Student::whereNotNull('tax_code')->where('tax_code', '!=', '')->get()
                    ->filter(fn ($s) => !$this->cfIsValid($s->tax_code))
                    ->pluck('id')->values()->all()),

            'G4_cf_duplicato' => $this->gate('warning', array_keys($dupCf)),

            'G5_omonimi' => $this->gate('warning', array_keys($homonyms)),

            'G6_minore_senza_tutore' => $this->gate('warning',
                Student::whereNotNull('birth_date')->get()
                    // diffInYears in Carbon 3 è SIGNED: forzo il valore assoluto (= età all'anno).
                    ->filter(fn ($s) => Carbon::parse($s->birth_date)->diffInYears($today, true) < 18)
                    ->filter(fn ($s) => $s->guardians()->count() === 0)
                    ->pluck('id')->values()->all()),

            'G7_tutore_senza_contatti' => $this->gate('warning',
                Guardian::all()->filter(function ($g) {
                    foreach (['cell_1', 'cell_2', 'cell_3', 'cell_4', 'email_1', 'email_2', 'email_3'] as $c) {
                        if (!empty($g->{$c})) {
                            return false;
                        }
                    }

                    return true;
                })->pluck('id')->values()->all()),

            'G8_tutore_senza_consenso' => $this->gate('warning',
                Guardian::where('privacy_consent', false)->orWhereNull('privacy_consent')
                    ->pluck('id')->all()),
        ];
    }

    private function gate(string $severity, array $offenders): array
    {
        return ['count' => count($offenders), 'severity' => $severity, 'sample' => $offenders];
    }

    /** Verdetto: certificabile sse TUTTI i gate strutturali sono a zero. */
    private function isCertificabile(array $checklist): bool
    {
        foreach ($checklist as $gate) {
            if ($gate['severity'] === 'structural' && $gate['count'] > 0) {
                return false;
            }
        }

        return true;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 1. VALIDAZIONI PREVENTIVE = WARNING (non bloccanti) sul flusso reale
    // ═════════════════════════════════════════════════════════════════════════

    /** La regola preventiva "CF deve essere valido" NON blocca: è un warning. */
    public function test_validazione_preventiva_cf_non_valido_non_blocca_il_save(): void
    {
        $cfSporco = 'ABC123';
        $this->assertFalse($this->cfIsValid($cfSporco), 'Premessa: CF non valido per il motore import.');

        $this->postStudent(['tax_code' => $cfSporco])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('students', ['tax_code' => $cfSporco]);
    }

    /** La regola preventiva "minore deve avere un tutore" NON blocca: è un warning. */
    public function test_validazione_preventiva_minore_senza_tutore_non_blocca_il_save(): void
    {
        $this->postStudent(['first_name' => 'Min', 'last_name' => 'Ore', 'birth_date' => '2015-01-01'])
            ->assertSessionHasNoErrors();
        $this->assertSame(0, Student::firstWhere('last_name', 'Ore')->guardians()->count());
    }

    /** Le regole preventive sono di livello WARNING: nessun gate del catalogo è "blocking". */
    public function test_catalogo_regole_preventive_sono_tutte_warning_mai_blocking(): void
    {
        // Il dato sporco minimo (CF assente/non valido, minore senza tutore, consenso assente)
        // entra sempre: nessuna regola preventiva produce un 422.
        $this->postStudent(['tax_code' => null, 'birth_date' => '2016-05-01'])->assertSessionHasNoErrors();

        $resp = $this->actingAs($this->user)->post(route('admin.guardians.store'), [
            'first_name' => 'Anna',
            'last_name' => 'Bianchi',
            'privacy_consent' => false,
        ]);
        $resp->assertSessionHasNoErrors();
        $this->assertDatabaseHas('guardians', ['last_name' => 'Bianchi', 'privacy_consent' => false]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 2. CHECKLIST — ogni gate RILEVA il dato sporco seminato
    // ═════════════════════════════════════════════════════════════════════════

    public function test_checklist_rileva_warning_su_dataset_sporco(): void
    {
        // CF: uno assente, uno non valido, due distinti con lo stesso CF (duplicato)
        $this->makeStudent(['first_name' => 'Senza', 'last_name' => 'Cf', 'tax_code' => null]);
        $this->makeStudent(['first_name' => 'Brutto', 'last_name' => 'Cf', 'tax_code' => 'XXX']);
        $this->makeStudent(['first_name' => 'Mario', 'last_name' => 'Rossi', 'tax_code' => 'RSSMRA12A01H501Z']);
        $this->makeStudent(['first_name' => 'Maria', 'last_name' => 'Verdi', 'tax_code' => 'RSSMRA12A01H501Z']);

        // Omonimi: stesso nome+cognome su due record
        $this->makeStudent(['first_name' => 'Luca', 'last_name' => 'Neri', 'tax_code' => null]);
        $this->makeStudent(['first_name' => 'Luca', 'last_name' => 'Neri', 'tax_code' => null]);

        // Minore senza tutore
        $this->makeStudent(['first_name' => 'Gio', 'last_name' => 'Mino', 'birth_date' => '2015-01-01']);

        // Tutore senza contatti e senza consenso
        Guardian::create(['first_name' => 'Tut', 'last_name' => 'NoContatti', 'privacy_consent' => false]);

        $c = $this->closureChecklist();

        // CF assenti: "Senza Cf" + i due omonimi "Luca Neri" + il minore "Gio Mino" = 4.
        $this->assertSame(4, $c['G2_cf_assente']['count'], 'Attesi 4 CF assenti.');
        $this->assertGreaterThanOrEqual(1, $c['G3_cf_non_valido']['count'], 'Atteso ≥1 CF non valido.');
        $this->assertSame(1, $c['G4_cf_duplicato']['count'], 'Atteso 1 CF condiviso da nominativi diversi.');
        $this->assertSame(1, $c['G5_omonimi']['count'], 'Atteso 1 gruppo di omonimi.');
        $this->assertSame(1, $c['G6_minore_senza_tutore']['count'], 'Atteso 1 minore senza tutore.');
        $this->assertGreaterThanOrEqual(1, $c['G7_tutore_senza_contatti']['count']);
        $this->assertGreaterThanOrEqual(1, $c['G8_tutore_senza_consenso']['count']);
    }

    public function test_checklist_rileva_gate_strutturali_orfani(): void
    {
        $s = $this->makeStudent(['first_name' => 'Con', 'last_name' => 'Anno']);

        // Allievo SENZA anno (struttura rotta dall'import parziale)
        $this->makeStudent(['first_name' => 'Senza', 'last_name' => 'Anno'], withYear: false);

        // Fattura senza righe (orfano §6)
        Invoice::create([
            'academic_year_id' => $this->year->id,
            'student_id' => $s->id,
            'invoice_number' => 'F-ORF-1',
            'invoice_date' => '2025-10-01',
            'due_date' => '2025-11-01',
            'total_amount' => 0,
            'status' => 'draft',
        ]);

        $c = $this->closureChecklist();

        $this->assertSame(1, $c['G1_allievi_senza_anno']['count'], 'Atteso 1 allievo senza anno.');
        $this->assertSame(1, $c['G9_fatture_senza_righe']['count'], 'Attesa 1 fattura senza righe.');

        // G10 (contratto dangling): SCOPERTA — la FK su contracts.student_id rende
        // impossibile inserire un contratto orfano per id inesistente. Lo schema vivo
        // GARANTISCE il gate a zero; resta una regola dello scanner per i casi non
        // FK-protetti (es. allievo archiviato/soft-deleted in futuro).
        $this->assertSame(0, $c['G10_contratti_orfani']['count']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        Contract::create([
            'academic_year_id' => $this->year->id,
            'student_id' => 999999, // inesistente → FK la rifiuta
            'contract_number' => 'C-ORF-1',
            'type' => 'standard',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'status' => 'draft',
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 3. VERDETTO di certificabilità (gate strutturali = porta; warning = annotazione)
    // ═════════════════════════════════════════════════════════════════════════

    /** Dataset pulito a livello STRUTTURALE ⇒ migrazione certificabile (anche con warning). */
    public function test_verdetto_certificabile_con_solo_warning(): void
    {
        // Allievo completo + un warning innocuo (CF assente) → resta certificabile.
        $this->makeStudent(['first_name' => 'Ok', 'last_name' => 'Uno', 'tax_code' => 'RSSMRA12A01H501Z']);
        $this->makeStudent(['first_name' => 'Warn', 'last_name' => 'Due', 'tax_code' => null]);

        $c = $this->closureChecklist();

        $this->assertSame(0, $c['G1_allievi_senza_anno']['count']);
        $this->assertSame(0, $c['G9_fatture_senza_righe']['count']);
        $this->assertSame(0, $c['G10_contratti_orfani']['count']);
        $this->assertGreaterThanOrEqual(1, $c['G2_cf_assente']['count'], 'C’è un warning residuo…');
        $this->assertTrue($this->isCertificabile($c), '…ma i warning NON impediscono la certificazione.');
    }

    /** Un solo orfano STRUTTURALE ⇒ migrazione NON certificabile finché non risolto. */
    public function test_verdetto_non_certificabile_con_gate_strutturale_aperto(): void
    {
        $this->makeStudent(['first_name' => 'Senza', 'last_name' => 'Anno'], withYear: false);

        $c = $this->closureChecklist();

        $this->assertSame(1, $c['G1_allievi_senza_anno']['count']);
        $this->assertFalse($this->isCertificabile($c), 'Un gate strutturale aperto blocca la certificazione.');
    }

    /** Loop "segnala → risolvi → ri-certifica": risolto l'orfano, il verdetto diventa verde. */
    public function test_loop_risolvi_orfano_poi_certificabile(): void
    {
        $s = $this->makeStudent(['first_name' => 'Da', 'last_name' => 'Sistemare'], withYear: false);

        $this->assertFalse($this->isCertificabile($this->closureChecklist()));

        // Azione correttiva: aggancio l'anno mancante.
        StudentYear::create([
            'student_id' => $s->id,
            'academic_year_id' => $this->year->id,
            'status' => 'enrolled',
            'privacy_consent' => true,
        ]);

        $this->assertTrue($this->isCertificabile($this->closureChecklist()), 'Risolto l’orfano ⇒ ri-certificabile.');
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 4. BLOCCO — scanner/comando/report persistito/rotte ASSENTI (registro vivo)
    // ═════════════════════════════════════════════════════════════════════════

    public function test_blocco_scanner_e_report_chiusura_assenti(): void
    {
        $this->assertFalse(class_exists(\App\Services\DataQualityScanner::class),
            'DataQualityScanner non atteso in R12 (solo design).');
        $this->assertFalse(class_exists(\App\Services\MigrationClosureService::class),
            'Servizio di chiusura migrazione non atteso in R12.');

        // Nessun comando artisan di chiusura/certificazione migrazione.
        $commands = array_keys(Artisan::all());
        foreach ($commands as $name) {
            $this->assertStringNotContainsString('migration:closure', $name);
            $this->assertStringNotContainsString('quality:certify', $name);
        }

        // Nessuna tabella di report persistito della certificazione.
        $this->assertFalse(
            \Illuminate\Support\Facades\Schema::hasTable('migration_closure_reports'),
            'Tabella report chiusura non attesa in R12.'
        );
    }

    public function test_blocco_rotte_pannello_qualita_assenti(): void
    {
        $this->assertFalse(Route::has('admin.data-quality.index'));
        $this->assertFalse(Route::has('admin.migration-closure.index'));
        // L'unica voce presente resta la dashboard.
        $this->assertTrue(Route::has('admin.dashboard'));
    }
}
