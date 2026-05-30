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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * R12 · Controllo finale — Merge guidato studenti/genitori/corsi con log (attività #8534).
 *
 * CONTESTO: l'attività #8534 chiede un "tool merge con anteprima, conferma e log
 * decisioni, reversibile per N giorni" per le entità duplicabili (allievi, tutori, corsi).
 * Il design di riferimento è il §3 del doc 39 (unione duplicati con anteprima/archiviazione)
 * approfondito nel doc 41. L'IMPLEMENTAZIONE non è parte di R12 (doc 39 §10, doc 40 §1):
 * NON esistono `StudentMergeService`/`GuardianMergeService`/`CourseMergeService`, la tabella
 * `merge_logs`, le colonne `merged_into_id`, né le rotte/azioni del pannello. Quindi un E2E
 * su controller/UI NON è eseguibile (= "blocco").
 *
 * Questo test fa due cose oneste:
 *   1) VALIDA LA SEMANTICA DEL MERGE come spec ESEGUIBILE sul DATO VIVO e sullo SCHEMA REALE:
 *        - ribaltamento FK di TUTTE le relazioni dell'assorbito sul mantenuto (1:N + pivot);
 *        - DEDUP del pivot `student_guardian` (vincolo unique(student_id,guardian_id) rispettato);
 *        - ARCHIVIAZIONE (soft-delete) dell'assorbito, mai hard-delete (§0.7/§3 del design);
 *        - ANTEPRIMA = conteggio dry-run di "cosa si sposta / cosa si archivia" SENZA scritture;
 *        - LOG decisione con chi/quando/da→a/conteggi + snapshot per il ripristino;
 *        - REVERSIBILITÀ entro N giorni: revert ri-punta le FK e ri-attiva l'assorbito;
 *        - estensione a TUTORI (dedup pivot) e CORSI (ri-punta `course_offerings`, FK restrict).
 *   2) FOTOGRAFA IL BLOCCO: assert che i merge service / `merge_logs` / colonne `merged_into_id`
 *      / rotte sono ASSENTI — registro vivo del gap (doc 39 §10).
 *
 * Trascrizioni: parte 1 r.116-122 (CF ripetuti / schede doppie), 454-474 (pulizia anagrafiche).
 */
class MergeGuidatoReversibileE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    /** Finestra di reversibilità di default (giorni). Il design la vuole configurabile (Setting). */
    private const REVERTIBLE_DAYS = 30;

    /** Relazioni 1:N dell'allievo da ribaltare nel merge (FK `student_id`). */
    private const STUDENT_RELATIONS = [
        'student_years', 'enrollments', 'contracts', 'invoices', 'instrument_rentals',
        'exams', 'documents', 'student_levels', 'student_availability', 'book_distributions',
    ];

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
        return Student::create(['first_name' => $first, 'last_name' => $last, 'birth_date' => $birth, 'tax_code' => $cf]);
    }

    private function makeGuardian(string $first, string $last, array $attrs = []): Guardian
    {
        return Guardian::create(array_merge([
            'first_name' => $first, 'last_name' => $last, 'relationship' => 'other', 'privacy_consent' => true,
        ], $attrs));
    }

    private function attach(Student $s, Guardian $g, bool $primary = false, bool $billing = false): void
    {
        $s->guardians()->attach($g->id, [
            'relationship_type' => 'mother', 'is_primary' => $primary, 'is_billing_contact' => $billing,
        ]);
    }

    private function offering(string $courseCode, ?Course $course = null): CourseOffering
    {
        if (! $course) {
            $type = CourseType::firstOrCreate(['code' => substr($courseCode, 0, 2)], ['name' => 'Strumento', 'duration_minutes' => 30]);
            $course = Course::firstOrCreate(['code' => $courseCode], ['course_type_id' => $type->id, 'name' => 'Corso ' . $courseCode]);
        }

        return CourseOffering::create([
            'course_id' => $course->id,
            'academic_year_id' => $this->year->id,
            'status' => 'active',
        ]);
    }

    private function enroll(Student $s, CourseOffering $o): Enrollment
    {
        return Enrollment::create([
            'academic_year_id' => $this->year->id, 'student_id' => $s->id, 'course_offering_id' => $o->id,
            'enrollment_date' => '2025-09-01', 'start_date' => '2025-09-01', 'status' => 'active',
        ]);
    }

    private function contract(Student $s): Contract
    {
        return Contract::create([
            'academic_year_id' => $this->year->id, 'student_id' => $s->id,
            'contract_number' => 'C-' . $s->id . '-' . DB::table('contracts')->count(),
            'status' => 'draft', 'start_date' => '2025-09-01',
        ]);
    }

    private function invoice(Student $s): Invoice
    {
        return Invoice::create([
            'academic_year_id' => $this->year->id, 'student_id' => $s->id,
            'invoice_number' => 'INV-' . $s->id . '-' . DB::table('invoices')->count(),
            'invoice_date' => '2025-09-01', 'due_date' => '2025-09-30',
            'subtotal' => 100, 'total_amount' => 100, 'status' => 'sent',
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // SPEC ESEGUIBILE DEL MERGE (doc 41) — quando il servizio verrà implementato
    // basterà spostarci questa logica mantenendo gli stessi risultati.
    // ═════════════════════════════════════════════════════════════════════════

    /** doc 41 §2: ANTEPRIMA = sola lettura. Conta cosa verrebbe spostato/archiviato, senza scrivere. */
    private function previewStudentMerge(Student $keep, Student $absorb): array
    {
        $moves = [];
        foreach (self::STUDENT_RELATIONS as $t) {
            if (Schema::hasColumn($t, 'student_id')) {
                $n = DB::table($t)->where('student_id', $absorb->id)->count();
                if ($n > 0) {
                    $moves[$t] = $n;
                }
            }
        }
        // pivot: solo i legami NON già presenti su keep verranno spostati, gli altri scartati come doppioni
        $keepGuardians = DB::table('student_guardian')->where('student_id', $keep->id)->pluck('guardian_id')->all();
        $absorbGuardians = DB::table('student_guardian')->where('student_id', $absorb->id)->pluck('guardian_id')->all();
        $moves['student_guardian'] = count(array_diff($absorbGuardians, $keepGuardians));

        return [
            'entity' => 'student',
            'keep_id' => $keep->id,
            'absorb_id' => $absorb->id,
            'will_move' => array_filter($moves),
            'will_archive' => "student:{$absorb->id}",
        ];
    }

    /** doc 41 §3-§4: esegue il merge in TRANSAZIONE, archivia l'assorbito, produce log con snapshot. */
    private function mergeStudents(Student $keep, Student $absorb, string $by): array
    {
        return DB::transaction(function () use ($keep, $absorb, $by) {
            $snapshot = [];

            // 1:N — cattura gli id spostati (per il ripristino) poi ri-punta la FK
            foreach (self::STUDENT_RELATIONS as $t) {
                if (! Schema::hasColumn($t, 'student_id')) {
                    continue;
                }
                $ids = DB::table($t)->where('student_id', $absorb->id)->pluck('id')->all();
                if ($ids) {
                    DB::table($t)->whereIn('id', $ids)->update(['student_id' => $keep->id]);
                    $snapshot[$t] = $ids;
                }
            }

            // pivot student_guardian con DEDUP sul vincolo unique(student_id, guardian_id)
            $keepGuardians = DB::table('student_guardian')->where('student_id', $keep->id)->pluck('guardian_id')->all();
            $movedPivot = [];
            $droppedPivot = [];
            foreach (DB::table('student_guardian')->where('student_id', $absorb->id)->get() as $row) {
                if (in_array($row->guardian_id, $keepGuardians, true)) {
                    DB::table('student_guardian')->where('id', $row->id)->delete(); // doppione: keep ce l'ha già
                    $droppedPivot[] = $row->guardian_id;
                } else {
                    DB::table('student_guardian')->where('id', $row->id)->update(['student_id' => $keep->id]);
                    $movedPivot[] = $row->guardian_id;
                }
            }

            // ARCHIVIA l'assorbito (soft-delete) — mai hard-delete (§0.7)
            $absorb->delete();

            return [
                'entity' => 'student',
                'keep_id' => $keep->id,
                'absorb_id' => $absorb->id,
                'snapshot' => $snapshot,
                'pivot_moved' => $movedPivot,
                'pivot_dropped' => $droppedPivot,
                'performed_by' => $by,
                'performed_at' => Carbon::now()->toIso8601String(),
                'revertible_until' => Carbon::now()->addDays(self::REVERTIBLE_DAYS)->toIso8601String(),
            ];
        });
    }

    /** doc 41 §5: REVERT entro la finestra — ri-punta le FK all'assorbito e lo ri-attiva. */
    private function revertStudentMerge(array $log): void
    {
        $this->assertTrue(Carbon::now()->lt(Carbon::parse($log['revertible_until'])), 'revert oltre la finestra non consentito');

        DB::transaction(function () use ($log) {
            foreach ($log['snapshot'] as $t => $ids) {
                DB::table($t)->whereIn('id', $ids)->update(['student_id' => $log['absorb_id']]);
            }
            foreach ($log['pivot_moved'] as $guardianId) {
                DB::table('student_guardian')
                    ->where('student_id', $log['keep_id'])->where('guardian_id', $guardianId)
                    ->update(['student_id' => $log['absorb_id']]);
            }
            Student::withTrashed()->findOrFail($log['absorb_id'])->restore();
        });
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 1. SUBSTRATO — le relazioni che il merge deve ribaltare esistono a schema
    // ═════════════════════════════════════════════════════════════════════════

    public function test_substrato_relazioni_student_presenti(): void
    {
        foreach (self::STUDENT_RELATIONS as $t) {
            $this->assertTrue(Schema::hasTable($t), "tabella {$t} attesa");
            $this->assertTrue(Schema::hasColumn($t, 'student_id'), "{$t}.student_id atteso (FK da ribaltare)");
        }
        $this->assertTrue(Schema::hasTable('student_guardian'));
        $this->assertTrue(Schema::hasTable('course_offerings'));
    }

    /** §0.7/§3: l'archiviazione si appoggia a SoftDeletes già presente su Student e Course (NON su Guardian). */
    public function test_substrato_softdeletes_per_archiviazione(): void
    {
        $this->assertTrue(in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses(Student::class), true));
        $this->assertTrue(in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses(Course::class), true));
        // Guardian NON ha SoftDeletes → l'implementazione dovrà aggiungerlo (o flag merged) per archiviare un tutore.
        $this->assertFalse(in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses(Guardian::class), true));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 2. ANTEPRIMA (dry-run, sola lettura) — doc 41 §2
    // ═════════════════════════════════════════════════════════════════════════

    public function test_anteprima_conta_senza_scrivere(): void
    {
        $keep = $this->makeStudent('Mario', 'Rossi', 'RSSMRA10A01H501U');
        $absorb = $this->makeStudent('Mario', 'Rossi', 'rssmra10a01h501u');
        $this->enroll($absorb, $this->offering('PF1'));
        $this->contract($absorb);
        $this->attach($absorb, $this->makeGuardian('Anna', 'Rossi'), primary: true);

        $before = [
            'enrollments' => Enrollment::count(),
            'contracts' => Contract::count(),
            'pivot' => DB::table('student_guardian')->count(),
            'students' => Student::count(),
        ];

        $preview = $this->previewStudentMerge($keep, $absorb);

        // l'anteprima dichiara cosa si sposta…
        $this->assertSame(1, $preview['will_move']['enrollments']);
        $this->assertSame(1, $preview['will_move']['contracts']);
        $this->assertSame(1, $preview['will_move']['student_guardian']);
        $this->assertSame("student:{$absorb->id}", $preview['will_archive']);

        // …ma NON ha scritto nulla
        $this->assertSame($before['enrollments'], Enrollment::count());
        $this->assertSame($before['contracts'], Contract::count());
        $this->assertSame($before['pivot'], DB::table('student_guardian')->count());
        $this->assertSame($before['students'], Student::count());
        $this->assertNull($absorb->fresh()->deleted_at);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 3. MERGE ALLIEVI — ribaltamento FK + archiviazione (doc 41 §3)
    // ═════════════════════════════════════════════════════════════════════════

    public function test_merge_ribalta_tutte_le_fk_e_archivia_lassorbito(): void
    {
        $keep = $this->makeStudent('Mario', 'Rossi', 'RSSMRA10A01H501U');
        $absorb = $this->makeStudent('Mario', 'Rossi', 'rssmra10a01h501u');

        StudentYear::create(['student_id' => $absorb->id, 'academic_year_id' => $this->year->id, 'status' => 'enrolled']);
        $this->enroll($absorb, $this->offering('PF1'));
        $this->contract($absorb);
        $this->invoice($absorb);

        $this->mergeStudents($keep, $absorb, by: 'segreteria@altramusica');

        // tutto è ora del mantenuto
        $this->assertSame(1, Enrollment::where('student_id', $keep->id)->count());
        $this->assertSame(1, Contract::where('student_id', $keep->id)->count());
        $this->assertSame(1, Invoice::where('student_id', $keep->id)->count());
        $this->assertSame(1, StudentYear::where('student_id', $keep->id)->count());

        // niente più legato all'assorbito
        $this->assertSame(0, Enrollment::where('student_id', $absorb->id)->count());
        $this->assertSame(0, Contract::where('student_id', $absorb->id)->count());

        // l'assorbito è ARCHIVIATO (soft-delete), non sparito
        $this->assertNotNull($absorb->fresh()->deleted_at);
        $this->assertNull($keep->fresh()->deleted_at);
        $this->assertNotNull(Student::withTrashed()->find($absorb->id));
    }

    /** §3: il pivot tutori non viola il vincolo unique(student_id,guardian_id) → dedup dei legami condivisi. */
    public function test_merge_dedup_pivot_tutori_condivisi(): void
    {
        $keep = $this->makeStudent('Mario', 'Rossi');
        $absorb = $this->makeStudent('Mario', 'Rossi');

        $mamma = $this->makeGuardian('Anna', 'Rossi');   // tutore CONDIVISO
        $papa = $this->makeGuardian('Luca', 'Rossi');    // solo sull'assorbito

        $this->attach($keep, $mamma, primary: true);
        $this->attach($absorb, $mamma);                  // stesso tutore → doppione da scartare
        $this->attach($absorb, $papa);                   // nuovo → da spostare

        $log = $this->mergeStudents($keep, $absorb, by: 'admin');

        // keep ha esattamente 2 tutori distinti (mamma + papà), nessun duplicato
        $guardianIds = DB::table('student_guardian')->where('student_id', $keep->id)->pluck('guardian_id');
        $this->assertCount(2, $guardianIds);
        $this->assertEqualsCanonicalizing([$mamma->id, $papa->id], $guardianIds->all());

        $this->assertSame([$papa->id], $log['pivot_moved']);
        $this->assertSame([$mamma->id], $log['pivot_dropped']);
        // nessuna riga residua sull'assorbito
        $this->assertSame(0, DB::table('student_guardian')->where('student_id', $absorb->id)->count());
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 4. LOG DECISIONE + REVERSIBILITÀ ENTRO N GIORNI (doc 41 §4-§5)
    // ═════════════════════════════════════════════════════════════════════════

    public function test_log_decisione_contiene_chi_quando_e_finestra(): void
    {
        $keep = $this->makeStudent('Mario', 'Rossi');
        $absorb = $this->makeStudent('Mario', 'Rossi');
        $this->enroll($absorb, $this->offering('PF1'));

        $log = $this->mergeStudents($keep, $absorb, by: 'segreteria@altramusica');

        $this->assertSame('student', $log['entity']);
        $this->assertSame($keep->id, $log['keep_id']);
        $this->assertSame($absorb->id, $log['absorb_id']);
        $this->assertSame('segreteria@altramusica', $log['performed_by']);
        $this->assertArrayHasKey('enrollments', $log['snapshot']); // snapshot per il ripristino
        // finestra di reversibilità = performed_at + N giorni
        $this->assertEqualsWithDelta(
            self::REVERTIBLE_DAYS,
            Carbon::parse($log['performed_at'])->diffInDays(Carbon::parse($log['revertible_until'])),
            1,
        );
    }

    public function test_reversibile_entro_n_giorni_ripristina_tutto(): void
    {
        $keep = $this->makeStudent('Mario', 'Rossi');
        $absorb = $this->makeStudent('Mario', 'Rossi');
        $en = $this->enroll($absorb, $this->offering('PF1'));
        $co = $this->contract($absorb);
        $papa = $this->makeGuardian('Luca', 'Rossi');
        $this->attach($absorb, $papa);

        $log = $this->mergeStudents($keep, $absorb, by: 'admin');
        // post-merge: tutto su keep, assorbito archiviato
        $this->assertSame($keep->id, $en->fresh()->student_id);
        $this->assertNotNull($absorb->fresh()->deleted_at);

        // REVERT entro la finestra
        $this->revertStudentMerge($log);

        // tutto torna all'assorbito, che è di nuovo attivo
        $this->assertSame($absorb->id, $en->fresh()->student_id);
        $this->assertSame($absorb->id, $co->fresh()->student_id);
        $this->assertSame($absorb->id, DB::table('student_guardian')->where('guardian_id', $papa->id)->value('student_id'));
        $this->assertNull(Student::withTrashed()->find($absorb->id)->deleted_at);
        $this->assertSame(0, Enrollment::where('student_id', $keep->id)->count());
    }

    public function test_revert_oltre_la_finestra_e_bloccato(): void
    {
        $keep = $this->makeStudent('Mario', 'Rossi');
        $absorb = $this->makeStudent('Mario', 'Rossi');
        $this->enroll($absorb, $this->offering('PF1'));

        $log = $this->mergeStudents($keep, $absorb, by: 'admin');
        // simula scadenza della finestra: revertible_until nel passato
        $log['revertible_until'] = Carbon::now()->subDay()->toIso8601String();

        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->revertStudentMerge($log); // l'assert interno scatta → oltre N giorni il merge è definitivo
    }

    /** §3: in caso di errore a metà, la transazione fa ROLLBACK — nessun merge parziale. */
    public function test_merge_e_atomico_rollback_su_errore(): void
    {
        $keep = $this->makeStudent('Mario', 'Rossi');
        $absorb = $this->makeStudent('Mario', 'Rossi');
        $en = $this->enroll($absorb, $this->offering('PF1'));

        try {
            DB::transaction(function () use ($keep, $absorb) {
                DB::table('enrollments')->where('student_id', $absorb->id)->update(['student_id' => $keep->id]);
                throw new \RuntimeException('errore simulato dopo il primo spostamento');
            });
        } catch (\RuntimeException $e) {
            // atteso
        }

        // nessuna scrittura sopravvive: enrollment ancora dell'assorbito, assorbito non archiviato
        $this->assertSame($absorb->id, $en->fresh()->student_id);
        $this->assertNull($absorb->fresh()->deleted_at);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 5. ESTENSIONE A TUTORI E CORSI (doc 41 §6) — "studenti/genitori/corsi"
    // ═════════════════════════════════════════════════════════════════════════

    /** Merge tutori: ri-punta il pivot guardian_id, dedup sui legami condivisi. */
    public function test_merge_tutori_ripunta_pivot_con_dedup(): void
    {
        $keep = $this->makeGuardian('Anna', 'Rossi', ['email_1' => 'anna@example.com']);
        $absorb = $this->makeGuardian('Anna', 'Rossi', ['cell_1' => '3331112233']);

        $figlioComune = $this->makeStudent('Figlio', 'Comune');
        $figlioSolo = $this->makeStudent('Figlio', 'Solo');
        $this->attach($figlioComune, $keep);     // legato a keep
        $this->attach($figlioComune, $absorb);   // stesso figlio anche su absorb → doppione
        $this->attach($figlioSolo, $absorb);     // solo su absorb → da spostare

        // ribalta pivot con dedup sul vincolo unique(student_id, guardian_id)
        $keepStudents = DB::table('student_guardian')->where('guardian_id', $keep->id)->pluck('student_id')->all();
        foreach (DB::table('student_guardian')->where('guardian_id', $absorb->id)->get() as $row) {
            if (in_array($row->student_id, $keepStudents, true)) {
                DB::table('student_guardian')->where('id', $row->id)->delete();
            } else {
                DB::table('student_guardian')->where('id', $row->id)->update(['guardian_id' => $keep->id]);
            }
        }

        $students = DB::table('student_guardian')->where('guardian_id', $keep->id)->pluck('student_id');
        $this->assertCount(2, $students);
        $this->assertEqualsCanonicalizing([$figlioComune->id, $figlioSolo->id], $students->all());
        $this->assertSame(0, DB::table('student_guardian')->where('guardian_id', $absorb->id)->count());
    }

    /** Merge corsi (catalogo): ri-punta `course_offerings.course_id`, poi archivia il corso assorbito. */
    public function test_merge_corsi_ripunta_offerte_e_archivia(): void
    {
        $type = CourseType::firstOrCreate(['code' => 'PF'], ['name' => 'Pianoforte', 'duration_minutes' => 30]);
        $keep = Course::create(['course_type_id' => $type->id, 'code' => 'PF-A', 'name' => 'Pianoforte']);
        $absorb = Course::create(['course_type_id' => $type->id, 'code' => 'PF-B', 'name' => 'Piano (duplicato)']);

        $this->offering('', $keep);
        $offAbsorb = $this->offering('', $absorb);

        // course_offerings.course_id è onDelete('restrict'): bisogna PRIMA ri-puntare le offerte
        DB::table('course_offerings')->where('course_id', $absorb->id)->update(['course_id' => $keep->id]);
        $absorb->delete(); // archivia (SoftDeletes), ora che non ha più offerte

        $this->assertSame($keep->id, $offAbsorb->fresh()->course_id);
        $this->assertSame(2, CourseOffering::where('course_id', $keep->id)->count());
        $this->assertNotNull($absorb->fresh()->deleted_at);
        $this->assertNull($keep->fresh()->deleted_at);
        // le iscrizioni seguono l'offerta → restano collegate al corso mantenuto via hasManyThrough
        $this->assertSame(0, CourseOffering::where('course_id', $absorb->id)->count());
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 6. BLOCCO — fotografia del gap di implementazione (doc 39 §10, NON parte di R12)
    // ═════════════════════════════════════════════════════════════════════════

    public function test_blocco_merge_services_assenti(): void
    {
        $this->assertFalse(class_exists(\App\Services\StudentMergeService::class), 'StudentMergeService::merge($keep,$absorb) da implementare');
        $this->assertFalse(class_exists(\App\Services\GuardianMergeService::class), 'GuardianMergeService da implementare');
        $this->assertFalse(class_exists(\App\Services\CourseMergeService::class), 'CourseMergeService da implementare');
        // (in alternativa un unico MergeService generico per entità) — comunque assente oggi
        $this->assertFalse(class_exists(\App\Services\MergeService::class));
    }

    public function test_blocco_tabella_merge_logs_assente(): void
    {
        $this->assertFalse(
            Schema::hasTable('merge_logs'),
            'Tabella merge_logs (entity/keep_id/absorb_id/snapshot/performed_by/performed_at/revertible_until) da creare',
        );
    }

    public function test_blocco_colonne_merged_into_assenti(): void
    {
        $this->assertFalse(Schema::hasColumn('students', 'merged_into_id'), 'students.merged_into_id da aggiungere (puntatore al mantenuto)');
        $this->assertFalse(Schema::hasColumn('guardians', 'merged_into_id'));
        $this->assertFalse(Schema::hasColumn('courses', 'merged_into_id'));
        // Guardian non archiviabile oggi: manca anche deleted_at
        $this->assertFalse(Schema::hasColumn('guardians', 'deleted_at'), 'guardians.deleted_at (SoftDeletes) da aggiungere per archiviare un tutore');
    }

    public function test_blocco_rotte_merge_assenti(): void
    {
        $this->assertFalse(Route::has('admin.data-quality.merge.preview'));
        $this->assertFalse(Route::has('admin.data-quality.merge'));
        $this->assertFalse(Route::has('admin.data-quality.merge.revert'));
        // la dashboard operativa esiste (substrato), il tool di merge no
        $this->assertTrue(Route::has('admin.dashboard'));
    }
}
