@extends('layouts.admin')

@section('title', 'Richieste famiglie')
@section('page-title', 'Richieste famiglie')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Richieste dalle famiglie</h2>
</div>

<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="{{ route('admin.family-requests.index') }}"
       class="btn btn-sm {{ $currentStatus === null ? 'btn-dark' : 'btn-outline-dark' }}">
        Tutte
    </a>
    @foreach($statuses as $value => $label)
        <a href="{{ route('admin.family-requests.index', ['status' => $value]) }}"
           class="btn btn-sm {{ $currentStatus === $value ? 'btn-dark' : 'btn-outline-dark' }}">
            {{ $label }}
            @if(($counts[$value] ?? 0) > 0)
                <span class="badge bg-light text-dark ms-1">{{ $counts[$value] }}</span>
            @endif
        </a>
    @endforeach
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Stato</th>
                    <th>Oggetto</th>
                    <th>Famiglia</th>
                    <th>Studente</th>
                    <th>Argomento</th>
                    <th>Ultimo msg</th>
                    <th>Presa in carico</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr onclick="window.location='{{ route('admin.family-requests.show', $req) }}'" style="cursor:pointer;">
                        <td><span class="badge {{ $req->statusBadgeClass() }}">{{ $req->statusLabel() }}</span></td>
                        <td class="fw-semibold">
                            {{ $req->subject }}
                            @if($req->last_message_role === 'family')
                                <span class="badge bg-primary-subtle text-primary ms-1">da rispondere</span>
                            @endif
                        </td>
                        <td>{{ $req->guardian?->full_name ?? '—' }}</td>
                        <td>{{ $req->student?->full_name ?? '—' }}</td>
                        <td class="small text-muted">{{ $req->categoryLabel() }}</td>
                        <td class="small text-muted text-nowrap">{{ optional($req->last_message_at ?? $req->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="small text-muted">{{ $req->assignedTo?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Nessuna richiesta.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $requests->links() }}</div>
@endsection
