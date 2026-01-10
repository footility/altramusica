@extends('layouts.admin')

@section('title', 'Genera Proposte Orarie')
@section('page-title', 'Genera Proposte Orarie')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.schedule-proposals.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="student_ids" class="form-label">Studenti <span class="text-danger">*</span></label>
                <select name="student_ids[]" id="student_ids" class="form-select @error('student_ids') is-invalid @enderror" multiple size="10" required>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ in_array($s->id, old('student_ids', $selectedStudents)) ? 'selected' : '' }}>
                            {{ $s->last_name }} {{ $s->first_name }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Tieni premuto Ctrl (o Cmd su Mac) per selezionare più studenti</small>
                @error('student_ids')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="course_id" class="form-label">Corso (opzionale)</label>
                    <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror">
                        <option value="">Nessun corso specifico</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="teacher_id" class="form-label">Docente (opzionale)</label>
                    <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                        <option value="">Qualsiasi docente disponibile</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->last_name }} {{ $t->first_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Note</label>
                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Il sistema genererà automaticamente proposte orarie basate sulle disponibilità degli studenti e dei docenti selezionati.
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.schedule-proposals.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Genera Proposte</button>
            </div>
        </form>
    </div>
</div>
@endsection

