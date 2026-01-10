@extends('layouts.admin')

@section('title', 'Disponibilità Studenti')
@section('page-title', 'Disponibilità Studenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Disponibilità Studenti</h2>
    <a href="{{ route('admin.student-availability.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuova Disponibilità
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.student-availability.index') }}" class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Studente</label>
                <select name="student_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti gli studenti</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->last_name }} {{ $s->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Giorno</label>
                <select name="day_of_week" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti i giorni</option>
                    <option value="monday" {{ request('day_of_week') == 'monday' ? 'selected' : '' }}>Lunedì</option>
                    <option value="tuesday" {{ request('day_of_week') == 'tuesday' ? 'selected' : '' }}>Martedì</option>
                    <option value="wednesday" {{ request('day_of_week') == 'wednesday' ? 'selected' : '' }}>Mercoledì</option>
                    <option value="thursday" {{ request('day_of_week') == 'thursday' ? 'selected' : '' }}>Giovedì</option>
                    <option value="friday" {{ request('day_of_week') == 'friday' ? 'selected' : '' }}>Venerdì</option>
                    <option value="saturday" {{ request('day_of_week') == 'saturday' ? 'selected' : '' }}>Sabato</option>
                    <option value="sunday" {{ request('day_of_week') == 'sunday' ? 'selected' : '' }}>Domenica</option>
                </select>
            </div>
        </form>

        @php
            $columns = [
                ['key' => 'student.full_name', 'label' => 'Studente', 'format' => 'custom', 'custom' => function($item) { return $item->student->last_name . ' ' . $item->student->first_name; }],
                ['key' => 'day_of_week', 'label' => 'Giorno', 'format' => 'custom', 'custom' => function($item) { 
                    $days = ['monday' => 'Lunedì', 'tuesday' => 'Martedì', 'wednesday' => 'Mercoledì', 'thursday' => 'Giovedì', 'friday' => 'Venerdì', 'saturday' => 'Sabato', 'sunday' => 'Domenica'];
                    return $days[$item->day_of_week] ?? $item->day_of_week;
                }],
                ['key' => 'time_start', 'label' => 'Orario Inizio', 'format' => 'custom', 'custom' => function($item) { 
                    return $item->time_start ? $item->time_start->format('H:i') : '-';
                }],
                ['key' => 'time_end', 'label' => 'Orario Fine', 'format' => 'custom', 'custom' => function($item) { 
                    return $item->time_end ? $item->time_end->format('H:i') : '-';
                }],
                ['key' => 'available', 'label' => 'Disponibile', 'format' => 'badge', 'badge_class' => 'bg-success', 'badge_true' => 'Sì', 'badge_false' => 'No'],
            ];
        @endphp
        
        <x-admin.data-table 
            :items="$availabilities"
            :columns="$columns"
            :actions="[
                ['type' => 'edit', 'route' => 'admin.student-availability.edit'],
                ['type' => 'delete', 'route' => 'admin.student-availability.destroy', 'confirm' => 'Sei sicuro di voler eliminare questa disponibilità?'],
            ]"
        />
    </div>
</div>
@endsection

