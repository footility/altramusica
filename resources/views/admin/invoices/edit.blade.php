@extends('layouts.admin')

@section('title', 'Modifica Fattura')
@section('page-title', 'Modifica Fattura')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST">
            @csrf
            @method('PUT')

            <x-admin.form-field 
                name="academic_year_id" 
                label="Anno Accademico" 
                type="select"
                :options="$years->pluck('name', 'id')->toArray()"
                value="{{ old('academic_year_id', $invoice->academic_year_id) }}"
            />

            <x-admin.form-field 
                name="student_id" 
                label="Studente" 
                type="select"
                :options="$students->pluck('full_name', 'id')->toArray()"
                value="{{ old('student_id', $invoice->student_id) }}"
                required
            />

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="invoice_number" 
                        label="Numero Fattura" 
                        value="{{ old('invoice_number', $invoice->invoice_number) }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="status" 
                        label="Stato" 
                        type="select"
                        :options="[
                            'draft' => 'Bozza',
                            'sent' => 'Inviata',
                            'paid' => 'Pagata',
                            'overdue' => 'Scaduta',
                            'cancelled' => 'Cancellata',
                        ]"
                        value="{{ old('status', $invoice->status) }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="invoice_date" 
                        label="Data Fattura" 
                        type="date"
                        value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="due_date" 
                        label="Data Scadenza" 
                        type="date"
                        value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <x-admin.form-field 
                        name="subtotal" 
                        label="Subtotale (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('subtotal', $invoice->subtotal) }}"
                        required
                    />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field 
                        name="discount_amount" 
                        label="Sconto (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('discount_amount', $invoice->discount_amount) }}"
                    />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field 
                        name="tax_amount" 
                        label="IVA (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('tax_amount', $invoice->tax_amount) }}"
                    />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field 
                        name="total_amount" 
                        label="Totale (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('total_amount', $invoice->total_amount) }}"
                        required
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="payment_terms" 
                label="Termini di Pagamento" 
                type="textarea"
                value="{{ old('payment_terms', $invoice->payment_terms) }}"
            />

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes', $invoice->notes) }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

