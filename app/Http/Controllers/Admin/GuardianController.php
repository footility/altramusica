<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    public function index(Request $request)
    {
        $query = Guardian::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('tax_code', 'like', "%{$search}%")
                  ->orWhere('email_1', 'like', "%{$search}%");
            });
        }

        $guardians = $query->withCount('students')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return view('admin.guardians.index', compact('guardians'));
    }

    public function create()
    {
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        return view('admin.guardians.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tax_code' => 'nullable|string|max:16',
            'relationship' => 'nullable|in:madre,padre,tutore,other',
            'phone_home' => 'nullable|string|max:20',
            'phone_work' => 'nullable|string|max:20',
            'cell_1' => 'nullable|string|max:20',
            'cell_2' => 'nullable|string|max:20',
            'cell_3' => 'nullable|string|max:20',
            'cell_4' => 'nullable|string|max:20',
            'email_1' => 'nullable|email|max:255',
            'email_2' => 'nullable|email|max:255',
            'email_3' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'privacy_consent' => 'boolean',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $studentIds = $validated['student_ids'] ?? [];
        unset($validated['student_ids']);

        $guardian = Guardian::create($validated);

        if (!empty($studentIds)) {
            foreach ($studentIds as $index => $studentId) {
                $guardian->students()->attach($studentId, [
                    'relationship_type' => $validated['relationship'] ?? 'other',
                    'is_primary' => $index === 0,
                    'is_billing_contact' => $index === 0,
                ]);
            }
        }

        return redirect()->route('admin.guardians.index')
            ->with('success', 'Genitore/Tutore creato con successo.');
    }

    public function show(Guardian $guardian)
    {
        $guardian->load('students');
        return view('admin.guardians.show', compact('guardian'));
    }

    public function edit(Guardian $guardian)
    {
        $guardian->load('students');
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        return view('admin.guardians.edit', compact('guardian', 'students'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tax_code' => 'nullable|string|max:16',
            'relationship' => 'nullable|in:madre,padre,tutore,other',
            'phone_home' => 'nullable|string|max:20',
            'phone_work' => 'nullable|string|max:20',
            'cell_1' => 'nullable|string|max:20',
            'cell_2' => 'nullable|string|max:20',
            'cell_3' => 'nullable|string|max:20',
            'cell_4' => 'nullable|string|max:20',
            'email_1' => 'nullable|email|max:255',
            'email_2' => 'nullable|email|max:255',
            'email_3' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'privacy_consent' => 'boolean',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $studentIds = $validated['student_ids'] ?? [];
        unset($validated['student_ids']);

        $guardian->update($validated);

        if (isset($studentIds)) {
            $guardian->students()->sync($studentIds);
        }

        return redirect()->route('admin.guardians.index')
            ->with('success', 'Genitore/Tutore aggiornato con successo.');
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->students()->detach();
        $guardian->delete();

        return redirect()->route('admin.guardians.index')
            ->with('success', 'Genitore/Tutore eliminato con successo.');
    }
}
