@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if(!$currentYear)
    <div class="alert alert-warning">
        <h5>Nessun anno scolastico attivo</h5>
        <p>Configura un anno scolastico per iniziare a utilizzare il sistema.</p>
        <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">Crea Anno Scolastico</a>
    </div>
@else
    <div class="mb-4">
        <h3>Anno Scolastico: {{ $currentYear->name }}</h3>
        <p class="text-muted">{{ $currentYear->start_date->format('d/m/Y') }} - {{ $currentYear->end_date->format('d/m/Y') }}</p>
    </div>

    {{-- Alert anagrafiche incomplete --}}
    @if($incompleteCount > 0)
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-exclamation-triangle"></i>
                <strong>{{ $incompleteCount }}</strong> anagrafic{{ $incompleteCount == 1 ? 'a incompleta' : 'he incomplete' }}
                (codice fiscale, data di nascita o genitore/tutore mancante).
            </div>
            <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#incompleteList">
                Mostra
            </button>
        </div>
        <div class="collapse mb-3" id="incompleteList">
            <div class="card card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($incompleteStudents->take(20) as $student)
                        <li class="mb-1">
                            <a href="{{ route('admin.students.show', $student) }}">{{ $student->full_name }}</a>
                            <small class="text-muted">
                                @if(empty($student->tax_code)) · CF mancante @endif
                                @if(empty($student->birth_date)) · data nascita mancante @endif
                                @if($student->guardians->isEmpty()) · nessun genitore/tutore @endif
                            </small>
                        </li>
                    @endforeach
                </ul>
                @if($incompleteCount > 20)
                    <small class="text-muted mt-2">… e altri {{ $incompleteCount - 20 }}.</small>
                @endif
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <!-- Studenti -->
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Studenti</h5>
                    <h2>{{ $stats['students']['total'] }}</h2>
                    <small>
                        Iscritti: {{ $stats['students']['enrolled'] }} |
                        Interessati: {{ $stats['students']['interested'] }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Iscrizioni -->
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Iscrizioni</h5>
                    <h2>{{ $stats['enrollments']['total'] }}</h2>
                    <small>Attive: {{ $stats['enrollments']['active'] }}</small>
                </div>
            </div>
        </div>

        <!-- Lezioni della settimana -->
        <div class="col-md-3">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Lezioni settimana</h5>
                    <h2>{{ $weekLessonsCount }}</h2>
                    <small>
                        <a href="{{ route('admin.lessons.calendar') }}" class="text-white text-decoration-underline">Vai al calendario</a>
                    </small>
                </div>
            </div>
        </div>

        @can('invoices.view')
        <!-- Fatture aperte -->
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Fatture aperte</h5>
                    <h2>{{ $stats['invoices']['pending'] }}</h2>
                    <small>
                        Pagate: {{ $stats['invoices']['paid'] }} | Totale: {{ $stats['invoices']['total'] }}
                    </small>
                    <div class="mt-2">
                        <strong>Importo: € {{ number_format($stats['invoices']['total_amount'], 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Contratti (alternativa quando non si ha accesso alla contabilità) -->
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Contratti</h5>
                    <h2>{{ $stats['contracts']['total'] }}</h2>
                    <small>
                        Firmati: {{ $stats['contracts']['signed'] }} |
                        In attesa: {{ $stats['contracts']['pending'] }}
                    </small>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <div class="row">
        {{-- Prossime scadenze (solo contabilità) --}}
        @can('invoices.view')
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Prossime scadenze (7 giorni)</h5>
                    @if($overdueCount > 0)
                        <span class="badge bg-danger">{{ $overdueCount }} scadut{{ $overdueCount == 1 ? 'a' : 'e' }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($dueSoon->isEmpty())
                        <p class="text-muted mb-0">Nessuna rata in scadenza nei prossimi 7 giorni.</p>
                    @else
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Scadenza</th>
                                    <th>Studente</th>
                                    <th class="text-end">Importo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dueSoon as $plan)
                                    <tr>
                                        <td>{{ $plan->due_date->format('d/m/Y') }}</td>
                                        <td>
                                            @if($plan->invoice && $plan->invoice->student)
                                                <a href="{{ route('admin.invoices.show', $plan->invoice) }}">{{ $plan->invoice->student->full_name }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">€ {{ number_format($plan->amount, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
        @endcan

        {{-- Lezioni della settimana --}}
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lezioni della settimana</h5>
                </div>
                <div class="card-body">
                    @if($weekLessons->isEmpty())
                        <p class="text-muted mb-0">Nessuna lezione pianificata questa settimana.</p>
                    @else
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Giorno</th>
                                    <th>Ora</th>
                                    <th>Corso</th>
                                    <th>Docente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weekLessons->take(15) as $lesson)
                                    <tr>
                                        <td>{{ $lesson->date->format('d/m') }}</td>
                                        <td>{{ \Illuminate\Support\Str::of($lesson->time_start)->substr(0, 5) }}</td>
                                        <td>{{ optional($lesson->courseOffering->course ?? null)->name ?? '—' }}</td>
                                        <td>{{ optional($lesson->teacher)->full_name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($weekLessonsCount > 15)
                            <small class="text-muted">… e altre {{ $weekLessonsCount - 15 }} lezioni.</small>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Azioni Rapide</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Nuovo Studente
                        </a>
                        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-success">
                            <i class="bi bi-clipboard-plus"></i> Nuova Iscrizione
                        </a>
                        <a href="{{ route('admin.contracts.create') }}" class="btn btn-info">
                            <i class="bi bi-file-earmark-plus"></i> Nuovo Contratto
                        </a>
                        @can('invoices.view')
                        <a href="{{ route('admin.invoices.create') }}" class="btn btn-warning">
                            <i class="bi bi-receipt-cutoff"></i> Nuova Fattura
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Link Utili</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><a href="{{ route('admin.students.index') }}">Gestione Studenti</a></li>
                        <li><a href="{{ route('admin.courses.index') }}">Gestione Corsi</a></li>
                        <li><a href="{{ route('admin.academic-years.index') }}">Anni Scolastici</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
