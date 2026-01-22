<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = AcademicYear::getCurrent();
        $query = CourseOffering::query()
            ->with(['course.courseType', 'teacher'])
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id));

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('course', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('course_type_id')) {
            $courseTypeId = $request->course_type_id;
            $query->whereHas('course', function ($q) use ($courseTypeId) {
                $q->where('course_type_id', $courseTypeId);
            });
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->with(['enrollments'])
            ->withCount('enrollments')
            ->orderByDesc('id')
            ->paginate(20);

        $courseTypes = CourseType::active()->orderBy('name')->get();
        $teachers = Teacher::active()->orderBy('last_name')->get();

        return view('admin.courses.index', compact('courses', 'courseTypes', 'teachers', 'currentYear'));
    }

    public function create()
    {
        $courseTypes = CourseType::active()->orderBy('name')->get();
        $teachers = Teacher::active()->orderBy('last_name')->get();
        $currentYear = AcademicYear::getCurrent();
        return view('admin.courses.create', compact('courseTypes', 'teachers', 'currentYear'));
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
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'required|in:planned,active,completed,cancelled',
            'price_per_lesson' => 'required|numeric|min:0',
            'lessons_per_week' => 'required|integer|min:1',
            'weeks_per_year' => 'nullable|integer|min:1',
        ]);

        $currentYear = AcademicYear::getCurrent();

        // courses.code non è nullable: genera un codice se non fornito
        if (empty($validated['code'])) {
            $base = strtoupper(Str::slug($validated['name'], '-'));
            $base = $base !== '' ? $base : 'COURSE';
            $code = $base;
            $i = 1;
            while (Course::where('code', $code)->exists()) {
                $i++;
                $code = $base . '-' . $i;
            }
            $validated['code'] = $code;
        }

        $course = Course::create([
            'course_type_id' => $validated['course_type_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        CourseOffering::create([
            'course_id' => $course->id,
            'academic_year_id' => $currentYear?->id,
            'teacher_id' => $validated['teacher_id'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'day_of_week' => $validated['day_of_week'] ?? null,
            'time_start' => $validated['time_start'] ?? null,
            'time_end' => $validated['time_end'] ?? null,
            'max_students' => $validated['max_students'] ?? null,
            'current_students' => 0,
            'status' => $validated['status'],
            'price_per_lesson' => $validated['price_per_lesson'],
            'lessons_per_week' => $validated['lessons_per_week'],
            'weeks_per_year' => $validated['weeks_per_year'] ?? 36,
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso creato con successo.');
    }

    public function show(Course $course)
    {
        $currentYear = AcademicYear::getCurrent();
        $course->load(['courseType']);
        $offering = $course->offerings()
            ->with(['teacher', 'enrollments.student'])
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id))
            ->orderByDesc('id')
            ->first();

        return view('admin.courses.show', compact('course', 'offering', 'currentYear'));
    }

    public function edit(Course $course)
    {
        $courseTypes = CourseType::active()->orderBy('name')->get();
        $teachers = Teacher::active()->orderBy('last_name')->get();
        $currentYear = AcademicYear::getCurrent();
        $offering = $course->offerings()
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id))
            ->orderByDesc('id')
            ->first();

        return view('admin.courses.edit', compact('course', 'courseTypes', 'teachers', 'offering', 'currentYear'));
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
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'required|in:planned,active,completed,cancelled',
            'price_per_lesson' => 'required|numeric|min:0',
            'lessons_per_week' => 'required|integer|min:1',
            'weeks_per_year' => 'nullable|integer|min:1',
        ]);

        $currentYear = AcademicYear::getCurrent();

        if (empty($validated['code'])) {
            $validated['code'] = $course->code;
        }

        $course->update([
            'course_type_id' => $validated['course_type_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $offering = $course->offerings()->firstOrCreate(
            ['academic_year_id' => $currentYear?->id],
            ['course_id' => $course->id]
        );
        $offering->update([
            'teacher_id' => $validated['teacher_id'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'day_of_week' => $validated['day_of_week'] ?? null,
            'time_start' => $validated['time_start'] ?? null,
            'time_end' => $validated['time_end'] ?? null,
            'max_students' => $validated['max_students'] ?? null,
            'status' => $validated['status'],
            'price_per_lesson' => $validated['price_per_lesson'],
            'lessons_per_week' => $validated['lessons_per_week'],
            'weeks_per_year' => $validated['weeks_per_year'] ?? 36,
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso aggiornato con successo.');
    }

    public function destroy(Course $course)
    {
        $course->offerings()->delete();
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Corso eliminato con successo.');
    }
}
