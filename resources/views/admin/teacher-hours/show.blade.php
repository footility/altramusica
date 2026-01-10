@extends('layouts.admin')

@section('title', 'Dettaglio Conto Orario')
@section('page-title', 'Dettaglio Conto Orario')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Informazioni Conto Orario</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Docente:</strong> 
                    <a href="{{ route('admin.teachers.show', $teacherHour->teacher) }}">
                        {{ $teacherHour->teacher->last_name }} {{ $teacherHour->teacher->first_name }}
                    </a>
                </p>
                <p><strong>Anno Accademico:</strong> {{ $teacherHour->academicYear->name }}</p>
                <p><strong>Periodo:</strong> 
                    {{ $teacherHour->period_start->format('d/m/Y') }} - {{ $teacherHour->period_end->format('d/m/Y') }}
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Stato:</strong> 
                    @if($teacherHour->status == 'draft')
                        <span class="badge bg-secondary">Bozza</span>
                    @elseif($teacherHour->status == 'calculated')
                        <span class="badge bg-info">Calcolato</span>
                    @elseif($teacherHour->status == 'approved')
                        <span class="badge bg-success">Approvato</span>
                    @else
                        <span class="badge bg-primary">Pagato</span>
                    @endif
                </p>
                @if($teacherHour->approved_at)
                    <p><strong>Approvato il:</strong> {{ $teacherHour->approved_at->format('d/m/Y H:i') }}</p>
                    @if($teacherHour->approvedBy)
                        <p><strong>Approvato da:</strong> {{ $teacherHour->approvedBy->name }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Dettaglio Calcolo</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th>Lezioni Effettuate</th>
                <td>{{ $teacherHour->lessons_count }}</td>
            </tr>
            <tr>
                <th>Ore Totali</th>
                <td>{{ number_format($teacherHour->hours_total, 2) }}h</td>
            </tr>
            <tr>
                <th>Tariffa Oraria</th>
                <td>€ {{ number_format($teacherHour->hourly_rate, 2) }}/ora</td>
            </tr>
            <tr>
                <th>Importo Base</th>
                <td>€ {{ number_format($teacherHour->base_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Bonus</th>
                <td>€ {{ number_format($teacherHour->bonus_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Forfait</th>
                <td>€ {{ number_format($teacherHour->forfait_amount, 2) }}</td>
            </tr>
            <tr class="table-primary">
                <th><strong>Totale</strong></th>
                <td><strong>€ {{ number_format($teacherHour->total_amount, 2) }}</strong></td>
            </tr>
        </table>
        
        @if($teacherHour->notes)
            <div class="mt-3">
                <strong>Note:</strong>
                <p class="mt-2">{{ $teacherHour->notes }}</p>
            </div>
        @endif
    </div>
</div>

@if(in_array($teacherHour->status, ['calculated', 'approved']))
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Azioni</h5>
    </div>
    <div class="card-body">
        @if($teacherHour->status == 'calculated')
            <form action="{{ route('admin.teacher-hours.approve', $teacherHour) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Approvare questo conto orario?')">
                    <i class="bi bi-check-circle"></i> Approva
                </button>
            </form>
        @endif
        
        @if($teacherHour->status == 'approved')
            <form action="{{ route('admin.teacher-hours.mark-paid', $teacherHour) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary" onclick="return confirm('Marcare questo conto orario come pagato?')">
                    <i class="bi bi-cash"></i> Segna come Pagato
                </button>
            </form>
        @endif
    </div>
</div>
@endif

<div class="mt-3">
    <a href="{{ route('admin.teacher-hours.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Torna all'elenco
    </a>
</div>
@endsection

