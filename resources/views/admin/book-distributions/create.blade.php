@extends('layouts.admin')

@section('title', 'Nuova Distribuzione Libro')
@section('page-title', 'Nuova Distribuzione Libro')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nuova Distribuzione Libro</h2>
    <a href="{{ route('admin.book-distributions.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Indietro
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.book-distributions.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field
                        name="academic_year_id"
                        label="Anno scolastico"
                        type="select"
                        :options="$years->pluck('name','id')->toArray()"
                        :value="old('academic_year_id', $currentYear?->id)"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field
                        name="distribution_date"
                        label="Data distribuzione"
                        type="date"
                        :value="old('distribution_date')"
                        required="true"
                    />
                </div>
                <div class="col-md-2">
                    <x-admin.form-field
                        name="quantity"
                        label="Quantità"
                        type="number"
                        :value="old('quantity', 1)"
                        required="true"
                    />
                </div>
                <div class="col-md-2">
                    <x-admin.form-field
                        name="price_paid"
                        label="Prezzo pagato"
                        type="number"
                        :value="old('price_paid', 0)"
                        required="true"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field
                        name="student_id"
                        label="Studente"
                        type="select"
                        :options="$students->pluck('full_name','id')->toArray()"
                        :value="old('student_id', $preselectedStudent?->id)"
                        required="true"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field
                        name="book_id"
                        label="Libro"
                        type="select"
                        :options="$books->pluck('title','id')->toArray()"
                        :value="old('book_id', request('book_id'))"
                        required="true"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field
                        name="course_offering_id"
                        label="Corso (opzionale)"
                        type="select"
                        :options="$courseOfferings->mapWithKeys(fn($o) => [$o->id => ($o->course?->name ?? 'Corso')])->toArray()"
                        :value="old('course_offering_id')"
                    />
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i> Salva
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

