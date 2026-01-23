@extends('layouts.admin')

@section('title', 'Modifica Studente')
@section('page-title', 'Modifica Studente')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')

            <x-admin.form-field 
                name="academic_year_id" 
                label="Anno Scolastico" 
                type="select"
                :options="$years->pluck('name', 'id')->toArray()"
                value="{{ old('academic_year_id', $selectedYearId) }}"
            />

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="first_name" 
                        label="Nome" 
                        value="{{ old('first_name', $student->first_name) }}"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="last_name" 
                        label="Cognome" 
                        value="{{ old('last_name', $student->last_name) }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="code" 
                        label="Codice" 
                        value="{{ old('code', $studentYear?->code) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="birth_date" 
                        label="Data Nascita" 
                        type="date"
                        value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="tax_code" 
                        label="Codice Fiscale" 
                        value="{{ old('tax_code', $student->tax_code) }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="status" 
                        label="Stato" 
                        type="select"
                        :options="[
                            'prospect' => 'Prospect',
                            'interested' => 'Interessato',
                            'enrolled' => 'Iscritto',
                            'withdrawn' => 'Ritirato',
                        ]"
                        value="{{ old('status', $studentYear?->status ?? 'prospect') }}"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="last_contact_date" 
                        label="Data Ultimo Contatto" 
                        type="date"
                        value="{{ old('last_contact_date', $studentYear?->last_contact_date?->format('Y-m-d')) }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="school_origin" 
                label="Scuola di Provenienza" 
                value="{{ old('school_origin', $studentYear?->school_origin) }}"
            />

            <x-admin.form-field 
                name="how_know_us" 
                label="Come ci ha conosciuto" 
                value="{{ old('how_know_us', $studentYear?->how_know_us) }}"
            />

            <x-admin.form-field 
                name="preferences" 
                label="Preferenze" 
                type="textarea"
                value="{{ old('preferences', $studentYear?->preferences) }}"
            />

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes', $studentYear?->notes) }}"
            />

            <x-admin.form-field 
                name="admin_notes" 
                label="Note Amministrative" 
                type="textarea"
                value="{{ old('admin_notes', $studentYear?->admin_notes) }}"
            />

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="privacy_consent" 
                        label="Consenso Privacy" 
                        type="checkbox"
                        value="{{ old('privacy_consent', $studentYear?->privacy_consent ?? false) }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="photo_consent" 
                        label="Consenso Foto" 
                        type="checkbox"
                        value="{{ old('photo_consent', $studentYear?->photo_consent ?? false) }}"
                    />
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Aggiorna</button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
