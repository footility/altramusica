@extends('layouts.admin')

@section('title', 'Comunicazioni')
@section('page-title', 'Gestione Comunicazioni')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Comunicazioni</h2>
    <div>
        <a href="{{ route('admin.communications.create') }}" class="btn btn-primary">
            <i class="bi bi-envelope"></i> Nuova Comunicazione
        </a>
        <a href="{{ route('admin.communications.bulk') }}" class="btn btn-info">
            <i class="bi bi-envelope-heart"></i> Comunicazione Massiva
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.communications.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti i tipi</option>
                    <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="sms" {{ request('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="whatsapp" {{ request('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stato</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti gli stati</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Inviata</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Consegnata</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Fallita</option>
                </select>
            </div>
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
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @php
            $columns = [
                ['key' => 'type', 'label' => 'Tipo', 'format' => 'badge', 'badge_class' => 'bg-primary', 'badge_map' => ['email' => 'Email', 'sms' => 'SMS', 'whatsapp' => 'WhatsApp']],
                ['key' => 'student.full_name', 'label' => 'Studente', 'format' => 'custom', 'custom' => function($item) { return $item->student ? $item->student->last_name . ' ' . $item->student->first_name : '-'; }],
                ['key' => 'subject', 'label' => 'Oggetto'],
                ['key' => 'status', 'label' => 'Stato', 'format' => 'badge', 'badge_class' => 'bg-success', 'badge_map' => ['sent' => 'Inviata', 'delivered' => 'Consegnata', 'failed' => 'Fallita']],
                ['key' => 'sent_at', 'label' => 'Data Invio', 'format' => 'date'],
            ];
        @endphp
        
        <x-admin.data-table 
            :items="$communications"
            :columns="$columns"
            :actions="[
                ['type' => 'show', 'route' => 'admin.communications.show'],
            ]"
        />
    </div>
</div>
@endsection

