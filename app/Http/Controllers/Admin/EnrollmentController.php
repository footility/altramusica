<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\AcademicYear;
use App\Services\EnrollmentService;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    protected $enrollmentService;
    protected $academicYearService;

    public function __construct(EnrollmentService $enrollmentService, AcademicYearService $academicYearService)
    {
        $this->enrollmentService = $enrollmentService;
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $query = Enrollment::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } elseif ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('course_offering_id')) {
            $query->where('course_offering_id', $request->course_offering_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->with(['student', 'courseOffering.course', 'academicYear'])
            ->orderBy('enrollment_date', 'desc')
            ->paginate(20);

        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $offeringsQuery = CourseOffering::with(['course'])->orderBy('id', 'desc');
        if ($currentYear) {
            $offeringsQuery->where('academic_year_id', $currentYear->id);
        }
        $courseOfferings = $offeringsQuery->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.enrollments.index', compact('enrollments', 'students', 'courseOfferings', 'years', 'currentYear'));
    }

    public function create(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $offeringsQuery = CourseOffering::with(['course'])->orderBy('id', 'desc');
        if ($currentYear) {
            $offeringsQuery->where('academic_year_id', $currentYear->id);
        }
        $courseOfferings = $offeringsQuery->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();

        $preselectedStudent = $request->get('student_id') ? Student::find($request->get('student_id')) : null;

        return view('admin.enrollments.create', compact('students', 'courseOfferings', 'years', 'currentYear', 'preselectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'course_offering_id' => 'required|exists:course_offerings,id',
            'enrollment_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,cancelled',
            'notes' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        // Se non specificato, usa anno corrente
        if (!$validated['academic_year_id']) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        $enrollment = $this->enrollmentService->createEnrollment($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Iscrizione creata con successo.');
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'courseOffering.course', 'academicYear']);
        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function edit(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'courseOffering.course', 'academicYear']);
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $courseOfferings = CourseOffering::with(['course'])->orderBy('id', 'desc')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courseOfferings', 'years'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'course_offering_id' => 'required|exists:course_offerings,id',
            'enrollment_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,cancelled',
            'notes' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $this->enrollmentService->updateEnrollment($enrollment, $validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Iscrizione aggiornata con successo.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Iscrizione eliminata con successo.');
    }
}
