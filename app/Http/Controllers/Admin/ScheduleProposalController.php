<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleProposal;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Services\ScheduleProposalService;
use Illuminate\Http\Request;

class ScheduleProposalController extends Controller
{
    protected $scheduleProposalService;

    public function __construct(ScheduleProposalService $scheduleProposalService)
    {
        $this->scheduleProposalService = $scheduleProposalService;
    }

    public function index(Request $request)
    {
        $query = ScheduleProposal::with(['student', 'teacher', 'course']);
        
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['draft', 'proposed']);
        }
        
        $proposals = $query->orderBy('day_of_week')
            ->orderBy('time_start')
            ->paginate(50);
        
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.schedule-proposals.index', compact('proposals', 'students'));
    }
    
    public function create(Request $request)
    {
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $teachers = Teacher::active()->orderBy('last_name')->orderBy('first_name')->get();
        $courses = Course::active()->orderBy('name')->get();
        
        $selectedStudents = [];
        if ($request->has('student_ids')) {
            $selectedStudents = is_array($request->student_ids) ? $request->student_ids : [$request->student_ids];
        }
        
        return view('admin.schedule-proposals.create', compact('students', 'teachers', 'courses', 'selectedStudents'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'course_id' => 'nullable|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'notes' => 'nullable|string',
        ]);
        
        $proposals = $this->scheduleProposalService->generateProposals(
            $validated['student_ids'],
            [
                'course_id' => $validated['course_id'] ?? null,
                'teacher_id' => $validated['teacher_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );
        
        return redirect()->route('admin.schedule-proposals.index')
            ->with('success', "Generate " . count($proposals) . " proposte orarie.");
    }
    
    public function show(ScheduleProposal $scheduleProposal)
    {
        $scheduleProposal->load(['student', 'teacher', 'course', 'acceptedBy']);
        
        return view('admin.schedule-proposals.show', compact('scheduleProposal'));
    }
    
    public function accept(ScheduleProposal $scheduleProposal)
    {
        $scheduleProposal->accept();
        
        // Opzionalmente crea Enrollment se c'è un corso
        if ($scheduleProposal->course_id) {
            // TODO: Creare Enrollment se necessario
        }
        
        return redirect()->route('admin.schedule-proposals.show', $scheduleProposal)
            ->with('success', 'Proposta accettata.');
    }
    
    public function reject(ScheduleProposal $scheduleProposal)
    {
        $scheduleProposal->reject();
        
        return redirect()->route('admin.schedule-proposals.show', $scheduleProposal)
            ->with('success', 'Proposta rifiutata.');
    }
    
    public function destroy(ScheduleProposal $scheduleProposal)
    {
        $scheduleProposal->delete();
        
        return redirect()->route('admin.schedule-proposals.index')
            ->with('success', 'Proposta eliminata.');
    }
}
