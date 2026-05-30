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
                    <dd class="col-sm-9">{{ $guardian->relationship_label }}</dd>

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

        {{-- R13 — Invito all'area famiglie (token monouso) --}}
        <div class="card mt-3">
            <div class="card-header">Area famiglie</div>
            <div class="card-body">
                @if($guardian->user)
                    <p class="text-success small mb-2">Accesso attivo ({{ $guardian->user->email }}).</p>
                @endif
                @if($guardian->privacy_consent && $guardian->primary_email)
                    <form method="POST" action="{{ route('admin.guardians.invite', $guardian) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary w-100">
                            Invita all'area famiglie
                        </button>
                    </form>
                    <p class="text-muted small mt-2 mb-0">Genera un link di attivazione (scade in 7 giorni) per {{ $guardian->primary_email }}.</p>
                @else
                    <p class="text-muted small mb-0">Per invitare il tutore servono consenso privacy ed email valida.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
