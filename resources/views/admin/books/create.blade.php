@extends('layouts.admin')

@section('title', 'Nuovo Libro')
@section('page-title', 'Nuovo Libro')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nuovo Libro</h2>
    <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Indietro
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.books.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <x-admin.form-field name="title" label="Titolo" :value="old('title')" required="true" />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field name="author" label="Autore" :value="old('author')" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field name="isbn" label="ISBN" :value="old('isbn')" />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field name="publisher" label="Editore" :value="old('publisher')" />
                </div>
                <div class="col-md-2">
                    <x-admin.form-field name="price" label="Prezzo" type="number" :value="old('price', 0)" required="true" />
                </div>
                <div class="col-md-2">
                    <x-admin.form-field name="stock_quantity" label="Stock" type="number" :value="old('stock_quantity', 0)" required="true" />
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

