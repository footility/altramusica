@extends('layouts.admin')

@section('title', 'Strumenti')
@section('page-title', 'Gestione Strumenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Strumenti</h2>
    <a href="{{ route('admin.instruments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Strumento
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.instruments.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per tipo, marca, modello...', 'width' => 4],
                ['name' => 'status', 'type' => 'select', 'options' => ['available' => 'Disponibile', 'rented' => 'Noleggiato', 'sold' => 'Venduto', 'maintenance' => 'In Manutenzione', 'retired' => 'Ritirato'], 'placeholder' => 'Tutti gli stati', 'width' => 4],
                ['name' => 'type', 'type' => 'text', 'placeholder' => 'Tipo strumento', 'width' => 4],
            ]"
        />

        <x-admin.data-table 
            :items="$instruments"
            :columns="[
                ['key' => 'type', 'label' => 'Tipo'],
                ['key' => 'brand', 'label' => 'Marca'],
                ['key' => 'model', 'label' => 'Modello'],
                ['key' => 'size', 'label' => 'Misura'],
                ['key' => 'serial_number', 'label' => 'Numero Seriale'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge'],
                ['key' => 'rentals_count', 'label' => 'Noleggi'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.instruments.show'],
                ['type' => 'edit', 'route' => 'admin.instruments.edit'],
                ['type' => 'delete', 'route' => 'admin.instruments.destroy'],
            ]"
        />
    </div>
</div>
@endsection

