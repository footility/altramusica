<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Services\OdsImportService;

class GuardianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Nota: I genitori vengono importati insieme agli studenti nel StudentSeeder.
     * Questo seeder può essere usato per aggiornare/verificare le relazioni.
     */
    public function run(): void
    {
        $this->command->info('=== Verifica Relazioni Studenti-Genitori ===');
        
        // I genitori sono già stati importati con gli studenti
        // Questo seeder può essere usato per verificare/aggiornare le relazioni
        
        $studentsWithGuardians = Student::has('guardians')->count();
        $studentsWithoutGuardians = Student::doesntHave('guardians')->count();
        $totalGuardians = \App\Models\Guardian::count();
        $totalRelations = \Illuminate\Support\Facades\DB::table('student_guardian')->count();
        
        $this->command->info("Studenti con genitori: {$studentsWithGuardians}");
        $this->command->info("Studenti senza genitori: {$studentsWithoutGuardians}");
        $this->command->info("Totale genitori: {$totalGuardians}");
        $this->command->info("Totale relazioni: {$totalRelations}");
        
        $this->command->info("✓ Verifica completata");
    }
}
