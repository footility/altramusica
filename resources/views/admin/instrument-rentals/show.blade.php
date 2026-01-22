@extends('layouts.admin')

@section('title', 'Dettaglio Noleggio')
@section('page-title', 'Dettaglio Noleggio Strumento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Noleggio</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.instrument-rentals.edit', $rental) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('admin.instrument-rentals.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Elenco
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Anno</dt>
            <dd class="col-sm-9">{{ $rental->academicYear->name ?? '-' }}</dd>

            <dt class="col-sm-3">Studente</dt>
            <dd class="col-sm-9">
                <a href="{{ route('admin.students.show', $rental->student) }}">
                    {{ $rental->student->full_name ?? '-' }}
                </a>
            </dd>

            <dt class="col-sm-3">Strumento</dt>
            <dd class="col-sm-9">
                <a href="{{ route('admin.instruments.show', $rental->instrument) }}">
                    {{ trim(($rental->instrument->type ?? '').' '.($rental->instrument->brand ?? '').' '.($rental->instrument->model ?? '').' '.$rental->instrument->serial_number) }}
                </a>
            </dd>

            <dt class="col-sm-3">Inizio</dt>
            <dd class="col-sm-9">{{ $rental->start_date?->format('d/m/Y') ?? '-' }}</dd>

            <dt class="col-sm-3">Fine</dt>
            <dd class="col-sm-9">{{ $rental->end_date?->format('d/m/Y') ?? '-' }}</dd>

            <dt class="col-sm-3">Mensile</dt>
            <dd class="col-sm-9">€ {{ number_format($rental->monthly_fee, 2, ',', '.') }}</dd>

            <dt class="col-sm-3">Cauzione</dt>
            <dd class="col-sm-9">€ {{ number_format($rental->deposit, 2, ',', '.') }}</dd>

            <dt class="col-sm-3">Stato</dt>
            <dd class="col-sm-9">{{ $rental->status }}</dd>

            <dt class="col-sm-3">Restituzione</dt>
            <dd class="col-sm-9">{{ $rental->return_date?->format('d/m/Y') ?? '-' }}</dd>

            <dt class="col-sm-3">Condizioni</dt>
            <dd class="col-sm-9">{{ $rental->return_condition ?? '-' }}</dd>

            <dt class="col-sm-3">Note</dt>
            <dd class="col-sm-9">{{ $rental->notes ?: '-' }}</dd>
        </dl>
    </div>
</div>
@endsection

