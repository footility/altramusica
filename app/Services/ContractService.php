<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Student;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ContractService
{
    /**
     * Genera numero contratto univoco
     */
    public function generateContractNumber(): string
    {
        $year = now()->year;
        $lastContract = Contract::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastContract ? ((int) substr($lastContract->contract_number, -4)) + 1 : 1;
        
        return "CONTR-{$year}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crea contratto da iscrizione
     */
    public function createFromEnrollment(Enrollment $enrollment, string $type = 'regular'): Contract
    {
        $contract = Contract::create([
            'student_id' => $enrollment->student_id,
            'academic_year_id' => $enrollment->academic_year_id,
            'contract_number' => $this->generateContractNumber(),
            'type' => $type,
            'start_date' => $enrollment->start_date,
            'end_date' => $enrollment->end_date,
            'status' => 'draft',
        ]);

        return $contract;
    }

    /**
     * Invia contratto (cambia stato a sent)
     */
    public function sendContract(Contract $contract): Contract
    {
        $contract->update([
            'status' => 'sent',
            'sent_date' => now(),
        ]);

        return $contract;
    }

    /**
     * Firma contratto (cambia stato a signed).
     *
     * La firma mock (R3, doc 30 §5) registra la data che compare sulla copia
     * cartacea firmata, che può essere diversa da oggi. Per questo accetta una
     * $signedDate opzionale; in assenza usa now() (comportamento storico).
     */
    public function signContract(Contract $contract, $signedDate = null): Contract
    {
        $contract->update([
            'status' => 'signed',
            'signed_date' => $signedDate ?? now(),
        ]);

        return $contract;
    }

    /**
     * Genera token per link precompilato
     */
    public function generatePrecompiledLinkToken(Contract $contract): string
    {
        $token = Str::random(64);
        
        // Salva token nel contratto (potresti aggiungere campo token al model)
        // Per ora usiamo un approccio semplice
        
        return route('contracts.accept', ['token' => $token]);
    }
}

