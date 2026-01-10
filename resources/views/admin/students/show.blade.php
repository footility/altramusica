@extends('layouts.admin')

@section('title', 'Dettaglio Studente')
@section('page-title', $student->full_name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Informazioni Anagrafiche</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Codice:</dt>
                    <dd class="col-sm-9">{{ $student->code ?? '-' }}</dd>

                    <dt class="col-sm-3">Nome:</dt>
                    <dd class="col-sm-9">{{ $student->first_name }}</dd>

                    <dt class="col-sm-3">Cognome:</dt>
                    <dd class="col-sm-9">{{ $student->last_name }}</dd>

                    <dt class="col-sm-3">Data Nascita:</dt>
                    <dd class="col-sm-9">{{ $student->birth_date ? $student->birth_date->format('d/m/Y') : '-' }}</dd>

                    <dt class="col-sm-3">Codice Fiscale:</dt>
                    <dd class="col-sm-9">{{ $student->tax_code ?? '-' }}</dd>

                    <dt class="col-sm-3">Stato:</dt>
                    <dd class="col-sm-9">
                        <span class="badge bg-{{ $student->status == 'enrolled' ? 'success' : ($student->status == 'interested' ? 'info' : 'secondary') }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </dd>

                    <dt class="col-sm-3">Anno Scolastico:</dt>
                    <dd class="col-sm-9">{{ $student->academicYear?->name ?? '-' }}</dd>

                    @if($student->school_origin)
                        <dt class="col-sm-3">Scuola di Provenienza:</dt>
                        <dd class="col-sm-9">{{ $student->school_origin }}</dd>
                    @endif

                    @if($student->how_know_us)
                        <dt class="col-sm-3">Come ci ha conosciuto:</dt>
                        <dd class="col-sm-9">{{ $student->how_know_us }}</dd>
                    @endif

                    @if($student->last_contact_date)
                        <dt class="col-sm-3">Data Ultimo Contatto:</dt>
                        <dd class="col-sm-9">{{ $student->last_contact_date->format('d/m/Y') }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($student->guardians->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h5>Genitori/Tutori</h5>
            </div>
            <div class="card-body">
                @foreach($student->guardians as $guardian)
                    <div class="border-bottom pb-2 mb-2">
                        <strong>{{ $guardian->first_name }} {{ $guardian->last_name }}</strong>
                        @if($guardian->pivot->is_primary)
                            <span class="badge bg-primary">Primario</span>
                        @endif
                        @if($guardian->pivot->is_billing_contact)
                            <span class="badge bg-info">Contatto Fatturazione</span>
                        @endif
                        <br>
                        <small class="text-muted">
                            {{ $guardian->email_1 ?? '' }} | {{ $guardian->cell_1 ?? '' }}
                        </small>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($student->enrollments->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h5>Iscrizioni</h5>
            </div>
            <div class="card-body">
                <x-admin.data-table 
                    :items="$student->enrollments"
                    :columns="[
                        ['key' => 'course.name', 'label' => 'Corso', 'relation' => 'course'],
                        ['key' => 'start_date', 'label' => 'Data Inizio', 'format' => 'date'],
                        ['key' => 'end_date', 'label' => 'Data Fine', 'format' => 'date'],
                        ['key' => 'status', 'label' => 'Stato', 'format' => 'badge'],
                        ['key' => 'total_amount', 'label' => 'Totale', 'format' => 'currency'],
                    ]"
                />
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.enrollments.create', ['student_id' => $student->id]) }}" class="btn btn-success w-100 mb-2">Nuova Iscrizione</a>
                <a href="{{ route('admin.contracts.create', ['student_id' => $student->id]) }}" class="btn btn-info w-100 mb-2">Nuovo Contratto</a>
                <a href="{{ route('admin.invoices.create', ['student_id' => $student->id]) }}" class="btn btn-warning w-100 mb-2">Nuova Fattura</a>
            </div>
        </div>

        @if($student->notes || $student->admin_notes)
        <div class="card mb-3">
            <div class="card-header">
                <h5>Note</h5>
            </div>
            <div class="card-body">
                @if($student->notes)
                    <p><strong>Note:</strong><br>{{ $student->notes }}</p>
                @endif
                @if($student->admin_notes)
                    <p><strong>Note Amministrative:</strong><br>{{ $student->admin_notes }}</p>
                @endif
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5>Privacy</h5>
            </div>
            <div class="card-body">
                <p>
                    <strong>Consenso Privacy:</strong> 
                    <span class="badge bg-{{ $student->privacy_consent ? 'success' : 'danger' }}">
                        {{ $student->privacy_consent ? 'Sì' : 'No' }}
                    </span>
                </p>
                <p>
                    <strong>Consenso Foto:</strong> 
                    <span class="badge bg-{{ $student->photo_consent ? 'success' : 'danger' }}">
                        {{ $student->photo_consent ? 'Sì' : 'No' }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
