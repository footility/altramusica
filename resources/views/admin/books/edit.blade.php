@extends('layouts.admin')

@section('title', 'Modifica Libro')
@section('page-title', 'Modifica Libro')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Modifica Libro</h2>
    <a href="{{ route('admin.books.show', $book) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Indietro
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.books.update', $book) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <x-admin.form-field name="title" label="Titolo" :value="old('title', $book->title)" required="true" />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field name="author" label="Autore" :value="old('author', $book->author)" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field name="isbn" label="ISBN" :value="old('isbn', $book->isbn)" />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field name="publisher" label="Editore" :value="old('publisher', $book->publisher)" />
                </div>
                <div class="col-md-2">
                    <x-admin.form-field name="price" label="Prezzo" type="number" :value="old('price', $book->price)" required="true" />
                </div>
                <div class="col-md-2">
                    <x-admin.form-field name="stock_quantity" label="Stock" type="number" :value="old('stock_quantity', $book->stock_quantity)" required="true" />
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

