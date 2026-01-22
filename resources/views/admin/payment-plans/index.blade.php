@extends('layouts.admin')

@section('title', 'Scadenzario Rate')
@section('page-title', 'Scadenzario Rate')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Scadenzario Rate</h2>
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-receipt"></i> Vai alle Fatture
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar
            :route="route('admin.payment-plans.index')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca studente o n. fattura...', 'width' => 4],
                ['name' => 'academic_year_id', 'type' => 'select', 'options' => $years->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti gli anni', 'width' => 3],
                ['name' => 'student_id', 'type' => 'select', 'options' => $students->pluck('full_name', 'id')->toArray(), 'placeholder' => 'Tutti gli studenti', 'width' => 3],
                ['name' => 'status', 'type' => 'select', 'options' => $statuses, 'placeholder' => 'Tutti gli stati', 'width' => 3],
                ['name' => 'from_due_date', 'type' => 'date', 'placeholder' => 'Da (scadenza)', 'width' => 3],
                ['name' => 'to_due_date', 'type' => 'date', 'placeholder' => 'A (scadenza)', 'width' => 3],
            ]"
        />

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Scadenza</th>
                        <th>Anno</th>
                        <th>Studente</th>
                        <th>Fattura</th>
                        <th class="text-end">Importo</th>
                        <th>Stato</th>
                        <th>Pagata il</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($installments as $inst)
                        @php
                            $isOverdue = ($inst->status === 'pending' && $inst->due_date < now()->startOfDay());
                            $statusLabel = $inst->status;
                            $badge = 'bg-secondary';
                            if ($inst->status === 'paid') { $badge = 'bg-success'; $statusLabel = 'paid'; }
                            if ($inst->status === 'overdue' || $isOverdue) { $badge = 'bg-danger'; $statusLabel = 'overdue'; }
                        @endphp
                        <tr class="{{ ($inst->status === 'overdue' || $isOverdue) ? 'table-danger' : '' }}">
                            <td>{{ $inst->due_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $inst->invoice?->academicYear?->name ?? '-' }}</td>
                            <td>
                                @if($inst->invoice?->student)
                                    <a href="{{ route('admin.students.show', $inst->invoice->student) }}">
                                        {{ $inst->invoice->student->full_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($inst->invoice)
                                    <a href="{{ route('admin.invoices.show', $inst->invoice) }}">
                                        {{ $inst->invoice->invoice_number ?? ('Fattura #' . $inst->invoice->id) }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end">€ {{ number_format($inst->amount, 2, ',', '.') }}</td>
                            <td><span class="badge {{ $badge }}">{{ $statusLabel }}</span></td>
                            <td>{{ $inst->paid_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-end">
                                @if($inst->invoice)
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.invoices.show', $inst->invoice) }}">
                                        Apri
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Nessuna rata trovata.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $installments->links() }}
        </div>
    </div>
</div>
@endsection

