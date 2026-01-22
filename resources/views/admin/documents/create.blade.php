@extends('layouts.admin')

@section('title', 'Carica Documento')
@section('page-title', 'Carica Documento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Carica Documento</h2>
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Indietro
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <x-admin.form-field
                        name="type"
                        label="Tipo"
                        type="select"
                        :options="$types"
                        :value="old('type', 'other')"
                        required="true"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field
                        name="student_id"
                        label="Studente (opzionale)"
                        type="select"
                        :options="$students->pluck('full_name','id')->toArray()"
                        :value="old('student_id', $preselectedStudent?->id)"
                    />
                </div>
                <div class="col-md-4">
                    <x-admin.form-field
                        name="contract_id"
                        label="Contratto (opzionale)"
                        type="select"
                        :options="$contracts->pluck('contract_number','id')->toArray()"
                        :value="old('contract_id', $preselectedContract?->id)"
                    />
                </div>
            </div>

            <div class="mb-3">
                <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" id="file" required>
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Max 20MB.</div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Carica
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

