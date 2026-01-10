@extends('layouts.admin')

@section('title', 'Dettaglio Primo Contatto')
@section('page-title', 'Dettaglio Primo Contatto')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Informazioni Contatto</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nome:</strong> {{ $firstContact->first_name }}</p>
                <p><strong>Cognome:</strong> {{ $firstContact->last_name }}</p>
                <p><strong>Data Nascita:</strong> {{ $firstContact->birth_date ? $firstContact->birth_date->format('d/m/Y') : '-' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Email:</strong> {{ $firstContact->email ?: '-' }}</p>
                <p><strong>Telefono:</strong> {{ $firstContact->phone ?: '-' }}</p>
                <p><strong>Stato:</strong> 
                    @if($firstContact->status == 'pending')
                        <span class="badge bg-warning">In Attesa</span>
                    @elseif($firstContact->status == 'converted')
                        <span class="badge bg-success">Convertito</span>
                    @else
                        <span class="badge bg-secondary">Scartato</span>
                    @endif
                </p>
            </div>
        </div>
        
        @if($firstContact->notes)
            <div class="mt-3">
                <strong>Note:</strong>
                <p class="mt-2">{{ $firstContact->notes }}</p>
            </div>
        @endif
        
        @if($firstContact->student)
            <div class="mt-3">
                <strong>Studente Convertito:</strong>
                <p class="mt-2">
                    <a href="{{ route('admin.students.show', $firstContact->student) }}">
                        {{ $firstContact->student->first_name }} {{ $firstContact->student->last_name }}
                    </a>
                </p>
            </div>
        @endif
        
        @if(session('link'))
            <div class="alert alert-info mt-3">
                <strong>Link precompilato:</strong><br>
                <input type="text" class="form-control mt-2" value="{{ session('link') }}" readonly onclick="this.select()">
            </div>
        @endif
    </div>
</div>

@if($firstContact->status == 'pending')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Azioni</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.first-contacts.convert', $firstContact) }}" method="POST" class="d-inline">
            @csrf
            <div class="mb-3">
                <label for="academic_year_id" class="form-label">Anno Scolastico (opzionale)</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select">
                    <option value="">Anno corrente</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success" onclick="return confirm('Convertire questo primo contatto in studente?')">
                <i class="bi bi-person-plus"></i> Converti in Studente
            </button>
        </form>
        
        <form action="{{ route('admin.first-contacts.dismiss', $firstContact) }}" method="POST" class="d-inline ms-2">
            @csrf
            <button type="submit" class="btn btn-secondary" onclick="return confirm('Scartare questo primo contatto?')">
                <i class="bi bi-x-circle"></i> Scarta
            </button>
        </form>
        
        <a href="{{ route('admin.first-contacts.generate-link', $firstContact) }}" class="btn btn-info ms-2">
            <i class="bi bi-link-45deg"></i> Genera Link Precompilato
        </a>
    </div>
</div>
@endif

<div class="mt-3">
    <a href="{{ route('admin.first-contacts.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Torna all'elenco
    </a>
</div>
@endsection

