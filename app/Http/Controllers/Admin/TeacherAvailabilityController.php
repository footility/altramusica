<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherAvailability;
use Illuminate\Http\Request;

class TeacherAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherAvailability::with('teacher');
        
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        
        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }
        
        $availabilities = $query->orderBy('day_of_week')
            ->orderBy('time_start')
            ->paginate(50);
        
        $teachers = Teacher::active()->orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.teacher-availability.index', compact('availabilities', 'teachers'));
    }
    
    public function create(Request $request)
    {
        $teacher = null;
        if ($request->has('teacher_id')) {
            $teacher = Teacher::findOrFail($request->teacher_id);
        }
        
        $teachers = Teacher::active()->orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.teacher-availability.create', compact('teacher', 'teachers'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'available' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        TeacherAvailability::create($validated);
        
        return redirect()->route('admin.teacher-availability.index')
            ->with('success', 'Disponibilità creata con successo.');
    }
    
    public function edit(TeacherAvailability $teacherAvailability)
    {
        $teachers = Teacher::active()->orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.teacher-availability.edit', compact('teacherAvailability', 'teachers'));
    }
    
    public function update(Request $request, TeacherAvailability $teacherAvailability)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'available' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        $teacherAvailability->update($validated);
        
        return redirect()->route('admin.teacher-availability.index')
            ->with('success', 'Disponibilità aggiornata con successo.');
    }
    
    public function destroy(TeacherAvailability $teacherAvailability)
    {
        $teacherAvailability->delete();
        
        return redirect()->route('admin.teacher-availability.index')
            ->with('success', 'Disponibilità eliminata con successo.');
    }
}
