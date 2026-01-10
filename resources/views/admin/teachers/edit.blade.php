@extends('layouts.admin')

@section('title', 'Modifica Docente')
@section('page-title', 'Modifica Docente')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="first_name" 
                        label="Nome" 
                        value="{{ old('first_name', $teacher->first_name) }}"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="last_name" 
                        label="Cognome" 
                        value="{{ old('last_name', $teacher->last_name) }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="email" 
                        label="Email" 
                        type="email"
                        value="{{ old('email', $teacher->email) }}"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="phone" 
                        label="Telefono" 
                        value="{{ old('phone', $teacher->phone) }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="tax_code" 
                        label="Codice Fiscale" 
                        value="{{ old('tax_code', $teacher->tax_code) }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="contract_type" 
                        label="Tipo Contratto" 
                        type="select"
                        :options="[
                            'partita_iva' => 'Partita IVA',
                            'cooperativa' => 'Cooperativa',
                            'ritenuta' => 'Ritenuta d\'Acconto',
                        ]"
                        value="{{ old('contract_type', $teacher->contract_type) }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="hire_date" 
                        label="Data Assunzione" 
                        type="date"
                        value="{{ old('hire_date', $teacher->hire_date?->format('Y-m-d')) }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="address" 
                        label="Indirizzo" 
                        value="{{ old('address', $teacher->address) }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes', $teacher->notes) }}"
            />

            <x-admin.form-field 
                name="active" 
                label="Attivo" 
                type="checkbox"
                value="{{ old('active', $teacher->active) }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Aggiorna</button>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

