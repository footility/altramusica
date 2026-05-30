@extends('family.layout')

@section('content')
<h1 class="h3 mb-1">Ciao {{ $guardian->first_name }}</h1>
<p class="text-muted">Area riservata alle famiglie · sola consultazione</p>

<div class="mb-4">
    <h2 class="h6 text-uppercase text-muted">I tuoi ragazzi</h2>
    @forelse($children as $child)
        <a href="{{ route('family.student', $child) }}" class="btn btn-outline-primary mb-2 me-2">
            {{ $child->full_name }}
            @if($child->currentYear?->code) <span class="badge bg-light text-dark">{{ $child->currentYear->code }}</span> @endif
        </a>
    @empty
        <p class="text-muted">Nessuna scheda attiva al momento. Per informazioni contatta la segreteria.</p>
    @endforelse
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Prossime lezioni</div>
            <ul class="list-group list-group-flush">
                @forelse($lessons as $lesson)
                    <li class="list-group-item">
                        <div class="fw-semibold">{{ $lesson->date?->format('d/m') }} · {{ \Illuminate\Support\Str::of($lesson->time_start)->substr(0,5) }}</div>
                        <div class="small text-muted">
                            {{ $lesson->course?->name ?? 'Lezione' }}
                            @if($lesson->classroom) · {{ $lesson->classroom->name }} @endif
                            @if($lesson->teacher) · M° {{ $lesson->teacher->full_name }} @endif
                        </div>
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
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Rata {{ $plan->installment_number }} · € {{ number_format($plan->amount, 2, ',', '.') }}</div>
                            <div class="small text-muted">
                                {{ $plan->invoice?->student?->full_name }} · entro {{ $plan->due_date?->format('d/m/Y') }}
                            </div>
                        </div>
                        @php $late = $plan->due_date && $plan->due_date->isPast(); @endphp
                        <span class="badge {{ $late ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ $late ? 'In ritardo' : 'Da pagare' }}
                        </span>
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
                        <div>
                            {{ $doc->file_name }}
                            <span class="small text-muted">· {{ $doc->student?->full_name }}</span>
                        </div>
                        <a href="{{ route('family.document.download', $doc) }}" class="btn btn-sm btn-outline-secondary">Scarica</a>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nessun documento condiviso.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsectiontion
