@extends('layouts.admin')

@section('title', 'Proposte Orarie')
@section('page-title', 'Gestione Proposte Orarie')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Proposte Orarie</h2>
    <a href="{{ route('admin.schedule-proposals.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Genera Proposte
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.schedule-proposals.index') }}" class="row g-3">
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
                <label class="form-label">Stato</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Bozze e Proposte</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bozza</option>
                    <option value="proposed" {{ request('status') == 'proposed' ? 'selected' : '' }}>Proposta</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accettata</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rifiutata</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @php
            $columns = [
                ['key' => 'student.full_name', 'label' => 'Studente', 'format' => 'custom', 'custom' => function($item) { return $item->student->last_name . ' ' . $item->student->first_name; }],
                ['key' => 'teacher.full_name', 'label' => 'Docente', 'format' => 'custom', 'custom' => function($item) { return $item->teacher ? $item->teacher->last_name . ' ' . $item->teacher->first_name : '-'; }],
                ['key' => 'day_of_week', 'label' => 'Giorno', 'format' => 'custom', 'custom' => function($item) { 
                    $days = ['monday' => 'Lunedì', 'tuesday' => 'Martedì', 'wednesday' => 'Mercoledì', 'thursday' => 'Giovedì', 'friday' => 'Venerdì', 'saturday' => 'Sabato', 'sunday' => 'Domenica'];
                    return $days[$item->day_of_week] ?? $item->day_of_week;
                }],
                ['key' => 'time_start', 'label' => 'Orario', 'format' => 'custom', 'custom' => function($item) { 
                    return ($item->time_start ? $item->time_start->format('H:i') : '-') . ' - ' . ($item->time_end ? $item->time_end->format('H:i') : '-');
                }],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge', 'badge_class' => 'bg-info', 'badge_map' => ['draft' => 'Bozza', 'proposed' => 'Proposta', 'accepted' => 'Accettata', 'rejected' => 'Rifiutata']],
            ];
        @endphp
        
        <x-admin.data-table 
            :items="$proposals"
            :columns="$columns"
            :actions="[
                ['type' => 'show', 'route' => 'admin.schedule-proposals.show'],
                ['type' => 'delete', 'route' => 'admin.schedule-proposals.destroy', 'confirm' => 'Sei sicuro di voler eliminare questa proposta?'],
            ]"
        />
    </div>
</div>
@endsection

