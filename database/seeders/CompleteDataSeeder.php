<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentLevel;
use App\Models\StudentAvailability;
use App\Models\Instrument;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\InstrumentRental;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompleteDataSeeder extends Seeder
{
    private int $debugEnrollmentsShown = 0;
    private int $debugEnrollmentsErrorsShown = 0;

    /**
     * Importa TUTTI i dati dal file ODS principale
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Completa Dati ODS ===');
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error("Nessun anno accademico attivo trovato");
            return;
        }
        
        $filePath = base_path('docs/materiale cliente/db 2025-26 gestionale.ods');
        
        if (!file_exists($filePath)) {
            $this->command->error("File non trovato: {$filePath}");
            return;
        }
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheetByName('dati');
            
            if (!$sheet) {
                $this->command->error("Foglio 'dati' non trovato");
                return;
            }
            
            // Leggi header completo
            $headerRow = 1;
            $highestCol = $sheet->getHighestColumn();
            $highestColIndex = Coordinate::columnIndexFromString($highestCol);
            
            $headerMap = [];
            for ($c = 1; $c <= $highestColIndex; $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $header = trim((string)$sheet->getCell($colLetter . $headerRow)->getValue());
                if (!empty($header)) {
                    $headerMap[strtolower($header)] = $colLetter;
                }
            }
            
            $this->command->info("Trovate " . count($headerMap) . " colonne con header");
            
            $highestRow = $sheet->getHighestRow();
            $this->command->info("Processando {$highestRow} righe...");
            
            $stats = [
                'levels' => 0,
                'availability' => 0,
                'instruments' => 0,
                'orchestra' => 0,
                'enrollments' => 0,
            ];
            
            // Processa ogni riga
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    // Trova studente
                    $cognome = $this->getCellValue($sheet, $row, $headerMap['cognome'] ?? null);
                    $nome = $this->getCellValue($sheet, $row, $headerMap['nome'] ?? null);
                    
                    if (empty($cognome) && empty($nome)) {
                        continue;
                    }
                    
                    $student = Student::whereRaw('LOWER(last_name) = ?', [strtolower($cognome)])
                        ->whereRaw('LOWER(first_name) = ?', [strtolower($nome)])
                        ->where('academic_year_id', $academicYear->id)
                        ->first();
                    
                    if (!$student) {
                        continue;
                    }
                    
                    // 1. Importa Livelli
                    $this->importLevels($student, $sheet, $row, $headerMap);
                    $stats['levels']++;
                    
                    // 2. Importa Disponibilità (se non già importata)
                    $this->importAvailabilityFromOds($student, $sheet, $row, $headerMap);
                    $stats['availability']++;
                    
                    // 3. Importa Strumenti collegati
                    $this->importStudentInstrument($student, $sheet, $row, $headerMap);
                    $stats['instruments']++;
                    
                    // 4. Importa Orchestra/Coro
                    $this->importOrchestraCoro($student, $sheet, $row, $headerMap, $academicYear);
                    $stats['orchestra']++;
                    
                    // 5. Importa Iscrizioni Corsi
                    $stats['enrollments'] += $this->importEnrollments($student, $sheet, $row, $headerMap, $academicYear);
                    
                    if ($row % 50 == 0) {
                        $this->command->info("  Processate {$row} righe...");
                    }
                    
                } catch (\Exception $e) {
                    // Continua con la prossima riga
                }
            }
            
            $this->command->info("✓ Importazione completata:");
            $this->command->info("  - Livelli: {$stats['levels']}");
            $this->command->info("  - Disponibilità: {$stats['availability']}");
            $this->command->info("  - Strumenti: {$stats['instruments']}");
            $this->command->info("  - Orchestra/Coro: {$stats['orchestra']}");
            $this->command->info("  - Iscrizioni (create): {$stats['enrollments']}");
            
        } catch (\Exception $e) {
            $this->command->error("Errore: " . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
    
    protected function getCellValue($sheet, $row, $col)
    {
        if (!$col) return null;
        try {
            $cell = $sheet->getCell($col . $row);
            $value = $cell->getValue();

            // Se è formula o vuoto, prova getCalculatedValue
            if ($value === null || $value === '' || (is_string($value) && str_starts_with(trim($value), '='))) {
                try {
                    $calc = $cell->getCalculatedValue();
                    if ($calc !== null && $calc !== '') {
                        $value = $calc;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            return $value !== null ? trim((string)$value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    protected function importLevels($student, $sheet, $row, $headerMap)
    {
        $livello = $this->getCellValue($sheet, $row, $headerMap['livello'] ?? null);
        $livelloStr = $this->getCellValue($sheet, $row, $headerMap['livello str.'] ?? null);
        $livelloTeoria = $this->getCellValue($sheet, $row, $headerMap['livello teoria'] ?? null);
        $musicaInsieme = $this->getCellValue($sheet, $row, $headerMap['musica di insieme'] ?? null);
        
        if (empty($livello) && empty($livelloStr) && empty($livelloTeoria)) {
            return;
        }
        
        // Determina tipo strumento (da colonna tipo strumento o da corso)
        $instrumentType = 'piano'; // Default, da migliorare
        
        StudentLevel::updateOrCreate(
            [
                'student_id' => $student->id,
                'instrument_type' => $instrumentType,
            ],
            [
                'level_abrsm_instrument' => $this->parseLevel($livelloStr),
                'level_abrsm_theory' => $this->parseLevel($livelloTeoria),
                'notes' => $musicaInsieme,
            ]
        );
    }
    
    protected function importAvailabilityFromOds($student, $sheet, $row, $headerMap)
    {
        $days = [
            'lu' => 'monday',
            'ma' => 'tuesday',
            'me' => 'wednesday',
            'gio' => 'thursday',
            've' => 'friday',
            'sab' => 'saturday',
        ];
        
        foreach ($days as $colKey => $dayOfWeek) {
            $col = $headerMap[$colKey] ?? null;
            if (!$col) continue;
            
            $value = $this->getCellValue($sheet, $row, $col);
            if (empty($value)) continue;
            
            // Parse orario (es. "17:00-18:00" o "17-18")
            $times = $this->parseTimeRange($value);
            
            if ($times) {
                StudentAvailability::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'day_of_week' => $dayOfWeek,
                        'time_start' => $times['start'],
                        'time_end' => $times['end'],
                    ],
                    [
                        'available' => true,
                        'notes' => $this->getCellValue($sheet, $row, $headerMap['note disponibilità'] ?? null),
                    ]
                );
            }
        }
    }
    
    protected function importStudentInstrument($student, $sheet, $row, $headerMap)
    {
        $tipo = $this->getCellValue($sheet, $row, $headerMap['tipo'] ?? null);
        if (empty($tipo)) return;
        
        $marca = $this->getCellValue($sheet, $row, $headerMap['marca'] ?? null);
        $mod = $this->getCellValue($sheet, $row, $headerMap['mod'] ?? null);
        $misura = $this->getCellValue($sheet, $row, $headerMap['misura'] ?? null);
        $cod = $this->getCellValue($sheet, $row, $headerMap['cod'] ?? null);
        $noleggio = $this->getCellValue($sheet, $row, $headerMap['noleggio/proprietà'] ?? null);
        
        // Crea o trova strumento
        $instrument = Instrument::firstOrCreate(
            [
                'type' => $tipo,
                'serial_number' => $cod,
            ],
            [
                'brand' => $marca,
                'model' => $mod,
                'size' => $misura,
                'status' => 'available',
            ]
        );
        
        // Se è noleggio, crea rental
        if (strtolower($noleggio ?? '') === 'noleggio' || strtolower($noleggio ?? '') === 'noleggio/proprietà') {
            InstrumentRental::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'instrument_id' => $instrument->id,
                ],
                [
                    'academic_year_id' => $student->academic_year_id,
                    'start_date' => now(),
                    'monthly_fee' => 0, // Da calcolare
                    'status' => 'active',
                ]
            );
        }
    }
    
    protected function importOrchestraCoro($student, $sheet, $row, $headerMap, $academicYear)
    {
        // FASE 1: l'ODS contiene campi orchestra/coro, ma la gestione "attività extra" è fuori scope.
        // Qui registriamo l'informazione in modo non strutturato nelle note dello studente per non perdere dati in import.
        $orch1 = $this->getCellValue($sheet, $row, $headerMap['orch 1'] ?? null);
        $coro = $this->getCellValue($sheet, $row, $headerMap['coro'] ?? null);

        $tags = [];
        if (!empty($orch1)) $tags[] = "Orchestra: {$orch1}";
        if (!empty($coro)) $tags[] = "Coro: {$coro}";

        if (!empty($tags)) {
            $existing = trim((string) ($student->notes ?? ''));
            $append = implode(' | ', $tags);
            $student->notes = $existing ? ($existing . ' | ' . $append) : $append;
            $student->save();
        }
    }
    
    protected function importEnrollments($student, $sheet, $row, $headerMap, $academicYear)
    {
        $created = 0;
        $seen = 0;
        
        // Importa Corso 1, 2, 3
        for ($courseNum = 1; $courseNum <= 3; $courseNum++) {
            // Prova vari nomi di colonna (colonne reali: CC=CC, CD=CD, CE=CE per corso 1)
            $siglaCol = $headerMap["sigla corso {$courseNum}"] ?? null;
            if (!$siglaCol && $courseNum == 1) {
                // Fallback: colonna CC per sigla corso 1
                $siglaCol = 'CC';
            } elseif (!$siglaCol && $courseNum == 2) {
                $siglaCol = 'CX';
            } elseif (!$siglaCol && $courseNum == 3) {
                $siglaCol = 'DR';
            }
            
            $sigla = $this->getCellValue($sheet, $row, $siglaCol);
            
            if (empty($sigla)) continue;
            $seen++;
            if ($this->debugEnrollmentsShown < 5) {
                $this->command->warn("DEBUG enrollments: riga {$row} corso {$courseNum} sigla='{$sigla}'");
                $this->debugEnrollmentsShown++;
            }
            
            // Colonne descrizione: CD per corso 1, CY per corso 2, DS per corso 3
            $descCol = $headerMap["descrizione corso {$courseNum}"] ?? null;
            if (!$descCol && $courseNum == 1) $descCol = 'CD';
            elseif (!$descCol && $courseNum == 2) $descCol = 'CY';
            elseif (!$descCol && $courseNum == 3) $descCol = 'DS';
            
            // Colonne tipologia: CE per corso 1, CZ per corso 2, DT per corso 3
            $tipCol = $headerMap["tipologia corso {$courseNum}"] ?? null;
            if (!$tipCol && $courseNum == 1) $tipCol = 'CE';
            elseif (!$tipCol && $courseNum == 2) $tipCol = 'CZ';
            elseif (!$tipCol && $courseNum == 3) $tipCol = 'DT';
            
            $descrizione = $this->getCellValue($sheet, $row, $descCol);
            $tipologia = $this->getCellValue($sheet, $row, $tipCol);
            $docente = $this->getCellValue($sheet, $row, $headerMap["docente strumento/canto"] ?? null);
            $giorno = $this->getCellValue($sheet, $row, $headerMap["giorno strum"] ?? null);
            $ora = $this->getCellValue($sheet, $row, $headerMap["ora strumento/canto"] ?? null);
            
            // Data inizio: CB per corso 1, CW per corso 2
            $dataInizioCol = $headerMap["data inizio corso {$courseNum}"] ?? null;
            if (!$dataInizioCol && $courseNum == 1) $dataInizioCol = 'CB';
            elseif (!$dataInizioCol && $courseNum == 2) $dataInizioCol = 'CW';
            
            $dataInizio = $this->getCellValue($sheet, $row, $dataInizioCol);
            
            // Trova o crea tipo corso
            $courseTypeName = $tipologia ?: 'Standard';
            $courseTypeCode = strtoupper(Str::slug($courseTypeName, '_'));
            if ($courseTypeCode === '') {
                $courseTypeCode = 'STANDARD';
            }
            $courseType = CourseType::firstOrCreate(
                ['code' => $courseTypeCode],
                ['name' => $courseTypeName, 'description' => $courseTypeName]
            );
            
            // Trova docente
            $teacher = null;
            if (!empty($docente)) {
                $teacher = Teacher::whereRaw('LOWER(CONCAT(last_name, " ", first_name)) LIKE ?', ['%' . strtolower($docente) . '%'])
                    ->first();
            }
            
            // Crea corso (usa code univoco per evitare duplicati)
            $courseCode = $sigla . '-' . $courseNum;
            $course = Course::firstOrCreate(
                [
                    'code' => $courseCode,
                ],
                [
                    'course_type_id' => $courseType->id,
                    'name' => $descrizione ?? $sigla,
                    'description' => $descrizione,
                ]
            );

            $offering = CourseOffering::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'teacher_id' => $teacher?->id,
                    'day_of_week' => $this->parseDayOfWeek($giorno),
                    'time_start' => $this->parseTime($ora),
                    'start_date' => $this->parseDate($dataInizio) ?? $academicYear->start_date,
                    'status' => 'active',
                ]
            );
            
            // Crea iscrizione solo se non esiste già
            $enrollmentDate = $this->parseDate($dataInizio) ?? now();
            try {
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_offering_id' => $offering->id,
                    ],
                    [
                        'academic_year_id' => $academicYear->id,
                        'enrollment_date' => $enrollmentDate,
                        'start_date' => $enrollmentDate,
                        'status' => 'active',
                    ]
                );
                if ($enrollment->wasRecentlyCreated) {
                    $created++;
                }
            } catch (\Exception $e) {
                if ($this->debugEnrollmentsErrorsShown < 10) {
                    $this->command->warn("DEBUG enrollments ERROR riga {$row} corso {$courseNum}: " . $e->getMessage());
                    $this->debugEnrollmentsErrorsShown++;
                }
            }
        }
        
        return $created;
    }
    
    protected function parseLevel($value)
    {
        if (empty($value)) return null;
        $value = preg_replace('/[^0-9]/', '', $value);
        return is_numeric($value) ? (int)$value : null;
    }
    
    protected function parseTimeRange($value)
    {
        if (empty($value)) return null;
        
        // Formati: "17:00-18:00", "17-18", "17.00-18.00"
        $value = str_replace(['.', ' '], ['', ''], $value);
        
        if (preg_match('/(\d{1,2}):?(\d{2})?-(\d{1,2}):?(\d{2})?/', $value, $matches)) {
            $startHour = (int)$matches[1];
            $startMin = isset($matches[2]) ? (int)$matches[2] : 0;
            $endHour = (int)$matches[3];
            $endMin = isset($matches[4]) ? (int)$matches[4] : 0;
            
            return [
                'start' => Carbon::createFromTime($startHour, $startMin),
                'end' => Carbon::createFromTime($endHour, $endMin),
            ];
        }
        
        return null;
    }
    
    protected function parseTime($value)
    {
        if (empty($value)) return null;
        
        $value = str_replace(['.', ' '], ['', ''], $value);
        
        if (preg_match('/(\d{1,2}):?(\d{2})?/', $value, $matches)) {
            $hour = (int)$matches[1];
            $min = isset($matches[2]) ? (int)$matches[2] : 0;
            // TIME column expects HH:MM:SS
            return sprintf('%02d:%02d:00', $hour, $min);
        }
        
        return null;
    }
    
    protected function parseDayOfWeek($value)
    {
        if (empty($value)) return null;
        
        $days = [
            'lunedì' => 'monday', 'lun' => 'monday',
            'martedì' => 'tuesday', 'mar' => 'tuesday',
            'mercoledì' => 'wednesday', 'mer' => 'wednesday',
            'giovedì' => 'thursday', 'gio' => 'thursday',
            'venerdì' => 'friday', 'ven' => 'friday',
            'sabato' => 'saturday', 'sab' => 'saturday',
            'domenica' => 'sunday', 'dom' => 'sunday',
        ];
        
        $value = strtolower(trim($value));
        return $days[$value] ?? null;
    }
    
    protected function parseDate($value)
    {
        if (empty($value)) return null;
        
        try {
            // Prova vari formati
            if (is_numeric($value)) {
                // Data Excel
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            }
            
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value);
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}

