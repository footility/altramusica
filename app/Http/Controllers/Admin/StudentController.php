<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentYear;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $query = Student::query();

        // Filtro per anno scolastico (su tabella annuale student_years)
        $yearId = $request->get('academic_year_id') ?: ($currentYear?->id);
        if ($yearId) {
            $query->join('student_years', function ($join) use ($yearId) {
                $join->on('student_years.student_id', '=', 'students.id')
                    ->where('student_years.academic_year_id', '=', $yearId);
            })->addSelect([
                'students.*',
                'student_years.code as code',
                'student_years.status as status',
            ]);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('tax_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('student_years.status', $request->status);
        }

        $students = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $statuses = [
            'prospect' => 'Prospect',
            'interested' => 'Interessato',
            'enrolled' => 'Iscritto',
            'withdrawn' => 'Ritirato',
        ];

        return view('admin.students.index', compact('students', 'years', 'statuses', 'currentYear'));
    }

    public function create()
    {
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.students.create', compact('years'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'code' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:120',
            'tax_code' => 'nullable|string|max:16',
            'status' => 'required|in:prospect,interested,enrolled,withdrawn',
            'school_origin' => 'nullable|string|max:255',
            'how_know_us' => 'nullable|string|max:255',
            'preferences' => 'nullable|string',
            'notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'privacy_consent' => 'boolean',
            'photo_consent' => 'boolean',
            'last_contact_date' => 'nullable|date',
        ]);

        // Se non specificato, usa anno corrente
        if (!$validated['academic_year_id']) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        $student = Student::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'age' => $validated['age'] ?? null,
            'tax_code' => $validated['tax_code'] ?? null,
        ]);

        StudentYear::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $validated['academic_year_id'],
            ],
            [
                'code' => $validated['code'] ?? null,
                'status' => $validated['status'],
                'school_origin' => $validated['school_origin'] ?? null,
                'how_know_us' => $validated['how_know_us'] ?? null,
                'preferences' => $validated['preferences'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'admin_notes' => $validated['admin_notes'] ?? null,
                'privacy_consent' => (bool) ($validated['privacy_consent'] ?? false),
                'photo_consent' => (bool) ($validated['photo_consent'] ?? false),
                'last_contact_date' => $validated['last_contact_date'] ?? null,
            ]
        );

        return redirect()->route('admin.students.index')
            ->with('success', 'Studente creato con successo.');
    }

    public function show(Student $student)
    {
        $student->load([
            'years.academicYear',
            'guardians',
            'enrollments.course',
            'enrollments.academicYear',
            'invoices',
            'contracts',
            'exams',
            'instrumentRentals.instrument',
            'bookDistributions',
        ]);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.students.edit', compact('student', 'years'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'code' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:120',
            'tax_code' => 'nullable|string|max:16',
            'status' => 'required|in:prospect,interested,enrolled,withdrawn',
            'school_origin' => 'nullable|string|max:255',
            'how_know_us' => 'nullable|string|max:255',
            'preferences' => 'nullable|string',
            'notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'privacy_consent' => 'boolean',
            'photo_consent' => 'boolean',
            'last_contact_date' => 'nullable|date',
        ]);

        if (!$validated['academic_year_id']) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        $student->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'age' => $validated['age'] ?? null,
            'tax_code' => $validated['tax_code'] ?? null,
        ]);

        if ($validated['academic_year_id']) {
            StudentYear::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $validated['academic_year_id'],
                ],
                [
                    'code' => $validated['code'] ?? null,
                    'status' => $validated['status'],
                    'school_origin' => $validated['school_origin'] ?? null,
                    'how_know_us' => $validated['how_know_us'] ?? null,
                    'preferences' => $validated['preferences'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'admin_notes' => $validated['admin_notes'] ?? null,
                    'privacy_consent' => (bool) ($validated['privacy_consent'] ?? false),
                    'photo_consent' => (bool) ($validated['photo_consent'] ?? false),
                    'last_contact_date' => $validated['last_contact_date'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Studente aggiornato con successo.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Studente eliminato con successo.');
    }

    public function export(Request $request)
    {
        // TODO: Implementare export Excel/CSV
        return response()->json(['message' => 'Export non ancora implementato']);
    }
}
