@extends('layouts.admin')

@section('title', 'Modifica Noleggio')
@section('page-title', 'Modifica Noleggio Strumento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Modifica Noleggio</h2>
    <a href="{{ route('admin.instrument-rentals.show', $instrumentRental) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Indietro
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.instrument-rentals.update', $instrumentRental) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field
                        name="academic_year_id"
                        label="Anno scolastico"
                        type="select"
                        :options="$years->pluck('name','id')->toArray()"
                        :value="old('academic_year_id', $instrumentRental->academic_year_id ?? $currentYear?->id)"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field name="start_date" label="Data inizio" type="date" :value="old('start_date', $instrumentRental->start_date)" required="true" />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field name="end_date" label="Data fine (opzionale)" type="date" :value="old('end_date', $instrumentRental->end_date)" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field
                        name="student_id"
                        label="Studente"
                        type="select"
                        :options="$students->pluck('full_name','id')->toArray()"
                        :value="old('student_id', $instrumentRental->student_id)"
                        required="true"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field
                        name="instrument_id"
                        label="Strumento"
                        type="select"
                        :options="$instruments->mapWithKeys(fn($i) => [$i->id => trim(($i->type ?? '').' '.($i->brand ?? '').' '.($i->model ?? '').' '.$i->serial_number)])->toArray()"
                        :value="old('instrument_id', $instrumentRental->instrument_id)"
                        required="true"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <x-admin.form-field name="monthly_fee" label="Costo mensile" type="number" :value="old('monthly_fee', $instrumentRental->monthly_fee)" />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field name="deposit" label="Cauzione" type="number" :value="old('deposit', $instrumentRental->deposit)" />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field
                        name="status"
                        label="Stato"
                        type="select"
                        :options="$statuses"
                        :value="old('status', $instrumentRental->status)"
                        required="true"
                    />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field name="return_date" label="Data restituzione" type="date" :value="old('return_date', $instrumentRental->return_date)" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field
                        name="return_condition"
                        label="Condizioni restituzione"
                        type="select"
                        :options="['excellent' => 'Eccellenti', 'good' => 'Buone', 'fair' => 'Discrete', 'poor' => 'Scarse']"
                        :value="old('return_condition', $instrumentRental->return_condition)"
                    />
                </div>
                <div class="col-md-8">
                    <x-admin.form-field name="notes" label="Note" type="textarea" :value="old('notes', $instrumentRental->notes)" />
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i> Salva modifiche
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

