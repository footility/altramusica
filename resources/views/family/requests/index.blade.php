@extends('family.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-1">Le tue richieste</h1>
        <p class="text-muted mb-0">Scrivi alla segreteria e segui lo stato delle tue richieste.</p>
    </div>
    <a href="{{ route('family.requests.create') }}" class="btn btn-primary">Nuova richiesta</a>
</div>

@forelse($requests as $req)
    <a href="{{ route('family.requests.show', $req) }}" class="text-decoration-none text-reset">
        <div class="card mb-2">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge {{ $req->statusBadgeClass() }}">{{ $req->statusLabel() }}</span>
                        <span class="ms-1 fw-semibold">{{ $req->subject }}</span>
                        <div class="small text-muted mt-1">
                            {{ $req->categoryLabel() }}
                            @if($req->student) · {{ $req->student->full_name }} @endif
                        </div>
                    </div>
                    <div class="small text-muted text-nowrap ms-3">
                        {{ optional($req->last_message_at ?? $req->created_at)->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </a>
@empty
    <div class="alert alert-light border">Non hai ancora inviato richieste. Usa il pulsante <strong>Nuova richiesta</strong> per scrivere alla segreteria.</div>
@endforelse

{{ $requests->links() }}
@endsection
