@extends('layouts.admin')

@section('title', 'Nuovo Corso')
@section('page-title', 'Crea Nuovo Corso')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.courses.store') }}" method="POST">
            @csrf

            <x-admin.form-field 
                name="course_type_id" 
                label="Tipo Corso" 
                type="select"
                :options="$courseTypes->pluck('name', 'id')->toArray()"
                value="{{ old('course_type_id') }}"
                required
            />

            <x-admin.form-field 
                name="teacher_id" 
                label="Docente" 
                type="select"
                :options="$teachers->pluck('full_name', 'id')->toArray()"
                value="{{ old('teacher_id') }}"
                placeholder="Seleziona docente (opzionale)"
            />

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="code" 
                        label="Codice" 
                        value="{{ old('code') }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="name" 
                        label="Nome Corso" 
                        value="{{ old('name') }}"
                        required
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="description" 
                label="Descrizione" 
                type="textarea"
                value="{{ old('description') }}"
            />

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="start_date" 
                        label="Data Inizio" 
                        type="date"
                        value="{{ old('start_date') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="end_date" 
                        label="Data Fine" 
                        type="date"
                        value="{{ old('end_date') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="day_of_week" 
                        label="Giorno Settimana" 
                        type="select"
                        :options="[
                            'monday' => 'Lunedì',
                            'tuesday' => 'Martedì',
                            'wednesday' => 'Mercoledì',
                            'thursday' => 'Giovedì',
                            'friday' => 'Venerdì',
                            'saturday' => 'Sabato',
                            'sunday' => 'Domenica',
                        ]"
                        value="{{ old('day_of_week') }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="time_start" 
                        label="Ora Inizio" 
                        type="time"
                        value="{{ old('time_start') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="time_end" 
                        label="Ora Fine" 
                        type="time"
                        value="{{ old('time_end') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="max_students" 
                        label="Numero Massimo Studenti" 
                        type="number"
                        value="{{ old('max_students') }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="price_per_lesson" 
                        label="Prezzo per Lezione (€)" 
                        type="number"
                        step="0.01"
                        value="{{ old('price_per_lesson') }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="lessons_per_week" 
                        label="Lezioni per Settimana" 
                        type="number"
                        value="{{ old('lessons_per_week', 1) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="weeks_per_year" 
                        label="Settimane per Anno" 
                        type="number"
                        value="{{ old('weeks_per_year') }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="status" 
                label="Stato" 
                type="select"
                :options="[
                    'planned' => 'Pianificato',
                    'active' => 'Attivo',
                    'completed' => 'Completato',
                    'cancelled' => 'Cancellato',
                ]"
                value="{{ old('status', 'planned') }}"
                required
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection


