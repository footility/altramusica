@extends('layouts.admin')

@section('title', 'Attività Extra')
@section('page-title', 'Gestione Attività Extra')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Attività Extra</h2>
    <a href="{{ route('admin.extra-activities.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuova Attività
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.extra-activities.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti i tipi</option>
                    <option value="orchestra" {{ request('type') == 'orchestra' ? 'selected' : '' }}>Orchestra</option>
                    <option value="choir" {{ request('type') == 'choir' ? 'selected' : '' }}>Coro</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Altro</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Stato</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Attive</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inattive</option>
                    <option value="" {{ !request('status') ? 'selected' : '' }}>Tutte</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @php
            $columns = [
                ['key' => 'code', 'label' => 'Codice'],
                ['key' => 'name', 'label' => 'Nome'],
                ['key' => 'type', 'label' => 'Tipo', 'format' => 'badge', 'badge_class' => 'bg-primary', 'badge_map' => ['orchestra' => 'Orchestra', 'choir' => 'Coro', 'other' => 'Altro']],
                ['key' => 'teacher.full_name', 'label' => 'Docente', 'format' => 'custom', 'custom' => function($item) { return $item->teacher ? $item->teacher->last_name . ' ' . $item->teacher->first_name : '-'; }],
                ['key' => 'start_date', 'label' => 'Data Inizio', 'format' => 'date'],
                ['key' => 'price', 'label' => 'Prezzo', 'format' => 'custom', 'custom' => function($item) { return $item->price ? '€ ' . number_format($item->price, 2) : '-'; }],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge', 'badge_class' => 'bg-success', 'badge_map' => ['active' => 'Attiva', 'inactive' => 'Inattiva']],
            ];
        @endphp
        
        <x-admin.data-table 
            :items="$activities"
            :columns="$columns"
            :actions="[
                ['type' => 'show', 'route' => 'admin.extra-activities.show'],
                ['type' => 'edit', 'route' => 'admin.extra-activities.edit'],
                ['type' => 'delete', 'route' => 'admin.extra-activities.destroy', 'confirm' => 'Sei sicuro di voler eliminare questa attività?'],
            ]"
        />
    </div>
</div>
@endsection

