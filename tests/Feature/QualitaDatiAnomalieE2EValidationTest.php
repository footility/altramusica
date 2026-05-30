<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Contract;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\StudentYear;
use App\Services\OdsImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

/**
 * R12 · Controllo finale — Report anomalie su tutte le entità (attività #8533).
 *
 * CONTESTO: R12 (#8532, doc 39) è SOLO DESIGN. Il §10 "Impatti tecnici" dichiara
 * esplicitamente che l'implementazione NON è parte di R12. Non esistono il ruleset
 * condiviso `DataQualityRuleset`, lo scanner `DataQualityScanner`, il controller
 * `Admin\DataQualityController`, il `StudentMergeService`, la tabella delle eccezioni
 * `data_quality_dismissals` né rotte/voce di menu "Qualità dati ▸ Anomalie".
 * Quindi un E2E su controller/UI del pannello NON è eseguibile (= "blocco").
 *
 * Questo test fa due cose oneste:
 *   1) VALIDA LE REGOLE DI RILEVAMENTO del design sul DATO VIVO (record, non righe ODS),
 *      sulle quattro categorie richieste dall'attività #8533:
 *        - DUPLICATI per CF e per nome+cognome(+data nascita)  (riusa logica $duplicateCfs);
 *        - OMONIMI: stesso nome, CF/data diversi               (riusa logica $homonymNames);
 *        - CAMPI CRITICI MANCANTI: minore senza tutore di fatturazione, nessun contatto,
 *          consenso privacy mancante, CF assente/non valido, data nascita mancante;
 *        - RECORD ORFANI: contratto senza iscrizione, fattura senza righe, allievo senza
 *          anno, tutore senza allievi, iscrizione a offerta inesistente, contratto con
 *          riferimento allievo non risolvibile (dangling, da import parziale).
 *      Dimostra inoltre il RIUSO del motore di `OdsImportService` (stessa validazione CF:
 *      `cleanTaxCode` + `TAX_CODE_REGEX`) → "pulito nel pannello = pulito all'import".
 *   2) FOTOGRAFA IL BLOCCO: assert che ruleset/scanner/controller/merge service/tabella
 *      eccezioni/rotte del §10 sono ASSENTI — registro vivo del gap da colmare.
 *
 * Trascrizioni: parte 1 r.116-122 (dati sporchi/CF ripetuti), 454-474 (pulizia anagrafiche).
 */
class QualitaDatiAnomalieE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();
        $this->year = AcademicYear::create([
            'name' => '2025/26',
            'slug' => '2025-26',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper di scena (SOLO model esistenti)
    // ─────────────────────────────────────────────────────────────────────────

    private function makeStudent(string $first, string $last, ?string $cf = null, ?string $birth = '2012-01-01'): Student
    {
        return Student::create([
            'first_name' => $first,
            'last_name' => $last,
            'birth_date' => $birth,
            'tax_code' => $cf,
        ]);
    }

    private function makeGuardian(string $first, string $last, array $attrs = []): Guardian
    {
        return Guardian::create(array_merge([
            'first_name' => $first,
            'last_name' => $last,
            'relationship' => 'other',
            'privacy_consent' => true,
        ], $attrs));
    }

    private function attach(Student $s, Guardian $g, bool $primary, bool $billing): void
    {
        $s->guardians()->attach($g->id, [
            'relationship_type' => 'mother',
            'is_primary' => $primary,
            'is_billing_contact' => $billing,
        ]);
    }

    private function offering(string $courseCode): CourseOffering
    {
        $type = CourseType::firstOrCreate(['code' => substr($courseCode, 0, 2)], ['name' => 'Strumento', 'duration_minutes' => 30]);
        $course = Course::firstOrCreate(['code' => $courseCode], ['course_type_id' => $type->id, 'name' => 'Corso']);

        return CourseOffering::firstOrCreate(
            ['course_id' => $course->id, 'academic_year_id' => $this->year->id],
            ['status' => 'active'],
        );
    }

    private function enroll(Student $s, CourseOffering $o): Enrollment
    {
        return Enrollment::create([
            'academic_year_id' => $this->year->id,
            'student_id' => $s->id,
            'course_offering_id' => $o->id,
            'enrollment_date' => '2025-09-01',
            'start_date' => '2025-09-01',
            'status' => 'active',
        ]);
    }

    private function contract(Student $s): Contract
    {
        return Contract::create([
            'academic_year_id' => $this->year->id,
            'student_id' => $s->id,
            'contract_number' => 'C-' . $s->id . '-' . DB::table('contracts')->count(),
            'status' => 'draft',
            'start_date' => '2025-09-01',
        ]);
    }

    private function invoice(Student $s): Invoice
    {
        return Invoice::create([
            'academic_year_id' => $this->year->id,
            'student_id' => $s->id,
            'invoice_number' => 'INV-' . $s->id . '-' . DB::table('invoices')->count(),
            'invoice_date' => '2025-09-01',
            'due_date' => '2025-09-30',
            'subtotal' => 100,
            'total_amount' => 100,
            'status' => 'sent',
        ]);
    }

    /** Accesso a un metodo protected di OdsImportService (prova del riuso, non duplicazione). */
    private function odsMethod(string $name): ReflectionMethod
    {
        $m = (new ReflectionClass(OdsImportService::class))->getMethod($name);
        $m->setAccessible(true);

        return $m;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // MOTORE DI SCANSIONE (doc §10 DataQualityScanner) — sola lettura sul dato vivo.
    // Specchio onesto del design: quando il servizio verrà implementato basterà
    // spostarci questa logica mantenendo gli stessi risultati.
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §3: CF condiviso da più allievi (riusa la nozione $duplicateCfs dell'import). */
    private function duplicatesByTaxCode(): array
    {
        $clean = $this->odsMethod('cleanTaxCode');
        $svc = new OdsImportService;

        return Student::whereNotNull('tax_code')->get()
            ->groupBy(fn ($s) => $clean->invoke($svc, $s->tax_code))
            ->filter(fn ($g) => $g->count() > 1)
            ->keys()->all();
    }

    /** doc §3: stesso nome+cognome+data nascita → quasi certamente stessa persona. */
    private function duplicatesByNameBirth(): array
    {
        return Student::all()
            ->groupBy(fn ($s) => mb_strtolower(trim($s->first_name . '|' . $s->last_name . '|' . optional($s->birth_date)->format('Y-m-d'))))
            ->filter(fn ($g) => $g->count() > 1)
            ->keys()->all();
    }

    /** doc §4: stesso nome+cognome ma CF/data nascita DIVERSI (riusa la nozione $homonymNames). */
    private function homonyms(): array
    {
        return Student::all()
            ->groupBy(fn ($s) => mb_strtolower(trim($s->first_name . '|' . $s->last_name)))
            ->filter(function ($g) {
                if ($g->count() < 2) {
                    return false;
                }
                // omonimi veri: almeno un CF o una data di nascita differente nel gruppo
                $cf = $g->pluck('tax_code')->unique();
                $bd = $g->map(fn ($s) => optional($s->birth_date)->format('Y-m-d'))->unique();

                return $cf->count() > 1 || $bd->count() > 1;
            })
            ->keys()->all();
    }

    /** doc §5: il CF è valido secondo la STESSA regola dell'import (TAX_CODE_REGEX). */
    private function taxCodeIsValid(?string $cf): bool
    {
        if (empty($cf)) {
            return false;
        }
        $regex = (new ReflectionClass(OdsImportService::class))->getConstant('TAX_CODE_REGEX');
        $clean = $this->odsMethod('cleanTaxCode')->invoke(new OdsImportService, $cf);

        return (bool) preg_match($regex, $clean);
    }

    private function isMinor(Student $s): bool
    {
        return $s->birth_date !== null && $s->birth_date->age < 18;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 1. SUBSTRATO + RIUSO DEL MOTORE ESISTENTE (doc §0.6/§1/§10)
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §1: tutte le entità scansionate dal pannello esistono già a schema. */
    public function test_substrato_entita_presenti(): void
    {
        foreach (['students', 'guardians', 'student_guardian', 'student_years', 'enrollments', 'contracts', 'invoices', 'invoice_items', 'payments'] as $t) {
            $this->assertTrue(Schema::hasTable($t), "tabella {$t} attesa nel substrato");
        }
    }

    /** doc §0.6/§10: il motore anomalie ESISTE già in OdsImportService → fonte unica di regole. */
    public function test_motore_anomalie_esiste_in_ods_import_service(): void
    {
        $ref = new ReflectionClass(OdsImportService::class);
        $this->assertTrue($ref->hasMethod('detectRowAnomalies'), 'detectRowAnomalies è il cuore riusabile');
        $this->assertTrue($ref->hasMethod('cleanTaxCode'));
        $this->assertNotNull($ref->getConstant('TAX_CODE_REGEX'));
    }

    /** doc §5/§10: la validazione CF del pannello è IDENTICA a quella dell'import (single source). */
    public function test_validazione_cf_identica_allimport(): void
    {
        // valido (normalizzato): spazi/minuscolo tollerati come all'import
        $this->assertTrue($this->taxCodeIsValid('rss mra 10a01 h501u'));
        $this->assertTrue($this->taxCodeIsValid('RSSMRA10A01H501U'));
        // invalidi
        $this->assertFalse($this->taxCodeIsValid('RSSMRA10A01H501'));  // troppo corto
        $this->assertFalse($this->taxCodeIsValid('1234567890123456')); // formato errato
        $this->assertFalse($this->taxCodeIsValid(null));               // assente
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 2. DUPLICATI (doc §3) — CF e nome+cognome
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §3: stesso CF su due schede → possibile duplicato (anche con CF scritto in modo diverso). */
    public function test_duplicati_per_codice_fiscale(): void
    {
        $this->makeStudent('Mario', 'Rossi', 'RSSMRA10A01H501U');
        $this->makeStudent('Mario', 'Rossi', 'rssmra10a01h501u'); // stesso CF, normalizza uguale
        $this->makeStudent('Lucia', 'Bianchi', 'BNCLCU12A41H501Z'); // CF unico

        $dups = $this->duplicatesByTaxCode();
        $this->assertCount(1, $dups);
        $this->assertContains('RSSMRA10A01H501U', $dups);
    }

    /** doc §3: stesso nome+cognome+data nascita → quasi certamente la stessa persona inserita 2 volte. */
    public function test_duplicati_per_nome_cognome_e_data(): void
    {
        $this->makeStudent('Anna', 'Verdi', null, '2010-05-10');
        $this->makeStudent('Anna', 'Verdi', null, '2010-05-10'); // doppione
        $this->makeStudent('Anna', 'Verdi', null, '2008-01-01'); // stesso nome, data diversa → NON duplicato

        $dups = $this->duplicatesByNameBirth();
        $this->assertCount(1, $dups);
        $this->assertContains('anna|verdi|2010-05-10', $dups);
    }

    /** doc §3: CF unici e nomi unici → nessun duplicato (nessun falso positivo). */
    public function test_nessun_duplicato_quando_dati_distinti(): void
    {
        $this->makeStudent('Mario', 'Rossi', 'RSSMRA10A01H501U', '2010-01-01');
        $this->makeStudent('Lucia', 'Bianchi', 'BNCLCU12A41H501Z', '2012-01-01');

        $this->assertCount(0, $this->duplicatesByTaxCode());
        $this->assertCount(0, $this->duplicatesByNameBirth());
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 3. OMONIMI (doc §4) — stesso nome, persone diverse
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §4: stesso nome+cognome ma CF/data diversi → due persone (omonimi, NON duplicato). */
    public function test_omonimi_stesso_nome_dati_diversi(): void
    {
        $this->makeStudent('Mario', 'Rossi', 'RSSMRA10A01H501U', '2010-01-01');
        $this->makeStudent('Mario', 'Rossi', 'RSSMRA08H14H501K', '2008-06-14'); // diverso CF + data

        $this->assertContains('mario|rossi', $this->homonyms());
        // e NON è un duplicato per nome+data (date diverse)
        $this->assertCount(0, $this->duplicatesByNameBirth());
    }

    /** doc §3/§4: se invece CF o data COINCIDONO, sale a duplicato e NON resta solo omonimia. */
    public function test_omonimi_con_dati_coincidenti_sono_duplicato(): void
    {
        $this->makeStudent('Mario', 'Rossi', 'RSSMRA10A01H501U', '2010-01-01');
        $this->makeStudent('Mario', 'Rossi', 'RSSMRA10A01H501U', '2010-01-01'); // tutto uguale

        $this->assertCount(0, $this->homonyms()); // niente CF/data diversi → non è "solo omonimia"
        $this->assertCount(1, $this->duplicatesByTaxCode());
        $this->assertCount(1, $this->duplicatesByNameBirth());
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 4. CAMPI CRITICI MANCANTI (doc §5)
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §5 (🔴): minorenne senza tutore di FATTURAZIONE → non si emette contratto/fattura. */
    public function test_critico_minore_senza_tutore_fatturazione(): void
    {
        $minore = $this->makeStudent('Lucia', 'Bianchi', null, '2014-03-01'); // ~11 anni
        $g = $this->makeGuardian('Anna', 'Bianchi', ['cell_1' => '3331112233']);
        $this->attach($minore, $g, primary: true, billing: false); // tutore SENZA flag fatturazione

        $maggiorenne = $this->makeStudent('Paolo', 'Neri', null, '2000-01-01');

        $this->assertTrue($this->isMinor($minore));

        // scanner: minori senza alcun tutore is_billing_contact
        $minoriSenzaBilling = Student::all()
            ->filter(fn ($s) => $this->isMinor($s))
            ->filter(fn ($s) => ! $s->guardians()->wherePivot('is_billing_contact', true)->exists())
            ->pluck('id');

        $this->assertTrue($minoriSenzaBilling->contains($minore->id));
        $this->assertFalse($minoriSenzaBilling->contains($maggiorenne->id)); // maggiorenne non richiede tutore
    }

    /** doc §5 (🔴): nessun contatto (email/telefono) sul tutore primario → nessuna comunicazione (R9). */
    public function test_critico_nessun_contatto_su_tutore(): void
    {
        $senza = $this->makeGuardian('Senza', 'Contatti'); // nessuna cell/email
        $con = $this->makeGuardian('Con', 'Contatti', ['email_1' => 'ok@example.com']);

        $hasContact = fn (Guardian $g) => collect([$g->cell_1, $g->cell_2, $g->cell_3, $g->cell_4, $g->email_1, $g->email_2, $g->email_3])
            ->filter()->isNotEmpty();

        $this->assertFalse($hasContact($senza->fresh()));
        $this->assertTrue($hasContact($con->fresh()));
    }

    /** doc §5 (🔴, GDPR): consenso privacy mancante sul tutore che riceve comunicazioni. */
    public function test_critico_consenso_privacy_mancante(): void
    {
        $no = $this->makeGuardian('No', 'Consenso', ['privacy_consent' => false]);
        $si = $this->makeGuardian('Si', 'Consenso', ['privacy_consent' => true]);

        $senzaConsenso = Guardian::where('privacy_consent', false)->orWhereNull('privacy_consent')->pluck('id');
        $this->assertTrue($senzaConsenso->contains($no->id));
        $this->assertFalse($senzaConsenso->contains($si->id));
    }

    /** doc §5 (🟠): CF assente o non valido + data di nascita mancante = campi critici. */
    public function test_critico_cf_assente_o_invalido_e_data_mancante(): void
    {
        $ok = $this->makeStudent('Ok', 'Valido', 'RSSMRA10A01H501U', '2010-01-01');
        $cfAssente = $this->makeStudent('Senza', 'Cf', null, '2011-01-01');
        $cfInvalido = $this->makeStudent('Cf', 'Rotto', 'ABC123', '2011-01-01');
        $senzaData = $this->makeStudent('Senza', 'Data', 'BNCLCU12A41H501Z', null);

        $cfMancanteOInvalido = Student::all()->filter(fn ($s) => ! $this->taxCodeIsValid($s->tax_code))->pluck('id');
        $this->assertTrue($cfMancanteOInvalido->contains($cfAssente->id));
        $this->assertTrue($cfMancanteOInvalido->contains($cfInvalido->id));
        $this->assertFalse($cfMancanteOInvalido->contains($ok->id));

        $senzaNascita = Student::whereNull('birth_date')->pluck('id');
        $this->assertTrue($senzaNascita->contains($senzaData->id));
        $this->assertFalse($senzaNascita->contains($ok->id));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 5. RECORD ORFANI (doc §6) — integrità referenziale
    // ═════════════════════════════════════════════════════════════════════════

    /** attività #8533 / doc §6: contratto per un allievo SENZA iscrizione (Enrollment). */
    public function test_orfano_contratto_senza_iscrizione(): void
    {
        $iscritto = $this->makeStudent('Con', 'Iscrizione');
        $this->enroll($iscritto, $this->offering('PF1'));
        $contrIscritto = $this->contract($iscritto);

        $senzaIscr = $this->makeStudent('No', 'Iscrizione');
        $contrOrfano = $this->contract($senzaIscr); // contratto ma 0 enrollment

        $orfani = Contract::whereDoesntHave('student.enrollments')->pluck('id');
        $this->assertTrue($orfani->contains($contrOrfano->id));
        $this->assertFalse($orfani->contains($contrIscritto->id));
    }

    /** attività #8533 / doc §6: fattura SENZA righe (nessun InvoiceItem). */
    public function test_orfano_fattura_senza_righe(): void
    {
        $s = $this->makeStudent('Fat', 'Tura');
        $invVuota = $this->invoice($s);
        $invPiena = $this->invoice($s);
        $invPiena->items()->create([
            'item_type' => 'enrollment',
            'description' => 'Quota corso',
            'quantity' => 1,
            'unit_price' => 100,
            'total_price' => 100,
        ]);

        $senzaRighe = Invoice::whereDoesntHave('items')->pluck('id');
        $this->assertTrue($senzaRighe->contains($invVuota->id));
        $this->assertFalse($senzaRighe->contains($invPiena->id));
    }

    /** doc §6 (🟠): allievo senza alcun anno (StudentYear) — import parziale tipico. */
    public function test_orfano_allievo_senza_anno(): void
    {
        $senza = $this->makeStudent('Orfano', 'DiAnno');
        $con = $this->makeStudent('Con', 'Anno');
        StudentYear::create([
            'student_id' => $con->id,
            'academic_year_id' => $this->year->id,
            'status' => 'enrolled',
        ]);

        $orfani = Student::whereDoesntHave('years')->pluck('id');
        $this->assertTrue($orfani->contains($senza->id));
        $this->assertFalse($orfani->contains($con->id));
    }

    /** doc §6 (🟡): tutore senza allievi (rimasto dopo cancellazioni). */
    public function test_orfano_tutore_senza_allievi(): void
    {
        $solo = $this->makeGuardian('Solo', 'Tutore');
        $legato = $this->makeGuardian('Legato', 'Tutore');
        $s = $this->makeStudent('Al', 'Lievo');
        $this->attach($s, $legato, primary: true, billing: true);

        $orfani = Guardian::whereDoesntHave('students')->pluck('id');
        $this->assertTrue($orfani->contains($solo->id));
        $this->assertFalse($orfani->contains($legato->id));
    }

    /**
     * doc §6 (🔴): contratto con riferimento allievo NON risolvibile (dangling).
     *
     * Caveat reale per l'implementazione: `Student` usa SoftDeletes, e il design §3/§6 vuole
     * proprio "archivia, non cancella". Quando un allievo viene ARCHIVIATO (soft-delete), il suo
     * contratto resta e — per via dello scope globale SoftDeletes — `whereDoesntHave('student')`
     * lo segnala come orfano. Lo scanner deve quindi distinguere "allievo archiviato" da "allievo
     * inesistente" usando `withTrashed()` per evitare falsi orfani sugli archiviati. Qui lo
     * dimostriamo: query pulita = 0 orfani; dopo archiviazione l'orfano emerge e con `withTrashed`
     * sparisce di nuovo.
     */
    public function test_orfano_contratto_riferimento_allievo_e_soft_delete(): void
    {
        $s = $this->makeStudent('Sara', 'Verra', null, '2010-01-01');
        $c = $this->contract($s);

        // 1) tutto risolve → 0 orfani
        $this->assertCount(0, Contract::whereDoesntHave('student')->get());

        // 2) allievo ARCHIVIATO (soft-delete): la query naive lo vede come orfano
        $s->delete();
        $naive = Contract::whereDoesntHave('student')->pluck('id');
        $this->assertTrue($naive->contains($c->id), 'soft-delete dell\'allievo → falso orfano se non gestito');

        // 3) lo scanner corretto usa withTrashed → nessun falso orfano sugli archiviati
        $corretto = Contract::whereDoesntHave('student', fn ($q) => $q->withTrashed())->pluck('id');
        $this->assertFalse($corretto->contains($c->id));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 6. SEVERITÀ E ORDINAMENTO (doc §0.4/§2)
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §0.4: tre livelli (🔴 critica / 🟠 da sistemare / 🟡 cosmetica) ordinano la lista. */
    public function test_anomalie_ordinate_per_gravita(): void
    {
        $anomalie = [
            ['type' => 'citta_mancante', 'severity' => 'cosmetica'],
            ['type' => 'minore_senza_tutore_fatturazione', 'severity' => 'critica'],
            ['type' => 'cf_invalido', 'severity' => 'da_sistemare'],
        ];

        $rank = ['critica' => 0, 'da_sistemare' => 1, 'cosmetica' => 2];
        usort($anomalie, fn ($a, $b) => $rank[$a['severity']] <=> $rank[$b['severity']]);

        $this->assertSame('critica', $anomalie[0]['severity']);     // si lavora dall'alto
        $this->assertSame('da_sistemare', $anomalie[1]['severity']);
        $this->assertSame('cosmetica', $anomalie[2]['severity']);
    }

    /** doc §8: stato vuoto positivo — dati puliti → nessuna anomalia su nessuna categoria. */
    public function test_stato_pulito_nessuna_anomalia(): void
    {
        $s = $this->makeStudent('Pulito', 'Allievo', 'RSSMRA10A01H501U', '2014-01-01');
        $g = $this->makeGuardian('Mamma', 'Pulita', ['email_1' => 'mamma@example.com', 'privacy_consent' => true]);
        $this->attach($s, $g, primary: true, billing: true);
        StudentYear::create(['student_id' => $s->id, 'academic_year_id' => $this->year->id, 'status' => 'enrolled']);

        $this->assertCount(0, $this->duplicatesByTaxCode());
        $this->assertCount(0, $this->duplicatesByNameBirth());
        $this->assertCount(0, $this->homonyms());
        $this->assertCount(0, Student::whereDoesntHave('years')->get());
        $this->assertCount(0, Guardian::whereDoesntHave('students')->get());
        $this->assertCount(0, Contract::whereDoesntHave('student')->get());
        $this->assertTrue($this->taxCodeIsValid($s->tax_code));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 7. BLOCCO — fotografia del gap di implementazione (doc §10, NON parte di R12)
    // ═════════════════════════════════════════════════════════════════════════

    /** §10: il ruleset condiviso (estratto da OdsImportService) NON esiste ancora. */
    public function test_blocco_data_quality_ruleset_assente(): void
    {
        $this->assertFalse(
            class_exists(\App\Services\DataQualityRuleset::class),
            'DataQualityRuleset (regole estratte da OdsImportService, fonte unica) da implementare',
        );
        // il motore-sorgente da cui estrarre, invece, esiste già (substrato)
        $this->assertTrue(class_exists(OdsImportService::class));
    }

    /** §10: lo scanner sola-lettura sul dato vivo NON esiste (qui è solo spec eseguibile). */
    public function test_blocco_data_quality_scanner_assente(): void
    {
        $this->assertFalse(
            class_exists(\App\Services\DataQualityScanner::class),
            'DataQualityScanner (duplicates/homonyms/missingCriticalFields/orphans) da implementare',
        );
    }

    /** §10: il merge allievi con anteprima/log/archiviazione NON esiste. */
    public function test_blocco_student_merge_service_assente(): void
    {
        $this->assertFalse(
            class_exists(\App\Services\StudentMergeService::class),
            'StudentMergeService::merge($keep,$absorb) con log reversibile da implementare',
        );
    }

    /** §10: il controller/vista del pannello qualità dati NON esiste. */
    public function test_blocco_data_quality_controller_assente(): void
    {
        $this->assertFalse(
            class_exists(\App\Http\Controllers\Admin\DataQualityController::class),
            'Admin\\DataQualityController (lista + azioni merge/archivia/ignora) da implementare',
        );
    }

    /** §7/§10: la tabella eccezioni `data_quality_dismissals` (falsi positivi) NON esiste. */
    public function test_blocco_tabella_dismissals_assente(): void
    {
        $this->assertFalse(
            Schema::hasTable('data_quality_dismissals'),
            'Tabella eccezioni (anomaly_type/entity_key/dismissed_by/...) da creare',
        );
    }

    /** §2/§10: nessuna rotta/voce di menu "Qualità dati ▸ Anomalie" + badge header. */
    public function test_blocco_rotte_qualita_dati_assenti(): void
    {
        $this->assertFalse(Route::has('admin.data-quality.index'));
        $this->assertFalse(Route::has('admin.data-quality.merge'));
        $this->assertFalse(Route::has('admin.data-quality.dismiss'));
        $this->assertFalse(Route::has('admin.qualita-dati.index'));
        // la dashboard operativa esiste (substrato), il pannello qualità no
        $this->assertTrue(Route::has('admin.dashboard'));
    }
}
