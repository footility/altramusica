<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // L'admin ha sempre accesso completo (super-admin): bypassa ogni check permessi.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // Regole password "sicure" condivise da registrazione e reset.
        Password::defaults(fn () => Password::min(8)->letters()->numbers());

        // Il log accessi (App\Listeners\LogAuthenticationActivity) è registrato
        // via auto-discovery sugli eventi Login/Logout/Failed.
    }
}
