@extends('layouts.admin')

@section('title', 'Nuova Comunicazione')
@section('page-title', 'Nuova Comunicazione')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.communications.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="type" class="form-label">Tipo Comunicazione <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required onchange="toggleSubject()">
                    <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="sms" {{ old('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="whatsapp" {{ old('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="student_id" class="form-label">Studente</label>
                    <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror">
                        <option value="">Seleziona studente</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ old('student_id', $student?->id) == $s->id ? 'selected' : '' }}>
                                {{ $s->last_name }} {{ $s->first_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="guardian_id" class="form-label">Genitore</label>
                    <select name="guardian_id" id="guardian_id" class="form-select @error('guardian_id') is-invalid @enderror">
                        <option value="">Seleziona genitore</option>
                        @foreach($guardians as $g)
                            <option value="{{ $g->id }}" {{ old('guardian_id', $guardian?->id) == $g->id ? 'selected' : '' }}>
                                {{ $g->last_name }} {{ $g->first_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3" id="subject-field">
                <label for="subject" class="form-label">Oggetto <span class="text-danger">*</span></label>
                <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}">
                @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="message" class="form-label">Messaggio <span class="text-danger">*</span></label>
                <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="6" required>{{ old('message') }}</textarea>
                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="template_name" class="form-label">Nome Template (opzionale)</label>
                <input type="text" name="template_name" id="template_name" class="form-control @error('template_name') is-invalid @enderror" value="{{ old('template_name') }}" placeholder="Es: Convocazione prova">
                @error('template_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.communications.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Invia</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleSubject() {
    const type = document.getElementById('type').value;
    const subjectField = document.getElementById('subject-field');
    const subjectInput = document.getElementById('subject');
    
    if (type === 'email') {
        subjectField.style.display = 'block';
        subjectInput.required = true;
    } else {
        subjectField.style.display = 'none';
        subjectInput.required = false;
    }
}

// Esegui al caricamento
document.addEventListener('DOMContentLoaded', toggleSubject);
</script>
@endpush
@endsection

