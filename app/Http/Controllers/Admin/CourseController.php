<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseType;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('course_type_id')) {
            $query->where('course_type_id', $request->course_type_id);
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->with(['courseType', 'teacher', 'enrollments'])
            ->withCount('enrollments')
            ->orderBy('name')
            ->paginate(20);

        $courseTypes = CourseType::active()->orderBy('name')->get();
        $teachers = Teacher::active()->orderBy('last_name')->get();

        return view('admin.courses.index', compact('courses', 'courseTypes', 'teachers'));
    }

    public function create()
    {
        $courseTypes = CourseType::active()->orderBy('name')->get();
        $teachers = Teacher::active()->orderBy('last_name')->get();
        return view('admin.courses.create', compact('courseTypes', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_type_id' => 'required|exists:course_types,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'code' => 'nullable|string|unique:courses,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'required|in:planned,active,completed,cancelled',
            'price_per_lesson' => 'required|numeric|min:0',
            'lessons_per_week' => 'required|integer|min:1',
            'weeks_per_year' => 'nullable|integer|min:1',
        ]);

        $validated['current_students'] = 0;

        Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso creato con successo.');
    }

    public function show(Course $course)
    {
        $course->load(['courseType', 'teacher', 'enrollments.student']);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $courseTypes = CourseType::active()->orderBy('name')->get();
        $teachers = Teacher::active()->orderBy('last_name')->get();
        return view('admin.courses.edit', compact('course', 'courseTypes', 'teachers'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_type_id' => 'required|exists:course_types,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'code' => 'nullable|string|unique:courses,code,' . $course->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'required|in:planned,active,completed,cancelled',
            'price_per_lesson' => 'required|numeric|min:0',
            'lessons_per_week' => 'required|integer|min:1',
            'weeks_per_year' => 'nullable|integer|min:1',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso aggiornato con successo.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso eliminato con successo.');
    }
}
