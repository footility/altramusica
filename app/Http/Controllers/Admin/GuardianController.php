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
            'relationship' => 'nullable|in:mother,father,guardian,other',
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

        $this->syncStudents($guardian, $studentIds);

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
            'relationship' => 'nullable|in:mother,father,guardian,other',
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

        $this->syncStudents($guardian, $studentIds);

        return redirect()->route('admin.guardians.index')
            ->with('success', 'Genitore/Tutore aggiornato con successo.');
    }

    /**
     * Sincronizza gli studenti associati preservando i campi del pivot
     * (relationship_type, is_primary, is_billing_contact).
     *
     * sync() con una semplice lista di ID non valorizza i pivot fields sulle
     * nuove associazioni: qui costruiamo un array associativo id => pivot in cui
     * le associazioni esistenti mantengono i loro valori e quelle nuove ricevono
     * default sensati (un solo primary / billing contact per genitore).
     */
    private function syncStudents(Guardian $guardian, array $studentIds): void
    {
        $existing = $guardian->students()->get()->keyBy('id');

        $defaultRelationship = $this->mapRelationshipType($guardian->relationship);
        $hasPrimary = $existing->contains(fn ($student) => (bool) $student->pivot->is_primary);

        $syncData = [];

        foreach (array_values($studentIds) as $studentId) {
            $studentId = (int) $studentId;

            if ($existing->has($studentId)) {
                // Preserva i pivot fields del rapporto già esistente.
                $pivot = $existing[$studentId]->pivot;
                $syncData[$studentId] = [
                    'relationship_type' => $pivot->relationship_type,
                    'is_primary' => (bool) $pivot->is_primary,
                    'is_billing_contact' => (bool) $pivot->is_billing_contact,
                ];

                continue;
            }

            // Nuova associazione: primo/unico studente senza primary diventa
            // contatto principale e di fatturazione.
            $makePrimary = ! $hasPrimary;

            $syncData[$studentId] = [
                'relationship_type' => $defaultRelationship,
                'is_primary' => $makePrimary,
                'is_billing_contact' => $makePrimary,
            ];

            if ($makePrimary) {
                $hasPrimary = true;
            }
        }

        $guardian->students()->sync($syncData);
    }

    /**
     * Normalizza la relazione sull'enum del pivot (mother/father/guardian/other).
     * Guardian.relationship e student_guardian.relationship_type condividono lo
     * stesso enum, quindi il valore viene riusato così com'è.
     */
    private function mapRelationshipType(?string $relationship): string
    {
        return in_array($relationship, ['mother', 'father', 'guardian'], true)
            ? $relationship
            : 'other';
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->students()->detach();
        $guardian->delete();

        return redirect()->route('admin.guardians.index')
            ->with('success', 'Genitore/Tutore eliminato con successo.');
    }
}
