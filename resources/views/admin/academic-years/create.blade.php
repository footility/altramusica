@extends('layouts.admin')

@section('title', 'Nuovo Anno Scolastico')
@section('page-title', 'Crea Nuovo Anno Scolastico')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.academic-years.store') }}" method="POST">
            @csrf

            <x-admin.form-field 
                name="name" 
                label="Nome" 
                value="{{ old('name') }}"
                required
                placeholder="es. 2025-2026"
            />

            <x-admin.form-field 
                name="slug" 
                label="Slug" 
                value="{{ old('slug') }}"
                required
                placeholder="es. 2025-26"
                help="Identificativo univoco (solo lettere, numeri e trattini)"
            />

            <x-admin.form-field 
                name="start_date" 
                label="Data Inizio" 
                type="date"
                value="{{ old('start_date') }}"
                required
            />

            <x-admin.form-field 
                name="end_date" 
                label="Data Fine" 
                type="date"
                value="{{ old('end_date') }}"
                required
            />

            <x-admin.form-field 
                name="is_active" 
                label="Attivo" 
                type="checkbox"
                value="{{ old('is_active', false) }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

