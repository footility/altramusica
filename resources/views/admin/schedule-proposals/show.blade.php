@extends('layouts.admin')

@section('title', 'Dettaglio Proposta Oraria')
@section('page-title', 'Dettaglio Proposta Oraria')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Informazioni Proposta</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Studente:</strong> 
                    <a href="{{ route('admin.students.show', $scheduleProposal->student) }}">
                        {{ $scheduleProposal->student->last_name }} {{ $scheduleProposal->student->first_name }}
                    </a>
                </p>
                <p><strong>Docente:</strong> 
                    @if($scheduleProposal->teacher)
                        {{ $scheduleProposal->teacher->last_name }} {{ $scheduleProposal->teacher->first_name }}
                    @else
                        <span class="text-muted">Non assegnato</span>
                    @endif
                </p>
                <p><strong>Corso:</strong> 
                    @if($scheduleProposal->course)
                        {{ $scheduleProposal->course->name }}
                    @else
                        <span class="text-muted">Nessun corso</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Giorno:</strong> 
                    @php
                        $days = ['monday' => 'Lunedì', 'tuesday' => 'Martedì', 'wednesday' => 'Mercoledì', 'thursday' => 'Giovedì', 'friday' => 'Venerdì', 'saturday' => 'Sabato', 'sunday' => 'Domenica'];
                    @endphp
                    {{ $days[$scheduleProposal->day_of_week] ?? $scheduleProposal->day_of_week }}
                </p>
                <p><strong>Orario:</strong> 
                    {{ $scheduleProposal->time_start->format('H:i') }} - {{ $scheduleProposal->time_end->format('H:i') }}
                </p>
                <p><strong>Stato:</strong> 
                    @if($scheduleProposal->status == 'draft')
                        <span class="badge bg-secondary">Bozza</span>
                    @elseif($scheduleProposal->status == 'proposed')
                        <span class="badge bg-info">Proposta</span>
                    @elseif($scheduleProposal->status == 'accepted')
                        <span class="badge bg-success">Accettata</span>
                    @else
                        <span class="badge bg-danger">Rifiutata</span>
                    @endif
                </p>
            </div>
        </div>
        
        @if($scheduleProposal->notes)
            <div class="mt-3">
                <strong>Note:</strong>
                <p class="mt-2">{{ $scheduleProposal->notes }}</p>
            </div>
        @endif
        
        @if($scheduleProposal->accepted_at)
            <div class="mt-3">
                <strong>Accettata il:</strong> {{ $scheduleProposal->accepted_at->format('d/m/Y H:i') }}
                @if($scheduleProposal->acceptedBy)
                    da {{ $scheduleProposal->acceptedBy->name }}
                @endif
            </div>
        @endif
    </div>
</div>

@if(in_array($scheduleProposal->status, ['draft', 'proposed']))
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Azioni</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.schedule-proposals.accept', $scheduleProposal) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success" onclick="return confirm('Accettare questa proposta? Verrà creato l\'orario definitivo.')">
                <i class="bi bi-check-circle"></i> Accetta
            </button>
        </form>
        
        <form action="{{ route('admin.schedule-proposals.reject', $scheduleProposal) }}" method="POST" class="d-inline ms-2">
            @csrf
            <button type="submit" class="btn btn-danger" onclick="return confirm('Rifiutare questa proposta?')">
                <i class="bi bi-x-circle"></i> Rifiuta
            </button>
        </form>
    </div>
</div>
@endif

<div class="mt-3">
    <a href="{{ route('admin.schedule-proposals.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Torna all'elenco
    </a>
</div>
@endsection

