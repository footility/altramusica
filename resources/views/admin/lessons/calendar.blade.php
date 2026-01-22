@extends('layouts.admin')

@section('title', 'Calendario Lezioni')
@section('page-title', 'Calendario Lezioni')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Calendario Lezioni</h2>
    <div>
        <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary">
            <i class="bi bi-calendar-check"></i> Calendario Base
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.lessons.calendar') }}" class="row g-3" id="filterForm">
            <div class="col-md-3">
                <label class="form-label">Anno Accademico</label>
                <select name="year_id" class="form-select" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y->id }}" {{ $year->id == $y->id ? 'selected' : '' }}>
                            {{ $y->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Docente</label>
                <select name="teacher_id" id="teacher_id" class="form-select" onchange="updateCalendar()">
                    <option value="">Tutti i docenti</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->last_name }} {{ $t->first_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Corso</label>
                <select name="course_offering_id" id="course_offering_id" class="form-select" onchange="updateCalendar()">
                    <option value="">Tutti i corsi</option>
                    @foreach($courseOfferings as $o)
                        <option value="{{ $o->id }}">{{ $o->course?->name ?? 'Corso' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Aula</label>
                <select name="classroom_id" id="classroom_id" class="form-select" onchange="updateCalendar()">
                    <option value="">Tutte le aule</option>
                    @foreach($classrooms as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/it.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'it',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            var params = new URLSearchParams({
                year_id: {{ $year->id }},
                start: fetchInfo.startStr,
                end: fetchInfo.endStr,
            });
            
            var teacherId = document.getElementById('teacher_id')?.value;
            var courseOfferingId = document.getElementById('course_offering_id')?.value;
            var classroomId = document.getElementById('classroom_id')?.value;
            
            if (teacherId) params.append('teacher_id', teacherId);
            if (courseOfferingId) params.append('course_offering_id', courseOfferingId);
            if (classroomId) params.append('classroom_id', classroomId);
            
            fetch('{{ route("admin.lessons.calendar.events") }}?' + params.toString())
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                return false;
            }
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        height: 'auto',
    });
    
    calendar.render();
    
    window.updateCalendar = function() {
        calendar.refetchEvents();
    };
});
</script>
@endpush
@endsection

