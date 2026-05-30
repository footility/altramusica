<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Family\Concerns\ScopesToGuardian;
use App\Models\Communication;
use Illuminate\Database\Eloquent\Builder;

/**
 * R13 — Comunicazioni ricevute, consultabili dall'area famiglie.
 *
 * Sola lettura: il tutore vede le comunicazioni effettivamente inviate
 * (status sent/delivered) rivolte a sé o ai propri figli. Scoping server-side
 * tramite {@see ScopesToGuardian}: mai un find() non filtrato.
 */
class CommunicationsController extends Controller
{
    use ScopesToGuardian;

    /** Comunicazioni considerate consegnabili alla famiglia. */
    private const VISIBLE_STATUSES = ['sent', 'delivered'];

    /** Elenco paginato delle comunicazioni rivolte alla famiglia. */
    public function index()
    {
        $communications = $this->familyScope()
            ->with('student')
            ->orderByDesc('sent_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('family.communications.index', [
            'guardian' => $this->guardian(),
            'communications' => $communications,
        ]);
    }

    /** Dettaglio di una singola comunicazione, se nel perimetro del tutore. */
    public function show(string $communication)
    {
        $model = $this->familyScope()->with('student')->find($communication);

        abort_if($model === null, 404, 'Comunicazione non disponibile.');

        return view('family.communications.show', [
            'guardian' => $this->guardian(),
            'communication' => $model,
        ]);
    }

    /**
     * Base query delle comunicazioni visibili al tutore: inviate (non in errore,
     * con sent_at valorizzato) e rivolte a un proprio figlio o al tutore stesso.
     */
    private function familyScope(): Builder
    {
        $childIds = $this->childIds();
        $guardianId = $this->guardian()->id;

        return Communication::query()
            ->whereIn('status', self::VISIBLE_STATUSES)
            ->whereNotNull('sent_at')
            ->where(function (Builder $q) use ($childIds, $guardianId) {
                $q->where('guardian_id', $guardianId);

                if (! empty($childIds)) {
                    $q->orWhereIn('student_id', $childIds);
                }
            });
    }
}
