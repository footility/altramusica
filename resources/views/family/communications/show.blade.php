@extends('family.layout')

@section('content')
<a href="{{ route('family.communications.index') }}" class="btn btn-link px-0 mb-2">&larr; Torna alle comunicazioni</a>

<div class="card" style="max-width: 720px;">
    <div class="card-body">
        <h1 class="h5 mb-1">{{ $communication->subject ?: 'Comunicazione' }}</h1>
        <p class="text-muted mb-2">
            <span class="badge bg-light text-dark">{{ ucfirst($communication->type) }}</span>
            · {{ optional($communication->sent_at)->format('d/m/Y H:i') }}
            @if($communication->student) · {{ $communication->student->full_name }} @endif
        </p>
        <div class="mt-3">{!! nl2br(e($communication->message)) !!}</div>
    </div>
</div>
@endsection
