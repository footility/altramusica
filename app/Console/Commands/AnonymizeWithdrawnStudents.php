<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnonymizeWithdrawnStudents extends Command
{
    protected $signature = 'privacy:anonymize-withdrawn
        {--years= : Sovrascrive gli anni di retention da config/privacy.php}
        {--dry-run : Mostra cosa verrebbe anonimizzato senza scrivere}';

    protected $description = 'Anonimizza i dati personali degli ex-studenti ritirati da oltre N anni (policy retention GDPR).';

    public function handle(): int
    {
        $years = (int) ($this->option('years') ?: config('privacy.retention_years'));
        $dryRun = (bool) $this->option('dry-run');

        if ($years < 1) {
            $this->error('Anni di retention non validi.');
            return self::FAILURE;
        }

        $cutoff = now()->subYears($years)->toDateString();
        $placeholder = config('privacy.anonymized_placeholder', 'Anonimizzato');

        $this->info("Retention: {$years} anni · cutoff ritiro <= {$cutoff}" . ($dryRun ? ' · DRY-RUN' : ''));

        // Candidati: studenti non già anonimizzati, con almeno un anno ritirato oltre il cutoff,
        // e senza alcun anno ancora attivo (status != withdrawn) o ritirato dopo il cutoff.
        $candidates = Student::query()
            ->whereNull('anonymized_at')
            ->whereHas('years', function ($q) use ($cutoff) {
                $q->where('status', 'withdrawn')
                  ->whereNotNull('withdrawn_at')
                  ->whereDate('withdrawn_at', '<=', $cutoff);
            })
            ->whereDoesntHave('years', function ($q) use ($cutoff) {
                $q->where('status', '!=', 'withdrawn')
                  ->orWhereNull('withdrawn_at')
                  ->orWhereDate('withdrawn_at', '>', $cutoff);
            })
            ->get();

        if ($candidates->isEmpty()) {
            $this->info('Nessun ex-studente da anonimizzare.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($candidates as $student) {
            $this->line("- #{$student->id} {$student->first_name} {$student->last_name}");

            if ($dryRun) {
                $count++;
                continue;
            }

            DB::transaction(function () use ($student, $placeholder) {
                $student->update([
                    'first_name'   => $placeholder,
                    'last_name'    => "#{$student->id}",
                    'birth_date'   => null,
                    'age'          => null,
                    'tax_code'     => null,
                    'anonymized_at' => now(),
                ]);

                // Azzera i campi liberi che possono contenere dati personali
                StudentYear::where('student_id', $student->id)->update([
                    'school_origin' => null,
                    'how_know_us'   => null,
                    'preferences'   => null,
                    'notes'         => null,
                    'admin_notes'   => null,
                ]);
            });

            $count++;
        }

        $verb = $dryRun ? 'da anonimizzare' : 'anonimizzati';
        $this->info("Totale {$verb}: {$count}.");

        return self::SUCCESS;
    }
}
