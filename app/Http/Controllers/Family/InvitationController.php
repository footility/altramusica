<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Models\GuardianInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

/**
 * R13 — Attivazione su invito: il tutore imposta la password e accetta
 * l'informativa privacy. Crea (o aggiorna) l'account `family` collegato al tutore.
 */
class InvitationController extends Controller
{
    public function show(string $token)
    {
        $invitation = $this->validInvitation($token);

        return view('family.auth.activate', [
            'invitation' => $invitation,
            'privacyVersion' => $this->currentPrivacyVersion(),
        ]);
    }

    public function activate(Request $request, string $token)
    {
        $invitation = $this->validInvitation($token);

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
            'privacy_accept' => ['accepted'],
        ], [
            'privacy_accept.accepted' => "Devi accettare l'informativa privacy per attivare l'accesso.",
        ]);

        $user = DB::transaction(function () use ($invitation, $request) {
            // Riusa l'account famiglia esistente per il tutore, altrimenti lo crea.
            $user = User::firstOrNew(['guardian_id' => $invitation->guardian_id]);
            $user->fill([
                'name' => $invitation->guardian->full_name,
                'email' => $invitation->email,
                'password' => $request->string('password'),
            ])->save();

            $familyRole = Role::findOrCreate('family', 'web');
            if (! $user->hasRole($familyRole)) {
                $user->assignRole($familyRole);
            }

            $invitation->forceFill(['accepted_at' => now()])->save();

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('family.dashboard')
            ->with('status', 'Accesso attivato. Benvenuto nell\'area famiglie.');
    }

    /** Token valido (esiste, non scaduto, non già usato) oppure 404. */
    protected function validInvitation(string $token): GuardianInvitation
    {
        $invitation = GuardianInvitation::with('guardian')->where('token', $token)->first();

        abort_if($invitation === null || ! $invitation->isPending(), 404, 'Invito non valido o scaduto.');

        return $invitation;
    }

    protected function currentPrivacyVersion(): ?string
    {
        return \App\Models\Setting::get('privacy_policy_version');
    }
}
