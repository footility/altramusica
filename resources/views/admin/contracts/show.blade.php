@extends('layouts.admin')

@section('title', 'Dettaglio Contratto')
@section('page-title', 'Contratto #' . $contract->contract_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Informazioni Contratto</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Numero:</dt>
                    <dd class="col-sm-9">{{ $contract->contract_number }}</dd>

                    <dt class="col-sm-3">Studente:</dt>
                    <dd class="col-sm-9">{{ $contract->student->full_name }}</dd>

                    <dt class="col-sm-3">Anno Accademico:</dt>
                    <dd class="col-sm-9">{{ $contract->academicYear->name ?? '-' }}</dd>

                    <dt class="col-sm-3">Tipo:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-info">{{ ucfirst($contract->type) }}</span>
                    </dd>

                    <dt class="col-sm-3">Periodo:</dt>
                    <dd class="col-sm-9">
                        {{ $contract->start_date->format('d/m/Y') }} - 
                        {{ $contract->end_date->format('d/m/Y') }}
                    </dd>

                    <dt class="col-sm-3">Stato:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-{{ $contract->status == 'signed' ? 'success' : ($contract->status == 'sent' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($contract->status) }}
                        </span>
                    </dd>

                    @if($contract->sent_date)
                    <dt class="col-sm-3">Data Invio:</dt>
                    <dd class="col-sm-9">{{ $contract->sent_date->format('d/m/Y H:i') }}</dd>
                    @endif

                    @if($contract->signed_date)
                    <dt class="col-sm-3">Data Firma:</dt>
                    <dd class="col-sm-9">{{ $contract->signed_date->format('d/m/Y H:i') }}</dd>
                    @endif

                    @if($contract->token)
                    <dt class="col-sm-3">Link Precompilato:</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('contracts.public.show', $contract->token) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Visualizza Link
                        </a>
                    </dd>
                    @endif

                    @if($contract->terms)
                    <dt class="col-sm-3">Termini:</dt>
                    <dd class="col-sm-9">{{ $contract->terms }}</dd>
                    @endif

                    @if($contract->notes)
                    <dt class="col-sm-3">Note:</dt>
                    <dd class="col-sm-9">{{ $contract->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($contract->status == 'draft')
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('admin.contracts.send', $contract) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">Invia Contratto</button>
                </form>
            </div>
        </div>
        @endif

        @if($contract->status == 'sent')
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('admin.contracts.sign', $contract) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Segna come Firmato</button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.contracts.edit', $contract) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary w-100">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection

