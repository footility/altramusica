@extends('layouts.admin')

@section('title', 'Modifica Corso')
@section('page-title', 'Modifica: ' . $course->name)

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.courses.update', $course) }}" method="POST">
            @csrf
            @method('PUT')

            <x-admin.form-field 
                name="course_type_id" 
                label="Tipo Corso" 
                type="select"
                :options="$courseTypes->pluck('name', 'id')->toArray()"
                value="{{ old('course_type_id', $course->course_type_id) }}"
                required
            />

            <x-admin.form-field 
                name="teacher_id" 
                label="Docente" 
                type="select"
                :options="$teachers->pluck('full_name', 'id')->toArray()"
                value="{{ old('teacher_id', $course->teacher_id) }}"
                placeholder="Seleziona docente (opzionale)"
            />

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="code" 
                        label="Codice" 
                        value="{{ old('code', $course->code) }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="name" 
                        label="Nome Corso" 
                        value="{{ old('name', $course->name) }}"
                        required
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="description" 
                label="Descrizione" 
                type="textarea"
                value="{{ old('description', $course->description) }}"
            />

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="start_date" 
                        label="Data Inizio" 
                        type="date"
                        value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="end_date" 
                        label="Data Fine" 
                        type="date"
                        value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}"
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
                        value="{{ old('day_of_week', $course->day_of_week) }}"
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
                        value="{{ old('time_start', $course->time_start?->format('H:i')) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="time_end" 
                        label="Ora Fine" 
                        type="time"
                        value="{{ old('time_end', $course->time_end?->format('H:i')) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="max_students" 
                        label="Numero Massimo Studenti" 
                        type="number"
                        value="{{ old('max_students', $course->max_students) }}"
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
                        value="{{ old('price_per_lesson', $course->price_per_lesson) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="lessons_per_week" 
                        label="Lezioni per Settimana" 
                        type="number"
                        value="{{ old('lessons_per_week', $course->lessons_per_week) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="weeks_per_year" 
                        label="Settimane per Anno" 
                        type="number"
                        value="{{ old('weeks_per_year', $course->weeks_per_year) }}"
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
                value="{{ old('status', $course->status) }}"
                required
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

