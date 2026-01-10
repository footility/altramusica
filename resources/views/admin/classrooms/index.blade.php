@extends('layouts.admin')

@section('title', 'Aule')
@section('page-title', 'Gestione Aule')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Aule</h2>
    <a href="{{ route('admin.classrooms.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuova Aula
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.classrooms.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Disponibilità</label>
                <select name="available" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutte</option>
                    <option value="1" {{ request('available') == '1' ? 'selected' : '' }}>Disponibili</option>
                    <option value="0" {{ request('available') == '0' ? 'selected' : '' }}>Non Disponibili</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.data-table 
            :items="$classrooms"
            :columns="[
                ['key' => 'code', 'label' => 'Codice'],
                ['key' => 'name', 'label' => 'Nome'],
                ['key' => 'capacity', 'label' => 'Capacità'],
                ['key' => 'available', 'label' => 'Disponibile', 'format' => 'badge', 'badge_class' => 'bg-success', 'badge_true' => 'Sì', 'badge_false' => 'No'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.classrooms.show'],
                ['type' => 'edit', 'route' => 'admin.classrooms.edit'],
                ['type' => 'delete', 'route' => 'admin.classrooms.destroy', 'confirm' => 'Sei sicuro di voler eliminare questa aula?'],
            ]"
        />
    </div>
</div>
@endsection

