@extends('layouts.admin')

@section('title', 'Nuovo Genitore/Tutore')
@section('page-title', 'Crea Nuovo Genitore/Tutore')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.guardians.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="first_name" 
                        label="Nome" 
                        value="{{ old('first_name') }}"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="last_name" 
                        label="Cognome" 
                        value="{{ old('last_name') }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="tax_code" 
                        label="Codice Fiscale" 
                        value="{{ old('tax_code') }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="relationship" 
                        label="Relazione" 
                        type="select"
                        :options="[
                            'madre' => 'Madre',
                            'padre' => 'Padre',
                            'tutore' => 'Tutore',
                            'other' => 'Altro',
                        ]"
                        value="{{ old('relationship') }}"
                    />
                </div>
            </div>

            <h5 class="mt-4">Contatti</h5>
            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="email_1" 
                        label="Email Principale" 
                        type="email"
                        value="{{ old('email_1') }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="cell_1" 
                        label="Cellulare Principale" 
                        value="{{ old('cell_1') }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="phone_home" 
                        label="Telefono Casa" 
                        value="{{ old('phone_home') }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="phone_work" 
                        label="Telefono Lavoro" 
                        value="{{ old('phone_work') }}"
                    />
                </div>
            </div>

            <h5 class="mt-4">Indirizzo</h5>
            <x-admin.form-field 
                name="address" 
                label="Indirizzo" 
                value="{{ old('address') }}"
            />

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="city" 
                        label="Città" 
                        value="{{ old('city') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="postal_code" 
                        label="CAP" 
                        value="{{ old('postal_code') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="country" 
                        label="Paese" 
                        value="{{ old('country', 'Italia') }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="privacy_consent" 
                label="Consenso Privacy" 
                type="checkbox"
                value="{{ old('privacy_consent', false) }}"
            />

            <h5 class="mt-4">Studenti Associati</h5>
            <x-admin.form-field 
                name="student_ids" 
                label="Studenti" 
                type="select"
                :options="$students->pluck('full_name', 'id')->toArray()"
                value="{{ old('student_ids') }}"
                placeholder="Seleziona studenti (opzionale)"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.guardians.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
