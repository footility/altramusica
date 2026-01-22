<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LessonCalendarController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        
        if (!$currentYear) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Nessun anno scolastico attivo.');
        }

        $yearId = $request->get('year_id', $currentYear->id);
        $year = AcademicYear::findOrFail($yearId);

        $teachers = Teacher::active()->orderBy('last_name')->orderBy('first_name')->get();
        $courses = Course::active()->orderBy('name')->get();
        $classrooms = Classroom::available()->orderBy('name')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.lessons.calendar', compact('year', 'years', 'teachers', 'courses', 'classrooms'));
    }

    public function events(Request $request)
    {
        $yearId = $request->get('year_id');
        $start = $request->get('start');
        $end = $request->get('end');
        $teacherId = $request->get('teacher_id');
        $courseId = $request->get('course_id');
        $classroomId = $request->get('classroom_id');

        if (!$yearId) {
            $currentYear = $this->academicYearService->getCurrent();
            $yearId = $currentYear ? $currentYear->id : null;
        }

        if (!$yearId) {
            return response()->json([]);
        }

        $query = Lesson::with(['course', 'teacher', 'classroom'])
            ->whereHas('course', function($q) use ($yearId) {
                $q->whereHas('enrollments', function($eq) use ($yearId) {
                    $eq->whereHas('student', function($sq) use ($yearId) {
                        $sq->where('academic_year_id', $yearId);
                    });
                });
            })
            ->where('date', '>=', $start)
            ->where('date', '<=', $end);

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
        }

        $lessons = $query->orderBy('date')->orderBy('time_start')->get();

        $events = [];
        foreach ($lessons as $lesson) {
            $title = $lesson->course->name ?? 'Lezione';
            if ($lesson->teacher) {
                $title .= ' - ' . $lesson->teacher->last_name;
            }
            if ($lesson->classroom) {
                $title .= ' (' . $lesson->classroom->code . ')';
            }
            
            // Colore in base allo stato (Fase 1: niente presenze/assenze)
            $color = $lesson->completed ? '#6c757d' : '#007bff';
            
            $events[] = [
                'id' => 'lesson-' . $lesson->id,
                'title' => $title,
                'start' => $lesson->date->format('Y-m-d') . 'T' . $lesson->time_start->format('H:i:s'),
                'end' => $lesson->date->format('Y-m-d') . 'T' . $lesson->time_end->format('H:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#fff',
                // NOTE (Fase 1): rimosso registro docente (teacher.register.*). Non forniamo link click.
                'url' => null,
                'extendedProps' => [
                    'type' => 'lesson',
                    'lesson_id' => $lesson->id,
                    'course_id' => $lesson->course_id,
                    'teacher_id' => $lesson->teacher_id,
                    'classroom_id' => $lesson->classroom_id,
                    'completed' => $lesson->completed,
                    'attendances_count' => null,
                ]
            ];
        }

        return response()->json($events);
    }
}

