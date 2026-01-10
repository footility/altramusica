<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        $exams = $query->with('student')
            ->orderBy('exam_date', 'desc')
            ->paginate(20);

        $students = Student::orderBy('last_name')->orderBy('first_name')->get();

        return view('admin.exams.index', compact('exams', 'students'));
    }

    public function create(Request $request)
    {
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $preselectedStudent = $request->get('student_id') ? Student::find($request->get('student_id')) : null;

        return view('admin.exams.create', compact('students', 'preselectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_type' => 'required|in:abrsm,lcm,internal,other',
            'level' => 'required|integer|min:0|max:8',
            'subject' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'registration_date' => 'nullable|date',
            'registration_fee' => 'nullable|numeric|min:0',
            'result' => 'nullable|in:passed,failed,pending',
            'grade' => 'nullable|string|max:10',
            'certificate_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Exam::create($validated);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Esame creato con successo.');
    }

    public function show(Exam $exam)
    {
        $exam->load('student');
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $exam->load('student');
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        return view('admin.exams.edit', compact('exam', 'students'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_type' => 'required|in:abrsm,lcm,internal,other',
            'level' => 'required|integer|min:0|max:8',
            'subject' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'registration_date' => 'nullable|date',
            'registration_fee' => 'nullable|numeric|min:0',
            'result' => 'nullable|in:passed,failed,pending',
            'grade' => 'nullable|string|max:10',
            'certificate_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $exam->update($validated);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Esame aggiornato con successo.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Esame eliminato con successo.');
    }
}
