<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Models\Enrollment;
use App\Models\Contract;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Genera numero fattura univoco
     */
    public function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $lastInvoice = Invoice::whereYear('invoice_date', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        return "FATT-{$year}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crea fattura da contratto firmato
     */
    public function createFromContract(Contract $contract): Invoice
    {
        // Calcola totale da enrollments collegati
        $totalAmount = $contract->student->enrollments()
            ->where('academic_year_id', $contract->academic_year_id)
            ->sum('total_amount');

        $invoice = Invoice::create([
            'student_id' => $contract->student_id,
            'academic_year_id' => $contract->academic_year_id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $totalAmount,
            'tax_amount' => 0, // IVA se applicabile
            'total_amount' => $totalAmount,
            'status' => 'draft',
        ]);

        // Aggiungi items da enrollments
        foreach ($contract->student->enrollments()->where('academic_year_id', $contract->academic_year_id)->get() as $enrollment) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'enrollment',
                'item_id' => $enrollment->id,
                'description' => "Corso: {$enrollment->course->name}",
                'quantity' => 1,
                'unit_price' => $enrollment->total_amount,
                'total_price' => $enrollment->total_amount,
            ]);
        }

        return $invoice;
    }

    /**
     * Crea piano di pagamento con rate
     */
    public function createPaymentPlan(Invoice $invoice, int $numberOfInstallments, Carbon $firstDueDate = null)
    {
        $firstDueDate = $firstDueDate ?? now()->addDays(30);
        $installmentAmount = $invoice->total_amount / $numberOfInstallments;

        // In questo progetto `payment_plans` rappresenta le singole rate (non un "piano" aggregato).
        // Crea quindi N record PaymentPlan (rata) collegati alla fattura.
        for ($i = 0; $i < $numberOfInstallments; $i++) {
            $dueDate = $firstDueDate->copy()->addMonths($i);
            $amount = ($i === $numberOfInstallments - 1) 
                ? $invoice->total_amount - ($installmentAmount * ($numberOfInstallments - 1)) // Ultima rata con resto
                : $installmentAmount;

            PaymentPlan::create([
                'invoice_id' => $invoice->id,
                'installment_number' => $i + 1,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }

        return;
    }

    /**
     * Registra pagamento
     */
    public function recordPayment(Invoice $invoice, float $amount, string $method = 'bank_transfer', Carbon $paymentDate = null): Payment
    {
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'payment_date' => $paymentDate ?? now(),
            'payment_method' => $method,
        ]);

        // Collega in modo semplice il pagamento alle rate più vecchie (MVP, senza parziali)
        $remaining = $amount;
        $installments = $invoice->paymentPlans()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->get();

        foreach ($installments as $installment) {
            if ($remaining <= 0) {
                break;
            }

            // MVP: consideriamo pagamenti che coprono intere rate
            if ($remaining + 0.00001 >= (float) $installment->amount) {
                $installment->update([
                    'status' => 'paid',
                    'paid_date' => $payment->payment_date,
                    'payment_id' => $payment->id,
                ]);
                $remaining -= (float) $installment->amount;
            }
        }

        // Aggiorna stato fattura se completamente pagata
        if ($invoice->remaining_amount <= 0) {
            $invoice->update(['status' => 'paid']);
        } elseif ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent']);
        }

        return $payment;
    }

    /**
     * Crea nota di credito
     */
    public function createCreditNote(Invoice $invoice, float $amount, string $reason): \App\Models\CreditNote
    {
        return \App\Models\CreditNote::create([
            'invoice_id' => $invoice->id,
            'credit_note_number' => 'NC-' . $invoice->invoice_number,
            'amount' => $amount,
            'reason' => $reason,
            'credit_note_date' => now(),
        ]);
    }
}

