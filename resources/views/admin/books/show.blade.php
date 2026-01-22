@extends('layouts.admin')

@section('title', 'Dettaglio Libro')
@section('page-title', 'Dettaglio Libro')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">{{ $book->title }}</h2>
        <div class="text-muted">{{ $book->author }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.book-distributions.create', ['book_id' => $book->id]) }}" class="btn btn-outline-primary">
            <i class="bi bi-journal-arrow-up"></i> Distribuisci
        </a>
        <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Elenco
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <dl class="mb-0">
                    <dt>ISBN</dt>
                    <dd>{{ $book->isbn ?: '-' }}</dd>
                    <dt>Editore</dt>
                    <dd>{{ $book->publisher ?: '-' }}</dd>
                    <dt>Prezzo</dt>
                    <dd>€ {{ number_format($book->price, 2, ',', '.') }}</dd>
                    <dt>Stock</dt>
                    <dd>{{ $book->stock_quantity }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Distribuzioni</h5>
                <x-admin.data-table
                    :items="$book->distributions"
                    :columns="[
                        ['key' => 'distribution_date', 'label' => 'Data', 'format' => 'date'],
                        ['relation' => 'student', 'key' => 'full_name', 'label' => 'Studente'],
                        ['relation' => 'course', 'key' => 'name', 'label' => 'Corso'],
                        ['key' => 'quantity', 'label' => 'Q.tà'],
                        ['key' => 'price_paid', 'label' => 'Pagato', 'format' => 'currency'],
                    ]"
                    :actions="[
                        ['type' => 'show', 'route' => 'admin.book-distributions.show'],
                    ]"
                />
            </div>
        </div>
    </div>
</div>
@endsection

