@extends('layouts.admin')

@section('title', 'Comunicazione Massiva')
@section('page-title', 'Comunicazione Massiva')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.communications.send-bulk') }}" method="POST">
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
            
            <div class="mb-3">
                <label for="student_ids" class="form-label">Studenti <span class="text-danger">*</span></label>
                <select name="student_ids[]" id="student_ids" class="form-select @error('student_ids') is-invalid @enderror" multiple size="10" required>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ in_array($s->id, old('student_ids', [])) ? 'selected' : '' }}>
                            {{ $s->last_name }} {{ $s->first_name }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Tieni premuto Ctrl (o Cmd su Mac) per selezionare più studenti. La comunicazione verrà inviata ai genitori degli studenti selezionati.</small>
                @error('student_ids')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
            
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Attenzione: questa operazione invierà la comunicazione a tutti i genitori degli studenti selezionati. Assicurati che il messaggio sia corretto.
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.communications.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Invia Comunicazione Massiva</button>
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

document.addEventListener('DOMContentLoaded', toggleSubject);
</script>
@endpush
@endsection

