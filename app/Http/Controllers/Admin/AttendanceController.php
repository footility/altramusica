<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['lesson.course', 'student']);
        
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from')) {
            $query->whereHas('lesson', function($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }
        
        if ($request->has('date_to')) {
            $query->whereHas('lesson', function($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }
        
        $attendances = $query->orderBy('created_at', 'desc')
            ->paginate(50);
        
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.attendances.index', compact('attendances', 'students'));
    }
    
    public function show(Attendance $attendance)
    {
        $attendance->load(['lesson.course', 'student']);
        
        return view('admin.attendances.show', compact('attendance'));
    }
    
    public function edit(Attendance $attendance)
    {
        $attendance->load(['lesson', 'student']);
        
        return view('admin.attendances.edit', compact('attendance'));
    }
    
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string',
        ]);
        
        $attendance->update($validated);
        
        return redirect()->route('admin.attendances.show', $attendance)
            ->with('success', 'Presenza aggiornata con successo.');
    }
    
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        
        return redirect()->route('admin.attendances.index')
            ->with('success', 'Presenza eliminata con successo.');
    }
}
