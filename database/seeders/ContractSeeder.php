<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contract;
use App\Models\Student;
use App\Models\StudentYear;
use App\Models\AcademicYear;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Contratti ===');
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error("Nessun anno accademico attivo trovato");
            return;
        }
        
        $filePath = base_path('docs/materiale cliente/Db Contratti 25-26.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato: {$filePath}");
            return;
        }
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheetByName('Contratti');
            
            if (!$sheet) {
                $this->command->warn("Foglio 'Contratti' non trovato");
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
            
            // Mapping header -> campo
            $fieldMap = [
                'codice contratto' => 'contract_number',
                'stato' => 'status',
                'cognome' => 'student_last_name',
                'nome' => 'student_first_name',
                'data di nascita' => 'student_birth_date',
                'cod. fiscale allievo' => 'student_tax_code',
                'data inizio corso 1' => 'course1_start',
                'corso 1' => 'course1_name',
                'descrizione' => 'course1_description',
                'tipologia' => 'course1_type',
                '1 rata' => 'rate1_amount',
                '2 rata' => 'rate2_amount',
                '3 rata' => 'rate3_amount',
                'tot corso1' => 'course1_total',
                'data inizio corso 2' => 'course2_start',
                'corso 2' => 'course2_name',
                'tot corso2' => 'course2_total',
                'data inizio corso 3' => 'course3_start',
                'corso 3' => 'course3_name',
                'tot corso 3' => 'course3_total',
                'tot 1 rata' => 'total_rate1',
                'tot 2 rata' => 'total_rate2',
                'tot 3 rata' => 'total_rate3',
                'totale anno corsi' => 'total_year',
                'orchestra 1' => 'orchestra1',
                'costo orch 1' => 'orchestra1_cost',
                'data inizio orchestre' => 'orchestra_start',
                'totale anno' => 'total_year_with_orch',
                'data invio/consegna' => 'sent_date',
                'data invio sollecito' => 'reminder_date',
                'data ritorno' => 'signed_date',
                'privacy' => 'privacy',
                'note' => 'notes',
            ];
            
            $colMap = [];
            foreach ($fieldMap as $headerKey => $field) {
                if (isset($headerMap[$headerKey])) {
                    $colMap[$field] = $headerMap[$headerKey];
                }
            }
            
            // Debug: mostra mapping trovato
            $this->command->info("Colonne mappate: " . count($colMap));
            if (count($colMap) < 5) {
                $this->command->warn("Attenzione: poche colonne mappate. Headers trovati:");
                foreach (array_slice($headerMap, 0, 20) as $header => $col) {
                    $this->command->warn("  '{$header}' => {$col}");
                }
            }
            
            $highestRow = $sheet->getHighestRow();
            $totalRows = $highestRow - $headerRow;
            $imported = 0;
            $skipped = 0;
            $errors = [];
            $chunkSize = 50; // Processa 50 righe alla volta
            
            $this->command->info("Trovate {$highestRow} righe (da processare: {$totalRows})");
            $this->command->info("Processamento a chunk di {$chunkSize} righe");
            $this->command->newLine();
            
            // Carica file gestionale una volta sola (per formule esterne)
            $gestionaleFile = base_path('docs/materiale cliente/db 2025-26 gestionale.ods');
            $gestionaleSheet = null;
            if (file_exists($gestionaleFile)) {
                try {
                    $this->command->info("Caricamento file gestionale per risolvere formule esterne...");
                    $gestionaleSpreadsheet = IOFactory::load($gestionaleFile);
                    $gestionaleSheet = $gestionaleSpreadsheet->getSheetByName('dati');
                    $this->command->info("✓ File gestionale caricato");
                } catch (\Exception $e) {
                    $this->command->warn("⚠ Impossibile caricare file gestionale: " . $e->getMessage());
                }
            }
            
            $this->command->newLine();
            $this->command->info("Inizio importazione...");
            $this->command->newLine();
            
            // Processa a chunk
            $chunks = array_chunk(range($headerRow + 1, $highestRow), $chunkSize);
            $totalChunks = count($chunks);
            $currentChunk = 0;
            
            foreach ($chunks as $chunkRows) {
                $currentChunk++;
                $this->command->info("--- Chunk {$currentChunk}/{$totalChunks} (righe " . min($chunkRows) . "-" . max($chunkRows) . ") ---");
                
                foreach ($chunkRows as $row) {
                try {
                    $data = $this->readRowByMap($sheet, $row, $colMap, $gestionaleSheet);
                    
                    if (empty($data['student_first_name']) && empty($data['student_last_name'])) {
                        $skipped++;
                        $this->command->warn("  Riga {$row}: Saltata (dati vuoti)");
                        continue;
                    }
                    
                    $this->command->line("  Riga {$row}: Processando {$data['student_first_name']} {$data['student_last_name']}...");
                    
                    // Trova studente
                    $student = null;
                    if (!empty($data['student_tax_code'])) {
                        $student = Student::where('tax_code', strtoupper($data['student_tax_code']))->first();
                        if ($student) {
                            $this->command->line("    ✓ Studente trovato per CF: {$data['student_tax_code']}");
                        }
                    }
                    
                    if (!$student && !empty($data['student_first_name']) && !empty($data['student_last_name'])) {
                        $student = Student::where('first_name', $data['student_first_name'])
                            ->where('last_name', $data['student_last_name'])
                            ->first();
                        if ($student) {
                            $this->command->line("    ✓ Studente trovato per nome: {$data['student_first_name']} {$data['student_last_name']}");
                        }
                    }
                    
                    if (!$student) {
                        $errorMsg = "Riga {$row}: Studente non trovato ({$data['student_first_name']} {$data['student_last_name']})";
                        $errors[] = $errorMsg;
                        $this->command->error("    ✗ {$errorMsg}");
                        continue;
                    }
                    
                    // Normalizza dati contratto
                    $this->command->line("    Normalizzazione dati contratto...");
                    $contractData = $this->normalizeContractData($data, $academicYear, $student);
                    
                    // Cerca contratto esistente per studente e anno (non per numero, che può essere duplicato)
                    $contract = Contract::where('student_id', $student->id)
                        ->where('academic_year_id', $academicYear->id)
                        ->first();

                    // Assicura esistenza record annuale studente (status/note ecc) anche se lo StudentSeeder non lo ha creato
                    StudentYear::firstOrCreate(
                        ['student_id' => $student->id, 'academic_year_id' => $academicYear->id],
                        ['status' => 'prospect']
                    );
                    
                    if ($contract) {
                        $this->command->line("    ✓ Contratto esistente trovato per studente e anno (ID: {$contract->id}, Numero: {$contract->contract_number})");
                    }
                    
                    // Se il contract_number esiste già per un altro studente, genera uno nuovo univoco
                    if (!empty($contractData['contract_number'])) {
                        $existingWithSameNumber = Contract::where('contract_number', $contractData['contract_number'])
                            ->where('id', '!=', $contract ? $contract->id : 0)
                            ->first();
                        
                        if ($existingWithSameNumber) {
                            $this->command->warn("    ⚠ Numero contratto '{$contractData['contract_number']}' già usato, genero numero univoco");
                            $contractData['contract_number'] = 'CONTRACT-' . $student->id . '-' . $academicYear->slug;
                        }
                    }
                    
                    // Crea o aggiorna contratto
                    if ($contract) {
                        // Mantieni il contract_number esistente se è già univoco
                        $originalNumber = $contract->contract_number;
                        unset($contractData['contract_number']); // Non aggiornare il numero se il contratto esiste già
                        $contract->update($contractData);
                        $this->command->info("    ✓ Contratto aggiornato (mantiene numero: {$originalNumber})");
                    } else {
                        $contract = Contract::create($contractData);
                        $this->command->info("    ✓ Contratto creato: {$contractData['contract_number']}");
                    }
                    
                    $imported++;
                    $this->command->line("    ✓ Riga {$row} completata (Totale importati: {$imported})");
                    $this->command->newLine();
                    
                } catch (\Exception $e) {
                    $errorMsg = "Riga {$row}: " . $e->getMessage();
                    $errors[] = $errorMsg;
                    $this->command->error("    ✗ ERRORE: {$errorMsg}");
                    $this->command->newLine();
                }
            }
            
            $this->command->info("Chunk {$currentChunk}/{$totalChunks} completato");
            $this->command->newLine();
            }
            
            $this->command->info("✓ Contratti importati: {$imported}");
            $this->command->info("  Saltati: {$skipped}");
            if (!empty($errors)) {
                $this->command->warn("  Errori: " . count($errors));
            }
            
        } catch (\Exception $e) {
            $this->command->error("Errore: " . $e->getMessage());
        }
    }
    
    protected function readRowByMap($sheet, $row, $colMap, $gestionaleSheet = null)
    {
        $data = [];
        
        foreach ($colMap as $key => $col) {
            if (empty($col)) continue;
            try {
                $cell = $sheet->getCell($col . $row);
                $value = null;
                
                // Controlla se è una formula esterna
                $cellValue = $cell->getValue();
                if (is_string($cellValue) && strpos($cellValue, 'file://') !== false && $gestionaleSheet) {
                    // Estrai riferimento cella (es: #$dati!H2)
                    if (preg_match('/#\$dati!([A-Z]+)(\d+)/', $cellValue, $matches)) {
                        $refCol = $matches[1];
                        $refRow = $matches[2];
                        try {
                            $value = $gestionaleSheet->getCell($refCol . $refRow)->getValue();
                        } catch (\Exception $e) {
                            $value = null;
                        }
                    }
                } else {
                    // Prova prima getCalculatedValue() per formule
                    try {
                        $value = $cell->getCalculatedValue();
                    } catch (\Exception $e) {
                        // Se fallisce, usa getValue()
                        $value = $cell->getValue();
                    }
                }
                
                // Se ancora null, prova getFormattedValue()
                if ($value === null) {
                    $value = $cell->getFormattedValue();
                }
                
                $data[$key] = $value !== null ? trim((string)$value) : '';
            } catch (\Exception $e) {
                $data[$key] = '';
            }
        }
        return $data;
    }
    
    protected function normalizeContractData($data, $academicYear, $student)
    {
        // Determina data inizio (primo corso o data inizio anno)
        $startDate = null;
        if (!empty($data['course1_start'])) {
            $startDate = $this->parseDate($data['course1_start']);
        } elseif (!empty($data['course2_start'])) {
            $startDate = $this->parseDate($data['course2_start']);
        } elseif (!empty($data['course3_start'])) {
            $startDate = $this->parseDate($data['course3_start']);
        }
        
        if (!$startDate) {
            $startDate = $academicYear->start_date;
        }
        
        // Determina data fine (fine anno accademico)
        $endDate = $academicYear->end_date;
        
        // Normalizza stato
        $status = 'draft';
        if (!empty($data['status'])) {
            $statusStr = strtolower($data['status']);
            if (strpos($statusStr, 'firmato') !== false || strpos($statusStr, 'signed') !== false) {
                $status = 'signed';
            } elseif (strpos($statusStr, 'inviato') !== false || strpos($statusStr, 'sent') !== false) {
                $status = 'sent';
            }
        }
        
        // Normalizza tipo
        $type = 'regular';
        if (!empty($data['course1_type'])) {
            $typeStr = strtolower($data['course1_type']);
            if (strpos($typeStr, 'breve') !== false || strpos($typeStr, 'short') !== false) {
                $type = 'short';
            } elseif (strpos($typeStr, 'estivo') !== false || strpos($typeStr, 'summer') !== false) {
                $type = 'summer';
            }
        }
        
        // Costruisci terms (descrizione corsi)
        $terms = [];
        if (!empty($data['course1_name'])) {
            $terms[] = "Corso 1: {$data['course1_name']}";
            if (!empty($data['course1_description'])) {
                $terms[] = "  {$data['course1_description']}";
            }
        }
        if (!empty($data['course2_name'])) {
            $terms[] = "Corso 2: {$data['course2_name']}";
        }
        if (!empty($data['course3_name'])) {
            $terms[] = "Corso 3: {$data['course3_name']}";
        }
        if (!empty($data['orchestra1'])) {
            $terms[] = "Orchestra: {$data['orchestra1']}";
        }
        
        $contractNumber = !empty($data['contract_number']) 
            ? $data['contract_number'] 
            : 'CONTRACT-' . $student->id . '-' . $academicYear->slug;
        
        $sentDate = !empty($data['sent_date']) ? $this->parseDate($data['sent_date']) : null;
        $signedDate = !empty($data['signed_date']) ? $this->parseDate($data['signed_date']) : null;
        
        return [
            'academic_year_id' => $academicYear->id,
            'student_id' => $student->id,
            'contract_number' => $contractNumber,
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'sent_date' => $sentDate,
            'signed_date' => $signedDate,
            'terms' => implode("\n", $terms),
            'notes' => $data['notes'] ?? null,
            'token' => Str::random(64), // Per link precompilati
        ];
    }
    
    protected function parseDate($value)
    {
        if (empty($value)) return null;
        
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
        
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {
                continue;
            }
        }
        
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
