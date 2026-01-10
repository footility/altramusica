@extends('layouts.admin')

@section('title', 'Genitori/Tutori')
@section('page-title', 'Gestione Genitori/Tutori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Genitori/Tutori</h2>
    <a href="{{ route('admin.guardians.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Genitore/Tutore
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.guardians.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per nome, email, codice fiscale...', 'width' => 6],
            ]"
        />

        <x-admin.data-table 
            :items="$guardians"
            :columns="[
                ['key' => 'first_name', 'label' => 'Nome'],
                ['key' => 'last_name', 'label' => 'Cognome'],
                ['key' => 'email_1', 'label' => 'Email'],
                ['key' => 'cell_1', 'label' => 'Cellulare'],
                ['key' => 'students_count', 'label' => 'Studenti'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.guardians.show'],
                ['type' => 'edit', 'route' => 'admin.guardians.edit'],
                ['type' => 'delete', 'route' => 'admin.guardians.destroy'],
            ]"
        />
    </div>
</div>
@endsection
