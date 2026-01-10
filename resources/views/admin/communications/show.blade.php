@extends('layouts.admin')

@section('title', 'Dettaglio Comunicazione')
@section('page-title', 'Dettaglio Comunicazione')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Informazioni Comunicazione</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Tipo:</strong> 
                    <span class="badge bg-primary">
                        @if($communication->type == 'email') Email
                        @elseif($communication->type == 'sms') SMS
                        @elseif($communication->type == 'whatsapp') WhatsApp
                        @else {{ $communication->type }}
                        @endif
                    </span>
                </p>
                <p><strong>Studente:</strong> 
                    @if($communication->student)
                        <a href="{{ route('admin.students.show', $communication->student) }}">
                            {{ $communication->student->last_name }} {{ $communication->student->first_name }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
                <p><strong>Genitore:</strong> 
                    @if($communication->guardian)
                        {{ $communication->guardian->last_name }} {{ $communication->guardian->first_name }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                @if($communication->subject)
                    <p><strong>Oggetto:</strong> {{ $communication->subject }}</p>
                @endif
                <p><strong>Stato:</strong> 
                    @if($communication->status == 'delivered')
                        <span class="badge bg-success">Consegnata</span>
                    @elseif($communication->status == 'sent')
                        <span class="badge bg-info">Inviata</span>
                    @else
                        <span class="badge bg-danger">Fallita</span>
                    @endif
                </p>
                <p><strong>Inviata il:</strong> 
                    {{ $communication->sent_at ? $communication->sent_at->format('d/m/Y H:i') : '-' }}
                </p>
                @if($communication->sentBy)
                    <p><strong>Inviata da:</strong> {{ $communication->sentBy->name }}</p>
                @endif
            </div>
        </div>
        
        <div class="mt-3">
            <strong>Messaggio:</strong>
            <div class="mt-2 p-3 bg-light border rounded">
                {!! nl2br(e($communication->message)) !!}
            </div>
        </div>
        
        @if($communication->error_message)
            <div class="mt-3 alert alert-danger">
                <strong>Errore:</strong> {{ $communication->error_message }}
            </div>
        @endif
        
        @if($communication->recipients)
            <div class="mt-3">
                <strong>Destinatari:</strong>
                <ul>
                    @foreach($communication->recipients as $recipient)
                        <li>{{ $recipient['email'] ?? $recipient['phone'] ?? '-' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.communications.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Torna all'elenco
    </a>
</div>
@endsection

