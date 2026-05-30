<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\GuardianInvitation;
use Illuminate\Http\Request;

/**
 * R13 — Lato segreteria: invito di un tutore all'area famiglie.
 * Genera un token monouso a scadenza. L'invio email transazionale (SMTP R9)
 * si innesta qui; per ora il link di attivazione è mostrato alla segreteria.
 */
class GuardianInvitationController extends Controller
{
    public function store(Request $request, Guardian $guardian)
    {
        // Si invita solo chi ha consenso privacy e un'email valida.
        $email = $request->input('email', $guardian->primary_email);

        if (! $guardian->privacy_consent || empty($email)) {
            return back()->with('error', 'Invito non possibile: serve consenso privacy ed email valida del tutore.');
        }

        $invitation = GuardianInvitation::generateFor($guardian, $email, auth()->id());

        $link = route('family.invitation.show', $invitation->token);

        // TODO (innesto R9): inviare l'email "Attiva il tuo accesso" via SMTP reale.
        return back()->with('status', "Invito generato per {$email}. Link di attivazione (scade tra ".GuardianInvitation::EXPIRES_DAYS." gg): {$link}");
    }
}
