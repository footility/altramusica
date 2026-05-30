<?php

namespace App\Http\Controllers\Family\Concerns;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

/**
 * R13 — Scoping server-side dell'area famiglie.
 *
 * Tutto parte dal tutore autenticato: mai un find() non filtrato. Una richiesta
 * fuori perimetro (figlio non proprio, scheda ritirata/anonimizzata) → 404.
 */
trait ScopesToGuardian
{
    protected function guardian(): Guardian
    {
        $guardian = auth()->user()->guardian;

        abort_if($guardian === null, 403);

        return $guardian;
    }

    /**
     * Figli accessibili: legati al tutore, non anonimizzati e non ritirati
     * (la scheda ritirata sospende l'accesso famiglia, coerente con la retention).
     *
     * @return Collection<int, Student>
     */
    protected function children(): Collection
    {
        return $this->guardian()->students()
            ->whereNull('anonymized_at')
            ->with('currentYear')
            ->get()
            ->reject(fn (Student $s) => $s->currentYear?->withdrawn_at !== null)
            ->values();
    }

    /** Un singolo figlio dentro il perimetro, altrimenti 404. */
    protected function childOrFail($studentId): Student
    {
        $child = $this->children()->firstWhere('id', (int) $studentId);

        abort_if($child === null, 404);

        return $child;
    }

    /** @return array<int> id dei figli accessibili */
    protected function childIds(): array
    {
        return $this->children()->pluck('id')->all();
    }
}
