<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Contract;
use App\Models\AcademicYear;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;
use App\Services\OdsImportService;

class InvoiceSeeder extends Seeder
{
    protected $odsImportService;
    
    public function __construct()
    {
        $this->odsImportService = new OdsImportService();
    }
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Fatture ===');
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error("Nessun anno accademico attivo trovato");
            return;
        }
        
        $filePath = base_path('docs/materiale cliente/Db Contabile 2025-26.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato: {$filePath}");
            return;
        }
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            
            // Processa foglio "fatt corsi"
            $sheet = $spreadsheet->getSheetByName('fatt corsi');
            
            if (!$sheet) {
                $this->command->warn("Foglio 'fatt corsi' non trovato");
                return;
            }
            
            $this->command->info("Foglio 'fatt corsi' trovato");
            
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
            // Limita a 1000 righe per evitare problemi con formule estese
            $highestRow = min($highestRow, 1000);
            $imported = 0;
            $skipped = 0;
            
            $this->command->info("Trovate {$highestRow} righe (limitate a 1000 per sicurezza)");
            
            // Debug: mostra tutte le colonne trovate
            $this->command->info("Colonne trovate: " . implode(', ', array_keys($headerMap)));
            
            // Cerca colonne studente e importi (prova vari nomi, incluso typo "alllievo")
            $studentCol = $headerMap['cognome allievo'] ?? $headerMap['cognome'] ?? $headerMap['cognome studente'] ?? null;
            $studentNameCol = $headerMap['nome alllievo'] ?? $headerMap['nome allievo'] ?? $headerMap['nome'] ?? $headerMap['nome studente'] ?? null;
            
            // Se non trova, cerca pattern simili
            if (!$studentCol) {
                foreach ($headerMap as $header => $col) {
                    if (stripos($header, 'cognome') !== false || stripos($header, 'cogn') !== false) {
                        $studentCol = $col;
                        break;
                    }
                }
            }
            if (!$studentNameCol) {
                foreach ($headerMap as $header => $col) {
                    if ((stripos($header, 'nome') !== false && stripos($header, 'allievo') !== false) || 
                        (stripos($header, 'nome') !== false && stripos($header, 'studente') !== false)) {
                        $studentNameCol = $col;
                        break;
                    }
                }
            }
            
            // Cerca colonne rate (1° rata, 2° rata, etc.)
            $rateColumns = [];
            foreach ($headerMap as $header => $col) {
                if (preg_match('/(\d+)[°ª]\s*rata/i', $header, $matches)) {
                    $rateColumns[$matches[1]] = $col;
                }
            }
            
            if (empty($studentCol) || empty($studentNameCol)) {
                $this->command->warn("Colonne studente non trovate");
                return;
            }
            
            // Cerca colonna totale fatturato
            $totalCol = $headerMap['€ fatt'] ?? $headerMap['tot complessivo fatturato'] ?? $headerMap['€ fatturato'] ?? null;
            
            // Carica file gestionale per risolvere formule esterne
            $gestionaleFile = base_path('docs/materiale cliente/db 2025-26 gestionale.ods');
            $gestionaleSheet = null;
            if (file_exists($gestionaleFile)) {
                try {
                    $gestionaleSpreadsheet = IOFactory::load($gestionaleFile);
                    $gestionaleSheet = $gestionaleSpreadsheet->getSheetByName('dati');
                    $this->command->info("File gestionale caricato per risolvere formule");
                } catch (\Exception $e) {
                    $this->command->warn("Impossibile caricare file gestionale: " . $e->getMessage());
                }
            }
            
            // Processa righe (limita a prime 100 per debug)
            $maxRows = min($highestRow, 100);
            $debugCount = 0;
            
            for ($row = 2; $row <= $maxRows; $row++) {
                try {
                    // Usa getCalculatedValue per gestire formule
                    $cognomeValue = $sheet->getCell($studentCol . $row)->getValue();
                    $nomeValue = $sheet->getCell($studentNameCol . $row)->getValue();
                    
                    // Se è una formula che referenzia altro file, risolvi
                    if (is_string($cognomeValue) && strpos($cognomeValue, 'file://') !== false && $gestionaleSheet) {
                        if (preg_match('/!([A-Z]+)(\d+)/', $cognomeValue, $matches)) {
                            $cognomeValue = $gestionaleSheet->getCell($matches[1] . $matches[2])->getValue();
                        }
                    }
                    if (is_string($nomeValue) && strpos($nomeValue, 'file://') !== false && $gestionaleSheet) {
                        if (preg_match('/!([A-Z]+)(\d+)/', $nomeValue, $matches)) {
                            $nomeValue = $gestionaleSheet->getCell($matches[1] . $matches[2])->getValue();
                        }
                    }
                    
                    // Prova anche getCalculatedValue
                    if (empty($cognomeValue) || empty($nomeValue)) {
                        try {
                            $cognomeCalc = $sheet->getCell($studentCol . $row)->getCalculatedValue();
                            $nomeCalc = $sheet->getCell($studentNameCol . $row)->getCalculatedValue();
                            if (!empty($cognomeCalc)) $cognomeValue = $cognomeCalc;
                            if (!empty($nomeCalc)) $nomeValue = $nomeCalc;
                        } catch (\Exception $e) {
                            // Ignora errori di calcolo
                        }
                    }
                    
                    $cognome = trim((string)$cognomeValue);
                    $nome = trim((string)$nomeValue);
                    
                    if (empty($cognome) && empty($nome)) {
                        $skipped++;
                        continue;
                    }
                    
                    // Trova studente (cerca anche senza filtro anno accademico)
                    $student = Student::whereRaw('LOWER(last_name) = ?', [strtolower($cognome)])
                        ->whereRaw('LOWER(first_name) = ?', [strtolower($nome)])
                        ->where('academic_year_id', $academicYear->id)
                        ->first();
                    
                    // Se non trova, cerca in tutti gli anni
                    if (!$student) {
                        $student = Student::whereRaw('LOWER(last_name) = ?', [strtolower($cognome)])
                            ->whereRaw('LOWER(first_name) = ?', [strtolower($nome)])
                            ->first();
                    }
                    
                    if (!$student) {
                        if ($debugCount < 5) {
                            $this->command->warn("Riga {$row}: Studente non trovato: {$cognome} {$nome}");
                            $debugCount++;
                        }
                        $skipped++;
                        continue;
                    }
                    
                    // Calcola totale da colonna totale o da rate
                    $totalAmount = 0;
                    if ($totalCol) {
                        $totalValue = $sheet->getCell($totalCol . $row)->getCalculatedValue();
                        if (is_numeric($totalValue) && $totalValue > 0) {
                            $totalAmount = (float)$totalValue;
                        }
                    }
                    
                    // Se non c'è totale, calcola da rate
                    if ($totalAmount <= 0 && !empty($rateColumns)) {
                        foreach ($rateColumns as $rateNum => $col) {
                            try {
                                $rateValue = $sheet->getCell($col . $row)->getCalculatedValue();
                                if (is_numeric($rateValue) && $rateValue > 0) {
                                    $totalAmount += (float)$rateValue;
                                }
                            } catch (\Exception $e) {
                                // Ignora errori di calcolo
                            }
                        }
                    }
                    
                    if ($totalAmount <= 0) {
                        $skipped++;
                        continue;
                    }
                    
                    // Crea fattura
                    $invoice = Invoice::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'academic_year_id' => $academicYear->id,
                            'invoice_number' => 'FATT-' . $student->id . '-' . $academicYear->id,
                        ],
                        [
                            'invoice_date' => now(),
                            'due_date' => now()->addDays(30),
                            'total_amount' => $totalAmount,
                            'subtotal' => $totalAmount,
                            'status' => 'draft',
                        ]
                    );
                    
                    $imported++;
                    
                    if ($imported % 50 == 0) {
                        $this->command->info("  Importate {$imported} fatture...");
                    }
                    
                } catch (\Exception $e) {
                    $skipped++;
                }
            }
            
            $this->command->info("✓ Importate {$imported} fatture");
            $this->command->info("  Saltati: {$skipped}");
            
        } catch (\Exception $e) {
            $this->command->error("Errore: " . $e->getMessage());
        }
    }
}
