<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\User;
use App\Services\OdsImportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Docenti ===');
        
        $filePath = base_path('docs/materiale cliente/dati lavoratori 25-26.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato: {$filePath}");
            return;
        }
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheetByName('2025-26');
            
            if (!$sheet) {
                $this->command->warn("Foglio '2025-26' non trovato");
                return;
            }
            
            // Leggi header
            $headerRow = 1;
            $highestCol = $sheet->getHighestColumn();
            $highestColIndex = Coordinate::columnIndexFromString($highestCol);
            
            $headerMap = [];
            for ($c = 1; $c <= min($highestColIndex, 100); $c++) {
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
                'cod. fisc.' => 'tax_code',
                'p. iva' => 'vat_number',
                'nato il' => 'birth_date',
                'città nascita' => 'birth_city',
                'carta identità' => 'id_number',
                'data rilascio' => 'id_issue_date',
                'ente rilascio' => 'id_issuer',
                'domicilio indirizzo' => 'address',
                'domicilio cap' => 'postal_code',
                'domicilio città' => 'city',
                'residenza indirizzo' => 'residence_address',
                'residenza cap' => 'residence_postal_code',
                'residenza città' => 'residence_city',
                'iban' => 'iban',
                'tel abitazione' => 'phone_home',
                'cell 1' => 'phone_mobile',
                'e-mail 1' => 'email',
                'ruolo' => 'role',
                'corso' => 'courses',
                'lab teoria' => 'lab_theory',
                'inquadramento' => 'employment_type',
            ];
            
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
            
            $this->command->info("Trovate {$highestRow} righe");
            
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $data = $this->readRowByMap($sheet, $row, $colMap);
                    
                    if (empty($data['first_name']) && empty($data['last_name'])) {
                        $skipped++;
                        continue;
                    }
                    
                    // Normalizza dati
                    $teacherData = $this->normalizeTeacherData($data);
                    
                    // Cerca docente esistente
                    $teacher = null;
                    if (!empty($teacherData['tax_code'])) {
                        $teacher = Teacher::where('tax_code', $teacherData['tax_code'])->first();
                    }
                    
                    if (!$teacher && !empty($teacherData['first_name']) && !empty($teacherData['last_name'])) {
                        $teacher = Teacher::where('first_name', $teacherData['first_name'])
                            ->where('last_name', $teacherData['last_name'])
                            ->first();
                    }
                    
                    // Crea o aggiorna docente
                    if ($teacher) {
                        $teacher->update($teacherData);
                    } else {
                        $teacher = Teacher::create($teacherData);
                    }
                    
                    // Crea utente se non esiste
                    if (!$teacher->user_id && !empty($teacherData['email'])) {
                        $user = User::firstOrCreate(
                            ['email' => $teacherData['email']],
                            [
                                'name' => $teacher->full_name,
                                'password' => Hash::make('password'),
                                'role' => 'teacher',
                            ]
                        );
                        $teacher->update(['user_id' => $user->id]);
                    }
                    
                    $imported++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Riga {$row}: " . $e->getMessage();
                    if (count($errors) <= 10) {
                        $this->command->warn("  Errore riga {$row}: " . $e->getMessage());
                    }
                }
            }
            
            $this->command->info("✓ Docenti importati: {$imported}");
            $this->command->info("  Saltati: {$skipped}");
            if (!empty($errors)) {
                $this->command->warn("  Errori: " . count($errors));
            }
            
        } catch (\Exception $e) {
            $this->command->error("Errore: " . $e->getMessage());
        }
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
    
    protected function normalizeTeacherData($data)
    {
        $birthDate = !empty($data['birth_date']) ? $this->parseDate($data['birth_date']) : null;
        $idIssueDate = !empty($data['id_issue_date']) ? $this->parseDate($data['id_issue_date']) : null;
        
        // Normalizza ruolo (socio/non socio)
        $role = !empty($data['role']) ? strtolower($data['role']) : null;
        $isPartner = in_array($role, ['socio', 'partner']);
        
        return [
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'tax_code' => !empty($data['tax_code']) ? strtoupper($data['tax_code']) : null,
            'phone' => $data['phone_mobile'] ?? $data['phone_home'] ?? null,
            'email' => !empty($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? $data['email'] : null : null,
            'address' => $data['address'] ?? null,
            'contract_type' => $data['employment_type'] ?? null,
            'notes' => $this->buildNotes($data),
            'active' => true,
        ];
    }
    
    protected function buildNotes($data)
    {
        $notes = [];
        if (!empty($data['courses'])) $notes[] = "Corsi: {$data['courses']}";
        if (!empty($data['lab_theory'])) $notes[] = "Lab teoria: {$data['lab_theory']}";
        if (!empty($data['role'])) $notes[] = "Ruolo: {$data['role']}";
        if (!empty($data['vat_number'])) $notes[] = "P.IVA: {$data['vat_number']}";
        if (!empty($data['iban'])) $notes[] = "IBAN: {$data['iban']}";
        return implode(' | ', $notes);
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
