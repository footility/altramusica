@extends('layouts.admin')

@section('title', 'Modifica Disponibilità Docente')
@section('page-title', 'Modifica Disponibilità Docente')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.teacher-availability.update', $teacherAvailability) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="teacher_id" class="form-label">Docente <span class="text-danger">*</span></label>
                <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                    <option value="">Seleziona docente</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ old('teacher_id', $teacherAvailability->teacher_id) == $t->id ? 'selected' : '' }}>
                            {{ $t->last_name }} {{ $t->first_name }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="day_of_week" class="form-label">Giorno <span class="text-danger">*</span></label>
                <select name="day_of_week" id="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>
                    <option value="">Seleziona giorno</option>
                    <option value="monday" {{ old('day_of_week', $teacherAvailability->day_of_week) == 'monday' ? 'selected' : '' }}>Lunedì</option>
                    <option value="tuesday" {{ old('day_of_week', $teacherAvailability->day_of_week) == 'tuesday' ? 'selected' : '' }}>Martedì</option>
                    <option value="wednesday" {{ old('day_of_week', $teacherAvailability->day_of_week) == 'wednesday' ? 'selected' : '' }}>Mercoledì</option>
                    <option value="thursday" {{ old('day_of_week', $teacherAvailability->day_of_week) == 'thursday' ? 'selected' : '' }}>Giovedì</option>
                    <option value="friday" {{ old('day_of_week', $teacherAvailability->day_of_week) == 'friday' ? 'selected' : '' }}>Venerdì</option>
                    <option value="saturday" {{ old('day_of_week', $teacherAvailability->day_of_week) == 'saturday' ? 'selected' : '' }}>Sabato</option>
                    <option value="sunday" {{ old('day_of_week', $teacherAvailability->day_of_week) == 'sunday' ? 'selected' : '' }}>Domenica</option>
                </select>
                @error('day_of_week')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="time_start" class="form-label">Orario Inizio</label>
                    <input type="time" name="time_start" id="time_start" class="form-control @error('time_start') is-invalid @enderror" value="{{ old('time_start', $teacherAvailability->time_start ? $teacherAvailability->time_start->format('H:i') : '') }}">
                    @error('time_start')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="time_end" class="form-label">Orario Fine</label>
                    <input type="time" name="time_end" id="time_end" class="form-control @error('time_end') is-invalid @enderror" value="{{ old('time_end', $teacherAvailability->time_end ? $teacherAvailability->time_end->format('H:i') : '') }}">
                    @error('time_end')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="available" id="available" class="form-check-input" value="1" {{ old('available', $teacherAvailability->available) ? 'checked' : '' }}>
                    <label for="available" class="form-check-label">Disponibile</label>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Note</label>
                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $teacherAvailability->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.teacher-availability.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Salva</button>
            </div>
        </form>
    </div>
</div>
@endsection

