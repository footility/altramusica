<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Classroom::query();
        
        if ($request->has('available')) {
            $query->where('available', $request->available == '1');
        }
        
        $classrooms = $query->orderBy('name')
            ->paginate(20);
        
        return view('admin.classrooms.index', compact('classrooms'));
    }
    
    public function create()
    {
        return view('admin.classrooms.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:classrooms,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'equipment' => 'nullable|array',
            'available' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        Classroom::create($validated);
        
        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Aula creata con successo.');
    }
    
    public function show(Classroom $classroom)
    {
        return view('admin.classrooms.show', compact('classroom'));
    }
    
    public function edit(Classroom $classroom)
    {
        return view('admin.classrooms.edit', compact('classroom'));
    }
    
    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:classrooms,code,' . $classroom->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'equipment' => 'nullable|array',
            'available' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        $classroom->update($validated);
        
        return redirect()->route('admin.classrooms.show', $classroom)
            ->with('success', 'Aula aggiornata con successo.');
    }
    
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        
        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Aula eliminata con successo.');
    }
}
