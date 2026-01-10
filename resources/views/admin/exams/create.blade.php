@extends('layouts.admin')

@section('title', 'Nuovo Esame')
@section('page-title', 'Crea Nuovo Esame')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.exams.store') }}" method="POST">
            @csrf

            <x-admin.form-field 
                name="student_id" 
                label="Studente" 
                type="select"
                :options="$students->pluck('full_name', 'id')->toArray()"
                value="{{ old('student_id', $preselectedStudent?->id) }}"
                required
            />

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="exam_type" 
                        label="Tipo Esame" 
                        type="select"
                        :options="[
                            'abrsm' => 'ABRSM',
                            'lcm' => 'LCM',
                            'internal' => 'Interno',
                            'other' => 'Altro',
                        ]"
                        value="{{ old('exam_type') }}"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="level" 
                        label="Livello" 
                        type="number"
                        min="0"
                        max="8"
                        value="{{ old('level') }}"
                        required
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="subject" 
                label="Materia" 
                value="{{ old('subject') }}"
                required
            />

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="exam_date" 
                        label="Data Esame" 
                        type="date"
                        value="{{ old('exam_date') }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="registration_date" 
                        label="Data Registrazione" 
                        type="date"
                        value="{{ old('registration_date') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="registration_fee" 
                        label="Tassa Registrazione (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('registration_fee') }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="result" 
                        label="Risultato" 
                        type="select"
                        :options="[
                            'passed' => 'Superato',
                            'failed' => 'Non Superato',
                            'pending' => 'In Attesa',
                        ]"
                        value="{{ old('result', 'pending') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="grade" 
                        label="Voto" 
                        value="{{ old('grade') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="certificate_number" 
                        label="Numero Certificato" 
                        value="{{ old('certificate_number') }}"
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
                <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

