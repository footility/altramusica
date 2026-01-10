<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Esami ===');
        
        $filePath = base_path('docs/materiale cliente/Db Accessori 2025-26.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato: {$filePath}");
            return;
        }
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheetByName('accessori');
            
            if (!$sheet) {
                $this->command->warn("Foglio 'accessori' non trovato");
                return;
            }
            
            // Leggi header
            $headerRow = 1;
            $highestCol = $sheet->getHighestColumn();
            $highestColIndex = Coordinate::columnIndexFromString($highestCol);
            
            $headerMap = [];
            for ($c = 1; $c <= min($highestColIndex, 200); $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $header = trim((string)$sheet->getCell($colLetter . $headerRow)->getValue());
                if (!empty($header)) {
                    $headerMap[strtolower($header)] = $colLetter;
                }
            }
            
            $highestRow = $sheet->getHighestRow();
            $imported = 0;
            $skipped = 0;
            
            $this->command->info("Trovate {$highestRow} righe");
            
            // Cerca colonne esami (esame 1, esame 2, etc.)
            $examColumns = [];
            foreach ($headerMap as $header => $col) {
                if (preg_match('/esame\s*(\d+)/i', $header, $matches)) {
                    $examColumns[$matches[1]] = $col;
                }
            }
            
            if (empty($examColumns)) {
                $this->command->warn("Nessuna colonna esame trovata");
                return;
            }
            
            $this->command->info("Trovate " . count($examColumns) . " colonne esami");
            
            // Trova colonne studente
            $studentCol = $headerMap['cognome allievo'] ?? null;
            $studentNameCol = $headerMap['nome allievo'] ?? null;
            
            if (!$studentCol || !$studentNameCol) {
                $this->command->warn("Colonne studente non trovate");
                return;
            }
            
            // Processa righe
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $cognome = trim((string)$sheet->getCell($studentCol . $row)->getValue());
                    $nome = trim((string)$sheet->getCell($studentNameCol . $row)->getValue());
                    
                    if (empty($cognome) && empty($nome)) {
                        $skipped++;
                        continue;
                    }
                    
                    // Trova studente
                    $student = null;
                    if (!empty($cognome) && !empty($nome)) {
                        $student = Student::whereRaw('LOWER(last_name) = ?', [strtolower($cognome)])
                            ->whereRaw('LOWER(first_name) = ?', [strtolower($nome)])
                            ->first();
                    }
                    
                    if (!$student) {
                        $skipped++;
                        continue;
                    }
                    
                    // Processa ogni colonna esame
                    foreach ($examColumns as $examNum => $col) {
                        $examName = trim((string)$sheet->getCell($col . $row)->getValue());
                        
                        if (!empty($examName)) {
                            Exam::firstOrCreate(
                                [
                                    'student_id' => $student->id,
                                    'name' => $examName,
                                ],
                                [
                                    'exam_date' => null,
                                    'result' => null,
                                    'notes' => "Importato da seeder - Esame {$examNum}",
                                ]
                            );
                            $imported++;
                        }
                    }
                    
                } catch (\Exception $e) {
                    $skipped++;
                }
            }
            
            $this->command->info("✓ Importati {$imported} esami");
            $this->command->info("  Saltati: {$skipped}");
            
        } catch (\Exception $e) {
            $this->command->error("Errore: " . $e->getMessage());
        }
    }
}
