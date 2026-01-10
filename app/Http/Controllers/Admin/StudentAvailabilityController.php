<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAvailability;
use Illuminate\Http\Request;

class StudentAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentAvailability::with('student');
        
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }
        
        $availabilities = $query->orderBy('day_of_week')
            ->orderBy('time_start')
            ->paginate(50);
        
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.student-availability.index', compact('availabilities', 'students'));
    }
    
    public function create(Request $request)
    {
        $student = null;
        if ($request->has('student_id')) {
            $student = Student::findOrFail($request->student_id);
        }
        
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.student-availability.create', compact('student', 'students'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'available' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        StudentAvailability::create($validated);
        
        return redirect()->route('admin.student-availability.index')
            ->with('success', 'Disponibilità creata con successo.');
    }
    
    public function edit(StudentAvailability $studentAvailability)
    {
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.student-availability.edit', compact('studentAvailability', 'students'));
    }
    
    public function update(Request $request, StudentAvailability $studentAvailability)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'available' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        $studentAvailability->update($validated);
        
        return redirect()->route('admin.student-availability.index')
            ->with('success', 'Disponibilità aggiornata con successo.');
    }
    
    public function destroy(StudentAvailability $studentAvailability)
    {
        $studentAvailability->delete();
        
        return redirect()->route('admin.student-availability.index')
            ->with('success', 'Disponibilità eliminata con successo.');
    }
}
