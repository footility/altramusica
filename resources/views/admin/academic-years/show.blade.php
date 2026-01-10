@extends('layouts.admin')

@section('title', 'Dettaglio Anno Scolastico')
@section('page-title', $academicYear->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Informazioni</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Nome:</dt>
                    <dd class="col-sm-9">{{ $academicYear->name }}</dd>

                    <dt class="col-sm-3">Slug:</dt>
                    <dd class="col-sm-9">{{ $academicYear->slug }}</dd>

                    <dt class="col-sm-3">Data Inizio:</dt>
                    <dd class="col-sm-9">{{ $academicYear->start_date->format('d/m/Y') }}</dd>

                    <dt class="col-sm-3">Data Fine:</dt>
                    <dd class="col-sm-9">{{ $academicYear->end_date->format('d/m/Y') }}</dd>

                    <dt class="col-sm-3">Stato:</dt>
                    <dd class="col-sm-9">
                        @if($academicYear->is_active)
                            <span class="badge bg-success">Attivo</span>
                        @else
                            <span class="badge bg-secondary">Inattivo</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Statistiche</h5>
            </div>
            <div class="card-body">
                <p><strong>Studenti:</strong> {{ $academicYear->students_count }}</p>
                <p><strong>Iscrizioni:</strong> {{ $academicYear->enrollments_count }}</p>
                <p><strong>Contratti:</strong> {{ $academicYear->contracts_count }}</p>
                <p><strong>Fatture:</strong> {{ $academicYear->invoices_count }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                @if(!$academicYear->is_active)
                    <form action="{{ route('admin.academic-years.set-active', $academicYear) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">Attiva questo anno</button>
                    </form>
                @endif
                <a href="{{ route('admin.academic-years.edit', $academicYear) }}" class="btn btn-primary w-100 mt-2">Modifica</a>
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary w-100 mt-2">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection

