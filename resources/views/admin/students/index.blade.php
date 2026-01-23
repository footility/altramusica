@extends('layouts.admin')

@section('title', 'Studenti')
@section('page-title', 'Gestione Studenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Studenti</h2>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Studente
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.students.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per nome, cognome, codice...', 'width' => 4],
                ['name' => 'academic_year_id', 'type' => 'select', 'options' => $years->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti gli anni', 'width' => 3, 'value' => $currentYear?->id],
                ['name' => 'status', 'type' => 'select', 'options' => $statuses, 'placeholder' => 'Tutti gli stati', 'width' => 3],
            ]"
        />

        <x-admin.data-table 
            :items="$students"
            :columns="[
                ['key' => 'code', 'label' => 'Codice'],
                ['key' => 'first_name', 'label' => 'Nome'],
                ['key' => 'last_name', 'label' => 'Cognome'],
                ['key' => 'birth_date', 'label' => 'Data Nascita', 'format' => 'date'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge', 'badge_class' => 'bg-success'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.students.show'],
                ['type' => 'edit', 'route' => 'admin.students.edit'],
                ['type' => 'delete', 'route' => 'admin.students.destroy', 'confirm' => 'Sei sicuro di voler eliminare questo studente?'],
            ]"
        />
    </div>
</div>
@endsection
