<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Lesson;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * R8 · Controllo finale — Validazione registro/presenze/compensi (attività #8547).
 *
 * CONTESTO: R8 (#8528, doc 32) è SOLO DESIGN. Il §10 "Impatti tecnici" dichiara
 * esplicitamente che l'implementazione NON è parte di R8. Le tabelle `attendances`
 * e `teacher_hours` ESISTONO (migration 2025_12_22) ma sono DORMIENTI: nessun model
 * Attendance/TeacherHour, nessun controller, nessuna rotta (in routes/web.php le
 * risorse sono commentate "attendances/teacher-hours evolutivo"), nessuna vista.
 *
 * Di conseguenza un E2E su controller/UI NON è eseguibile (= "blocco" previsto dalla
 * richiesta). Questo test fa due cose oneste:
 *   1) VALIDA IL SUBSTRATO: le tabelle dormienti supportano davvero il design — schema,
 *      enum, vincoli, e i tre gesti richiesti (registra presenze / calcola compenso /
 *      override manuale) esercitati a livello DB+spec del motore, con tariffe DA
 *      CONFERMARE lette da configurazione (Setting), MAI cablate.
 *   2) FOTOGRAFA IL BLOCCO: assert che model/relazione/rotte sono assenti, così il test
 *      è un registro vivo del gap da colmare in fase di implementazione.
 *
 * Trascrizioni: parte 1 r.168-220 (conto orario, soci/non-soci, forfait, forzature ±,
 * pagato=segnato, supplente, pacchetto 5 lezioni, prospetto previsione, rate arrot. per
 * difetto + saldo) — parte 2 r.108-114 (bonus a consuntivo, supplenti ritenuta).
 */
class RegistroPresenzeCompensiE2EValidationTest extends TestCase
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
    // Helper di scena (usano SOLO model esistenti; attendances/teacher_hours via DB)
    // ─────────────────────────────────────────────────────────────────────────

    private function makeTeacher(string $first, string $last): Teacher
    {
        return Teacher::create([
            'first_name' => $first, 'last_name' => $last, 'active' => true,
        ]);
    }

    private function makeStudent(string $first, string $last): Student
    {
        return Student::create([
            'first_name' => $first, 'last_name' => $last, 'birth_date' => '2012-01-01',
        ]);
    }

    private function makeLesson(Teacher $teacher, string $date, string $start, string $end, bool $completed = false): Lesson
    {
        // scaffolding condiviso (riusabile tra più lezioni dello stesso test)
        $type = CourseType::firstOrCreate(['code' => 'PF'], ['name' => 'Pianoforte', 'duration_minutes' => 30]);
        $course = Course::firstOrCreate(['code' => 'PF1'], ['course_type_id' => $type->id, 'name' => 'Pianoforte']);
        $classroom = Classroom::firstOrCreate(['code' => 'A2'], ['name' => 'Aula A2']);
        $offering = CourseOffering::firstOrCreate(
            ['course_id' => $course->id, 'academic_year_id' => $this->year->id, 'teacher_id' => $teacher->id],
            ['classroom_id' => $classroom->id, 'status' => 'active'],
        );

        return Lesson::create([
            'course_offering_id' => $offering->id,
            'teacher_id' => $teacher->id,
            'date' => $date,
            'time_start' => $start,
            'time_end' => $end,
            'completed' => $completed,
        ]);
    }

    /**
     * Spec del MOTORE COMPENSI (doc 32 §2/§5/§8) esercitata a livello dati: aggrega le
     * lezioni CHIUSE e SEGNATE del docente effettivo nel periodo, converte in ore con la
     * durata standard configurata, moltiplica per la tariffa risolta (override docente →
     * categoria socio/non-socio → listino), somma forfait e forzature → scrive teacher_hours.
     * TUTTE le tariffe sono lette da Setting (DA CONFERMARE), nessuna costante nel codice.
     */
    private function calcolaConto(Teacher $teacher, string $from, string $to, float $forfait = 0.0, float $bonus = 0.0, ?string $note = null): array
    {
        // tariffa risolta: override docente se presente, altrimenti categoria
        $categoria = Setting::get("compensi.docente.{$teacher->id}.categoria", $teacher->contract_type ?? 'non_socio');
        $tariffaCategoria = (float) Setting::get("compensi.tariffa_oraria_{$categoria}", 0.0); // DA CONFERMARE → 0.0
        $override = Setting::get("compensi.docente.{$teacher->id}.tariffa_oraria"); // null = ereditato
        $tariffa = $override !== null ? (float) $override : $tariffaCategoria;

        $durataStdMin = (int) Setting::get('compensi.durata_lezione_standard_min', 30); // DA CONFERMARE

        // pagato = segnato: SOLO lezioni completate (chiuse) con almeno una presenza registrata
        $lessonIds = Lesson::query()
            ->where(fn ($q) => $q->where('teacher_id', $teacher->id)->orWhere('substitute_teacher_id', $teacher->id))
            ->where('completed', true)
            ->whereBetween('date', [$from, $to])
            ->whereIn('id', DB::table('attendances')->select('lesson_id')->distinct())
            ->pluck('id');

        $lessonsCount = $lessonIds->count();
        $minutiTotali = $lessonsCount * $durataStdMin;
        $hours = round($minutiTotali / 60, 2);
        $base = round($hours * $tariffa, 2);
        $total = round($base + $forfait + $bonus, 2);

        $id = DB::table('teacher_hours')->insertGetId([
            'teacher_id' => $teacher->id,
            'academic_year_id' => $this->year->id,
            'period_start' => $from,
            'period_end' => $to,
            'lessons_count' => $lessonsCount,
            'hours_total' => $hours,
            'hourly_rate' => $tariffa,
            'base_amount' => $base,
            'bonus_amount' => $bonus,
            'forfait_amount' => $forfait,
            'total_amount' => $total,
            'status' => 'calculated',
            'notes' => $note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return (array) DB::table('teacher_hours')->find($id);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. SUBSTRATO — le tabelle dormienti esistono e supportano il design (deve passare)
    // ─────────────────────────────────────────────────────────────────────────

    /** doc §1/§10: la migration c'è — `attendances` con le colonne attese. */
    public function test_substrato_tabella_attendances_pronta(): void
    {
        $this->assertTrue(Schema::hasTable('attendances'));
        $this->assertTrue(Schema::hasColumns('attendances', [
            'lesson_id', 'student_id', 'status', 'notes',
        ]));
    }

    /** doc §1/§2/§6/§8: `teacher_hours` ha lo schema ricco (base/bonus/forfait/total/status). */
    public function test_substrato_tabella_teacher_hours_pronta(): void
    {
        $this->assertTrue(Schema::hasTable('teacher_hours'));
        $this->assertTrue(Schema::hasColumns('teacher_hours', [
            'teacher_id', 'academic_year_id', 'period_start', 'period_end',
            'lessons_count', 'hours_total', 'hourly_rate',
            'base_amount', 'bonus_amount', 'forfait_amount', 'total_amount',
            'status', 'notes', 'approved_by', 'approved_at',
        ]));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. GESTO 1 — Registra presenze lezione (doc §3) — a livello DB
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * doc §3: tap ciclico Presente→Assente→Ritardo→Giustificato; upsert per lesson+student;
     * i 4 stati dell'enum sono accettati.
     */
    public function test_registra_presenze_ciclo_stati_e_upsert(): void
    {
        $teacher = $this->makeTeacher('Anna', 'Bianchi');
        $lesson = $this->makeLesson($teacher, '2026-05-26', '17:00:00', '17:30:00');
        $mario = $this->makeStudent('Mario', 'Rossi');

        // primo tap: present
        DB::table('attendances')->updateOrInsert(
            ['lesson_id' => $lesson->id, 'student_id' => $mario->id],
            ['status' => 'present', 'created_at' => now(), 'updated_at' => now()],
        );

        // ciclo dei 4 stati dell'enum (upsert idempotente sulla stessa riga)
        foreach (['absent', 'late', 'excused', 'present'] as $stato) {
            DB::table('attendances')->updateOrInsert(
                ['lesson_id' => $lesson->id, 'student_id' => $mario->id],
                ['status' => $stato, 'updated_at' => now()],
            );
            $this->assertSame($stato, DB::table('attendances')
                ->where(['lesson_id' => $lesson->id, 'student_id' => $mario->id])->value('status'));
        }

        // upsert: nessun duplicato lesson+student
        $this->assertSame(1, DB::table('attendances')
            ->where(['lesson_id' => $lesson->id, 'student_id' => $mario->id])->count());
    }

    /** doc §1: vincolo unique (lesson_id, student_id) attivo a DB — niente doppioni. */
    public function test_registra_presenze_vincolo_unique_lesson_student(): void
    {
        $teacher = $this->makeTeacher('Anna', 'Bianchi');
        $lesson = $this->makeLesson($teacher, '2026-05-26', '17:00:00', '17:30:00');
        $mario = $this->makeStudent('Mario', 'Rossi');

        DB::table('attendances')->insert([
            'lesson_id' => $lesson->id, 'student_id' => $mario->id,
            'status' => 'present', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);
        DB::table('attendances')->insert([
            'lesson_id' => $lesson->id, 'student_id' => $mario->id,
            'status' => 'absent', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /**
     * doc §0/§7: presenza allievo ≠ paga insegnante. Allievo ASSENTE in una lezione chiusa:
     * la lezione resta compensata (corso regolare), l'assenza serve solo a frequenza.
     */
    public function test_presenza_allievo_assente_non_toglie_compenso(): void
    {
        Setting::set('compensi.tariffa_oraria_socio', '20.00');
        $teacher = $this->makeTeacher('Anna', 'Bianchi');
        Setting::set("compensi.docente.{$teacher->id}.categoria", 'socio');
        $lesson = $this->makeLesson($teacher, '2026-05-26', '17:00:00', '17:30:00', completed: true);
        $luca = $this->makeStudent('Luca', 'Neri');

        // allievo ASSENTE ma la lezione è stata erogata e segnata
        DB::table('attendances')->insert([
            'lesson_id' => $lesson->id, 'student_id' => $luca->id,
            'status' => 'absent', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $conto = $this->calcolaConto($teacher, '2026-05-01', '2026-05-31');

        $this->assertSame(1, (int) $conto['lessons_count']); // la lezione conta
        $this->assertEquals(10.00, (float) $conto['base_amount']); // 0.5h × 20 €/h
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. GESTO 2 — Calcola compenso docente (doc §2/§5/§8) — pagato = segnato
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * doc §2/§3/§8: SOLO lezioni chiuse E segnate entrano nel conto. Una lezione APERTA
     * (senza registro) o CHIUSA-ma-non-segnata NON viene compensata.
     */
    public function test_calcola_compenso_solo_lezioni_chiuse_e_segnate(): void
    {
        Setting::set('compensi.tariffa_oraria_socio', '20.00');
        $teacher = $this->makeTeacher('Anna', 'Bianchi');
        Setting::set("compensi.docente.{$teacher->id}.categoria", 'socio');
        $studente = $this->makeStudent('Mario', 'Rossi');

        // (a) chiusa + segnata → conta
        $l1 = $this->makeLesson($teacher, '2026-05-05', '17:00:00', '17:30:00', completed: true);
        DB::table('attendances')->insert(['lesson_id' => $l1->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);
        // (b) chiusa + segnata → conta
        $l2 = $this->makeLesson($teacher, '2026-05-12', '17:00:00', '17:30:00', completed: true);
        DB::table('attendances')->insert(['lesson_id' => $l2->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);
        // (c) APERTA (non chiusa) ma segnata → NON conta (leva "pagato = segnato")
        $l3 = $this->makeLesson($teacher, '2026-05-19', '17:00:00', '17:30:00', completed: false);
        DB::table('attendances')->insert(['lesson_id' => $l3->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);
        // (d) chiusa ma NON segnata → NON conta
        $this->makeLesson($teacher, '2026-05-26', '17:00:00', '17:30:00', completed: true);

        $conto = $this->calcolaConto($teacher, '2026-05-01', '2026-05-31');

        $this->assertSame(2, (int) $conto['lessons_count']);   // solo (a)+(b)
        $this->assertEquals(1.00, (float) $conto['hours_total']); // 2 × 30min = 1h
        $this->assertEquals(20.00, (float) $conto['base_amount']); // 1h × 20
        $this->assertEquals(20.00, (float) $conto['total_amount']);
        $this->assertSame('calculated', $conto['status']);
    }

    /**
     * doc §2/§5 + r.190: il compenso del SUPPLENTE per la lezione coperta va a lui.
     * forTeacher copre sia teacher_id sia substitute_teacher_id.
     */
    public function test_calcola_compenso_va_al_supplente_della_lezione(): void
    {
        Setting::set('compensi.tariffa_oraria_non_socio', '15.00');
        $titolare = $this->makeTeacher('Anna', 'Bianchi');
        $supplente = $this->makeTeacher('Sara', 'Gialli');
        Setting::set("compensi.docente.{$supplente->id}.categoria", 'non_socio');
        $studente = $this->makeStudent('Mario', 'Rossi');

        // lezione del titolare, coperta dal supplente
        $lesson = $this->makeLesson($titolare, '2026-05-05', '17:00:00', '17:30:00', completed: true);
        $lesson->update(['substitute_teacher_id' => $supplente->id]);
        DB::table('attendances')->insert(['lesson_id' => $lesson->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);

        $contoSupplente = $this->calcolaConto($supplente, '2026-05-01', '2026-05-31');
        $this->assertSame(1, (int) $contoSupplente['lessons_count']);
        $this->assertEquals(7.50, (float) $contoSupplente['base_amount']); // 0.5h × 15
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. TARIFFE DA CONFERMARE — visibili e configurabili, mai cablate (doc §2/§4/§5)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * doc §0.4/§2/§5: le tariffe sono DA CONFERMARE = placeholder configurabili. Senza
     * configurazione la tariffa risolta è 0,00 (placeholder), NON un valore cablato.
     */
    public function test_tariffe_da_confermare_default_placeholder_zero(): void
    {
        $teacher = $this->makeTeacher('Anna', 'Bianchi');
        $studente = $this->makeStudent('Mario', 'Rossi');
        $lesson = $this->makeLesson($teacher, '2026-05-05', '17:00:00', '17:30:00', completed: true);
        DB::table('attendances')->insert(['lesson_id' => $lesson->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);

        // nessuna Setting tariffa impostata → placeholder DA CONFERMARE
        $conto = $this->calcolaConto($teacher, '2026-05-01', '2026-05-31');
        $this->assertEquals(0.00, (float) $conto['hourly_rate']);
        $this->assertEquals(0.00, (float) $conto['base_amount']);
        $this->assertSame(1, (int) $conto['lessons_count']); // la lezione c'è, manca solo la tariffa
    }

    /**
     * doc §2: tariffa differenziata socio vs non-socio; cambiare la Setting cambia il
     * calcolo (motore configurabile, non cablato).
     */
    public function test_tariffa_differenziata_socio_e_non_socio(): void
    {
        Setting::set('compensi.tariffa_oraria_socio', '22.00');
        Setting::set('compensi.tariffa_oraria_non_socio', '15.00');

        $socio = $this->makeTeacher('Anna', 'Bianchi');
        Setting::set("compensi.docente.{$socio->id}.categoria", 'socio');
        $nonSocio = $this->makeTeacher('Marco', 'Verdi');
        Setting::set("compensi.docente.{$nonSocio->id}.categoria", 'non_socio');
        $studente = $this->makeStudent('Mario', 'Rossi');

        foreach ([$socio, $nonSocio] as $t) {
            $l = $this->makeLesson($t, '2026-05-05', '17:00:00', '18:00:00', completed: true); // 60min
            DB::table('attendances')->insert(['lesson_id' => $l->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);
        }

        // durata standard 60min per leggibilità del check (1 lezione = 1h)
        Setting::set('compensi.durata_lezione_standard_min', '60', 'integer');

        $this->assertEquals(22.00, (float) $this->calcolaConto($socio, '2026-05-01', '2026-05-31')['base_amount']);
        $this->assertEquals(15.00, (float) $this->calcolaConto($nonSocio, '2026-05-01', '2026-05-31')['base_amount']);
    }

    /** doc §5: l'override per-docente VINCE sulla tariffa di categoria ("ereditato" vs "override"). */
    public function test_override_tariffa_per_docente_vince_su_categoria(): void
    {
        Setting::set('compensi.tariffa_oraria_socio', '20.00');
        Setting::set('compensi.durata_lezione_standard_min', '60', 'integer');
        $teacher = $this->makeTeacher('Anna', 'Bianchi');
        Setting::set("compensi.docente.{$teacher->id}.categoria", 'socio');
        Setting::set("compensi.docente.{$teacher->id}.tariffa_oraria", '28.50'); // override
        $studente = $this->makeStudent('Mario', 'Rossi');

        $l = $this->makeLesson($teacher, '2026-05-05', '17:00:00', '18:00:00', completed: true);
        DB::table('attendances')->insert(['lesson_id' => $l->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);

        $conto = $this->calcolaConto($teacher, '2026-05-01', '2026-05-31');
        $this->assertEquals(28.50, (float) $conto['hourly_rate']); // override, non 20
        $this->assertEquals(28.50, (float) $conto['base_amount']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. GESTO 3 — Override manuale / forzature ± (doc §6) + forfait (doc §5)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * doc §6 + r.187 ("hai segnato la presenza ma la lezione non l'hai fatta"): storno
     * negativo tracciato con motivo → total scende; il calcolo base resta intatto.
     */
    public function test_override_manuale_storno_negativo_tracciato(): void
    {
        Setting::set('compensi.tariffa_oraria_non_socio', '18.00');
        Setting::set('compensi.durata_lezione_standard_min', '60', 'integer');
        $teacher = $this->makeTeacher('Marco', 'Verdi');
        Setting::set("compensi.docente.{$teacher->id}.categoria", 'non_socio');
        $studente = $this->makeStudent('Mario', 'Rossi');

        // 2 lezioni chiuse+segnate = base 36,00 €
        foreach (['2026-05-07', '2026-05-14'] as $d) {
            $l = $this->makeLesson($teacher, $d, '17:00:00', '18:00:00', completed: true);
            DB::table('attendances')->insert(['lesson_id' => $l->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);
        }

        // storno di una lezione segnata ma non erogata: bonus negativo + nota obbligatoria
        $conto = $this->calcolaConto(
            $teacher, '2026-05-01', '2026-05-31',
            bonus: -18.00,
            note: 'Storno lezione 14/05 segnata ma non erogata',
        );

        $this->assertEquals(36.00, (float) $conto['base_amount']);   // base intatta
        $this->assertEquals(-18.00, (float) $conto['bonus_amount']); // forzatura tracciata
        $this->assertEquals(18.00, (float) $conto['total_amount']);  // 36 - 18
        $this->assertStringContainsString('14/05', $conto['notes']); // motivo tracciato
    }

    /**
     * doc §5 + r.186 (forfait supervisione progetto) + parte 2 r.110 (bonus a consuntivo):
     * voce forfettaria + bonus positivo confluiscono nel totale, slegati dalle ore.
     */
    public function test_forfait_e_bonus_positivo_nel_totale(): void
    {
        Setting::set('compensi.tariffa_oraria_socio', '20.00');
        Setting::set('compensi.durata_lezione_standard_min', '60', 'integer');
        $teacher = $this->makeTeacher('Sara', 'Gialli');
        Setting::set("compensi.docente.{$teacher->id}.categoria", 'socio');
        $studente = $this->makeStudent('Mario', 'Rossi');

        $l = $this->makeLesson($teacher, '2026-05-05', '17:00:00', '18:00:00', completed: true);
        DB::table('attendances')->insert(['lesson_id' => $l->id, 'student_id' => $studente->id, 'status' => 'present', 'created_at' => now(), 'updated_at' => now()]);

        $conto = $this->calcolaConto(
            $teacher, '2026-05-01', '2026-05-31',
            forfait: 100.00, // "Supervisione orchestra"
            bonus: 15.00,    // bonus valorizzato a consuntivo
            note: 'Forfait supervisione orchestra + premio',
        );

        $this->assertEquals(20.00, (float) $conto['base_amount']);
        $this->assertEquals(100.00, (float) $conto['forfait_amount']);
        $this->assertEquals(15.00, (float) $conto['bonus_amount']);
        $this->assertEquals(135.00, (float) $conto['total_amount']); // 20 + 100 + 15
    }

    /**
     * doc §8 + r.215 (piano pagamenti concordato, arrot. per difetto, ultima a saldo):
     * la rateizzazione è presentazione sul total_amount, non nuovo schema.
     */
    public function test_piano_pagamenti_arrotondato_per_difetto_ultima_a_saldo(): void
    {
        // totale previsto 1000,00 € in 9 mensilità
        $totale = 1000.00;
        $rate = 9;

        $quota = floor($totale / $rate * 100) / 100; // arrot. per DIFETTO al centesimo
        $pagamenti = array_fill(0, $rate - 1, $quota);
        $saldo = round($totale - $quota * ($rate - 1), 2); // ultima a saldo
        $pagamenti[] = $saldo;

        $this->assertEquals(111.11, $quota);
        $this->assertEquals(111.12, $saldo); // recupera gli arrotondamenti
        $this->assertEquals($totale, round(array_sum($pagamenti), 2)); // niente centesimi persi
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. BLOCCO — fotografia del gap di implementazione (doc §10, NON parte di R8)
    // ─────────────────────────────────────────────────────────────────────────

    /** §1/§10: i model che mapperebbero le tabelle dormienti NON esistono ancora. */
    public function test_blocco_model_attendance_e_teacherhour_assenti(): void
    {
        $this->assertFalse(class_exists(\App\Models\Attendance::class), 'Model Attendance ancora dormiente');
        $this->assertFalse(class_exists(\App\Models\TeacherHour::class), 'Model TeacherHour ancora dormiente');
    }

    /** §1: la relazione Lesson::attendances() è stata rimossa in Fase 1 — da ripristinare. */
    public function test_blocco_relazione_lesson_attendances_assente(): void
    {
        $this->assertFalse(method_exists(Lesson::class, 'attendances'), 'Lesson::attendances() da ripristinare');
    }

    /** §10: nessuna rotta admin per registro presenze / conto ore (commentate "evolutivo"). */
    public function test_blocco_rotte_presenze_e_compensi_assenti(): void
    {
        $this->assertFalse(Route::has('admin.attendances.index'));
        $this->assertFalse(Route::has('admin.attendances.store'));
        $this->assertFalse(Route::has('admin.teacher-hours.index'));
        $this->assertFalse(Route::has('admin.teacher-hours.store'));
    }

    /** §5: la categoria socio/non-socio non è ancora tipizzata sul Teacher (contract_type libero). */
    public function test_blocco_categoria_socio_non_tipizzata(): void
    {
        $teacher = $this->makeTeacher('Anna', 'Bianchi');
        $this->assertFalse(Schema::hasColumn('teachers', 'is_member'));
        $this->assertFalse(Schema::hasColumn('teachers', 'member_type'));
        // oggi vive solo come stringa libera (non discriminante affidabile di tariffa)
        $this->assertTrue(Schema::hasColumn('teachers', 'contract_type'));
    }
}
