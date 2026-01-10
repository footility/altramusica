<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtraActivity;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ExtraActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = ExtraActivity::with('teacher');
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }
        
        $activities = $query->orderBy('name')
            ->paginate(20);
        
        return view('admin.extra-activities.index', compact('activities'));
    }
    
    public function create()
    {
        $teachers = Teacher::active()->orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.extra-activities.create', compact('teachers'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:extra_activities,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:orchestra,choir,other',
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);
        
        ExtraActivity::create($validated);
        
        return redirect()->route('admin.extra-activities.index')
            ->with('success', 'Attività extra creata con successo.');
    }
    
    public function show(ExtraActivity $extraActivity)
    {
        $extraActivity->load(['teacher', 'enrollments.student']);
        
        return view('admin.extra-activities.show', compact('extraActivity'));
    }
    
    public function edit(ExtraActivity $extraActivity)
    {
        $teachers = Teacher::active()->orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.extra-activities.edit', compact('extraActivity', 'teachers'));
    }
    
    public function update(Request $request, ExtraActivity $extraActivity)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:extra_activities,code,' . $extraActivity->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:orchestra,choir,other',
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after:time_start',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);
        
        $extraActivity->update($validated);
        
        return redirect()->route('admin.extra-activities.show', $extraActivity)
            ->with('success', 'Attività extra aggiornata con successo.');
    }
    
    public function destroy(ExtraActivity $extraActivity)
    {
        $extraActivity->delete();
        
        return redirect()->route('admin.extra-activities.index')
            ->with('success', 'Attività extra eliminata con successo.');
    }
}
