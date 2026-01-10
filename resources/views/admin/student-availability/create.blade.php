@extends('layouts.admin')

@section('title', 'Nuova Disponibilità Studente')
@section('page-title', 'Nuova Disponibilità Studente')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.student-availability.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="student_id" class="form-label">Studente <span class="text-danger">*</span></label>
                <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
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
            
            <div class="mb-3">
                <label for="day_of_week" class="form-label">Giorno <span class="text-danger">*</span></label>
                <select name="day_of_week" id="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>
                    <option value="">Seleziona giorno</option>
                    <option value="monday" {{ old('day_of_week') == 'monday' ? 'selected' : '' }}>Lunedì</option>
                    <option value="tuesday" {{ old('day_of_week') == 'tuesday' ? 'selected' : '' }}>Martedì</option>
                    <option value="wednesday" {{ old('day_of_week') == 'wednesday' ? 'selected' : '' }}>Mercoledì</option>
                    <option value="thursday" {{ old('day_of_week') == 'thursday' ? 'selected' : '' }}>Giovedì</option>
                    <option value="friday" {{ old('day_of_week') == 'friday' ? 'selected' : '' }}>Venerdì</option>
                    <option value="saturday" {{ old('day_of_week') == 'saturday' ? 'selected' : '' }}>Sabato</option>
                    <option value="sunday" {{ old('day_of_week') == 'sunday' ? 'selected' : '' }}>Domenica</option>
                </select>
                @error('day_of_week')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="time_start" class="form-label">Orario Inizio</label>
                    <input type="time" name="time_start" id="time_start" class="form-control @error('time_start') is-invalid @enderror" value="{{ old('time_start') }}">
                    @error('time_start')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="time_end" class="form-label">Orario Fine</label>
                    <input type="time" name="time_end" id="time_end" class="form-control @error('time_end') is-invalid @enderror" value="{{ old('time_end') }}">
                    @error('time_end')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="available" id="available" class="form-check-input" value="1" {{ old('available', true) ? 'checked' : '' }}>
                    <label for="available" class="form-check-label">Disponibile</label>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Note</label>
                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.student-availability.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Salva</button>
            </div>
        </form>
    </div>
</div>
@endsection

