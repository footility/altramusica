@extends('layouts.admin')

@section('title', 'Libri')
@section('page-title', 'Gestione Libri')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Libri</h2>
    <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Libro
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar
            :route="route('admin.books.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per titolo/autore/ISBN...', 'width' => 6],
            ]"
        />

        <x-admin.data-table
            :items="$books"
            :columns="[
                ['key' => 'title', 'label' => 'Titolo'],
                ['key' => 'author', 'label' => 'Autore'],
                ['key' => 'isbn', 'label' => 'ISBN'],
                ['key' => 'price', 'label' => 'Prezzo', 'format' => 'currency'],
                ['key' => 'stock_quantity', 'label' => 'Stock'],
                ['key' => 'distributions_count', 'label' => 'Distribuzioni'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.books.show'],
                ['type' => 'edit', 'route' => 'admin.books.edit'],
                ['type' => 'delete', 'route' => 'admin.books.destroy', 'confirm' => 'Sei sicuro di voler eliminare questo libro?'],
            ]"
        />
    </div>
</div>
@endsection

