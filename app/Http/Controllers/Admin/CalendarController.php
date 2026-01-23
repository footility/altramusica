<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CalendarLesson;
use App\Models\CalendarSuspension;
use App\Services\CalendarService;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    protected $calendarService;
    protected $academicYearService;

    public function __construct(CalendarService $calendarService, AcademicYearService $academicYearService)
    {
        $this->calendarService = $calendarService;
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        
        if (!$currentYear) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Nessun anno scolastico attivo. Configura un anno scolastico prima.');
        }

        $yearId = $request->get('year_id', $currentYear->id);
        $year = AcademicYear::findOrFail($yearId);

        $suspensions = CalendarSuspension::forYear($year->id)
            ->orderBy('start_date')
            ->get();

        $years = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.calendar.index', compact('suspensions', 'year', 'years'));
    }

    public function events(Request $request)
    {
        $yearId = $request->get('year_id');
        $start = $request->get('start');
        $end = $request->get('end');
        $type = $request->get('type', 'all'); // all, lessons, schedule, availability

        if (!$yearId) {
            $currentYear = $this->academicYearService->getCurrent();
            $yearId = $currentYear ? $currentYear->id : null;
        }

        if (!$yearId) {
            return response()->json([]);
        }

        $events = [];

        // Lezioni attive (calendario base)
        if ($type === 'all' || $type === 'lessons') {
            $lessons = CalendarLesson::forYear($yearId)
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->orderBy('date')
                ->get();

            foreach ($lessons as $lesson) {
                $events[] = [
                    'id' => 'lesson-' . $lesson->id,
                    'title' => $lesson->is_active ? 'Lezione' : 'Lezione (Sospesa)',
                    'start' => $lesson->date->format('Y-m-d'),
                    'backgroundColor' => $lesson->is_active ? '#28a745' : '#dc3545',
                    'borderColor' => $lesson->is_active ? '#28a745' : '#dc3545',
                    'textColor' => '#fff',
                    'extendedProps' => [
                        'type' => 'lesson',
                        'is_active' => $lesson->is_active,
                        'day_of_week' => $lesson->day_of_week,
                        'notes' => $lesson->notes,
                    ]
                ];
            }
        }

        // Lezioni effettive (Lesson model)
        if ($type === 'all' || $type === 'schedule') {
            $actualLessons = \App\Models\Lesson::with(['courseOffering.course', 'teacher', 'classroom'])
                ->whereHas('courseOffering', function ($q) use ($yearId) {
                    $q->where('academic_year_id', $yearId);
                })
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->get();

            foreach ($actualLessons as $lesson) {
                $title = $lesson->courseOffering?->course?->name ?? 'Lezione';
                if ($lesson->teacher) {
                    $title .= ' - ' . $lesson->teacher->last_name;
                }
                if ($lesson->classroom) {
                    $title .= ' (' . $lesson->classroom->code . ')';
                }
                
                $events[] = [
                    'id' => 'actual-lesson-' . $lesson->id,
                    'title' => $title,
                    'start' => $lesson->date->format('Y-m-d') . 'T' . ($lesson->time_start ?? '00:00:00'),
                    'end' => $lesson->date->format('Y-m-d') . 'T' . ($lesson->time_end ?? '00:00:00'),
                    'backgroundColor' => $lesson->completed ? '#6c757d' : '#007bff',
                    'borderColor' => $lesson->completed ? '#6c757d' : '#007bff',
                    'textColor' => '#fff',
                    'extendedProps' => [
                        'type' => 'actual-lesson',
                        'lesson_id' => $lesson->id,
                        'course_offering_id' => $lesson->course_offering_id,
                        'teacher_id' => $lesson->teacher_id,
                        'classroom_id' => $lesson->classroom_id,
                        'completed' => $lesson->completed,
                    ]
                ];
            }
        }

        // Sospensioni
        if ($type === 'all' || $type === 'lessons') {
            $suspensions = CalendarSuspension::forYear($yearId)
                ->where('end_date', '>=', $start)
                ->where('start_date', '<=', $end)
                ->get();

            foreach ($suspensions as $suspension) {
                $endDate = $suspension->end_date->copy()->addDay(); // FullCalendar end è esclusivo
                $events[] = [
                    'id' => 'suspension-' . $suspension->id,
                    'title' => $suspension->name,
                    'start' => $suspension->start_date->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'backgroundColor' => '#ffc107',
                    'borderColor' => '#ffc107',
                    'textColor' => '#000',
                    'extendedProps' => [
                        'type' => 'suspension',
                        'notes' => $suspension->notes,
                    ]
                ];
            }
        }

        return response()->json($events);
    }

    public function generate(Request $request)
    {
        $year = AcademicYear::findOrFail($request->academic_year_id);
        
        $daysOfWeek = $request->input('days_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
        
        $count = $this->calendarService->generateLessonsForYear($year, $daysOfWeek);

        return redirect()->route('admin.calendar.index', ['year_id' => $year->id])
            ->with('success', "Generati {$count} giorni di lezione.");
    }

    public function createSuspension()
    {
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.calendar.create-suspension', compact('years'));
    }

    public function storeSuspension(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $year = AcademicYear::findOrFail($validated['academic_year_id']);
        
        $this->calendarService->applySuspension(
            $year,
            $validated['name'],
            Carbon::parse($validated['start_date']),
            Carbon::parse($validated['end_date']),
            $validated['notes'] ?? ''
        );

        return redirect()->route('admin.calendar.index', ['year_id' => $year->id])
            ->with('success', 'Sospensione creata e applicata al calendario.');
    }

    public function destroySuspension(CalendarSuspension $suspension)
    {
        $yearId = $suspension->academic_year_id;
        
        // Riattiva i giorni nella sospensione
        CalendarLesson::forYear($yearId)
            ->whereBetween('date', [$suspension->start_date, $suspension->end_date])
            ->update(['is_active' => true]);

        $suspension->delete();

        return redirect()->route('admin.calendar.index', ['year_id' => $yearId])
            ->with('success', 'Sospensione eliminata e giorni riattivati.');
    }
}
