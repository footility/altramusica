<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\CalendarLesson;
use App\Models\CalendarSuspension;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Calendario ===');
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error("Nessun anno accademico attivo trovato");
            return;
        }
        
        $filePath = base_path('docs/materiale cliente/Calendario 2025-26.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato: {$filePath}");
            return;
        }
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            
            // Analizza i fogli disponibili
            $sheetCount = $spreadsheet->getSheetCount();
            $this->command->info("Fogli trovati: {$sheetCount}");
            
            $importedLessons = 0;
            $importedSuspensions = 0;
            
            // Processa ogni foglio
            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestRow();
                
                $this->command->info("Processando foglio: {$sheetName} ({$highestRow} righe)");
                
                // Cerca colonne con date
                for ($row = 1; $row <= min($highestRow, 10); $row++) {
                    for ($col = 'A'; $col <= 'Z'; $col++) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        
                        // Se trovi una data, prova a parsarla
                        if (is_numeric($cellValue) && $cellValue > 40000) {
                            // Probabilmente una data Excel
                            try {
                                $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue));
                                
                                // Crea lezione se è nel range dell'anno accademico
                                if ($date->between($academicYear->start_date, $academicYear->end_date)) {
                                    CalendarLesson::firstOrCreate(
                                        [
                                            'academic_year_id' => $academicYear->id,
                                            'date' => $date->format('Y-m-d'),
                                        ],
                                        [
                                            'active' => true,
                                        ]
                                    );
                                    $importedLessons++;
                                }
                            } catch (\Exception $e) {
                                // Ignora errori di parsing
                            }
                        }
                    }
                }
            }
            
            $this->command->info("✓ Importate {$importedLessons} lezioni");
            $this->command->info("✓ Importate {$importedSuspensions} sospensioni");
            
        } catch (\Exception $e) {
            $this->command->error("Errore: " . $e->getMessage());
        }
    }
}
