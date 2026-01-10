@extends('layouts.admin')

@section('title', 'Nuovo Strumento')
@section('page-title', 'Crea Nuovo Strumento')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.instruments.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="type" 
                        label="Tipo Strumento" 
                        value="{{ old('type') }}"
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
                        value="{{ old('status', 'available') }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="brand" 
                        label="Marca" 
                        value="{{ old('brand') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="model" 
                        label="Modello" 
                        value="{{ old('model') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="size" 
                        label="Misura" 
                        value="{{ old('size') }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="serial_number" 
                        label="Numero Seriale" 
                        value="{{ old('serial_number') }}"
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
                        value="{{ old('condition') }}"
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="supplier" 
                        label="Fornitore" 
                        value="{{ old('supplier') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="purchase_date" 
                        label="Data Acquisto" 
                        type="date"
                        value="{{ old('purchase_date') }}"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="purchase_price" 
                        label="Prezzo Acquisto (€)" 
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('purchase_price') }}"
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="current_value" 
                label="Valore Attuale (€)" 
                type="number"
                step="0.01"
                min="0"
                value="{{ old('current_value') }}"
            />

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes') }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.instruments.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

