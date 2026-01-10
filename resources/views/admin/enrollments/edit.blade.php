@extends('layouts.admin')

@section('title', 'Modifica Iscrizione')
@section('page-title', 'Modifica Iscrizione')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST">
            @csrf
            @method('PUT')

            <x-admin.form-field 
                name="academic_year_id" 
                label="Anno Accademico" 
                type="select"
                :options="$years->pluck('name', 'id')->toArray()"
                value="{{ old('academic_year_id', $enrollment->academic_year_id) }}"
            />

            <x-admin.form-field 
                name="student_id" 
                label="Studente" 
                type="select"
                :options="$students->pluck('full_name', 'id')->toArray()"
                value="{{ old('student_id', $enrollment->student_id) }}"
                required
            />

            <x-admin.form-field 
                name="course_id" 
                label="Corso" 
                type="select"
                :options="$courses->pluck('name', 'id')->toArray()"
                value="{{ old('course_id', $enrollment->course_id) }}"
                required
            />

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="enrollment_date" 
                        label="Data Iscrizione" 
                        type="date"
                        value="{{ old('enrollment_date', $enrollment->enrollment_date->format('Y-m-d')) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="start_date" 
                        label="Data Inizio" 
                        type="date"
                        value="{{ old('start_date', $enrollment->start_date->format('Y-m-d')) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="end_date" 
                        label="Data Fine" 
                        type="date"
                        value="{{ old('end_date', $enrollment->end_date->format('Y-m-d')) }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="status" 
                        label="Stato" 
                        type="select"
                        :options="[
                            'active' => 'Attiva',
                            'completed' => 'Completata',
                            'cancelled' => 'Cancellata',
                        ]"
                        value="{{ old('status', $enrollment->status) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="discount_percentage" 
                        label="Sconto %" 
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        value="{{ old('discount_percentage', $enrollment->discount_percentage) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="discount_amount" 
                        label="Sconto Importo (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('discount_amount', $enrollment->discount_amount) }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes', $enrollment->notes) }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

