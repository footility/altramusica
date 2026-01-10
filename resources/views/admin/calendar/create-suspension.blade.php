@extends('layouts.admin')

@section('title', 'Nuova Sospensione')
@section('page-title', 'Aggiungi Sospensione Calendario')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.calendar.suspensions.store') }}" method="POST">
            @csrf

            <x-admin.form-field 
                name="academic_year_id" 
                label="Anno Scolastico" 
                type="select"
                :options="$years->pluck('name', 'id')->toArray()"
                value="{{ old('academic_year_id') }}"
                required
            />

            <x-admin.form-field 
                name="name" 
                label="Nome Sospensione" 
                value="{{ old('name') }}"
                required
                placeholder="es. Vacanze di Natale"
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
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes') }}"
                placeholder="Note opzionali sulla sospensione"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

