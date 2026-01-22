<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Guardian;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

class OdsImportService
{
    public function importStudents($filePath, $sheetName = 'dati', $academicYear = null)
    {
        if (!$academicYear) {
            $academicYear = AcademicYear::where('is_active', true)->first();
        }
        
        if (!$academicYear) {
            throw new \Exception("Nessun anno accademico attivo trovato");
        }
        
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $sheetName 
            ? $spreadsheet->getSheetByName($sheetName) 
            : $spreadsheet->getSheet(0);
        
        if (!$sheet) {
            throw new \Exception("Foglio non trovato: {$sheetName}");
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
        $imported = 0;
        $skipped = 0;
        $errors = [];
        
        // Inizia dalla riga 2 (dopo header)
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            try {
                $data = $this->readRowByMap($sheet, $row, $colMap);
                
                // Salta righe vuote
                if (empty($data['first_name']) && empty($data['last_name'])) {
                    $skipped++;
                    continue;
                }
                
                // Normalizza dati
                $studentData = $this->normalizeStudentData($data, $academicYear);
                
                // Cerca studente esistente
                $student = null;
                if (!empty($studentData['tax_code'])) {
                    // Fase 1 (Master vs Annuale con Student annuale): deduplica SOLO nell'anno corrente
                    $student = Student::where('tax_code', $studentData['tax_code'])
                        ->where('academic_year_id', $academicYear->id)
                        ->first();
                }
                
                if (!$student && !empty($studentData['first_name']) && !empty($studentData['last_name'])) {
                    $student = Student::where('first_name', $studentData['first_name'])
                        ->where('last_name', $studentData['last_name'])
                        ->where('academic_year_id', $academicYear->id)
                        ->first();
                }
                
                // Crea o aggiorna studente
                if ($student) {
                    $student->update($studentData);
                } else {
                    $student = Student::create($studentData);
                }
                
                // Gestisci genitori
                $this->importGuardians($student, $data);
                
                $imported++;
                
            } catch (\Exception $e) {
                // Log solo i primi 20 errori per debug
                if (count($errors) < 20) {
                    $errors[] = "Riga {$row}: " . $e->getMessage() . " (Dati: " . substr(json_encode($data), 0, 100) . ")";
                } elseif (count($errors) == 20) {
                    $errors[] = "... (altri errori omessi)";
                }
            }
        }
        
        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
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
        $birthDate = null;
        if (!empty($data['birth_date'])) {
            $birthDate = $this->parseDate($data['birth_date']);
        }
        
        $age = null;
        if (!empty($data['age'])) {
            $age = (int)$data['age'];
        } elseif ($birthDate) {
            $age = $birthDate->diffInYears(now());
        }
        
        $status = 'interested';
        if (!empty($data['status'])) {
            $statusStr = strtolower($data['status']);
            if (strpos($statusStr, 'iscritto') !== false || strpos($statusStr, 'attivo') !== false) {
                $status = 'enrolled';
            } elseif (strpos($statusStr, 'interessato') !== false) {
                $status = 'interested';
            }
        }
        
        $firstContactDate = !empty($data['first_contact_date']) 
            ? $this->parseDate($data['first_contact_date']) 
            : null;

        $lastContactDate = !empty($data['last_contact_date'])
            ? $this->parseDate($data['last_contact_date'])
            : null;
        
        // Nota: address, postal_code, city, phone, email non sono nella tabella students
        // Questi dati vanno nei genitori o in una tabella separata
        $notes = trim(($data['notes'] ?? '') . ' ' . ($data['admin_notes'] ?? ''));
        if (!empty($data['address'])) {
            $notes .= " | Indirizzo: {$data['address']}";
        }
        if (!empty($data['phone'])) {
            $notes .= " | Tel: {$data['phone']}";
        }
        if (!empty($data['email'])) {
            $notes .= " | Email: {$data['email']}";
        }
        
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
            'last_contact_date' => $lastContactDate,
            'notes' => $notes,
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
                'relationship' => 'other',
            ]);
            
            if ($guardian1 && !$student->guardians()->where('guardian_id', $guardian1->id)->exists()) {
                $student->guardians()->attach($guardian1->id, [
                    'relationship_type' => 'mother', // Usa valore enum valido
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
                'relationship' => 'other',
            ]);
            
            if ($guardian2 && !$student->guardians()->where('guardian_id', $guardian2->id)->exists()) {
                $student->guardians()->attach($guardian2->id, [
                    'relationship_type' => 'father', // Usa valore enum valido
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
        
        $guardian = Guardian::where('first_name', $data['first_name'])
            ->where('last_name', $data['last_name'])
            ->first();
        
        if (!$guardian) {
            $guardian = Guardian::create($data);
        } else {
            $guardian->update(array_filter($data));
        }
        
        return $guardian;
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
    
    protected function parseAmount($value)
    {
        if (empty($value)) return null;
        
        $value = str_replace(['€', ' ', ','], '', $value);
        $value = str_replace(',', '.', $value);
        
        return is_numeric($value) ? (float)$value : null;
    }
}

