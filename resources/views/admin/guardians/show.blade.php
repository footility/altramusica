@extends('layouts.admin')

@section('title', 'Dettaglio Genitore/Tutore')
@section('page-title', $guardian->full_name)

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
                    <dd class="col-sm-9">{{ $guardian->first_name }}</dd>

                    <dt class="col-sm-3">Cognome:</dt>
                    <dd class="col-sm-9">{{ $guardian->last_name }}</dd>

                    <dt class="col-sm-3">Codice Fiscale:</dt>
                    <dd class="col-sm-9">{{ $guardian->tax_code ?? '-' }}</dd>

                    <dt class="col-sm-3">Relazione:</dt>
                    <dd class="col-sm-9">{{ ucfirst($guardian->relationship ?? '-') }}</dd>

                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9">{{ $guardian->email_1 ?? '-' }}</dd>

                    <dt class="col-sm-3">Cellulare:</dt>
                    <dd class="col-sm-9">{{ $guardian->cell_1 ?? '-' }}</dd>

                    @if($guardian->address)
                        <dt class="col-sm-3">Indirizzo:</dt>
                        <dd class="col-sm-9">{{ $guardian->address }}, {{ $guardian->city }} {{ $guardian->postal_code }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($guardian->students->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5>Studenti Associati</h5>
            </div>
            <div class="card-body">
                <x-admin.data-table 
                    :items="$guardian->students"
                    :columns="[
                        ['key' => 'first_name', 'label' => 'Nome'],
                        ['key' => 'last_name', 'label' => 'Cognome'],
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
                <a href="{{ route('admin.guardians.edit', $guardian) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.guardians.index') }}" class="btn btn-secondary w-100">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection
