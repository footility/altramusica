@extends('layouts.admin')

@section('title', 'Primi Contatti')
@section('page-title', 'Gestione Primi Contatti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Primi Contatti</h2>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.first-contacts.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Stato</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>In Attesa</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Convertiti</option>
                    <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Scartati</option>
                    <option value="" {{ !request('status') ? 'selected' : '' }}>Tutti</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Cerca</label>
                <input type="text" name="search" class="form-control" placeholder="Nome, cognome, email, telefono..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Cerca</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.data-table 
            :items="$firstContacts"
            :columns="[
                ['key' => 'first_name', 'label' => 'Nome'],
                ['key' => 'last_name', 'label' => 'Cognome'],
                ['key' => 'birth_date', 'label' => 'Data Nascita', 'format' => 'date'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'phone', 'label' => 'Telefono'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge', 'badge_class' => 'bg-info', 'badge_map' => ['pending' => 'In Attesa', 'converted' => 'Convertito', 'dismissed' => 'Scartato']],
                ['key' => 'created_at', 'label' => 'Data', 'format' => 'date'],
            ]"
            :actions="[
                ['type' => 'show', 'route' => 'admin.first-contacts.show'],
            ]"
        />
    </div>
</div>
@endsection

