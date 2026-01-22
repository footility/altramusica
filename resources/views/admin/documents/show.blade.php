@extends('layouts.admin')

@section('title', 'Dettaglio Documento')
@section('page-title', 'Dettaglio Documento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Documento</h2>
    <div class="d-flex gap-2">
        @if($publicUrl)
            <a href="{{ $publicUrl }}" class="btn btn-outline-primary" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right"></i> Apri
            </a>
        @endif
        <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Elenco
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Tipo</dt>
            <dd class="col-sm-9">{{ $document->type }}</dd>

            <dt class="col-sm-3">File</dt>
            <dd class="col-sm-9">{{ $document->file_name }}</dd>

            <dt class="col-sm-3">Studente</dt>
            <dd class="col-sm-9">
                @if($document->student)
                    <a href="{{ route('admin.students.show', $document->student) }}">{{ $document->student->full_name }}</a>
                @else
                    -
                @endif
            </dd>

            <dt class="col-sm-3">Contratto</dt>
            <dd class="col-sm-9">
                @if($document->contract)
                    <a href="{{ route('admin.contracts.show', $document->contract) }}">{{ $document->contract->contract_number }}</a>
                @else
                    -
                @endif
            </dd>

            <dt class="col-sm-3">Caricato da</dt>
            <dd class="col-sm-9">{{ $document->uploadedBy->name ?? '-' }}</dd>

            <dt class="col-sm-3">Dimensione</dt>
            <dd class="col-sm-9">{{ $document->size ? number_format($document->size / 1024, 0, ',', '.') . ' KB' : '-' }}</dd>

            <dt class="col-sm-3">Mime</dt>
            <dd class="col-sm-9">{{ $document->mime_type ?? '-' }}</dd>

            <dt class="col-sm-3">Path</dt>
            <dd class="col-sm-9"><code>{{ $document->file_path }}</code></dd>
        </dl>
    </div>
</div>
@endsection

