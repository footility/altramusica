@extends('layouts.admin')

@section('title', 'Dettaglio Docente')
@section('page-title', $teacher->full_name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Informazioni</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Nome:</dt>
                    <dd class="col-sm-9">{{ $teacher->first_name }}</dd>

                    <dt class="col-sm-3">Cognome:</dt>
                    <dd class="col-sm-9">{{ $teacher->last_name }}</dd>

                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9">{{ $teacher->email }}</dd>

                    <dt class="col-sm-3">Telefono:</dt>
                    <dd class="col-sm-9">{{ $teacher->phone ?? '-' }}</dd>

                    <dt class="col-sm-3">Tipo Contratto:</dt>
                    <dd class="col-sm-9">{{ ucfirst(str_replace('_', ' ', $teacher->contract_type ?? '-')) }}</dd>

                    <dt class="col-sm-3">Stato:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-{{ $teacher->active ? 'success' : 'secondary' }}">
                            {{ $teacher->active ? 'Attivo' : 'Non Attivo' }}
                        </span>
                    </dd>

                    @if($teacher->user)
                        <dt class="col-sm-3">Account Utente:</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-info">Attivo</span>
                        </dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($teacher->courses->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h5>Corsi Assegnati</h5>
            </div>
            <div class="card-body">
                <x-admin.data-table 
                    :items="$teacher->courses"
                    :columns="[
                        ['key' => 'name', 'label' => 'Nome Corso'],
                        ['key' => 'day_of_week', 'label' => 'Giorno'],
                        ['key' => 'status', 'label' => 'Stato', 'format' => 'badge'],
                    ]"
                />
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary w-100">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection

