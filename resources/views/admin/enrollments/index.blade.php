@extends('layouts.admin')

@section('title', 'Iscrizioni')
@section('page-title', 'Gestione Iscrizioni')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Iscrizioni</h2>
    <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuova Iscrizione
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.enrollments.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca studente...', 'width' => 3],
                ['name' => 'academic_year_id', 'type' => 'select', 'options' => $years->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti gli anni', 'width' => 3, 'value' => $currentYear?->id],
                ['name' => 'student_id', 'type' => 'select', 'options' => $students->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti gli studenti', 'width' => 3],
                ['name' => 'course_id', 'type' => 'select', 'options' => $courses->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti i corsi', 'width' => 3],
                ['name' => 'status', 'type' => 'select', 'options' => ['active' => 'Attiva', 'completed' => 'Completata', 'cancelled' => 'Cancellata'], 'placeholder' => 'Tutti gli stati', 'width' => 3],
            ]"
        />

        <x-admin.data-table 
            :items="$enrollments"
            :columns="[
                ['key' => 'student.full_name', 'label' => 'Studente', 'relation' => 'student'],
                ['key' => 'course.name', 'label' => 'Corso', 'relation' => 'course'],
                ['key' => 'academicYear.name', 'label' => 'Anno', 'relation' => 'academicYear'],
                ['key' => 'enrollment_date', 'label' => 'Data Iscrizione', 'format' => 'date'],
                ['key' => 'start_date', 'label' => 'Data Inizio', 'format' => 'date'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.enrollments.show'],
                ['type' => 'edit', 'route' => 'admin.enrollments.edit'],
                ['type' => 'delete', 'route' => 'admin.enrollments.destroy'],
            ]"
        />
    </div>
</div>
@endsection

