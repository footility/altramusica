@extends('layouts.admin')

@section('title', 'Dettaglio Fattura')
@section('page-title', 'Fattura #' . $invoice->invoice_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Informazioni Fattura</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Numero:</dt>
                    <dd class="col-sm-9">{{ $invoice->invoice_number }}</dd>

                    <dt class="col-sm-3">Studente:</dt>
                    <dd class="col-sm-9">{{ $invoice->student->full_name }}</dd>

                    <dt class="col-sm-3">Anno Accademico:</dt>
                    <dd class="col-sm-9">{{ $invoice->academicYear->name ?? '-' }}</dd>

                    <dt class="col-sm-3">Data Fattura:</dt>
                    <dd class="col-sm-9">{{ $invoice->invoice_date->format('d/m/Y') }}</dd>

                    <dt class="col-sm-3">Scadenza:</dt>
                    <dd class="col-sm-9">{{ $invoice->due_date->format('d/m/Y') }}</dd>

                    <dt class="col-sm-3">Subtotale:</dt>
                    <dd class="col-sm-9">€ {{ number_format($invoice->subtotal, 2, ',', '.') }}</dd>

                    @if($invoice->discount_amount > 0)
                    <dt class="col-sm-3">Sconto:</dt>
                    <dd class="col-sm-9">€ {{ number_format($invoice->discount_amount, 2, ',', '.') }}</dd>
                    @endif

                    @if($invoice->tax_amount > 0)
                    <dt class="col-sm-3">IVA:</dt>
                    <dd class="col-sm-9">€ {{ number_format($invoice->tax_amount, 2, ',', '.') }}</dd>
                    @endif

                    <dt class="col-sm-3">Totale:</dt>
                    <dd class="col-sm-9"><strong>€ {{ number_format($invoice->total_amount, 2, ',', '.') }}</strong></dd>

                    <dt class="col-sm-3">Pagato:</dt>
                    <dd class="col-sm-9">€ {{ number_format($invoice->paid_amount, 2, ',', '.') }}</dd>

                    <dt class="col-sm-3">Residuo:</dt>
                    <dd class="col-sm-9">
                        <strong class="text-{{ $invoice->remaining_amount > 0 ? 'danger' : 'success' }}">
                            € {{ number_format($invoice->remaining_amount, 2, ',', '.') }}
                        </strong>
                    </dd>

                    <dt class="col-sm-3">Stato:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'overdue' ? 'danger' : 'warning') }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </dd>

                    @if($invoice->payment_terms)
                    <dt class="col-sm-3">Termini:</dt>
                    <dd class="col-sm-9">{{ $invoice->payment_terms }}</dd>
                    @endif

                    @if($invoice->notes)
                    <dt class="col-sm-3">Note:</dt>
                    <dd class="col-sm-9">{{ $invoice->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($invoice->payments->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h5>Pagamenti ({{ $invoice->payments->count() }})</h5>
            </div>
            <div class="card-body">
                <x-admin.data-table 
                    :items="$invoice->payments"
                    :columns="[
                        ['key' => 'payment_date', 'label' => 'Data', 'format' => 'date'],
                        ['key' => 'amount', 'label' => 'Importo', 'format' => 'currency'],
                        ['key' => 'payment_method', 'label' => 'Metodo'],
                    ]"
                />
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary w-100">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection

