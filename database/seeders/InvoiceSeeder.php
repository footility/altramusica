<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\StudentYear;
use App\Models\Contract;
use App\Models\AcademicYear;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use App\Services\OdsImportService;
use App\Models\PaymentPlan;

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
            
            // Colonne rate (preferiamo i totali: "tot 1° rata", ecc.)
            $installmentTotalCols = [
                1 => $headerMap['tot 1° rata'] ?? $headerMap['tot 1ª rata'] ?? null,
                2 => $headerMap['tot 2° rata'] ?? $headerMap['tot 2ª rata'] ?? null,
                3 => $headerMap['tot 3° rata'] ?? $headerMap['tot 3ª rata'] ?? null,
                4 => $headerMap['tot 4° rata'] ?? $headerMap['tot 4ª rata'] ?? null,
            ];

            // Date scadenze (in ODS spesso sono 1-2)
            $dueDateCols = [
                1 => $headerMap['scadenza 1'] ?? null,
                2 => $headerMap['scadenza 2'] ?? null,
                3 => $headerMap['scadenza 3'] ?? null,
                4 => $headerMap['scadenza 4'] ?? null,
            ];
            
            if (empty($studentCol) || empty($studentNameCol)) {
                $this->command->warn("Colonne studente non trovate");
                return;
            }
            
            // Totale fattura
            $totalCol = $headerMap['€ fatt'] ?? $headerMap['tot complessivo fatturato'] ?? $headerMap['€ fatturato'] ?? null;

            // Data fattura (se presente)
            $invoiceDateCol = $headerMap['data invio fatt'] ?? $headerMap['data invio'] ?? null;
            
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
            
            // Processa righe (tutte, entro il limite safety)
            $maxRows = $highestRow;
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
                    $studentYear = StudentYear::where('academic_year_id', $academicYear->id)
                        ->whereHas('student', function ($q) use ($cognome, $nome) {
                            $q->whereRaw('LOWER(last_name) = ?', [strtolower($cognome)])
                              ->whereRaw('LOWER(first_name) = ?', [strtolower($nome)]);
                        })
                        ->with('student')
                        ->first();
                    $student = $studentYear?->student;
                    
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
                        $totalValueRaw = $sheet->getCell($totalCol . $row)->getValue();
                        $totalValueCalc = null;
                        try { $totalValueCalc = $sheet->getCell($totalCol . $row)->getCalculatedValue(); } catch (\Throwable $e) {}

                        $totalAmount = $this->parseMoney($totalValueRaw);
                        if ($totalAmount <= 0 && $totalValueCalc !== null) {
                            $totalAmount = $this->parseMoney($totalValueCalc);
                        }
                    }
                    
                    // Se non c'è totale, calcola da rate
                    if ($totalAmount <= 0) {
                        foreach ($installmentTotalCols as $rateNum => $col) {
                            if (!$col) continue;
                            $rateValueRaw = $sheet->getCell($col . $row)->getValue();
                            $rateValueCalc = null;
                            try { $rateValueCalc = $sheet->getCell($col . $row)->getCalculatedValue(); } catch (\Throwable $e) {}

                            $amount = $this->parseMoney($rateValueRaw);
                            if ($amount <= 0 && $rateValueCalc !== null) {
                                $amount = $this->parseMoney($rateValueCalc);
                            }
                            if ($amount > 0) {
                                $totalAmount += $amount;
                            }
                        }
                    }
                    
                    if ($totalAmount <= 0) {
                        $skipped++;
                        continue;
                    }
                    
                    $invoiceDate = $invoiceDateCol ? $this->parseDateValue($sheet->getCell($invoiceDateCol . $row)->getValue()) : null;
                    $invoiceDate = $invoiceDate ?: now();

                    // Prima scadenza disponibile come due_date fattura
                    $firstDue = null;
                    foreach ($dueDateCols as $rateNum => $col) {
                        if (!$col) continue;
                        $parsed = $this->parseDateValue($sheet->getCell($col . $row)->getValue());
                        if ($parsed) { $firstDue = $parsed; break; }
                    }
                    $dueDate = $firstDue ?: $invoiceDate->copy()->addDays(30);

                    // invoice_number univoco (una fattura per studente/anno in MVP; se già esiste, aggiunge suffisso)
                    $baseNumber = 'FATT-' . $student->id . '-' . $academicYear->id;
                    $invoiceNumber = $baseNumber;
                    $suffix = 1;
                    while (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
                        $suffix++;
                        $invoiceNumber = $baseNumber . '-' . $suffix;
                        if ($suffix > 50) break; // safety
                    }

                    $invoice = Invoice::create([
                        'student_id' => $student->id,
                        'invoice_number' => $invoiceNumber,
                        'invoice_date' => $invoiceDate->format('Y-m-d'),
                        'due_date' => $dueDate->format('Y-m-d'),
                        'total_amount' => $totalAmount,
                        'subtotal' => $totalAmount,
                        'status' => 'draft',
                        'notes' => 'Importato da contabilità (ODS) - fatt corsi',
                    ]);

                    // Crea piano rate (se presenti importi rate, altrimenti 1 rata unica)
                    $hasAnyInstallment = false;
                    foreach ($installmentTotalCols as $rateNum => $col) {
                        if (!$col) continue;
                        $amount = $this->parseMoney($sheet->getCell($col . $row)->getValue());
                        if ($amount <= 0) {
                            try { $amount = $this->parseMoney($sheet->getCell($col . $row)->getCalculatedValue()); } catch (\Throwable $e) {}
                        }
                        if ($amount <= 0) continue;
                        $hasAnyInstallment = true;

                        $due = $dueDateCols[$rateNum] ?? null;
                        $dueParsed = $due ? $this->parseDateValue($sheet->getCell($due . $row)->getValue()) : null;
                        $dueParsed = $dueParsed ?: $invoiceDate->copy()->addDays(30 * $rateNum);

                        PaymentPlan::create([
                            'invoice_id' => $invoice->id,
                            'installment_number' => $rateNum,
                            'due_date' => $dueParsed->format('Y-m-d'),
                            'amount' => $amount,
                            'status' => 'pending',
                        ]);
                    }

                    if (!$hasAnyInstallment) {
                        PaymentPlan::create([
                            'invoice_id' => $invoice->id,
                            'installment_number' => 1,
                            'due_date' => $dueDate->format('Y-m-d'),
                            'amount' => $totalAmount,
                            'status' => 'pending',
                        ]);
                    }
                    
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

    private function parseMoney($value): float
    {
        if ($value === null) return 0.0;
        if (is_int($value) || is_float($value)) return (float) $value;

        $s = trim((string) $value);
        if ($s === '') return 0.0;

        // Rimuovi simboli e testo
        $s = str_replace(['€', "\xc2\xa0"], ['', ''], $s);
        $s = trim($s);

        // Gestione formati tipo "1.234,56" o "1234,56"
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);

        // Mantieni solo numeri, punto e meno
        $s = preg_replace('/[^0-9\\.-]/', '', $s);
        if ($s === '' || $s === '.' || $s === '-') return 0.0;

        return is_numeric($s) ? (float) $s : 0.0;
    }

    private function parseDateValue($value): ?Carbon
    {
        if ($value === null || $value === '') return null;

        // Excel serial date
        if (is_numeric($value) && $value > 30000) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value));
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $s = trim((string) $value);
        if ($s === '') return null;

        // dd/mm/yyyy o dd-mm-yyyy
        try {
            if (preg_match('/^(\\d{1,2})[\\/\\-](\\d{1,2})[\\/\\-](\\d{2,4})$/', $s, $m)) {
                $y = (int) $m[3];
                if ($y < 100) $y += 2000;
                return Carbon::createFromDate($y, (int) $m[2], (int) $m[1]);
            }
            return Carbon::parse($s);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
