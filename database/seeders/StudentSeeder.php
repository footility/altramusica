<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Services\OdsImportService;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Studenti ===');
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error("Nessun anno accademico attivo trovato");
            return;
        }
        
        $filePath = base_path('docs/materiale cliente/db 2025-26 gestionale.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato: {$filePath}");
            return;
        }
        
        $importService = new OdsImportService();
        
        try {
            $result = $importService->importStudents($filePath, 'dati', $academicYear);
            
            $this->command->info("✓ Studenti importati: {$result['imported']}");
            $this->command->info("  Saltati: {$result['skipped']}");
            
            if (!empty($result['errors'])) {
                $this->command->warn("  Errori: " . count($result['errors']));
                foreach (array_slice($result['errors'], 0, 10) as $error) {
                    $this->command->warn("    - {$error}");
                }
                if (count($result['errors']) > 10) {
                    $this->command->warn("    ... e altri " . (count($result['errors']) - 10) . " errori");
                }
            }
        } catch (\Exception $e) {
            $this->command->error("Errore importazione studenti: " . $e->getMessage());
        }
    }
}
