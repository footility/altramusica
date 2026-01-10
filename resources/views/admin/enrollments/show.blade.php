@extends('layouts.admin')

@section('title', 'Dettaglio Iscrizione')
@section('page-title', 'Iscrizione')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Informazioni Iscrizione</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Studente:</dt>
                    <dd class="col-sm-9">{{ $enrollment->student->full_name }}</dd>

                    <dt class="col-sm-3">Corso:</dt>
                    <dd class="col-sm-9">{{ $enrollment->course->name }}</dd>

                    <dt class="col-sm-3">Anno Accademico:</dt>
                    <dd class="col-sm-9">{{ $enrollment->academicYear->name ?? '-' }}</dd>

                    <dt class="col-sm-3">Data Iscrizione:</dt>
                    <dd class="col-sm-9">{{ $enrollment->enrollment_date->format('d/m/Y') }}</dd>

                    <dt class="col-sm-3">Periodo:</dt>
                    <dd class="col-sm-9">
                        {{ $enrollment->start_date->format('d/m/Y') }} - 
                        {{ $enrollment->end_date->format('d/m/Y') }}
                    </dd>

                    <dt class="col-sm-3">Stato:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-{{ $enrollment->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </dd>

                    @if($enrollment->total_amount)
                    <dt class="col-sm-3">Importo Totale:</dt>
                    <dd class="col-sm-9">€ {{ number_format($enrollment->total_amount, 2, ',', '.') }}</dd>
                    @endif

                    @if($enrollment->notes)
                    <dt class="col-sm-3">Note:</dt>
                    <dd class="col-sm-9">{{ $enrollment->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary w-100">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection

