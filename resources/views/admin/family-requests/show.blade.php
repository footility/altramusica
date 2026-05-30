@extends('layouts.admin')

@section('title', 'Richiesta famiglia')
@section('page-title', 'Richiesta famiglia')

@section('content')
<a href="{{ route('admin.family-requests.index') }}" class="btn btn-link px-0 mb-2">&larr; Tutte le richieste</a>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2 class="h4 mb-1">{{ $request->subject }}</h2>
                        <div class="text-muted small">
                            {{ $request->categoryLabel() }}
                            @if($request->student) · {{ $request->student->full_name }} @endif
                        </div>
                    </div>
                    <span class="badge {{ $request->statusBadgeClass() }} fs-6">{{ $request->statusLabel() }}</span>
                </div>
            </div>
        </div>

        <div class="mb-4">
            @foreach($request->messages as $msg)
                <div class="d-flex mb-2 {{ $msg->isFamily() ? 'justify-content-start' : 'justify-content-end' }}">
                    <div class="card {{ $msg->isFamily() ? '' : 'bg-primary text-white' }}" style="max-width: 85%;">
                        <div class="card-body py-2 px-3">
                            <div class="small {{ $msg->isFamily() ? 'text-muted' : 'text-white-50' }} mb-1">
                                {{ $msg->isFamily() ? ($request->guardian?->full_name ?? 'Famiglia') : ('Segreteria · ' . ($msg->author?->name ?? '')) }}
                                · {{ $msg->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div style="white-space: pre-wrap;">{{ $msg->body }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="h6">Rispondi alla famiglia</h3>
                <form method="POST" action="{{ route('admin.family-requests.reply', $request) }}">
                    @csrf
                    <textarea name="body" rows="4" maxlength="4000" class="form-control mb-2" required>{{ old('body') }}</textarea>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0" for="status">Imposta stato:</label>
                        <select name="status" id="status" class="form-select form-select-sm" style="max-width: 240px;">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($request->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm ms-auto">Invia risposta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h3 class="h6">Dettagli</h3>
                <dl class="row small mb-3">
                    <dt class="col-5 text-muted">Famiglia</dt>
                    <dd class="col-7">{{ $request->guardian?->full_name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">{{ $request->guardian?->primary_email ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Studente</dt>
                    <dd class="col-7">{{ $request->student?->full_name ?? 'Generale' }}</dd>
                    <dt class="col-5 text-muted">Aperta il</dt>
                    <dd class="col-7">{{ $request->created_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-5 text-muted">In carico a</dt>
                    <dd class="col-7">{{ $request->assignedTo?->name ?? '—' }}</dd>
                </dl>

                <h3 class="h6">Cambia stato</h3>
                <form method="POST" action="{{ route('admin.family-requests.status', $request) }}">
                    @csrf
                    @method('PATCH')
                    <div class="input-group input-group-sm">
                        <select name="status" class="form-select">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($request->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-secondary">Aggiorna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
