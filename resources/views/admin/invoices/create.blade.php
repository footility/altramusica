@extends('layouts.admin')

@section('title', 'Nuova Fattura')
@section('page-title', 'Crea Nuova Fattura')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.invoices.store') }}" method="POST">
            @csrf

            <x-admin.form-field 
                name="academic_year_id" 
                label="Anno Accademico" 
                type="select"
                :options="$years->pluck('name', 'id')->toArray()"
                value="{{ old('academic_year_id', $currentYear?->id) }}"
            />

            <x-admin.form-field 
                name="student_id" 
                label="Studente" 
                type="select"
                :options="$students->pluck('full_name', 'id')->toArray()"
                value="{{ old('student_id', $preselectedStudent?->id) }}"
                required
            />

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="invoice_number" 
                        label="Numero Fattura" 
                        value="{{ old('invoice_number') }}"
                        placeholder="Lasciare vuoto per generazione automatica"
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
                        value="{{ old('status', 'draft') }}"
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
                        value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="due_date" 
                        label="Data Scadenza" 
                        type="date"
                        value="{{ old('due_date') }}"
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
                        value="{{ old('subtotal', 0) }}"
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
                        value="{{ old('discount_amount', 0) }}"
                    />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field 
                        name="tax_amount" 
                        label="IVA (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('tax_amount', 0) }}"
                    />
                </div>
                <div class="col-md-3">
                    <x-admin.form-field 
                        name="total_amount" 
                        label="Totale (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('total_amount', 0) }}"
                        required
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="payment_terms" 
                label="Termini di Pagamento" 
                type="textarea"
                value="{{ old('payment_terms') }}"
            />

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes') }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

