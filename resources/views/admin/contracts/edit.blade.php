@extends('layouts.admin')

@section('title', 'Modifica Contratto')
@section('page-title', 'Modifica Contratto')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.contracts.update', $contract) }}" method="POST">
            @csrf
            @method('PUT')

            <x-admin.form-field 
                name="academic_year_id" 
                label="Anno Accademico" 
                type="select"
                :options="$years->pluck('name', 'id')->toArray()"
                value="{{ old('academic_year_id', $contract->academic_year_id) }}"
            />

            <x-admin.form-field 
                name="student_id" 
                label="Studente" 
                type="select"
                :options="$students->pluck('full_name', 'id')->toArray()"
                value="{{ old('student_id', $contract->student_id) }}"
                required
            />

            <div class="row">
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="contract_number" 
                        label="Numero Contratto" 
                        value="{{ old('contract_number', $contract->contract_number) }}"
                    />
                </div>
                <div class="col-md-6">
                    <x-admin.form-field 
                        name="type" 
                        label="Tipo Contratto" 
                        type="select"
                        :options="[
                            'regular' => 'Regolare',
                            'short' => 'Breve',
                            'summer' => 'Estivo',
                            'instrument_rental' => 'Noleggio Strumento',
                        ]"
                        value="{{ old('type', $contract->type) }}"
                        required
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="start_date" 
                        label="Data Inizio" 
                        type="date"
                        value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="end_date" 
                        label="Data Fine" 
                        type="date"
                        value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="status" 
                        label="Stato" 
                        type="select"
                        :options="[
                            'draft' => 'Bozza',
                            'sent' => 'Inviato',
                            'signed' => 'Firmato',
                            'expired' => 'Scaduto',
                            'cancelled' => 'Cancellato',
                        ]"
                        value="{{ old('status', $contract->status) }}"
                        required
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="terms" 
                label="Termini e Condizioni" 
                type="textarea"
                value="{{ old('terms', $contract->terms) }}"
            />

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes', $contract->notes) }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

