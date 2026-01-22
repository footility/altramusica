@extends('layouts.admin')

@section('title', 'Corsi')
@section('page-title', 'Gestione Corsi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Elenco Corsi (anno)</h2>
        @if(isset($currentYear))
            <small class="text-muted">Anno corrente: {{ $currentYear?->name ?? '-' }}</small>
        @endif
    </div>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Corso
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.courses.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per nome, codice...', 'width' => 3],
                ['name' => 'course_type_id', 'type' => 'select', 'options' => $courseTypes->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti i tipi', 'width' => 3],
                ['name' => 'teacher_id', 'type' => 'select', 'options' => $teachers->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti i docenti', 'width' => 3],
                ['name' => 'status', 'type' => 'select', 'options' => ['planned' => 'Pianificato', 'active' => 'Attivo', 'completed' => 'Completato', 'cancelled' => 'Cancellato'], 'placeholder' => 'Tutti gli stati', 'width' => 3],
            ]"
        />

        <x-admin.data-table 
            :items="$courses"
            :columns="[
                ['key' => 'course.name', 'label' => 'Nome'],
                ['key' => 'course.courseType.name', 'label' => 'Tipo'],
                ['key' => 'teacher.full_name', 'label' => 'Docente', 'relation' => 'teacher'],
                ['key' => 'day_of_week', 'label' => 'Giorno'],
                ['key' => 'enrollments_count', 'label' => 'Iscritti'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.courses.show', 'param' => 'course'],
                ['type' => 'edit', 'route' => 'admin.courses.edit', 'param' => 'course'],
                ['type' => 'delete', 'route' => 'admin.courses.destroy', 'param' => 'course'],
            ]"
        />
    </div>
</div>
@endsection


