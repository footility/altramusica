<?php /* family/receipts/index.blade.php */ ?>
@extends('layouts.family')

@section('family-content')
<div class="container py-4">
    <h1 class="h4 mb-1">Ricevute pagamenti</h1>
    <p class="text-muted">Ricevute dei pagamenti saldati di {{ $student->first_name }}.</p>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Ricevuta</th>
                        <th class="d-none d-md-table-cell">Data</th>
                        <th>Importo</th>
                        <th class="text-end">Azione</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td>{{ $inv->number ?? ('#'.$inv->id) }}</td>
                            <td class="d-none d-md-table-cell">{{ optional($inv->issued_at)->format('d/m/Y') }}</td>
                            <td>€ {{ number_format($inv->total ?? 0, 2, ',', '.') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-primary" href="{{ route('family.receipts.download', $inv) }}" target="_blank" rel="noopener">Scarica PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center py-4">Nessuna ricevuta disponibile.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
