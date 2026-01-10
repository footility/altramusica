@extends('layouts.admin')

@section('title', 'Conto Orario Insegnanti')
@section('page-title', 'Conto Orario Insegnanti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Conto Orario Insegnanti</h2>
    <div>
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#calculateAllModal">
            <i class="bi bi-calculator"></i> Calcola Tutti
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calculateModal">
            <i class="bi bi-calculator"></i> Calcola Singolo
        </button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.teacher-hours.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Docente</label>
                <select name="teacher_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti i docenti</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->last_name }} {{ $t->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Anno Accademico</label>
                <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti gli anni</option>
                    @foreach($academicYears as $y)
                        <option value="{{ $y->id }}" {{ request('academic_year_id') == $y->id ? 'selected' : '' }}>
                            {{ $y->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stato</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti gli stati</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bozza</option>
                    <option value="calculated" {{ request('status') == 'calculated' ? 'selected' : '' }}>Calcolato</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approvato</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagato</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @php
            $columns = [
                ['key' => 'teacher.full_name', 'label' => 'Docente', 'format' => 'custom', 'custom' => function($item) { return $item->teacher->last_name . ' ' . $item->teacher->first_name; }],
                ['key' => 'academic_year.name', 'label' => 'Anno', 'format' => 'custom', 'custom' => function($item) { return $item->academicYear->name; }],
                ['key' => 'period_start', 'label' => 'Periodo', 'format' => 'custom', 'custom' => function($item) { 
                    return $item->period_start->format('d/m/Y') . ' - ' . $item->period_end->format('d/m/Y');
                }],
                ['key' => 'lessons_count', 'label' => 'Lezioni'],
                ['key' => 'hours_total', 'label' => 'Ore', 'format' => 'custom', 'custom' => function($item) { return number_format($item->hours_total, 2) . 'h'; }],
                ['key' => 'total_amount', 'label' => 'Totale', 'format' => 'custom', 'custom' => function($item) { return '€ ' . number_format($item->total_amount, 2); }],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge', 'badge_class' => 'bg-info', 'badge_map' => ['draft' => 'Bozza', 'calculated' => 'Calcolato', 'approved' => 'Approvato', 'paid' => 'Pagato']],
            ];
        @endphp
        
        <x-admin.data-table 
            :items="$teacherHours"
            :columns="$columns"
            :actions="[
                ['type' => 'show', 'route' => 'admin.teacher-hours.show'],
            ]"
        />
    </div>
</div>

<!-- Modal Calcola Singolo -->
<div class="modal fade" id="calculateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.teacher-hours.calculate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Calcola Conto Orario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">Docente <span class="text-danger">*</span></label>
                        <select name="teacher_id" id="teacher_id" class="form-select" required>
                            <option value="">Seleziona docente</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->last_name }} {{ $t->first_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">Anno Accademico <span class="text-danger">*</span></label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                            @foreach($academicYears as $y)
                                <option value="{{ $y->id }}">{{ $y->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="period_start" class="form-label">Data Inizio</label>
                            <input type="date" name="period_start" id="period_start" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="period_end" class="form-label">Data Fine</label>
                            <input type="date" name="period_end" id="period_end" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Calcola</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Calcola Tutti -->
<div class="modal fade" id="calculateAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.teacher-hours.calculate-all') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Calcola Conto Orario per Tutti i Docenti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="academic_year_id_all" class="form-label">Anno Accademico <span class="text-danger">*</span></label>
                        <select name="academic_year_id" id="academic_year_id_all" class="form-select" required>
                            @foreach($academicYears as $y)
                                <option value="{{ $y->id }}">{{ $y->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="period_start_all" class="form-label">Data Inizio</label>
                            <input type="date" name="period_start" id="period_start_all" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="period_end_all" class="form-label">Data Fine</label>
                            <input type="date" name="period_end" id="period_end_all" class="form-control">
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Questa operazione calcolerà il conto orario per tutti i docenti attivi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Calcola Tutti</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

