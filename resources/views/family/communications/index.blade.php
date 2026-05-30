@extends('family.layout')

@section('content')
<h1 class="h3 mb-1">Comunicazioni</h1>
<p class="text-muted">Le comunicazioni inviate dalla scuola alla tua famiglia.</p>

<div class="card">
    <div class="card-body">
        @forelse($communications as $com)
            <div class="border-bottom py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <a class="fw-semibold text-decoration-none" href="{{ route('family.communications.show', $com) }}">
                        {{ $com->subject ?: 'Comunicazione' }}
                    </a>
                    <small class="text-muted text-nowrap ms-3">{{ optional($com->sent_at)->format('d/m/Y') }}</small>
                </div>
                <div class="small text-muted mt-1">
                    <span class="badge bg-light text-dark">{{ ucfirst($com->type) }}</span>
                    @if($com->student) · {{ $com->student->full_name }} @endif
                </div>
                @if($com->message)
                    <p class="mb-0 text-muted mt-1">{{ Str::limit(strip_tags($com->message), 160) }}</p>
                @endif
            </div>
        @empty
            <p class="text-muted mb-0">Nessuna comunicazione ricevuta.</p>
        @endforelse
    </div>
</div>

<div class="mt-3">
    {{ $communications->links() }}
</div>
@endsection
