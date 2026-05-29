<?php

namespace App\Listeners;

use App\Models\LoginLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogAuthenticationActivity
{
    /** Login riuscito: registra e aggiorna ultimo accesso utente. */
    public function handleLogin(Login $event): void
    {
        $this->record('login', $event->user->getAuthIdentifier(), $event->user->email ?? null);

        $event->user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => Request::ip(),
        ])->saveQuietly();
    }

    public function handleLogout(Logout $event): void
    {
        $userId = $event->user?->getAuthIdentifier();
        $email = $event->user?->email;
        $this->record('logout', $userId, $email);
    }

    /** Tentativo di login fallito (credenziali errate). */
    public function handleFailed(Failed $event): void
    {
        $this->record('failed', null, $event->credentials['email'] ?? null);
    }

    protected function record(string $eventName, $userId, ?string $email): void
    {
        LoginLog::create([
            'user_id' => $userId,
            'email' => $email,
            'event' => $eventName,
            'ip_address' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 1000),
            'created_at' => now(),
        ]);
    }
}
