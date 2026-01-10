<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher) {
            abort(403, 'Accesso riservato ai docenti');
        }
        
        $query = Lesson::with(['course', 'attendances.student'])
            ->where('teacher_id', $teacher->id)
            ->orWhere('substitute_teacher_id', $teacher->id);
        
        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', '>=', now()->toDateString());
        }
        
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        $lessons = $query->orderBy('date')
            ->orderBy('time_start')
            ->paginate(20);
        
        $courses = Course::where('teacher_id', $teacher->id)
            ->active()
            ->orderBy('name')
            ->get();
        
        return view('teacher.register.index', compact('lessons', 'courses'));
    }
    
    public function show(Lesson $lesson)
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher || ($lesson->teacher_id != $teacher->id && $lesson->substitute_teacher_id != $teacher->id)) {
            abort(403, 'Accesso negato');
        }
        
        $lesson->load(['course', 'attendances.student']);
        
        // Ottieni studenti iscritti al corso
        $enrolledStudents = $lesson->course->enrollments()
            ->with('student')
            ->get()
            ->pluck('student');
        
        return view('teacher.register.show', compact('lesson', 'enrolledStudents'));
    }
    
    public function updateAttendance(Request $request, Lesson $lesson)
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher || ($lesson->teacher_id != $teacher->id && $lesson->substitute_teacher_id != $teacher->id)) {
            abort(403, 'Accesso negato');
        }
        
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string',
        ]);
        
        foreach ($validated['attendances'] as $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'lesson_id' => $lesson->id,
                    'student_id' => $attendanceData['student_id'],
                ],
                [
                    'status' => $attendanceData['status'],
                    'notes' => $attendanceData['notes'] ?? null,
                ]
            );
        }
        
        $lesson->markAsCompleted();
        
        return redirect()->route('teacher.register.show', $lesson)
            ->with('success', 'Presenze registrate con successo.');
    }
}
