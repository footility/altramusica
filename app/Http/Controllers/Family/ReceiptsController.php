<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class ReceiptsController extends Controller
{
    /**
     * Stati considerati "pagati": solo per questi è disponibile la ricevuta.
     */
    private const PAID_STATUSES = ['paid'];

    /**
     * Elenco ricevute scaricabili: solo pagamenti saldati dello studente di sessione.
     */
    public function index(Request $request)
    {
        $student = $request->attributes->get('family_student');

        $invoices = $student->invoices()
            ->whereIn('status', self::PAID_STATUSES)
            ->orderByDesc('issued_at')
            ->get();

        return view('family.receipts.index', compact('student', 'invoices'));
    }

    /**
     * Ricevuta stampabile (HTML print-to-PDF) di un pagamento saldato.
     * Valida che la fattura appartenga allo studente di sessione e sia pagata.
     */
    public function download(Request $request, Invoice $invoice)
    {
        $student = $request->attributes->get('family_student');

        abort_unless(
            $invoice->student_id === $student->id && in_array($invoice->status, self::PAID_STATUSES, true),
            404
        );

        $invoice->load('lines', 'student');

        return view('family.receipts.pdf', [
            'student' => $student,
            'invoice' => $invoice,
        ]);
    }
}
