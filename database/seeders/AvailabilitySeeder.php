<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentAvailability;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Disponibilità ===');
        
        $filePath = base_path('docs/materiale cliente/Anagrafica e disponibilità a.s. 2025_26 (Risposte).xlsx');
        
        if (!file_exists($filePath)) {
            $this->command->error("File non trovato: {$filePath}");
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheet(0);
            
            $highestRow = $sheet->getHighestRow();
            $this->command->info("Trovate {$highestRow} righe");
            
            // Mapping colonne (riga 1 = header)
            $colMap = [
                'cognome' => 'B',
                'nome' => 'C',
                'cf' => 'J',
                'lunedi' => 'X',
                'martedi' => 'Y',
                'mercoledi' => 'Z',
                'giovedi' => 'AA',
                'venerdi' => 'AB',
                'sabato' => 'AC',
                'impegni' => 'AD',
                'definitivo' => 'AE',
                'preferenze' => 'AF',
            ];
            
            $imported = 0;
            $skipped = 0;
            $errors = [];
            
            $this->command->info("Inizio importazione disponibilità studenti...");
            $this->command->newLine();
            
            // Inizia dalla riga 2 (riga 1 = header)
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $cognome = $this->getCellValue($sheet, $colMap['cognome'] . $row);
                    $nome = $this->getCellValue($sheet, $colMap['nome'] . $row);
                    $cf = $this->getCellValue($sheet, $colMap['cf'] . $row);
                    
                    if (empty($cognome) && empty($nome)) {
                        $skipped++;
                        continue;
                    }
                    
                    // Trova studente
                    $student = null;
                    $cognome = trim($cognome);
                    $nome = trim($nome);
                    $cf = trim($cf);
                    
                    if (!empty($cf)) {
                        $student = Student::where('tax_code', strtoupper($cf))->first();
                    }
                    
                    if (!$student && !empty($cognome) && !empty($nome)) {
                        // Prova match esatto
                        $student = Student::whereRaw('LOWER(last_name) = ?', [strtolower($cognome)])
                            ->whereRaw('LOWER(first_name) = ?', [strtolower($nome)])
                            ->first();
                        
                        // Se non trovato, prova match parziale
                        if (!$student) {
                            $student = Student::whereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($cognome) . '%'])
                                ->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($nome) . '%'])
                                ->first();
                        }
                    }
                    
                    if (!$student) {
                        $skipped++;
                        if (count($errors) < 10) {
                            $errors[] = "Riga {$row}: Studente non trovato ({$cognome} {$nome} - CF: {$cf})";
                        }
                        continue;
                    }
                    
                    // Importa disponibilità per ogni giorno
                    $days = [
                        'lunedi' => 'monday',
                        'martedi' => 'tuesday',
                        'mercoledi' => 'wednesday',
                        'giovedi' => 'thursday',
                        'venerdi' => 'friday',
                    ];
                    
                    $notes = [];
                    $impegni = $this->getCellValue($sheet, $colMap['impegni'] . $row);
                    $preferenze = $this->getCellValue($sheet, $colMap['preferenze'] . $row);
                    
                    if (!empty($impegni)) {
                        $notes[] = "Impegni: {$impegni}";
                    }
                    if (!empty($preferenze)) {
                        $notes[] = "Preferenze: {$preferenze}";
                    }
                    
                    foreach ($days as $colKey => $dayOfWeek) {
                        $timeValue = $this->getCellValue($sheet, $colMap[$colKey] . $row);
                        
                        if (!empty($timeValue) && is_numeric($timeValue)) {
                            // Converti valore Excel (0-1) in orario
                            $time = $this->excelTimeToTime($timeValue);
                            
                            if ($time) {
                                // Crea o aggiorna disponibilità
                                StudentAvailability::updateOrCreate(
                                    [
                                        'student_id' => $student->id,
                                        'day_of_week' => $dayOfWeek,
                                    ],
                                    [
                                        'time_start' => $time,
                                        'time_end' => null, // Fine orario non specificata nel file
                                        'available' => true,
                                        'notes' => !empty($notes) ? implode(' | ', $notes) : null,
                                    ]
                                );
                            }
                        }
                    }
                    
                    // Sabato (sì/no)
                    $sabato = $this->getCellValue($sheet, $colMap['sabato'] . $row);
                    if (!empty($sabato) && strtolower(trim($sabato)) === 'no') {
                        // Non disponibile il sabato
                        StudentAvailability::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'day_of_week' => 'saturday',
                            ],
                            [
                                'available' => false,
                                'notes' => !empty($notes) ? implode(' | ', $notes) : null,
                            ]
                        );
                    }
                    
                    $imported++;
                    if ($imported % 50 == 0) {
                        $this->command->info("  Importati: {$imported} studenti");
                    }
                    
                } catch (\Exception $e) {
                    if (count($errors) < 10) {
                        $errors[] = "Riga {$row}: " . $e->getMessage();
                    }
                }
            }
            
            $this->command->newLine();
            $this->command->info("✓ Disponibilità importate per {$imported} studenti");
            $this->command->info("  Saltati: {$skipped}");
            if (!empty($errors)) {
                $this->command->warn("  Errori: " . count($errors));
                if (count($errors) <= 10) {
                    foreach ($errors as $error) {
                        $this->command->warn("    - {$error}");
                    }
                }
            }
            
        } catch (\Exception $e) {
            $this->command->error("Errore durante importazione: " . $e->getMessage());
        }
    }
    
    protected function getCellValue($sheet, $cell)
    {
        try {
            $value = $sheet->getCell($cell)->getCalculatedValue();
            return $value !== null ? trim((string)$value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Converte valore Excel time (0-1) in formato time HH:MM:SS
     * Esempio: 0.56944444444525 = 13:40:00
     */
    protected function excelTimeToTime($excelTime)
    {
        if (!is_numeric($excelTime) || $excelTime < 0 || $excelTime >= 1) {
            return null;
        }
        
        // Excel time è una frazione di giorno (0 = mezzanotte, 0.5 = mezzogiorno)
        $seconds = round($excelTime * 86400); // 86400 secondi in un giorno
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
