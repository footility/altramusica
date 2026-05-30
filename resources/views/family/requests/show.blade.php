@extends('family.layout')

@section('content')
<a href="{{ route('family.requests.index') }}" class="btn btn-link px-0 mb-2">&larr; Le tue richieste</a>

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h1 class="h3 mb-1">{{ $request->subject }}</h1>
        <div class="text-muted small">
            {{ $request->categoryLabel() }}
            @if($request->student) · {{ $request->student->full_name }} @endif
        </div>
    </div>
    <span class="badge {{ $request->statusBadgeClass() }}">{{ $request->statusLabel() }}</span>
</div>

<div class="mb-4">
    @foreach($request->messages as $msg)
        <div class="d-flex mb-2 {{ $msg->isFamily() ? 'justify-content-end' : 'justify-content-start' }}">
            <div class="card {{ $msg->isFamily() ? 'bg-primary text-white' : '' }}" style="max-width: 80%;">
                <div class="card-body py-2 px-3">
                    <div class="small {{ $msg->isFamily() ? 'text-white-50' : 'text-muted' }} mb-1">
                        {{ $msg->isFamily() ? 'Tu' : 'Segreteria' }} · {{ $msg->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div style="white-space: pre-wrap;">{{ $msg->body }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($request->isOpenForFamily())
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('family.requests.reply', $request) }}">
                @csrf
                <label class="form-label" for="body">Rispondi</label>
                <textarea name="body" id="body" rows="3" maxlength="4000"
                          class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <button type="submit" class="btn btn-primary mt-2">Invia</button>
            </form>
        </div>
    </div>
@else
    <div class="alert alert-secondary">Questa richiesta è stata chiusa dalla segreteria. Per nuove domande apri una nuova richiesta.</div>
@endif
@endsection
