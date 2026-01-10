@extends('layouts.admin')

@section('title', 'Dettaglio Attività Extra')
@section('page-title', 'Dettaglio Attività Extra')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Informazioni Attività</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nome:</strong> {{ $extraActivity->name }}</p>
                @if($extraActivity->code)
                    <p><strong>Codice:</strong> {{ $extraActivity->code }}</p>
                @endif
                <p><strong>Tipo:</strong> 
                    <span class="badge bg-primary">
                        @if($extraActivity->type == 'orchestra') Orchestra
                        @elseif($extraActivity->type == 'choir') Coro
                        @else Altro
                        @endif
                    </span>
                </p>
                @if($extraActivity->teacher)
                    <p><strong>Docente:</strong> 
                        <a href="{{ route('admin.teachers.show', $extraActivity->teacher) }}">
                            {{ $extraActivity->teacher->last_name }} {{ $extraActivity->teacher->first_name }}
                        </a>
                    </p>
                @endif
            </div>
            <div class="col-md-6">
                <p><strong>Data Inizio:</strong> {{ $extraActivity->start_date->format('d/m/Y') }}</p>
                @if($extraActivity->end_date)
                    <p><strong>Data Fine:</strong> {{ $extraActivity->end_date->format('d/m/Y') }}</p>
                @endif
                @if($extraActivity->day_of_week)
                    <p><strong>Giorno:</strong> 
                        @php
                            $days = ['monday' => 'Lunedì', 'tuesday' => 'Martedì', 'wednesday' => 'Mercoledì', 'thursday' => 'Giovedì', 'friday' => 'Venerdì', 'saturday' => 'Sabato', 'sunday' => 'Domenica'];
                        @endphp
                        {{ $days[$extraActivity->day_of_week] ?? $extraActivity->day_of_week }}
                    </p>
                @endif
                @if($extraActivity->time_start && $extraActivity->time_end)
                    <p><strong>Orario:</strong> 
                        {{ $extraActivity->time_start->format('H:i') }} - {{ $extraActivity->time_end->format('H:i') }}
                    </p>
                @endif
                @if($extraActivity->price)
                    <p><strong>Prezzo:</strong> € {{ number_format($extraActivity->price, 2) }}</p>
                @endif
                @if($extraActivity->max_participants)
                    <p><strong>Max Partecipanti:</strong> {{ $extraActivity->max_participants }}</p>
                @endif
                <p><strong>Stato:</strong> 
                    @if($extraActivity->status == 'active')
                        <span class="badge bg-success">Attiva</span>
                    @else
                        <span class="badge bg-secondary">Inattiva</span>
                    @endif
                </p>
            </div>
        </div>
        
        @if($extraActivity->description)
            <div class="mt-3">
                <strong>Descrizione:</strong>
                <p class="mt-2">{{ $extraActivity->description }}</p>
            </div>
        @endif
    </div>
</div>

@if($extraActivity->enrollments->count() > 0)
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Iscrizioni ({{ $extraActivity->enrollments->count() }})</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Studente</th>
                    <th>Data Iscrizione</th>
                    <th>Stato</th>
                </tr>
            </thead>
            <tbody>
                @foreach($extraActivity->enrollments as $enrollment)
                    <tr>
                        <td>
                            <a href="{{ route('admin.students.show', $enrollment->student) }}">
                                {{ $enrollment->student->last_name }} {{ $enrollment->student->first_name }}
                            </a>
                        </td>
                        <td>{{ $enrollment->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($enrollment->status == 'active')
                                <span class="badge bg-success">Attiva</span>
                            @else
                                <span class="badge bg-secondary">Inattiva</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="d-flex justify-content-between">
    <a href="{{ route('admin.extra-activities.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Torna all'elenco
    </a>
    <div>
        <a href="{{ route('admin.extra-activities.edit', $extraActivity) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Modifica
        </a>
    </div>
</div>
@endsection

