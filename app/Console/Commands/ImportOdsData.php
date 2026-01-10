<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ImportOdsData extends Command
{
    protected $signature = 'ods:import 
                            {file : Nome del file ODS da importare}
                            {--type= : Tipo di import (students, contracts, invoices, instruments, teachers, calendar)}
                            {--dry-run : Esegue solo l\'analisi senza importare}
                            {--sheet= : Nome del foglio specifico da importare}';
    
    protected $description = 'Importa dati da file ODS nel database';

    public function handle()
    {
        $fileName = $this->argument('file');
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');
        $sheetName = $this->option('sheet');
        
        $docsPath = base_path('docs/materiale cliente');
        $filePath = $docsPath . '/' . $fileName;
        
        if (!file_exists($filePath)) {
            $this->error("File non trovato: {$filePath}");
            return 1;
        }

        $this->info("Caricamento file: {$fileName}");
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheetCount = $spreadsheet->getSheetCount();
            
            $this->info("Fogli trovati: {$sheetCount}");
            
            // Se non specificato il tipo, mostra analisi
            if (!$type) {
                return $this->analyzeFile($spreadsheet, $sheetName);
            }
            
            // Importazione basata sul tipo
            return $this->importByType($spreadsheet, $type, $dryRun, $sheetName);
            
        } catch (\Exception $e) {
            $this->error("Errore: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    protected function analyzeFile($spreadsheet, $sheetName = null)
    {
        $sheetsToAnalyze = $sheetName 
            ? [$spreadsheet->getSheetByName($sheetName)]
            : $spreadsheet->getAllSheets();
        
        foreach ($sheetsToAnalyze as $sheet) {
            if (!$sheet) continue;
            
            $this->info("\n=== Foglio: {$sheet->getTitle()} ===");
            
            $highestRow = $sheet->getHighestRow();
            $highestCol = $sheet->getHighestColumn();
            $highestColIndex = Coordinate::columnIndexFromString($highestCol);
            
            $this->info("Righe: {$highestRow}, Colonne: {$highestCol} ({$highestColIndex})");
            
            // Cerca header (prima riga non vuota o con più celle piene)
            $headerRow = $this->findHeaderRow($sheet, min(10, $highestRow));
            
            if ($headerRow) {
                $this->info("Header trovato alla riga: {$headerRow}");
                $headers = $this->readRow($sheet, $headerRow, $highestColIndex);
                $this->table(['Col', 'Header'], array_map(fn($i, $h) => [chr(65 + $i), $h], array_keys($headers), $headers));
                
                // Mostra prime 3 righe dati
                $this->info("\nPrime 3 righe dati:");
                for ($r = $headerRow + 1; $r <= min($headerRow + 3, $highestRow); $r++) {
                    $row = $this->readRow($sheet, $r, $highestColIndex);
                    $this->line("Riga {$r}: " . implode(' | ', array_slice($row, 0, 10)));
                }
            } else {
                $this->warn("Nessun header trovato, mostro prime 5 righe:");
                for ($r = 1; $r <= min(5, $highestRow); $r++) {
                    $row = $this->readRow($sheet, $r, $highestColIndex);
                    $this->line("Riga {$r}: " . implode(' | ', array_slice($row, 0, 10)));
                }
            }
        }
        
        return 0;
    }

    protected function findHeaderRow($sheet, $maxRows)
    {
        // Cerca la riga con più celle non vuote (probabile header)
        $bestRow = 0;
        $maxCells = 0;
        
        for ($r = 1; $r <= $maxRows; $r++) {
            $nonEmpty = 0;
            $highestCol = $sheet->getHighestColumn($r);
            $highestColIndex = Coordinate::columnIndexFromString($highestCol);
            
            for ($c = 1; $c <= min($highestColIndex, 50); $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $cell = $sheet->getCell($colLetter . $r);
                if ($cell->getValue() !== null && trim($cell->getValue()) !== '') {
                    $nonEmpty++;
                }
            }
            
            if ($nonEmpty > $maxCells) {
                $maxCells = $nonEmpty;
                $bestRow = $r;
            }
        }
        
        return $maxCells > 3 ? $bestRow : null;
    }

    protected function readRow($sheet, $row, $maxCol)
    {
        $data = [];
        for ($c = 1; $c <= $maxCol; $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $cell = $sheet->getCell($colLetter . $row);
            $value = $cell->getValue();
            $data[] = $value !== null ? (string)$value : '';
        }
        return $data;
    }

    protected function importByType($spreadsheet, $type, $dryRun, $sheetName)
    {
        $this->info("Importazione tipo: {$type}");
        
        if ($dryRun) {
            $this->warn("DRY RUN - Nessun dato verrà importato");
        }
        
        switch ($type) {
            case 'students':
                return $this->importStudents($spreadsheet, $dryRun, $sheetName);
            case 'contracts':
                return $this->importContracts($spreadsheet, $dryRun, $sheetName);
            case 'invoices':
                return $this->importInvoices($spreadsheet, $dryRun, $sheetName);
            case 'instruments':
                return $this->importInstruments($spreadsheet, $dryRun, $sheetName);
            case 'teachers':
                return $this->importTeachers($spreadsheet, $dryRun, $sheetName);
            case 'calendar':
                return $this->importCalendar($spreadsheet, $dryRun, $sheetName);
            default:
                $this->error("Tipo non supportato: {$type}");
                return 1;
        }
    }

    protected function importStudents($spreadsheet, $dryRun, $sheetName)
    {
        $this->info("Importazione studenti dal file gestionale...");
        
        $sheet = $sheetName 
            ? $spreadsheet->getSheetByName($sheetName) 
            : $spreadsheet->getSheet(0);
        
        if (!$sheet) {
            $this->error("Foglio non trovato");
            return 1;
        }
        
        // Leggi header e crea mapping dinamico
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
        
        // Mapping header -> campo
        $fieldMap = [
            'cognome' => 'last_name',
            'nome' => 'first_name',
            'cod. fiscale allievo' => 'tax_code',
            'nato il' => 'birth_date',
            'età' => 'age',
            'minore' => 'is_minor',
            'indirizzo allievo' => 'address',
            'cap' => 'postal_code',
            'città' => 'city',
            'cell 3/ allievo' => 'phone',
            'mail allievo' => 'email',
            'data di arrivo' => 'first_contact_date',
            'come è venuto a sapere di noi' => 'source',
            'note prove e didattiche' => 'notes',
            'note varie' => 'admin_notes',
            'data ultimo' => 'last_contact_date',
            'stato' => 'status',
            'n. iscritto' => 'enrollment_code',
            '€ iscrizione' => 'enrollment_fee',
            'sconto' => 'discount',
            'nuovo iscritto' => 'is_new',
            // Genitori
            'cognome genitore 1' => 'guardian1_last',
            'nome genitore 1' => 'guardian1_first',
            'cognome genitore 2' => 'guardian2_last',
            'nome genitore 2' => 'guardian2_first',
            'cell 1 /madre' => 'guardian1_phone',
            'cell 2/padre' => 'guardian2_phone',
            'mail 1' => 'guardian1_email',
            'mail 2' => 'guardian2_email',
        ];
        
        // Crea mapping colonne
        $colMap = [];
        foreach ($fieldMap as $headerKey => $field) {
            if (isset($headerMap[$headerKey])) {
                $colMap[$field] = $headerMap[$headerKey];
            }
        }
        
        $highestRow = $sheet->getHighestRow();
        
        $this->info("Trovate {$highestRow} righe, {$highestColIndex} colonne");
        $this->info("Colonne mappate: " . count($colMap));
        
        // Verifica anno accademico
        $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->error("Nessun anno accademico attivo trovato. Creane uno prima di importare.");
            return 1;
        }
        
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        
        $bar = $this->output->createProgressBar($highestRow - $headerRow);
        $bar->start();
        
        // Inizia dalla riga 2 (dopo header)
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            try {
                $data = $this->readRowByMap($sheet, $row, $colMap);
                
                // Salta righe vuote
                if (empty($data['first_name']) && empty($data['last_name'])) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                if ($dryRun) {
                    $this->line("\n[DRY RUN] Riga {$row}: {$data['first_name']} {$data['last_name']}");
                    $bar->advance();
                    continue;
                }
                
                // Normalizza dati
                $studentData = $this->normalizeStudentData($data, $academicYear);
                
                // Cerca studente esistente (per codice fiscale o nome+cognome)
                $student = null;
                if (!empty($studentData['tax_code'])) {
                    $student = \App\Models\Student::where('tax_code', $studentData['tax_code'])->first();
                }
                
                if (!$student && !empty($studentData['first_name']) && !empty($studentData['last_name'])) {
                    $student = \App\Models\Student::where('first_name', $studentData['first_name'])
                        ->where('last_name', $studentData['last_name'])
                        ->where('academic_year_id', $academicYear->id)
                        ->first();
                }
                
                // Crea o aggiorna studente
                if ($student) {
                    $student->update($studentData);
                } else {
                    $student = \App\Models\Student::create($studentData);
                }
                
                // Gestisci genitori
                $this->importGuardians($student, $data);
                
                $imported++;
                
            } catch (\Exception $e) {
                $errors++;
                $this->warn("\nErrore riga {$row}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("Importazione completata!");
        $this->table(
            ['Risultato', 'Conteggio'],
            [
                ['Importati', $imported],
                ['Saltati', $skipped],
                ['Errori', $errors],
            ]
        );
        
        return 0;
    }
    
    protected function readRowByMap($sheet, $row, $colMap)
    {
        $data = [];
        foreach ($colMap as $key => $col) {
            if (empty($col)) continue;
            try {
                $cell = $sheet->getCell($col . $row);
                $value = $cell->getValue();
                $data[$key] = $value !== null ? trim((string)$value) : '';
            } catch (\Exception $e) {
                $data[$key] = '';
            }
        }
        return $data;
    }
    
    protected function normalizeStudentData($data, $academicYear)
    {
        // Normalizza data di nascita
        $birthDate = null;
        if (!empty($data['birth_date'])) {
            $birthDate = $this->parseDate($data['birth_date']);
        }
        
        // Calcola età se non presente
        $age = null;
        if (!empty($data['age'])) {
            $age = (int)$data['age'];
        } elseif ($birthDate) {
            $age = $birthDate->diffInYears(now());
        }
        
        // Normalizza stato
        $status = 'interested';
        if (!empty($data['status'])) {
            $statusStr = strtolower($data['status']);
            if (strpos($statusStr, 'iscritto') !== false || strpos($statusStr, 'attivo') !== false) {
                $status = 'enrolled';
            } elseif (strpos($statusStr, 'interessato') !== false) {
                $status = 'interested';
            }
        }
        
        // Normalizza date
        $firstContactDate = !empty($data['first_contact_date']) 
            ? $this->parseDate($data['first_contact_date']) 
            : null;
        $lastContactDate = !empty($data['last_contact_date']) 
            ? $this->parseDate($data['last_contact_date']) 
            : null;
        
        // Normalizza importi
        $enrollmentFee = !empty($data['enrollment_fee']) 
            ? $this->parseAmount($data['enrollment_fee']) 
            : null;
        
        return [
            'academic_year_id' => $academicYear->id,
            'code' => !empty($data['enrollment_code']) ? $data['enrollment_code'] : null,
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'birth_date' => $birthDate,
            'age' => $age,
            'tax_code' => !empty($data['tax_code']) ? strtoupper($data['tax_code']) : null,
            'status' => $status,
            'how_know_us' => $data['source'] ?? null,
            'notes' => trim(($data['notes'] ?? '') . ' ' . ($data['admin_notes'] ?? '')),
            // 'last_contact_date' => $lastContactDate, // Campo non presente nella tabella
            'address' => $data['address'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'city' => $data['city'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => !empty($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? $data['email'] : null : null,
        ];
    }
    
    protected function importGuardians($student, $data)
    {
        // Genitore 1
        if (!empty($data['guardian1_first']) || !empty($data['guardian1_last'])) {
            $guardian1 = $this->findOrCreateGuardian([
                'first_name' => $data['guardian1_first'] ?? '',
                'last_name' => $data['guardian1_last'] ?? '',
                'cell_1' => $data['guardian1_phone'] ?? null,
                'email_1' => !empty($data['guardian1_email']) 
                    ? filter_var($data['guardian1_email'], FILTER_VALIDATE_EMAIL) 
                        ? $data['guardian1_email'] 
                        : null 
                    : null,
                'relationship' => 'other', // Usa 'other' invece di 'madre'
            ]);
            
            if ($guardian1 && !$student->guardians()->where('guardian_id', $guardian1->id)->exists()) {
                $student->guardians()->attach($guardian1->id, [
                    'relationship_type' => 'madre',
                    'is_primary' => true,
                    'is_billing_contact' => true,
                ]);
            }
        }
        
        // Genitore 2
        if (!empty($data['guardian2_first']) || !empty($data['guardian2_last'])) {
            $guardian2 = $this->findOrCreateGuardian([
                'first_name' => $data['guardian2_first'] ?? '',
                'last_name' => $data['guardian2_last'] ?? '',
                'cell_2' => $data['guardian2_phone'] ?? null,
                'email_2' => !empty($data['guardian2_email']) 
                    ? filter_var($data['guardian2_email'], FILTER_VALIDATE_EMAIL) 
                        ? $data['guardian2_email'] 
                        : null 
                    : null,
                'relationship' => 'other', // Usa 'other' invece di 'padre'
            ]);
            
            if ($guardian2 && !$student->guardians()->where('guardian_id', $guardian2->id)->exists()) {
                $student->guardians()->attach($guardian2->id, [
                    'relationship_type' => 'padre',
                    'is_primary' => false,
                    'is_billing_contact' => false,
                ]);
            }
        }
    }
    
    protected function findOrCreateGuardian($data)
    {
        if (empty($data['first_name']) && empty($data['last_name'])) {
            return null;
        }
        
        // Cerca per nome e cognome
        $guardian = \App\Models\Guardian::where('first_name', $data['first_name'])
            ->where('last_name', $data['last_name'])
            ->first();
        
        if (!$guardian) {
            $guardian = \App\Models\Guardian::create($data);
        } else {
            // Aggiorna dati se necessario
            $guardian->update(array_filter($data));
        }
        
        return $guardian;
    }
    
    protected function parseDate($value)
    {
        if (empty($value)) return null;
        
        // Prova vari formati
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
        
        foreach ($formats as $format) {
            try {
                $date = \Carbon\Carbon::createFromFormat($format, $value);
                return $date;
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Prova parsing automatico
        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    protected function parseAmount($value)
    {
        if (empty($value)) return null;
        
        // Rimuovi € e spazi
        $value = str_replace(['€', ' ', ','], '', $value);
        $value = str_replace(',', '.', $value);
        
        return is_numeric($value) ? (float)$value : null;
    }

    protected function importContracts($spreadsheet, $dryRun, $sheetName)
    {
        $this->info("Importazione contratti...");
        // TODO: Implementare
        return 0;
    }

    protected function importInvoices($spreadsheet, $dryRun, $sheetName)
    {
        $this->info("Importazione fatture...");
        // TODO: Implementare
        return 0;
    }

    protected function importInstruments($spreadsheet, $dryRun, $sheetName)
    {
        $this->info("Importazione strumenti...");
        // TODO: Implementare
        return 0;
    }

    protected function importTeachers($spreadsheet, $dryRun, $sheetName)
    {
        $this->info("Importazione docenti...");
        // TODO: Implementare
        return 0;
    }

    protected function importCalendar($spreadsheet, $dryRun, $sheetName)
    {
        $this->info("Importazione calendario...");
        // TODO: Implementare
        return 0;
    }
}
