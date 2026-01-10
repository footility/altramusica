@extends('layouts.admin')

@section('title', 'Anni Scolastici')
@section('page-title', 'Gestione Anni Scolastici')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Elenco Anni Scolastici</h2>
    <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuovo Anno Scolastico
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.data-table 
            :items="$years"
            :columns="[
                ['key' => 'name', 'label' => 'Nome'],
                ['key' => 'start_date', 'label' => 'Data Inizio', 'format' => 'date'],
                ['key' => 'end_date', 'label' => 'Data Fine', 'format' => 'date'],
                ['key' => 'is_active', 'label' => 'Stato', 'format' => 'badge', 'badge_class' => 'bg-success'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.academic-years.show'],
                ['type' => 'edit', 'route' => 'admin.academic-years.edit'],
                ['type' => 'delete', 'route' => 'admin.academic-years.destroy'],
            ]"
        />
    </div>
</div>
@endsection

