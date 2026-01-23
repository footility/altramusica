@extends('layouts.admin')

@section('title', 'Distribuzioni Libri')
@section('page-title', 'Distribuzioni Libri')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Distribuzioni Libri</h2>
    <a href="{{ route('admin.book-distributions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuova Distribuzione
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar
            :route="route('admin.book-distributions.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca studente o libro...', 'width' => 4],
                ['name' => 'academic_year_id', 'type' => 'select', 'options' => $years->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti gli anni', 'width' => 3],
                ['name' => 'student_id', 'type' => 'select', 'options' => $students->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti gli studenti', 'width' => 3],
                ['name' => 'book_id', 'type' => 'select', 'options' => $books->pluck('title', 'id')->toArray(), 'placeholder' => 'Tutti i libri', 'width' => 3],
                ['name' => 'course_offering_id', 'type' => 'select', 'options' => $courseOfferings->mapWithKeys(fn($o) => [$o->id => ($o->course?->name ?? 'Corso')])->toArray(), 'placeholder' => 'Tutti i corsi', 'width' => 3],
            ]"
        />

        <x-admin.data-table
            :items="$distributions"
            :columns="[
                ['key' => 'distribution_date', 'label' => 'Data', 'format' => 'date'],
                ['relation' => 'academicYear', 'key' => 'name', 'label' => 'Anno'],
                ['relation' => 'student', 'key' => 'full_name', 'label' => 'Studente'],
                ['relation' => 'book', 'key' => 'title', 'label' => 'Libro'],
                ['key' => 'courseOffering.course.name', 'label' => 'Corso'],
                ['key' => 'quantity', 'label' => 'Q.tà'],
                ['key' => 'price_paid', 'label' => 'Pagato', 'format' => 'currency'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.book-distributions.show'],
                ['type' => 'edit', 'route' => 'admin.book-distributions.edit'],
                ['type' => 'delete', 'route' => 'admin.book-distributions.destroy', 'confirm' => 'Sei sicuro di voler eliminare questa distribuzione?'],
            ]"
        />
    </div>
</div>
@endsection

