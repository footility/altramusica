<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CalendarLesson;
use App\Models\CalendarSuspension;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\User;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * R7 · Controllo finale — Validazione calendario/recuperi (attività #8544).
 *
 * Test E2E del generatore calendario e del flusso recuperi che esercita SOLO
 * ciò che esiste in codice (CalendarService, CalendarController generate/
 * suspensions/events, CalendarLesson/CalendarSuspension/Lesson) e documenta
 * esplicitamente i gap rispetto al design R7.
 * Riferimenti: docs/26_UX_GENERATORE_CALENDARIO_E_RECUPERI.md §1, §3-§6, §9.
 */
class CalendarioRecuperiE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $year;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('acl:sync', ['--reset-defaults' => true]);

        // Anno scolastico realistico (AS-IS Calendario 2025-26.ods)
        $this->year = AcademicYear::create([
            'name' => '2025/26',
            'slug' => '2025-26',
            'start_date' => '2025-09-15',
            'end_date' => '2026-05-31',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    // ─────────────────────────────────────────────────────────────────────
    // CIÒ CHE ESISTE — motore base validato E2E
    // ─────────────────────────────────────────────────────────────────────

    /** @test Generazione calendario base lun-ven via HTTP (R7 §1: motore esiste). */
    public function genera_calendario_lun_ven(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.calendar.generate'), [
                'academic_year_id' => $this->year->id,
                'days_of_week' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            ])
            ->assertRedirect(route('admin.calendar.index', ['year_id' => $this->year->id]))
            ->assertSessionHas('success');

        $total = CalendarLesson::forYear($this->year->id)->count();

        // 15/09/2025 → 31/05/2026, solo lun-ven: nessun sabato/domenica generato.
        $this->assertGreaterThan(100, $total, 'Attesi oltre 100 giorni di lezione lun-ven');
        $this->assertSame(
            0,
            CalendarLesson::forYear($this->year->id)
                ->whereIn('day_of_week', ['saturday', 'sunday'])
                ->count(),
            'Nessun giorno weekend deve essere generato per lun-ven'
        );
        // Tutti attivi prima delle sospensioni
        $this->assertSame($total, CalendarLesson::forYear($this->year->id)->active()->count());
    }

    /** @test Aggiungi sospensione: crea il periodo e disattiva i giorni nel range (R7 §3-§4). */
    public function aggiungi_sospensione_disattiva_giorni_nel_range(): void
    {
        app(CalendarService::class)->generateLessonsForYear($this->year);

        $this->actingAs($this->user)
            ->post(route('admin.calendar.suspensions.store'), [
                'academic_year_id' => $this->year->id,
                'name' => 'Vacanze di Natale',
                'start_date' => '2025-12-23',
                'end_date' => '2026-01-06',
                'notes' => 'Chiusura natalizia',
            ])
            ->assertRedirect(route('admin.calendar.index', ['year_id' => $this->year->id]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('calendar_suspensions', [
            'academic_year_id' => $this->year->id,
            'name' => 'Vacanze di Natale',
        ]);

        // Tutti i giorni-lezione nel range Natale sono ora disattivati
        $inRange = CalendarLesson::forYear($this->year->id)
            ->whereBetween('date', ['2025-12-23', '2026-01-06'])
            ->get();
        $this->assertTrue($inRange->isNotEmpty(), 'Devono esserci giorni-lezione nel periodo natalizio');
        $this->assertTrue($inRange->every(fn ($l) => $l->is_active === false),
            'Tutti i giorni nel range della sospensione devono essere disattivati');

        // Un giorno fuori range resta attivo (10/11/2025 è un lunedì non sospeso)
        $fuoriRange = CalendarLesson::forYear($this->year->id)
            ->whereDate('date', '2025-11-10')->first();
        $this->assertNotNull($fuoriRange);
        $this->assertTrue($fuoriRange->is_active);
    }

    /** @test "Settimane previste → effettive": countActiveWeeks esclude i giorni sospesi (R7 §5). */
    public function settimane_effettive_escludono_le_sospensioni(): void
    {
        $service = app(CalendarService::class);
        $service->generateLessonsForYear($this->year);

        // Settimane di martedì prima della sospensione
        $previsteMartedi = $service->countWeeksForDay(
            $this->year, 'tuesday', Carbon::parse('2025-09-15')
        );

        // Sospendo due settimane (Natale) che includono dei martedì
        $service->applySuspension(
            $this->year, 'Natale', Carbon::parse('2025-12-23'), Carbon::parse('2026-01-06')
        );

        $effettiveMartedi = $service->countWeeksForDay(
            $this->year, 'tuesday', Carbon::parse('2025-09-15')
        );

        $this->assertLessThan($previsteMartedi, $effettiveMartedi,
            'Le settimane effettive devono diminuire dopo la sospensione (concetto previste→effettive R7 §5)');
    }

    /** @test events() espone giorni-lezione, lezioni effettive e banda sospensione (R7 §2). */
    public function events_espone_lezioni_e_sospensioni(): void
    {
        $service = app(CalendarService::class);
        $service->generateLessonsForYear($this->year);
        $service->applySuspension(
            $this->year, 'Natale', Carbon::parse('2025-12-23'), Carbon::parse('2026-01-06')
        );
        $lesson = $this->makeLesson('2025-10-07', '15:00:00', '15:45:00');

        $response = $this->actingAs($this->user)->getJson(
            route('admin.calendar.events', [
                'year_id' => $this->year->id,
                'start' => '2025-09-01',
                'end' => '2026-06-30',
                'type' => 'all',
            ])
        )->assertOk();

        $events = collect($response->json());
        $types = $events->pluck('extendedProps.type')->unique();

        $this->assertContains('lesson', $types, 'Attesi eventi giorno-lezione (calendario base)');
        $this->assertContains('actual-lesson', $types, 'Attesa la lezione effettiva creata');
        $this->assertContains('suspension', $types, 'Attesa la banda della sospensione');

        // La lezione effettiva riporta il titolo del corso
        $actual = $events->firstWhere('extendedProps.lesson_id', $lesson->id);
        $this->assertNotNull($actual);
    }

    /** @test destroySuspension riattiva i giorni nel range (coerenza con §4). */
    public function rimuovi_sospensione_riattiva_i_giorni(): void
    {
        $service = app(CalendarService::class);
        $service->generateLessonsForYear($this->year);
        $suspension = $service->applySuspension(
            $this->year, 'Natale', Carbon::parse('2025-12-23'), Carbon::parse('2026-01-06')
        );

        $this->actingAs($this->user)
            ->delete(route('admin.calendar.suspensions.destroy', $suspension))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('calendar_suspensions', ['id' => $suspension->id]);
        $inRange = CalendarLesson::forYear($this->year->id)
            ->whereBetween('date', ['2025-12-23', '2026-01-06'])
            ->get();
        $this->assertTrue($inRange->every(fn ($l) => $l->is_active === true),
            'Dopo la rimozione i giorni devono tornare attivi');
    }

    // ─────────────────────────────────────────────────────────────────────
    // GAP / BLOCCHI — design R7 non ancora implementato (coerente con §1, §9)
    // ─────────────────────────────────────────────────────────────────────

    /** @test BLOCCO: nessun concetto di "ciclo da 11 settimane" persistito (R7 §1, §9). */
    public function blocco_nessun_concetto_di_ciclo(): void
    {
        $this->assertFalse(Schema::hasTable('calendar_cycles'),
            'R7 §9: i cicli non sono persistiti (decisione Fase 2). Tabella assente come da design.');

        // generateLessonsForYear non accetta cicli: firma a soli days_of_week
        $params = (new \ReflectionMethod(CalendarService::class, 'generateLessonsForYear'))
            ->getParameters();
        $names = array_map(fn ($p) => $p->getName(), $params);
        $this->assertSame(['year', 'daysOfWeek'], $names,
            'Il generatore lavora per giorni della settimana, non per cicli (R7 §1).');
    }

    /** @test BLOCCO: nessuna preview/dry-run — generate scrive subito in DB (R7 §2, §5, §9). */
    public function blocco_nessuna_preview_prima_del_commit(): void
    {
        $routes = collect(app('router')->getRoutes())->map->getName()->filter()->values();

        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains($n, 'calendar') && str_contains($n, 'preview')),
            'R7 §5: nessun endpoint di anteprima (dry-run) prima del commit.'
        );

        // generate() scrive direttamente: dopo la chiamata il DB cambia
        $this->assertSame(0, CalendarLesson::forYear($this->year->id)->count());
        $this->actingAs($this->user)->post(route('admin.calendar.generate'), [
            'academic_year_id' => $this->year->id,
        ]);
        $this->assertGreaterThan(0, CalendarLesson::forYear($this->year->id)->count(),
            'generate() scrive subito: nessun passo di anteprima (R7 §2 "niente generazione al buio").');
    }

    /** @test BLOCCO: nessuna scelta Sovrascrivi/Aggiungi-mancanti, generate sovrascrive in silenzio (R7 §5). */
    public function blocco_overwrite_silenzioso(): void
    {
        // Il generatore usa updateOrCreate (sovrascrittura implicita), senza opzione di modalità.
        $source = file_get_contents(app_path('Services/CalendarService.php'));
        $this->assertStringContainsString('updateOrCreate', $source,
            'generateLessonsForYear sovrascrive sempre via updateOrCreate (R7 §5: oggi overwrite in silenzio).');

        // generate() non riconosce alcun parametro di modalità (Sovrascrivi / Aggiungi mancanti)
        $controller = file_get_contents(app_path('Http/Controllers/Admin/CalendarController.php'));
        $generateBody = substr($controller, strpos($controller, 'function generate'));
        $generateBody = substr($generateBody, 0, strpos($generateBody, 'function createSuspension'));
        $this->assertStringNotContainsString('mode', $generateBody);
        $this->assertStringNotContainsString('overwrite', $generateBody);
        $this->assertStringNotContainsString('mancanti', $generateBody,
            'R7 §5: nessuna scelta Sovrascrivi/Aggiungi-mancanti nel controller.');
    }

    /** @test BLOCCO: spostamento/recupero lezione assente — niente route, niente colonne traccia (R7 §6, §9). */
    public function blocco_recupero_spostamento_lezione(): void
    {
        $routes = collect(app('router')->getRoutes())->map->getName()->filter()->values();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains($n, 'reschedule') || str_contains($n, 'sposta')),
            'R7 §6/§9: nessun endpoint lessons/{id}/reschedule (azione "Sposta/Recupera").'
        );

        // Nessuna colonna traccia: original_date / reschedule_reason
        $this->assertFalse(Schema::hasColumn('lessons', 'original_date'),
            'R7 §9: manca lessons.original_date (storico data prevista).');
        $this->assertFalse(Schema::hasColumn('lessons', 'reschedule_reason'),
            'R7 §9: manca lessons.reschedule_reason (motivo recupero/spostamento/assenza).');

        // Il modello Lesson non è nemmeno predisposto a salvare classroom_id allo spostamento
        $this->assertNotContains('classroom_id', (new Lesson())->getFillable(),
            'R7 §6: Lesson.classroom_id non fillable, lo spostamento aula non sarebbe persistibile.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────

    private function makeLesson(string $date, string $start, string $end): Lesson
    {
        $type = CourseType::create([
            'code' => 'PF', 'name' => 'Pianoforte', 'duration_minutes' => 45,
        ]);
        $course = Course::create([
            'course_type_id' => $type->id, 'code' => 'PF1', 'name' => 'Pianoforte',
        ]);
        $teacher = Teacher::create([
            'first_name' => 'Mario', 'last_name' => 'Rossi', 'active' => true,
        ]);
        $classroom = Classroom::create(['code' => 'A2', 'name' => 'Aula A2']);
        $offering = CourseOffering::create([
            'course_id' => $course->id,
            'academic_year_id' => $this->year->id,
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'status' => 'active',
        ]);

        return Lesson::create([
            'course_offering_id' => $offering->id,
            'teacher_id' => $teacher->id,
            'date' => $date,
            'time_start' => $start,
            'time_end' => $end,
            'completed' => false,
        ]);
    }
}
