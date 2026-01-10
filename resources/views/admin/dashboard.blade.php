@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if(!$currentYear)
    <div class="alert alert-warning">
        <h5>Nessun anno scolastico attivo</h5>
        <p>Configura un anno scolastico per iniziare a utilizzare il sistema.</p>
        <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">Crea Anno Scolastico</a>
    </div>
@else
    <div class="mb-4">
        <h3>Anno Scolastico: {{ $currentYear->name }}</h3>
        <p class="text-muted">{{ $currentYear->start_date->format('d/m/Y') }} - {{ $currentYear->end_date->format('d/m/Y') }}</p>
    </div>

    <div class="row mb-4">
        <!-- Studenti -->
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Studenti</h5>
                    <h2>{{ $stats['students']['total'] }}</h2>
                    <small>
                        Iscritti: {{ $stats['students']['enrolled'] }} | 
                        Interessati: {{ $stats['students']['interested'] }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Iscrizioni -->
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Iscrizioni</h5>
                    <h2>{{ $stats['enrollments']['total'] }}</h2>
                    <small>Attive: {{ $stats['enrollments']['active'] }}</small>
                </div>
            </div>
        </div>

        <!-- Contratti -->
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Contratti</h5>
                    <h2>{{ $stats['contracts']['total'] }}</h2>
                    <small>
                        Firmati: {{ $stats['contracts']['signed'] }} | 
                        In attesa: {{ $stats['contracts']['pending'] }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Fatture -->
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Fatture</h5>
                    <h2>{{ $stats['invoices']['total'] }}</h2>
                    <small>
                        Pagate: {{ $stats['invoices']['paid'] }} | 
                        In attesa: {{ $stats['invoices']['pending'] }}
                    </small>
                    <div class="mt-2">
                        <strong>Totale: € {{ number_format($stats['invoices']['total_amount'], 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Azioni Rapide</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Nuovo Studente
                        </a>
                        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-success">
                            <i class="bi bi-clipboard-plus"></i> Nuova Iscrizione
                        </a>
                        <a href="{{ route('admin.contracts.create') }}" class="btn btn-info">
                            <i class="bi bi-file-earmark-plus"></i> Nuovo Contratto
                        </a>
                        <a href="{{ route('admin.invoices.create') }}" class="btn btn-warning">
                            <i class="bi bi-receipt-cutoff"></i> Nuova Fattura
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Link Utili</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><a href="{{ route('admin.students.index') }}">Gestione Studenti</a></li>
                        <li><a href="{{ route('admin.courses.index') }}">Gestione Corsi</a></li>
                        <li><a href="{{ route('admin.academic-years.index') }}">Anni Scolastici</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
