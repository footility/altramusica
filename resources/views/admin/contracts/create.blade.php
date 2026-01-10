@extends('layouts.admin')

@section('title', 'Nuovo Contratto')
@section('page-title', 'Crea Nuovo Contratto')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.contracts.store') }}" method="POST">
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
                        name="contract_number" 
                        label="Numero Contratto" 
                        value="{{ old('contract_number') }}"
                        placeholder="Lasciare vuoto per generazione automatica"
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
                        value="{{ old('type', 'regular') }}"
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
                        value="{{ old('start_date') }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field 
                        name="end_date" 
                        label="Data Fine" 
                        type="date"
                        value="{{ old('end_date') }}"
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
                        value="{{ old('status', 'draft') }}"
                        required
                    />
                </div>
            </div>

            <x-admin.form-field 
                name="terms" 
                label="Termini e Condizioni" 
                type="textarea"
                value="{{ old('terms') }}"
            />

            <x-admin.form-field 
                name="notes" 
                label="Note" 
                type="textarea"
                value="{{ old('notes') }}"
            />

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Salva</button>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

