@extends('layouts.admin')

@section('title', 'Fatture')
@section('page-title', 'Gestione Fatture')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Fatture</h2>
    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuova Fattura
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.invoices.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per numero, studente...', 'width' => 3],
                ['name' => 'academic_year_id', 'type' => 'select', 'options' => $years->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti gli anni', 'width' => 3, 'value' => $currentYear?->id],
                ['name' => 'student_id', 'type' => 'select', 'options' => $students->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti gli studenti', 'width' => 3],
                ['name' => 'status', 'type' => 'select', 'options' => ['draft' => 'Bozza', 'sent' => 'Inviata', 'paid' => 'Pagata', 'overdue' => 'Scaduta', 'cancelled' => 'Cancellata'], 'placeholder' => 'Tutti gli stati', 'width' => 3],
            ]"
        />

        <x-admin.data-table 
            :items="$invoices"
            :columns="[
                ['key' => 'invoice_number', 'label' => 'Numero'],
                ['key' => 'student.full_name', 'label' => 'Studente', 'relation' => 'student'],
                ['key' => 'invoice_date', 'label' => 'Data', 'format' => 'date'],
                ['key' => 'due_date', 'label' => 'Scadenza', 'format' => 'date'],
                ['key' => 'total_amount', 'label' => 'Importo', 'format' => 'currency'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.invoices.show'],
                ['type' => 'edit', 'route' => 'admin.invoices.edit'],
                ['type' => 'delete', 'route' => 'admin.invoices.destroy'],
            ]"
        />
    </div>
</div>
@endsection

