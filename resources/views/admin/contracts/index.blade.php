@extends('layouts.admin')

@section('title', 'Contratti')
@section('page-title', 'Gestione Contratti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Contratti</h2>
    <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Contratto
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar 
            :route="route('admin.contracts.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca per numero, studente...', 'width' => 3],
                ['name' => 'academic_year_id', 'type' => 'select', 'options' => $years->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti gli anni', 'width' => 3, 'value' => $currentYear?->id],
                ['name' => 'student_id', 'type' => 'select', 'options' => $students->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti gli studenti', 'width' => 3],
                ['name' => 'status', 'type' => 'select', 'options' => ['draft' => 'Bozza', 'sent' => 'Inviato', 'signed' => 'Firmato', 'expired' => 'Scaduto', 'cancelled' => 'Cancellato'], 'placeholder' => 'Tutti gli stati', 'width' => 3],
                ['name' => 'type', 'type' => 'select', 'options' => ['regular' => 'Regolare', 'short' => 'Breve', 'summer' => 'Estivo', 'instrument_rental' => 'Noleggio Strumento'], 'placeholder' => 'Tutti i tipi', 'width' => 3],
            ]"
        />

        <x-admin.data-table 
            :items="$contracts"
            :columns="[
                ['key' => 'contract_number', 'label' => 'Numero'],
                ['key' => 'student.full_name', 'label' => 'Studente', 'relation' => 'student'],
                ['key' => 'type', 'label' => 'Tipo', 'format' => 'badge'],
                ['key' => 'start_date', 'label' => 'Data Inizio', 'format' => 'date'],
                ['key' => 'end_date', 'label' => 'Data Fine', 'format' => 'date'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.contracts.show'],
                ['type' => 'edit', 'route' => 'admin.contracts.edit'],
                ['type' => 'delete', 'route' => 'admin.contracts.destroy'],
            ]"
        />
    </div>
</div>
@endsection

