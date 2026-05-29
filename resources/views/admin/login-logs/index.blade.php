@extends('layouts.admin')

@section('title', 'Log accessi')
@section('page-title', 'Log accessi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Log accessi</h2>
    <form method="GET" class="d-flex gap-2">
        <select name="event" class="form-select form-select-sm" style="min-width:140px" onchange="this.form.submit()">
            <option value="">Tutti gli eventi</option>
            <option value="login"  @selected(request('event')==='login')>Login</option>
            <option value="logout" @selected(request('event')==='logout')>Logout</option>
            <option value="failed" @selected(request('event')==='failed')>Falliti</option>
        </select>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Email…" style="min-width:200px">
        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Data/ora</th>
                    <th>Evento</th>
                    <th>Utente</th>
                    <th>Email</th>
                    <th>IP</th>
                    <th>Dispositivo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @php
                                $badge = ['login' => 'success', 'logout' => 'secondary', 'failed' => 'danger'][$log->event] ?? 'light';
                                $label = ['login' => 'Login', 'logout' => 'Logout', 'failed' => 'Fallito'][$log->event] ?? $log->event;
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                        </td>
                        <td>{{ $log->user?->name ?? '—' }}</td>
                        <td>{{ $log->email ?? '—' }}</td>
                        <td class="text-nowrap"><small>{{ $log->ip_address ?? '—' }}</small></td>
                        <td><small class="text-muted text-truncate d-inline-block" style="max-width:320px" title="{{ $log->user_agent }}">{{ $log->user_agent ?? '—' }}</small></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Nessun accesso registrato.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $logs->links() }}
</div>
@endsection
