<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\OdsImportService;
use Illuminate\Support\Facades\Hash;

class OdsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Dati ODS ===');
        
        // 1. Crea anno accademico
        $this->command->info('Creazione anno accademico...');
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025-26'],
            [
                'slug' => '2025-26',
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'is_active' => true,
            ]
        );
        
        // Disattiva altri anni
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        $this->command->info("✓ Anno accademico: {$academicYear->name}");
        
        // 2. Crea utente admin
        $this->command->info('Creazione utente admin...');
        $admin = User::firstOrCreate(
            ['email' => 'admin@altramusica.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        $this->command->info("✓ Utente admin: {$admin->email}");
        
        // 3. Importa dati ODS
        $importService = new OdsImportService();
        $docsPath = base_path('docs/materiale cliente');
        
        // Importa studenti
        $this->command->info("\nImportazione studenti...");
        $studentsFile = $docsPath . '/db 2025-26 gestionale.ods';
        if (file_exists($studentsFile)) {
            try {
                $result = $importService->importStudents($studentsFile, 'dati', $academicYear);
                $this->command->info("✓ Studenti importati: {$result['imported']}");
                $this->command->info("  Saltati: {$result['skipped']}");
                if (!empty($result['errors'])) {
                    $this->command->warn("  Errori: " . count($result['errors']));
                    foreach (array_slice($result['errors'], 0, 20) as $error) {
                        $this->command->warn("    - {$error}");
                    }
                    if (count($result['errors']) > 20) {
                        $this->command->warn("    ... e altri " . (count($result['errors']) - 20) . " errori");
                    }
                }
            } catch (\Exception $e) {
                $this->command->error("Errore importazione studenti: " . $e->getMessage());
            }
        } else {
            $this->command->warn("File studenti non trovato: {$studentsFile}");
        }
        
        $this->command->info("\n=== Importazione completata ===");
    }
}
