<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FamilyRequest;
use App\Models\FamilyRequestMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * R13 (#8539) — Canale richieste famiglia → gestionale (lato segreteria).
 *
 * Inbox delle richieste aperte dalle famiglie: la segreteria legge, risponde
 * e governa gli stati. Non scrive sulle entità del gestionale, è solo il
 * canale messaggi.
 */
class FamilyRequestController extends Controller
{
    /** Inbox con filtro per stato. */
    public function index(Request $request)
    {
        $status = $request->input('status');

        $requests = FamilyRequest::query()
            ->with(['guardian', 'student', 'assignedTo'])
            ->when(
                array_key_exists($status, FamilyRequest::STATUSES),
                fn ($q) => $q->where('status', $status)
            )
            ->orderByRaw("CASE status WHEN 'nuova' THEN 0 WHEN 'in_lavorazione' THEN 1 WHEN 'in_attesa_famiglia' THEN 2 ELSE 3 END")
            ->orderByRaw('COALESCE(last_message_at, created_at) DESC')
            ->paginate(20)
            ->withQueryString();

        return view('admin.family-requests.index', [
            'requests' => $requests,
            'statuses' => FamilyRequest::STATUSES,
            'currentStatus' => array_key_exists($status, FamilyRequest::STATUSES) ? $status : null,
            'counts' => FamilyRequest::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    /** Dettaglio + thread. */
    public function show(FamilyRequest $familyRequest)
    {
        $familyRequest->load(['guardian', 'student', 'assignedTo', 'messages.author']);

        return view('admin.family-requests.show', [
            'request' => $familyRequest,
            'statuses' => FamilyRequest::STATUSES,
        ]);
    }

    /** Risposta della segreteria nel thread. */
    public function reply(Request $request, FamilyRequest $familyRequest)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
            // Stato opzionale impostato contestualmente alla risposta.
            'status' => ['nullable', 'string', 'in:' . implode(',', array_keys(FamilyRequest::STATUSES))],
        ]);

        DB::transaction(function () use ($familyRequest, $data) {
            $familyRequest->messages()->create([
                'user_id' => auth()->id(),
                'author_role' => FamilyRequestMessage::ROLE_STAFF,
                'body' => $data['body'],
            ]);

            $newStatus = $data['status']
                ?? ($familyRequest->status === FamilyRequest::STATUS_NEW
                    ? FamilyRequest::STATUS_IN_PROGRESS
                    : $familyRequest->status);

            $familyRequest->forceFill([
                'last_message_at' => now(),
                'last_message_role' => FamilyRequestMessage::ROLE_STAFF,
                'status' => $newStatus,
                // Prima risposta senza un assegnatario → la prende chi risponde.
                'assigned_to_user_id' => $familyRequest->assigned_to_user_id ?? auth()->id(),
                'resolved_at' => $newStatus === FamilyRequest::STATUS_RESOLVED ? now() : $familyRequest->resolved_at,
            ])->save();
        });

        return redirect()
            ->route('admin.family-requests.show', $familyRequest)
            ->with('success', 'Risposta inviata alla famiglia.');
    }

    /** Cambio stato senza scrivere un messaggio (presa in carico, chiusura, ecc.). */
    public function updateStatus(Request $request, FamilyRequest $familyRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(FamilyRequest::STATUSES))],
        ]);

        $familyRequest->forceFill([
            'status' => $data['status'],
            'assigned_to_user_id' => $familyRequest->assigned_to_user_id ?? auth()->id(),
            'resolved_at' => $data['status'] === FamilyRequest::STATUS_RESOLVED ? now() : $familyRequest->resolved_at,
        ])->save();

        return redirect()
            ->route('admin.family-requests.show', $familyRequest)
            ->with('success', 'Stato aggiornato: ' . $familyRequest->statusLabel() . '.');
    }
}
