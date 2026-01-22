@extends('layouts.admin')

@section('title', 'Noleggi Strumenti')
@section('page-title', 'Noleggi Strumenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Noleggi Strumenti</h2>
    <a href="{{ route('admin.instrument-rentals.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Noleggio
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar
            :route="route('admin.instrument-rentals.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca studente o strumento...', 'width' => 4],
                ['name' => 'academic_year_id', 'type' => 'select', 'options' => $years->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti gli anni', 'width' => 3],
                ['name' => 'student_id', 'type' => 'select', 'options' => $students->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti gli studenti', 'width' => 3],
                ['name' => 'instrument_id', 'type' => 'select', 'options' => $instruments->mapWithKeys(fn($i) => [$i->id => trim(($i->type ?? '').' '.($i->brand ?? '').' '.($i->model ?? '').' '.$i->serial_number)])->toArray(), 'placeholder' => 'Tutti gli strumenti', 'width' => 3],
                ['name' => 'status', 'type' => 'select', 'options' => $statuses, 'placeholder' => 'Tutti gli stati', 'width' => 3],
            ]"
        />

        <x-admin.data-table
            :items="$rentals"
            :columns="[
                ['key' => 'start_date', 'label' => 'Inizio', 'format' => 'date'],
                ['key' => 'end_date', 'label' => 'Fine', 'format' => 'date'],
                ['relation' => 'academicYear', 'key' => 'name', 'label' => 'Anno'],
                ['relation' => 'student', 'key' => 'full_name', 'label' => 'Studente'],
                ['relation' => 'instrument', 'key' => 'type', 'label' => 'Strumento'],
                ['key' => 'monthly_fee', 'label' => 'Mensile', 'format' => 'currency'],
                ['key' => 'deposit', 'label' => 'Cauzione', 'format' => 'currency'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge', 'badge_class' => 'secondary'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.instrument-rentals.show'],
                ['type' => 'edit', 'route' => 'admin.instrument-rentals.edit'],
                ['type' => 'delete', 'route' => 'admin.instrument-rentals.destroy', 'confirm' => 'Sei sicuro di voler eliminare questo noleggio?'],
            ]"
        />
    </div>
</div>
@endsection

