@extends('family.layout')

@section('content')
<a href="{{ route('family.dashboard') }}" class="btn btn-link px-0 mb-2">&larr; Torna alla home</a>
<h1 class="h3">{{ $child->full_name }}</h1>
<p class="text-muted">
    @if($child->birth_date) Nato/a il {{ $child->birth_date->format('d/m/Y') }} · @endif
    Anno corrente: {{ $child->currentYear?->academicYear?->name ?? '—' }}
</p>

<div class="card mb-3">
    <div class="card-header">Iscrizioni</div>
    <ul class="list-group list-group-flush">
        @forelse($child->enrollments as $enr)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $enr->course?->name ?? 'Corso' }} · {{ $enr->academicYear?->name }}</span>
                <span class="badge bg-light text-dark">{{ ucfirst($enr->status) }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted">Nessuna iscrizione registrata.</li>
        @endforelse
    </ul>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Prossime lezioni</div>
            <ul class="list-group list-group-flush">
                @forelse($lessons as $lesson)
                    <li class="list-group-item">
                        <span class="fw-semibold">{{ $lesson->date?->format('d/m') }} · {{ \Illuminate\Support\Str::of($lesson->time_start)->substr(0,5) }}</span>
                        <span class="small text-muted">
                            @if($lesson->classroom) · {{ $lesson->classroom->name }} @endif
                            @if($lesson->teacher) · M° {{ $lesson->teacher->full_name }} @endif
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nessuna lezione in programma.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Scadenze</div>
            <ul class="list-group list-group-flush">
                @forelse($deadlines as $plan)
                    @php $late = $plan->due_date && $plan->due_date->isPast(); @endphp
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Rata {{ $plan->installment_number }} · € {{ number_format($plan->amount, 2, ',', '.') }} · entro {{ $plan->due_date?->format('d/m/Y') }}</span>
                        <span class="badge {{ $late ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $late ? 'In ritardo' : 'Da pagare' }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nessuna scadenza in sospeso.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">Documenti</div>
            <ul class="list-group list-group-flush">
                @forelse($documents as $doc)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $doc->file_name }}</span>
                        <a href="{{ route('family.document.download', $doc) }}" class="btn btn-sm btn-outline-secondary">Scarica</a>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nessun documento condiviso.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
