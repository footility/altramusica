@extends('layouts.admin')

@section('title', 'Modifica Anno Scolastico')
@section('page-title', 'Modifica Anno Scolastico')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.academic-years.update', $academicYear) }}" method="POST">
            @csrf
            @method('PUT')

            <x-admin.form-field 
                name="name" 
                label="Nome" 
                value="{{ old('name', $academicYear->name) }}"
                required
            />

            <x-admin.form-field 
                name="slug" 
                label="Slug" 
                value="{{ old('slug', $academicYear->slug) }}"
                required
            />

            <x-admin.form-field 
                name="start_date" 
                label="Data Inizio" 
                type="date"
                value="{{ old('start_date', $academicYear->start_date->format('Y-m-d')) }}"
                required
            />

            <x-admin.form-field 
                name="end_date" 
                label="Data Fine" 
                type="date"
                value="{{ old('end_date', $academicYear->end_date->format('Y-m-d')) }}"
                required
            />

            <x-admin.form-field 
                name="is_active" 
                label="Attivo" 
                type="checkbox"
                value="{{ old('is_active', $academicYear->is_active) }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Aggiorna</button>
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

