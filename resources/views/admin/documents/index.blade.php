@extends('layouts.admin')

@section('title', 'Documenti')
@section('page-title', 'Documenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Documenti</h2>
    <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Carica Documento
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar
            :route="route('admin.documents.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per nome file/studente...', 'width' => 4],
                ['name' => 'type', 'type' => 'select', 'options' => $types, 'placeholder' => 'Tutti i tipi', 'width' => 3],
                ['name' => 'student_id', 'type' => 'select', 'options' => $students->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti gli studenti', 'width' => 3],
                ['name' => 'contract_id', 'type' => 'select', 'options' => $contracts->pluck('contract_number', 'id')->toArray(), 'placeholder' => 'Tutti i contratti', 'width' => 3],
            ]"
        />

        <x-admin.data-table
            :items="$documents"
            :columns="[
                ['key' => 'created_at', 'label' => 'Caricato', 'format' => 'datetime'],
                ['key' => 'type', 'label' => 'Tipo', 'format' => 'badge', 'badge_class' => 'secondary'],
                ['key' => 'file_name', 'label' => 'File'],
                ['relation' => 'student', 'key' => 'full_name', 'label' => 'Studente'],
                ['relation' => 'contract', 'key' => 'contract_number', 'label' => 'Contratto'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.documents.show'],
                ['type' => 'edit', 'route' => 'admin.documents.edit'],
                ['type' => 'delete', 'route' => 'admin.documents.destroy', 'confirm' => 'Sei sicuro di voler eliminare questo documento?'],
            ]"
        />
    </div>
</div>
@endsection

