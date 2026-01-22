@extends('layouts.admin')

@section('title', 'Dettaglio Corso')
@section('page-title', $course->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Informazioni Corso</h5>
            </div>
            <div class="card-body">
                @if(isset($currentYear))
                    <div class="alert alert-light border mb-3">
                        <strong>Anno:</strong> {{ $currentYear?->name ?? '-' }}
                    </div>
                @endif
                <dl class="row">
                    <dt class="col-sm-3">Nome:</dt>
                    <dd class="col-sm-9">{{ $course->name }}</dd>

                    <dt class="col-sm-3">Tipo:</dt>
                    <dd class="col-sm-9">{{ $course->courseType->name ?? '-' }}</dd>

                    <dt class="col-sm-3">Docente:</dt>
                    <dd class="col-sm-9">{{ $offering?->teacher?->full_name ?? '-' }}</dd>

                    <dt class="col-sm-3">Giorno:</dt>
                    <dd class="col-sm-9">{{ $offering?->day_of_week ? ucfirst($offering->day_of_week) : '-' }}</dd>

                    <dt class="col-sm-3">Orario:</dt>
                    <dd class="col-sm-9">
                        @if($offering?->time_start && $offering?->time_end)
                            {{ $offering->time_start }} - {{ $offering->time_end }}
                        @else
                            -
                        @endif
                    </dd>

                    <dt class="col-sm-3">Prezzo per Lezione:</dt>
                    <dd class="col-sm-9">€ {{ number_format((float) ($offering?->price_per_lesson ?? 0), 2, ',', '.') }}</dd>

                    <dt class="col-sm-3">Lezioni per Settimana:</dt>
                    <dd class="col-sm-9">{{ $offering?->lessons_per_week ?? '-' }}</dd>

                    <dt class="col-sm-3">Stato:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-{{ ($offering?->status ?? null) == 'active' ? 'success' : 'secondary' }}">
                            {{ $offering?->status ? ucfirst($offering->status) : '-' }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>

        @if(($offering?->enrollments?->count() ?? 0) > 0)
        <div class="card">
            <div class="card-header">
                <h5>Iscrizioni ({{ $offering->enrollments->count() }})</h5>
            </div>
            <div class="card-body">
                <x-admin.data-table 
                    :items="$offering->enrollments"
                    :columns="[
                        ['key' => 'student.first_name', 'label' => 'Nome'],
                        ['key' => 'student.last_name', 'label' => 'Cognome'],
                        ['key' => 'start_date', 'label' => 'Data Inizio', 'format' => 'date'],
                        ['key' => 'status', 'label' => 'Stato', 'format' => 'badge'],
                    ]"
                />
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary w-100">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection


