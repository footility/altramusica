@extends('layouts.admin')

@section('title', 'Dettaglio Strumento')
@section('page-title', $instrument->type)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Informazioni Strumento</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Tipo:</dt>
                    <dd class="col-sm-9">{{ $instrument->type }}</dd>

                    <dt class="col-sm-3">Marca:</dt>
                    <dd class="col-sm-9">{{ $instrument->brand ?? '-' }}</dd>

                    <dt class="col-sm-3">Modello:</dt>
                    <dd class="col-sm-9">{{ $instrument->model ?? '-' }}</dd>

                    <dt class="col-sm-3">Misura:</dt>
                    <dd class="col-sm-9">{{ $instrument->size ?? '-' }}</dd>

                    <dt class="col-sm-3">Numero Seriale:</dt>
                    <dd class="col-sm-9">{{ $instrument->serial_number ?? '-' }}</dd>

                    <dt class="col-sm-3">Condizioni:</dt>
                    <dd class="col-sm-9">{{ ucfirst($instrument->condition ?? '-') }}</dd>

                    <dt class="col-sm-3">Stato:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-{{ $instrument->status == 'available' ? 'success' : 'secondary' }}">
                            {{ ucfirst($instrument->status) }}
                        </span>
                    </dd>

                    @if($instrument->purchase_date)
                    <dt class="col-sm-3">Data Acquisto:</dt>
                    <dd class="col-sm-9">{{ $instrument->purchase_date->format('d/m/Y') }}</dd>
                    @endif

                    @if($instrument->purchase_price)
                    <dt class="col-sm-3">Prezzo Acquisto:</dt>
                    <dd class="col-sm-9">€ {{ number_format($instrument->purchase_price, 2, ',', '.') }}</dd>
                    @endif

                    @if($instrument->current_value)
                    <dt class="col-sm-3">Valore Attuale:</dt>
                    <dd class="col-sm-9">€ {{ number_format($instrument->current_value, 2, ',', '.') }}</dd>
                    @endif

                    @if($instrument->notes)
                    <dt class="col-sm-3">Note:</dt>
                    <dd class="col-sm-9">{{ $instrument->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($instrument->rentals->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5>Storico Noleggi ({{ $instrument->rentals->count() }})</h5>
            </div>
            <div class="card-body">
                <x-admin.data-table 
                    :items="$instrument->rentals"
                    :columns="[
                        ['key' => 'student.full_name', 'label' => 'Studente', 'relation' => 'student'],
                        ['key' => 'start_date', 'label' => 'Data Inizio', 'format' => 'date'],
                        ['key' => 'end_date', 'label' => 'Data Fine', 'format' => 'date'],
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
                <a href="{{ route('admin.instruments.edit', $instrument) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.instruments.index') }}" class="btn btn-secondary w-100">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection

