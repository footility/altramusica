@extends('layouts.admin')

@section('title', 'Modifica Strumento')
@section('page-title', 'Modifica: ' . $instrument->type)

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.instruments.update', $instrument) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="type" 
                        label="Tipo Strumento" 
                        value="{{ old('type', $instrument->type) }}"
                        required
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="status" 
                        label="Stato" 
                        type="select"
                        :options="[
                            'available' => 'Disponibile',
                            'rented' => 'Noleggiato',
                            'sold' => 'Venduto',
                            'maintenance' => 'In Manutenzione',
                            'retired' => 'Ritirato',
                        ]"
                        value="{{ old('status', $instrument->status) }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="brand" 
                        label="Marca" 
                        value="{{ old('brand', $instrument->brand) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="model" 
                        label="Modello" 
                        value="{{ old('model', $instrument->model) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="size" 
                        label="Misura" 
                        value="{{ old('size', $instrument->size) }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="serial_number" 
                        label="Numero Seriale" 
                        value="{{ old('serial_number', $instrument->serial_number) }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="condition" 
                        label="Condizioni" 
                        type="select"
                        :options="[
                            'excellent' => 'Eccellenti',
                            'good' => 'Buone',
                            'fair' => 'Discrete',
                            'poor' => 'Povere',
                        ]"
                        value="{{ old('condition', $instrument->condition) }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="supplier" 
                        label="Fornitore" 
                        value="{{ old('supplier', $instrument->supplier) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="purchase_date" 
                        label="Data Acquisto" 
                        type="date"
                        value="{{ old('purchase_date', $instrument->purchase_date?->format('Y-m-d')) }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="purchase_price" 
                        label="Prezzo Acquisto (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('purchase_price', $instrument->purchase_price) }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="current_value" 
                label="Valore Attuale (€)" 
                type="number"
                step="0.01"
                min="0"
                value="{{ old('current_value', $instrument->current_value) }}"
            />

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes', $instrument->notes) }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                <a href="{{ route('admin.instruments.show', $instrument) }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

