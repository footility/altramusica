@extends('layouts.admin')

@section('title', 'Docenti')
@section('page-title', 'Gestione Docenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Docenti</h2>
    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Docente
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.teachers.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per nome, email...', 'width' => 6],
                ['name' => 'active', 'type' => 'select', 'options' => ['1' => 'Attivi', '0' => 'Non attivi'], 'placeholder' => 'Tutti', 'width' => 3],
            ]"
        />

        <x-admin.data-table 
            :items="$teachers"
            :columns="[
                ['key' => 'first_name', 'label' => 'Nome'],
                ['key' => 'last_name', 'label' => 'Cognome'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'contract_type', 'label' => 'Tipo Contratto'],
                ['key' => 'active', 'label' => 'Attivo', 'format' => 'badge', 'badge_class' => 'bg-success', 'badge_true' => 'Sì', 'badge_false' => 'No'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.teachers.show'],
                ['type' => 'edit', 'route' => 'admin.teachers.edit'],
                ['type' => 'delete', 'route' => 'admin.teachers.destroy'],
            ]"
        />
    </div>
</div>
@endsection

