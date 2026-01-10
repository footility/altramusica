<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherHour;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Services\TeacherHourService;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class TeacherHourController extends Controller
{
    protected $teacherHourService;
    protected $academicYearService;

    public function __construct(TeacherHourService $teacherHourService, AcademicYearService $academicYearService)
    {
        $this->teacherHourService = $teacherHourService;
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $query = TeacherHour::with(['teacher', 'academicYear']);
        
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        
        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $teacherHours = $query->orderBy('period_start', 'desc')
            ->paginate(20);
        
        $teachers = Teacher::active()->orderBy('last_name')->orderBy('first_name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        
        return view('admin.teacher-hours.index', compact('teacherHours', 'teachers', 'academicYears'));
    }
    
    public function show(TeacherHour $teacherHour)
    {
        $teacherHour->load(['teacher', 'academicYear', 'approvedBy']);
        
        return view('admin.teacher-hours.show', compact('teacherHour'));
    }
    
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date',
        ]);
        
        $teacher = Teacher::findOrFail($validated['teacher_id']);
        $academicYear = AcademicYear::findOrFail($validated['academic_year_id']);
        
        $teacherHour = $this->teacherHourService->calculateHours(
            $teacher,
            $academicYear,
            $validated['period_start'] ?? null,
            $validated['period_end'] ?? null
        );
        
        return redirect()->route('admin.teacher-hours.show', $teacherHour)
            ->with('success', 'Conto orario calcolato con successo.');
    }
    
    public function calculateAll(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date',
        ]);
        
        $academicYear = AcademicYear::findOrFail($validated['academic_year_id']);
        
        $results = $this->teacherHourService->calculateAllTeachers(
            $academicYear,
            $validated['period_start'] ?? null,
            $validated['period_end'] ?? null
        );
        
        return redirect()->route('admin.teacher-hours.index')
            ->with('success', 'Conti orari calcolati per ' . count($results) . ' docenti.');
    }
    
    public function approve(TeacherHour $teacherHour)
    {
        $teacherHour->approve();
        
        return redirect()->route('admin.teacher-hours.show', $teacherHour)
            ->with('success', 'Conto orario approvato.');
    }
    
    public function markAsPaid(TeacherHour $teacherHour)
    {
        $teacherHour->markAsPaid();
        
        return redirect()->route('admin.teacher-hours.show', $teacherHour)
            ->with('success', 'Conto orario marcato come pagato.');
    }
}
