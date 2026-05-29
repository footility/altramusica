<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\EnrollmentController;
use App\Models\AcademicYear;
use App\Models\CalendarLesson;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Student;
use App\Services\EnrollmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * R2 · Controllo finale — Validazione corsi/iscrizioni/rinnovi (attività #8541).
 *
 * Test E2E dell'area Didattica che esercita SOLO ciò che esiste in codice e
 * documenta esplicitamente i gap rispetto al design R2 (doc 21_UX_...).
 * Riferimenti design: docs/21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md §3-§6.
 */
class DidatticaE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $prevYear;
    private AcademicYear $currentYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prevYear = AcademicYear::create([
            'name' => '2024/25',
            'slug' => '2024-25',
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => false,
        ]);

        $this->currentYear = AcademicYear::create([
            'name' => '2025/26',
            'slug' => '2025-26',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
    }

    /** Crea un'offerta annuale (catalogo -> offerta) con calendario lezioni. */
    private function makeOffering(AcademicYear $year, string $courseCode = 'PF'): CourseOffering
    {
        $type = CourseType::firstOrCreate(
            ['code' => 'IND'],
            ['name' => 'Individuale', 'duration_minutes' => 30, 'max_students' => 1,
             'price_full' => 90, 'price_reduced' => 70, 'price_annual' => 810,
             'price_monthly' => 90, 'active' => true]
        );

        $course = Course::firstOrCreate(
            ['code' => $courseCode],
            ['course_type_id' => $type->id, 'name' => 'Pianoforte']
        );

        $offering = CourseOffering::create([
            'course_id' => $course->id,
            'academic_year_id' => $year->id,
            'start_date' => $year->start_date,
            'end_date' => $year->end_date,
            'day_of_week' => 'monday',
            'time_start' => '15:00',
            'time_end' => '15:30',
            'max_students' => 8,
            'current_students' => 0,
            'status' => 'active',
            'price_per_lesson' => 20,
            'lessons_per_week' => 1,
            'weeks_per_year' => 30,
        ]);

        // Calendario: 30 lunedì attivi nell'anno -> il costo non è 0
        $date = Carbon::parse($year->start_date)->next(Carbon::MONDAY);
        for ($i = 0; $i < 30; $i++) {
            CalendarLesson::create([
                'academic_year_id' => $year->id,
                'date' => $date->copy(),
                'day_of_week' => 'monday',
                'is_active' => true,
            ]);
            $date->addWeek();
        }

        return $offering;
    }

    private function makeStudent(string $first, string $last, ?Guardian $guardian = null): Student
    {
        $student = Student::create([
            'first_name' => $first,
            'last_name' => $last,
            'birth_date' => '2012-01-01',
        ]);
        if ($guardian) {
            $student->guardians()->attach($guardian->id, [
                'relationship_type' => 'father',
                'is_primary' => true,
                'is_billing_contact' => true,
            ]);
        }
        return $student;
    }

    /** FLUSSO 1+2 — Catalogo->Offerta->Iscrizione: deve funzionare end-to-end. */
    public function test_crea_offerta_e_iscrive_studente_con_costo_calcolato(): void
    {
        $offering = $this->makeOffering($this->currentYear);
        $student = $this->makeStudent('Mario', 'Rossi');

        $service = app(EnrollmentService::class);
        $enrollment = $service->createEnrollment([
            'academic_year_id' => $this->currentYear->id,
            'student_id' => $student->id,
            'course_offering_id' => $offering->id,
            'enrollment_date' => $this->currentYear->start_date,
            'start_date' => $this->currentYear->start_date,
            'end_date' => $this->currentYear->end_date,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'student_id' => $student->id,
            'course_offering_id' => $offering->id,
            'status' => 'active',
        ]);

        // 30 settimane * 20€ * 1 lezione = 600€
        $this->assertEquals(600.0, (float) $enrollment->fresh()->total_amount,
            'Il costo iscrizione deve essere calcolato dal calendario lezioni.');
    }

    /** Sconti: % e importo fisso devono ridurre il totale. */
    public function test_sconto_percentuale_e_fisso_sul_totale(): void
    {
        $offering = $this->makeOffering($this->currentYear);
        $student = $this->makeStudent('Anna', 'Bianchi');

        $service = app(EnrollmentService::class);
        $enrollment = $service->createEnrollment([
            'academic_year_id' => $this->currentYear->id,
            'student_id' => $student->id,
            'course_offering_id' => $offering->id,
            'enrollment_date' => $this->currentYear->start_date,
            'start_date' => $this->currentYear->start_date,
            'end_date' => $this->currentYear->end_date,
            'status' => 'active',
            'discount_percentage' => 10,   // -10% sconto fratelli (manuale)
            'discount_amount' => 45,       // -45€ credito residuo (manuale)
        ]);

        // 600 * 0.9 - 45 = 495
        $this->assertEquals(495.0, (float) $enrollment->fresh()->total_amount);
    }

    /** FLUSSO LISTA — filtri index (anno/studente/offerta/stato/search). */
    public function test_filtri_lista_iscrizioni(): void
    {
        $offCurrent = $this->makeOffering($this->currentYear, 'PF');
        $offPrev = $this->makeOffering($this->prevYear, 'PF');

        $mario = $this->makeStudent('Mario', 'Rossi');
        $luca = $this->makeStudent('Luca', 'Verdi');

        $service = app(EnrollmentService::class);
        $base = fn ($s, $o, $y, $st) => $service->createEnrollment([
            'academic_year_id' => $y->id, 'student_id' => $s->id,
            'course_offering_id' => $o->id, 'enrollment_date' => $y->start_date,
            'start_date' => $y->start_date, 'end_date' => $y->end_date, 'status' => $st,
        ]);

        $base($mario, $offCurrent, $this->currentYear, 'active');
        $base($luca, $offCurrent, $this->currentYear, 'cancelled');
        $base($mario, $offPrev, $this->prevYear, 'active');

        $controller = app(EnrollmentController::class);
        $get = function (array $params) use ($controller) {
            $view = $controller->index(new Request($params));
            return $view->getData()['enrollments'];
        };

        // Default: solo anno corrente -> 2 iscrizioni
        $this->assertCount(2, $get([]), 'Default filtra sull\'anno attivo.');
        // Anno precedente
        $this->assertCount(1, $get(['academic_year_id' => $this->prevYear->id]));
        // Per studente (Mario, anno corrente)
        $this->assertCount(1, $get(['student_id' => $mario->id]));
        // Per stato
        $this->assertCount(1, $get(['status' => 'cancelled']));
        $this->assertCount(1, $get(['status' => 'active']));
        // Per offerta
        $this->assertCount(2, $get(['course_offering_id' => $offCurrent->id]));
        // Ricerca per cognome
        $this->assertCount(1, $get(['search' => 'Verdi']));
    }

    /**
     * FLUSSO 3 — Rinnovo con fratello.
     * BLOCCO ATTESO: il design R2 §5-§6 dichiara rinnovo e sconto fratelli NON
     * implementati. Questo test verifica/documenta il gap: nessuna route 'renew',
     * nessun metodo sul controller, nessun campo sibling sull'Enrollment.
     */
    public function test_rinnovo_e_sconto_fratelli_non_implementati_gap_documentato(): void
    {
        // Setup nucleo familiare: due fratelli con stesso genitore.
        $guardian = Guardian::create(['first_name' => 'Giuseppe', 'last_name' => 'Rossi']);
        $mario = $this->makeStudent('Mario', 'Rossi', $guardian);
        $giulia = $this->makeStudent('Giulia', 'Rossi', $guardian);

        // Iscrizione anno precedente (da cui dovrebbe partire il rinnovo)
        $offPrev = $this->makeOffering($this->prevYear, 'PF');
        $service = app(EnrollmentService::class);
        $service->createEnrollment([
            'academic_year_id' => $this->prevYear->id, 'student_id' => $mario->id,
            'course_offering_id' => $offPrev->id, 'enrollment_date' => $this->prevYear->start_date,
            'start_date' => $this->prevYear->start_date, 'end_date' => $this->prevYear->end_date,
            'status' => 'active',
        ]);

        // Il nucleo è rilevabile (precondizione per lo sconto fratelli).
        $siblings = $mario->guardians()->first()->students()->where('students.id', '!=', $mario->id)->get();
        $this->assertTrue($siblings->contains('id', $giulia->id),
            'Il legame fratelli è rilevabile via nucleo (R1).');

        // GAP 1: nessuna route di rinnovo.
        $routes = collect(app('router')->getRoutes())->map->getName()->filter()->values();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'renew') || str_contains((string) $n, 'rinnov')),
            'GAP: manca una route di rinnovo (design R2 §5).'
        );

        // GAP 2: nessun metodo "renew" sul controller.
        $this->assertFalse(method_exists(EnrollmentController::class, 'renew'),
            'GAP: EnrollmentController non espone azione di rinnovo.');

        // GAP 3: l'Enrollment non modella lo sconto fratelli né il link al rinnovo.
        $enrollmentColumns = (new Enrollment())->getFillable();
        $this->assertNotContains('sibling_discount', $enrollmentColumns,
            'GAP: sconto fratelli non modellato (design R2 §6a).');
        $this->assertNotContains('renewed_from_enrollment_id', $enrollmentColumns,
            'GAP: nessun tracciamento del rinnovo anno->anno (design R2 §5).');
    }
}
