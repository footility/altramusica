@extends('layouts.admin')

@section('title', 'Dettaglio Distribuzione Libro')
@section('page-title', 'Dettaglio Distribuzione Libro')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Distribuzione</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.book-distributions.edit', $distribution) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('admin.book-distributions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Elenco
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Anno</dt>
            <dd class="col-sm-9">{{ $distribution->academicYear->name ?? '-' }}</dd>

            <dt class="col-sm-3">Data</dt>
            <dd class="col-sm-9">{{ $distribution->distribution_date?->format('d/m/Y') ?? '-' }}</dd>

            <dt class="col-sm-3">Studente</dt>
            <dd class="col-sm-9">
                <a href="{{ route('admin.students.show', $distribution->student) }}">
                    {{ $distribution->student->full_name ?? '-' }}
                </a>
            </dd>

            <dt class="col-sm-3">Libro</dt>
            <dd class="col-sm-9">
                <a href="{{ route('admin.books.show', $distribution->book) }}">
                    {{ $distribution->book->title ?? '-' }}
                </a>
            </dd>

            <dt class="col-sm-3">Corso</dt>
            <dd class="col-sm-9">{{ $distribution->course->name ?? '-' }}</dd>

            <dt class="col-sm-3">Quantità</dt>
            <dd class="col-sm-9">{{ $distribution->quantity }}</dd>

            <dt class="col-sm-3">Prezzo pagato</dt>
            <dd class="col-sm-9">€ {{ number_format($distribution->price_paid, 2, ',', '.') }}</dd>
        </dl>
    </div>
</div>
@endsection

