@extends('family.layout')

@section('content')
<h1 class="h3 mb-1">Ricevute pagamenti</h1>
<p class="text-muted">Ricevute dei pagamenti saldati. La ricevuta si apre in una pagina stampabile (Stampa / Salva PDF).</p>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ricevuta</th>
                    <th class="d-none d-md-table-cell">Allievo</th>
                    <th class="d-none d-md-table-cell">Data</th>
                    <th>Importo</th>
                    <th class="text-end">Azione</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td>{{ $inv->invoice_number }}</td>
                        <td class="d-none d-md-table-cell">{{ $inv->student?->full_name ?? '—' }}</td>
                        <td class="d-none d-md-table-cell">{{ optional($inv->invoice_date)->format('d/m/Y') }}</td>
                        <td>€ {{ number_format($inv->total_amount ?? 0, 2, ',', '.') }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-primary" href="{{ route('family.receipts.download', $inv) }}" target="_blank" rel="noopener">Scarica</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted text-center py-4">Nessuna ricevuta disponibile.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
