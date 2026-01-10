@extends('layouts.admin')

@section('title', 'Nuova Attività Extra')
@section('page-title', 'Nuova Attività Extra')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.extra-activities.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">Codice</label>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descrizione</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="orchestra" {{ old('type') == 'orchestra' ? 'selected' : '' }}>Orchestra</option>
                        <option value="choir" {{ old('type') == 'choir' ? 'selected' : '' }}>Coro</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Altro</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="teacher_id" class="form-label">Docente</label>
                    <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                        <option value="">Nessun docente</option>
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
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Data Inizio <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Data Fine</label>
                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="day_of_week" class="form-label">Giorno</label>
                    <select name="day_of_week" id="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror">
                        <option value="">Nessun giorno</option>
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
                <div class="col-md-4 mb-3">
                    <label for="time_start" class="form-label">Orario Inizio</label>
                    <input type="time" name="time_start" id="time_start" class="form-control @error('time_start') is-invalid @enderror" value="{{ old('time_start') }}">
                    @error('time_start')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="time_end" class="form-label">Orario Fine</label>
                    <input type="time" name="time_end" id="time_end" class="form-control @error('time_end') is-invalid @enderror" value="{{ old('time_end') }}">
                    @error('time_end')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Prezzo</label>
                    <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="max_participants" class="form-label">Max Partecipanti</label>
                    <input type="number" name="max_participants" id="max_participants" class="form-control @error('max_participants') is-invalid @enderror" value="{{ old('max_participants') }}" min="1">
                    @error('max_participants')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Stato <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Attiva</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inattiva</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.extra-activities.index') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Salva</button>
            </div>
        </form>
    </div>
</div>
@endsection

