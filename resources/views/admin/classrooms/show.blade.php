@extends('layouts.admin')

@section('title', 'Dettaglio Aula')
@section('page-title', 'Dettaglio Aula')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Informazioni Aula</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Codice:</strong> {{ $classroom->code }}</p>
                <p><strong>Nome:</strong> {{ $classroom->name }}</p>
                <p><strong>Capacità:</strong> {{ $classroom->capacity }} persone</p>
            </div>
            <div class="col-md-6">
                <p><strong>Disponibile:</strong> 
                    @if($classroom->available)
                        <span class="badge bg-success">Sì</span>
                    @else
                        <span class="badge bg-danger">No</span>
                    @endif
                </p>
            </div>
        </div>
        
        @if($classroom->description)
            <div class="mt-3">
                <strong>Descrizione:</strong>
                <p class="mt-2">{{ $classroom->description }}</p>
            </div>
        @endif
        
        @if($classroom->notes)
            <div class="mt-3">
                <strong>Note:</strong>
                <p class="mt-2">{{ $classroom->notes }}</p>
            </div>
        @endif
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Torna all'elenco
    </a>
    <div>
        <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Modifica
        </a>
    </div>
</div>
@endsection

