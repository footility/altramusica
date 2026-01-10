@extends('layouts.admin')

@section('title', 'Esami')
@section('page-title', 'Gestione Esami')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Esami</h2>
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Esame
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.exams.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca studente...', 'width' => 3],
                ['name' => 'student_id', 'type' => 'select', 'options' => $students->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti gli studenti', 'width' => 3],
                ['name' => 'exam_type', 'type' => 'select', 'options' => ['abrsm' => 'ABRSM', 'lcm' => 'LCM', 'internal' => 'Interno', 'other' => 'Altro'], 'placeholder' => 'Tutti i tipi', 'width' => 3],
                ['name' => 'level', 'type' => 'number', 'placeholder' => 'Livello', 'width' => 3],
            ]"
        />

        <x-admin.data-table 
            :items="$exams"
            :columns="[
                ['key' => 'student.full_name', 'label' => 'Studente', 'relation' => 'student'],
                ['key' => 'exam_type', 'label' => 'Tipo'],
                ['key' => 'level', 'label' => 'Livello'],
                ['key' => 'subject', 'label' => 'Materia'],
                ['key' => 'exam_date', 'label' => 'Data Esame', 'format' => 'date'],
                ['key' => 'result', 'label' => 'Risultato', 'format' => 'badge'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.exams.show'],
                ['type' => 'edit', 'route' => 'admin.exams.edit'],
                ['type' => 'delete', 'route' => 'admin.exams.destroy'],
            ]"
        />
    </div>
</div>
@endsection

