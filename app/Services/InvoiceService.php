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
    public function createPaymentPlan(Invoice $invoice, int $numberOfInstallments, Carbon $firstDueDate = null): PaymentPlan
    {
        $firstDueDate = $firstDueDate ?? now()->addDays(30);
        $installmentAmount = $invoice->total_amount / $numberOfInstallments;

        $plan = PaymentPlan::create([
            'invoice_id' => $invoice->id,
            'number_of_installments' => $numberOfInstallments,
            'total_amount' => $invoice->total_amount,
            'status' => 'active',
        ]);

        // Crea le rate
        for ($i = 0; $i < $numberOfInstallments; $i++) {
            $dueDate = $firstDueDate->copy()->addMonths($i);
            $amount = ($i === $numberOfInstallments - 1) 
                ? $invoice->total_amount - ($installmentAmount * ($numberOfInstallments - 1)) // Ultima rata con resto
                : $installmentAmount;

            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_plan_id' => $plan->id,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }

        return $plan;
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
            'status' => 'completed',
        ]);

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

