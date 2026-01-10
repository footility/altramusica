@extends('layouts.admin')

@section('title', 'Nuovo Docente')
@section('page-title', 'Crea Nuovo Docente')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.teachers.store') }}" method="POST">
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
                        name="email" 
                        label="Email" 
                        type="email"
                        value="{{ old('email') }}"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="phone" 
                        label="Telefono" 
                        value="{{ old('phone') }}"
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
                        name="contract_type" 
                        label="Tipo Contratto" 
                        type="select"
                        :options="[
                            'partita_iva' => 'Partita IVA',
                            'cooperativa' => 'Cooperativa',
                            'ritenuta' => 'Ritenuta d\'Acconto',
                        ]"
                        value="{{ old('contract_type') }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="hire_date" 
                        label="Data Assunzione" 
                        type="date"
                        value="{{ old('hire_date') }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="address" 
                        label="Indirizzo" 
                        value="{{ old('address') }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes') }}"
            />

            <x-admin.form-field 
                name="active" 
                label="Attivo" 
                type="checkbox"
                value="{{ old('active', true) }}"
            />

            <h5 class="mt-4">Account Utente</h5>
            <x-admin.form-field 
                name="create_user_account" 
                label="Crea Account Utente" 
                type="checkbox"
                value="{{ old('create_user_account', false) }}"
            />

            <div id="user-account-fields" style="display: none;">
                <x-admin.form-field 
                    name="password" 
                    label="Password" 
                    type="password"
                    value="{{ old('password') }}"
                    help="Minimo 8 caratteri"
                />
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('create_user_account').addEventListener('change', function() {
        document.getElementById('user-account-fields').style.display = this.checked ? 'block' : 'none';
        document.getElementById('password').required = this.checked;
    });
</script>
@endpush
@endsection

