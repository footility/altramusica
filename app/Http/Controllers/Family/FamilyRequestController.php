<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Family\Concerns\ScopesToGuardian;
use App\Models\FamilyRequest;
use App\Models\FamilyRequestMessage;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * R13 (#8539) — Canale richieste famiglia → gestionale (lato famiglia).
 *
 * La famiglia apre una richiesta e dialoga con la segreteria. Tutto è scopato
 * sul tutore autenticato (mai find() non filtrato) e l'eventuale studente di
 * riferimento deve essere un figlio dentro il perimetro.
 */
class FamilyRequestController extends Controller
{
    use ScopesToGuardian;

    /** Le mie richieste, dalla più recente. */
    public function index()
    {
        $requests = $this->guardian()->familyRequests()
            ->with('student')
            ->orderByRaw('COALESCE(last_message_at, created_at) DESC')
            ->paginate(15);

        return view('family.requests.index', [
            'requests' => $requests,
        ]);
    }

    /** Form nuova richiesta. */
    public function create()
    {
        return view('family.requests.create', [
            'children' => $this->children(),
            'categories' => FamilyRequest::CATEGORIES,
        ]);
    }

    /** Apertura richiesta + primo messaggio. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(FamilyRequest::CATEGORIES))],
            'student_id' => ['nullable', 'integer'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:4000'],
        ]);

        // Se indicato uno studente, deve essere un figlio dentro il perimetro.
        $studentId = null;
        if (! empty($data['student_id'])) {
            $studentId = $this->childOrFail($data['student_id'])->id;
        }

        $familyRequest = DB::transaction(function () use ($data, $studentId) {
            $familyRequest = $this->guardian()->familyRequests()->create([
                'student_id' => $studentId,
                'category' => $data['category'],
                'subject' => $data['subject'],
                'status' => FamilyRequest::STATUS_NEW,
                'last_message_at' => now(),
                'last_message_role' => FamilyRequestMessage::ROLE_FAMILY,
            ]);

            $familyRequest->messages()->create([
                'user_id' => auth()->id(),
                'author_role' => FamilyRequestMessage::ROLE_FAMILY,
                'body' => $data['body'],
            ]);

            return $familyRequest;
        });

        $this->log('family_request_created');

        return redirect()
            ->route('family.requests.show', $familyRequest)
            ->with('status', 'Richiesta inviata alla segreteria.');
    }

    /** Dettaglio + thread di una mia richiesta. */
    public function show(string $familyRequest)
    {
        $req = $this->findOwnOrFail($familyRequest);
        $req->load(['student', 'messages.author']);

        return view('family.requests.show', [
            'request' => $req,
        ]);
    }

    /** Risposta della famiglia nel thread (solo se non chiusa). */
    public function reply(Request $request, string $familyRequest)
    {
        $req = $this->findOwnOrFail($familyRequest);

        abort_unless($req->isOpenForFamily(), 403, 'Richiesta chiusa.');

        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        DB::transaction(function () use ($req, $data) {
            $req->messages()->create([
                'user_id' => auth()->id(),
                'author_role' => FamilyRequestMessage::ROLE_FAMILY,
                'body' => $data['body'],
            ]);

            $req->forceFill([
                'last_message_at' => now(),
                'last_message_role' => FamilyRequestMessage::ROLE_FAMILY,
                // Una risposta della famiglia rimette in coda alla segreteria
                // ciò che era risolto o in attesa di risposta.
                'status' => in_array($req->status, [FamilyRequest::STATUS_RESOLVED, FamilyRequest::STATUS_WAITING_FAMILY], true)
                    ? FamilyRequest::STATUS_IN_PROGRESS
                    : $req->status,
            ])->save();
        });

        $this->log('family_request_reply');

        return redirect()
            ->route('family.requests.show', $req)
            ->with('status', 'Messaggio inviato.');
    }

    /** Richiesta del tutore autenticato, altrimenti 404. */
    protected function findOwnOrFail(string $id): FamilyRequest
    {
        $req = $this->guardian()->familyRequests()->find((int) $id);

        abort_if($req === null, 404);

        return $req;
    }

    /** Tracciabilità (riuso di LoginLog come registro eventi area famiglie). */
    protected function log(string $event): void
    {
        LoginLog::create([
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 1000),
            'created_at' => now(),
        ]);
    }
}
