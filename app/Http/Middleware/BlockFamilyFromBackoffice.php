<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * R13 — Isolamento area famiglie/backoffice.
 *
 * Le rotte admin sono protette solo da `auth`: un account `family` autenticato
 * (stesso guard web) le supererebbe. Questo middleware rimanda gli utenti famiglia
 * alla loro dashboard invece di lasciarli entrare nel gestionale.
 */
class BlockFamilyFromBackoffice
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isFamily()) {
            return redirect()->route('family.dashboard');
        }

        return $next($request);
    }
}
