<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * R13 — Login/logout dell'area famiglie (guard web, ruolo `family`).
 * Niente self-registration: l'account nasce solo da un invito attivato.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('family.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Rate-limit base riusando il throttling del framework.
        $key = 'family-login:'.$request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Troppi tentativi. Riprova tra qualche minuto.',
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'Credenziali non valide.',
            ]);
        }

        // Solo gli account famiglia entrano da qui: un admin/teacher viene rifiutato.
        if (! Auth::user()->isFamily()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Questo accesso è riservato alle famiglie.',
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('family.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('family.login');
    }
}
