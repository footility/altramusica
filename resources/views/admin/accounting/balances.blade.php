@extends('layouts.admin')

@section('title', 'Crediti/Debiti')
@section('page-title', 'Crediti/Debiti (per studente)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Crediti/Debiti</h2>
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-receipt"></i> Fatture
    </a>
</div>

<div class="card">
    <div class="card-body">
        <x-admin.filter-bar
            :route="route('admin.accounting.balances')"
            :filters="[
                ['name' => 'search', 'type' => 'text', 'placeholder' => 'Cerca studente (nome/CF)...', 'width' => 4],
                ['name' => 'academic_year_id', 'type' => 'select', 'options' => $years->pluck('name', 'id')->toArray(), 'placeholder' => 'Tutti gli anni', 'width' => 3],
                ['name' => 'only_open', 'type' => 'select', 'options' => ['1' => 'Solo saldo aperto', '0' => 'Tutti'], 'placeholder' => null, 'width' => 3],
            ]"
        />

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Studente</th>
                        <th>CF</th>
                        <th class="text-end">Fatturato</th>
                        <th class="text-end">Pagato</th>
                        <th class="text-end">Saldo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        @php
                            $saldo = (float) $r->invoiced_total - (float) $r->paid_total;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.students.show', $r->id) }}">
                                    {{ $r->first_name }} {{ $r->last_name }}
                                </a>
                            </td>
                            <td>{{ $r->tax_code ?: '-' }}</td>
                            <td class="text-end">€ {{ number_format($r->invoiced_total, 2, ',', '.') }}</td>
                            <td class="text-end">€ {{ number_format($r->paid_total, 2, ',', '.') }}</td>
                            <td class="text-end">
                                <span class="fw-bold {{ $saldo > 0.01 ? 'text-danger' : 'text-success' }}">
                                    € {{ number_format($saldo, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.invoices.index', ['student_id' => $r->id, 'academic_year_id' => $yearId]) }}">
                                    Fatture
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Nessun dato.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $rows->links() }}
        </div>
    </div>
</div>
@endsection

