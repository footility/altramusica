@extends('layouts.admin')

@section('title', 'Dettaglio Esame')
@section('page-title', 'Esame')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Informazioni Esame</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Studente:</dt>
                    <dd class="col-sm-9">{{ $exam->student->full_name }}</dd>

                    <dt class="col-sm-3">Tipo Esame:</dt>
                    <dd class="col-sm-9">{{ strtoupper($exam->exam_type) }}</dd>

                    <dt class="col-sm-3">Livello:</dt>
                    <dd class="col-sm-9">{{ $exam->level }}</dd>

                    <dt class="col-sm-3">Materia:</dt>
                    <dd class="col-sm-9">{{ $exam->subject }}</dd>

                    <dt class="col-sm-3">Data Esame:</dt>
                    <dd class="col-sm-9">{{ $exam->exam_date->format('d/m/Y') }}</dd>

                    @if($exam->registration_date)
                    <dt class="col-sm-3">Data Registrazione:</dt>
                    <dd class="col-sm-9">{{ $exam->registration_date->format('d/m/Y') }}</dd>
                    @endif

                    @if($exam->registration_fee)
                    <dt class="col-sm-3">Tassa Registrazione:</dt>
                    <dd class="col-sm-9">€ {{ number_format($exam->registration_fee, 2, ',', '.') }}</dd>
                    @endif

                    <dt class="col-sm-3">Risultato:</dt>
                    <dd class="col-sm-9">
                        @if($exam->result)
                        <span class="badge bg-{{ $exam->result == 'passed' ? 'success' : ($exam->result == 'failed' ? 'danger' : 'warning') }}">
                            {{ ucfirst($exam->result) }}
                        </span>
                        @else
                        -
                        @endif
                    </dd>

                    @if($exam->grade)
                    <dt class="col-sm-3">Voto:</dt>
                    <dd class="col-sm-9">{{ $exam->grade }}</dd>
                    @endif

                    @if($exam->certificate_number)
                    <dt class="col-sm-3">Numero Certificato:</dt>
                    <dd class="col-sm-9">{{ $exam->certificate_number }}</dd>
                    @endif

                    @if($exam->notes)
                    <dt class="col-sm-3">Note:</dt>
                    <dd class="col-sm-9">{{ $exam->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-primary w-100 mb-2">Modifica</a>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary w-100">Torna all'elenco</a>
            </div>
        </div>
    </div>
</div>
@endsection

