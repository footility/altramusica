<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Family\Concerns\ScopesToGuardian;
use App\Models\Invoice;

/**
 * R13 — Ricevute pagamenti scaricabili dall'area famiglie.
 *
 * Sola lettura, scopata sui figli del tutore: vengono elencate e rese
 * scaricabili solo le fatture saldate (status = paid). La ricevuta è un
 * documento HTML stampabile (print-to-PDF, nessuna libreria PDF).
 */
class ReceiptsController extends Controller
{
    use ScopesToGuardian;

    /** Stati considerati "pagati": solo per questi è disponibile la ricevuta. */
    private const PAID_STATUSES = ['paid'];

    /** Elenco ricevute scaricabili: solo pagamenti saldati dei figli del tutore. */
    public function index()
    {
        $invoices = Invoice::whereIn('student_id', $this->childIds())
            ->whereIn('status', self::PAID_STATUSES)
            ->with('student')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->get();

        return view('family.receipts.index', [
            'guardian' => $this->guardian(),
            'invoices' => $invoices,
        ]);
    }

    /**
     * Ricevuta stampabile (HTML print-to-PDF) di un pagamento saldato.
     * Deve appartenere a un figlio nel perimetro ed essere saldata, altrimenti 404.
     */
    public function download(string $invoice)
    {
        $model = Invoice::whereIn('student_id', $this->childIds())
            ->whereIn('status', self::PAID_STATUSES)
            ->with(['items', 'student'])
            ->find($invoice);

        abort_if($model === null, 404, 'Ricevuta non disponibile.');

        return view('family.receipts.pdf', [
            'student' => $model->student,
            'invoice' => $model,
        ]);
    }
}
