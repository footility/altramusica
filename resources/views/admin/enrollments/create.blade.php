@extends('layouts.admin')

@section('title', 'Nuova Iscrizione')
@section('page-title', 'Crea Nuova Iscrizione')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.enrollments.store') }}" method="POST">
            @csrf

            <x-admin.form-field 
                name="academic_year_id" 
                label="Anno Accademico" 
                type="select"
                :options="$years->pluck('name', 'id')->toArray()"
                value="{{ old('academic_year_id', $currentYear?->id) }}"
            />

            <x-admin.form-field 
                name="student_id" 
                label="Studente" 
                type="select"
                :options="$students->pluck('full_name', 'id')->toArray()"
                value="{{ old('student_id', $preselectedStudent?->id) }}"
                required
            />

            <x-admin.form-field 
                name="course_id" 
                label="Corso" 
                type="select"
                :options="$courses->pluck('name', 'id')->toArray()"
                value="{{ old('course_id') }}"
                required
            />

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="enrollment_date" 
                        label="Data Iscrizione" 
                        type="date"
                        value="{{ old('enrollment_date', now()->format('Y-m-d')) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="start_date" 
                        label="Data Inizio" 
                        type="date"
                        value="{{ old('start_date') }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="end_date" 
                        label="Data Fine" 
                        type="date"
                        value="{{ old('end_date') }}"
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
                        value="{{ old('status', 'active') }}"
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
                        value="{{ old('discount_percentage') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="discount_amount" 
                        label="Sconto Importo (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('discount_amount') }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes') }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

