<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommunicationsController extends Controller
{
    /**
     * Tutte le comunicazioni rivolte alla famiglia dello studente di sessione.
     * Stesso filtro della dashboard, ma paginato e senza limite 10.
     */
    public function index(Request $request)
    {
        $student = $request->attributes->get('family_student');

        $communications = $this->familyScope($request)
            ->orderByDesc('published_at')
            ->paginate(20);

        return view('family.communications.index', compact('student', 'communications'));
    }

    /**
     * Dettaglio di una singola comunicazione.
     * Valida che sia rivolta alla famiglia dello studente di sessione.
     */
    public function show(Request $request, Communication $communication)
    {
        $student = $request->attributes->get('family_student');

        $visible = $communication->audience === 'families'
            && ($communication->student_id === null || $communication->student_id === $student->id);

        abort_unless($visible, 404);

        return view('family.communications.show', [
            'student' => $student,
            'communication' => $communication,
        ]);
    }

    /**
     * Comunicazioni rivolte alla famiglia: generali (student_id null) o del proprio studente.
     */
    private function familyScope(Request $request): Builder
    {
        $student = $request->attributes->get('family_student');

        return Communication::query()
            ->where('audience', 'families')
            ->where(function (Builder $q) use ($student) {
                $q->whereNull('student_id')
                    ->orWhere('student_id', $student->id);
            });
    }
}
