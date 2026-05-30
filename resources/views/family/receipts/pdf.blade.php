<?php /* family/receipts/pdf.blade.php — ricevuta stampabile (print-to-PDF) */ ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ricevuta {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #222; margin: 40px; font-size: 14px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #222; padding-bottom: 12px; margin-bottom: 24px; }
        .school { font-size: 18px; font-weight: bold; }
        .doc-title { font-size: 22px; font-weight: bold; text-align: right; }
        .meta { margin-bottom: 24px; }
        .meta div { margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
        td.num, th.num { text-align: right; }
        .total { margin-top: 16px; text-align: right; font-size: 16px; font-weight: bold; }
        .footer { margin-top: 40px; font-size: 12px; color: #777; }
        .actions { margin-bottom: 24px; }
        @media print { .actions { display: none; } body { margin: 0; } }
    </style>
</head>
<body onload="if (!window.location.hash) { try { window.print(); } catch (e) {} }">
    <div class="actions">
        <button onclick="window.print()">Stampa / Salva PDF</button>
    </div>

    <div class="header">
        <div class="school">Scuola di Musica Altramusica</div>
        <div class="doc-title">RICEVUTA</div>
    </div>

    <div class="meta">
        <div><strong>Numero:</strong> {{ $invoice->invoice_number }}</div>
        <div><strong>Data:</strong> {{ optional($invoice->invoice_date)->format('d/m/Y') }}</div>
        <div><strong>Allievo:</strong> {{ $student?->full_name ?? trim(($student->first_name ?? '').' '.($student->last_name ?? '')) }}</div>
        <div><strong>Stato:</strong> Pagato</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descrizione</th>
                <th class="num">Q.tà</th>
                <th class="num">Prezzo</th>
                <th class="num">Importo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="num">{{ rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ',') }}</td>
                    <td class="num">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="num">€ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Pagamento {{ $invoice->invoice_number }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">Totale: € {{ number_format($invoice->total_amount ?? 0, 2, ',', '.') }}</div>

    <div class="footer">
        Documento generato dall'Area Famiglie. Ricevuta relativa a un pagamento già saldato.
    </div>
</body>
</html>
