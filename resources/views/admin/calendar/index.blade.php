@extends('layouts.admin')

@section('title', 'Calendario Lezioni')
@section('page-title', 'Calendario Lezioni')

@push('styles')
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    #calendar {
        background-color: #fff;
        padding: 20px;
        border-radius: 4px;
    }
    .fc-event {
        cursor: pointer;
    }
    .fc-daygrid-day-number {
        color: #333;
    }
    .fc-col-header-cell {
        background-color: #f8f9fa;
    }
    .legend {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Calendario {{ $year->name }}</h2>
    <div>
        <form action="{{ route('admin.calendar.generate') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="academic_year_id" value="{{ $year->id }}">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-calendar-plus"></i> Genera Calendario
            </button>
        </form>
        <a href="{{ route('admin.calendar.suspensions.create') }}" class="btn btn-warning">
            <i class="bi bi-calendar-x"></i> Aggiungi Sospensione
        </a>
    </div>
</div>

@if($years->count() > 1)
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.calendar.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Anno Scolastico</label>
                <select name="year_id" class="form-select" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y->id }}" {{ $y->id == $year->id ? 'selected' : '' }}>
                            {{ $y->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5>Calendario Lezioni</h5>
            </div>
            <div class="card-body">
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #28a745;"></div>
                        <span>Lezione Attiva</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span>Lezione Sospesa</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffc107;"></div>
                        <span>Sospensione</span>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5>Sospensioni</h5>
            </div>
            <div class="card-body">
                @forelse($suspensions as $suspension)
                    <div class="border-bottom pb-2 mb-2">
                        <strong>{{ $suspension->name }}</strong><br>
                        <small class="text-muted">
                            {{ $suspension->start_date->format('d/m/Y') }} - {{ $suspension->end_date->format('d/m/Y') }}
                        </small>
                        @if($suspension->notes)
                            <p class="mb-0 mt-1"><small>{{ $suspension->notes }}</small></p>
                        @endif
                        <form action="{{ route('admin.calendar.suspensions.destroy', $suspension) }}" method="POST" class="mt-2" onsubmit="return confirm('Eliminare questa sospensione?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Elimina
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">Nessuna sospensione configurata.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const yearId = {{ $year->id }};
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'it',
        firstDay: 1, // Lunedì
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch(`{{ route('admin.calendar.events') }}?year_id=${yearId}&start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            let message = '';
            
            if (props.type === 'lesson') {
                message = `Lezione del ${info.event.start.toLocaleDateString('it-IT', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}\n`;
                message += `Stato: ${props.is_active ? 'Attiva' : 'Sospesa'}\n`;
                if (props.notes) {
                    message += `Note: ${props.notes}`;
                }
            } else if (props.type === 'suspension') {
                message = `Sospensione: ${info.event.title}\n`;
                message += `Dal ${info.event.start.toLocaleDateString('it-IT')} al ${info.event.end.toLocaleDateString('it-IT')}\n`;
                if (props.notes) {
                    message += `Note: ${props.notes}`;
                }
            }
            
            if (message) {
                alert(message);
            }
        },
        eventDisplay: 'block',
        height: 'auto',
        aspectRatio: 1.8,
    });
    
    calendar.render();
});
</script>
@endpush
