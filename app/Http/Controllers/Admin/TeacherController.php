<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('tax_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('active')) {
            $query->where('active', $request->active);
        }

        $teachers = $query->with('user')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tax_code' => 'nullable|string|max:16',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'address' => 'nullable|string|max:255',
            'contract_type' => 'nullable|in:partita_iva,cooperativa,ritenuta',
            'hire_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'active' => 'boolean',
            'create_user_account' => 'boolean',
        ]);

        // Crea account utente se richiesto
        $user = null;
        if ($validated['create_user_account'] ?? false) {
            $user = User::create([
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'teacher',
            ]);
        }

        $teacher = Teacher::create([
            'user_id' => $user?->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'tax_code' => $validated['tax_code'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
            'address' => $validated['address'] ?? null,
            'contract_type' => $validated['contract_type'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'active' => $validated['active'] ?? true,
        ]);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Docente creato con successo.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'courses', 'extraActivities']);
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tax_code' => 'nullable|string|max:16',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:teachers,email,' . $teacher->id,
            'address' => 'nullable|string|max:255',
            'contract_type' => 'nullable|in:partita_iva,cooperativa,ritenuta',
            'hire_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $teacher->update($validated);

        // Aggiorna anche l'utente se esiste
        if ($teacher->user) {
            $teacher->user->update([
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
            ]);
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Docente aggiornato con successo.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->user) {
            $teacher->user->delete();
        }
        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Docente eliminato con successo.');
    }
}
