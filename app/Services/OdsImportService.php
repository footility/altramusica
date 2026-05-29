<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentYear;
use App\Models\Guardian;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class OdsImportService
{
    /** Formato canonico codice fiscale italiano persona fisica. */
    protected const TAX_CODE_REGEX = '/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/';

    /**
     * Importa (o analizza in dry-run) gli studenti dal foglio indicato.
     *
     * @param  string  $filePath      Path assoluto del file XLSX/ODS.
     * @param  string  $sheetName     Nome del foglio (default 'dati').
     * @param  AcademicYear|null  $academicYear  Anno di destinazione; se null usa l'attivo.
     * @param  bool  $dryRun          Se true non scrive nulla: calcola solo cosa accadrebbe.
     * @return array  Report dettagliato (vedi chiavi sotto).
     */
    public function importStudents($filePath, $sheetName = 'dati', $academicYear = null, $dryRun = false)
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

        $colMap = $this->buildColumnMap($sheet);
        $headerRow = 1;
        $highestRow = $sheet->getHighestRow();

        // --- PASS 1: lettura grezza + indici per rilevare anomalie trasversali ---
        $rawRows = [];          // [row => data]
        $cfOwners = [];         // cf => set di "cognome|nome" distinti
        $nameRows = [];         // "cognome|nome" (lower) => [righe]

        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $data = $this->readRowByMap($sheet, $row, $colMap);

            if (empty($data['first_name']) && empty($data['last_name'])) {
                continue; // riga vuota: ignorata anche dall'indice
            }

            $rawRows[$row] = $data;

            $cf = $this->cleanTaxCode($data['tax_code'] ?? '');
            $nameKey = mb_strtolower(trim(($data['last_name'] ?? '') . '|' . ($data['first_name'] ?? '')));

            if ($cf) {
                $cfOwners[$cf][$nameKey] = true;
            }
            $nameRows[$nameKey][] = $row;
        }

        // CF presenti su nominativi diversi -> ambiguità di merge
        $duplicateCfs = [];
        foreach ($cfOwners as $cf => $owners) {
            if (count($owners) > 1) {
                $duplicateCfs[$cf] = array_keys($owners);
            }
        }
        // Nominativi ripetuti su più righe -> omonimi
        $homonymNames = array_filter($nameRows, fn ($rows) => count($rows) > 1);

        // --- PASS 2: normalizzazione, anomalie per-riga, scrittura (se non dry-run) ---
        $report = [
            'dry_run'    => (bool) $dryRun,
            'sheet'      => $sheet->getTitle(),
            'academic_year' => $academicYear->name,
            'total_rows' => count($rawRows),
            'created'    => 0,
            'updated'    => 0,
            'skipped'    => 0,
            'rows'       => [],   // dettaglio per riga
            'errors'     => [],   // retro-compatibilità
            'anomalies'  => [
                'missing_tax_code'   => [],
                'invalid_tax_code'   => [],
                'duplicate_tax_code' => [],
                'homonyms'           => [],
                'notes_with_data'    => [],
                'invalid_email'      => [],
                'unparsable_date'    => [],
            ],
        ];

        foreach ($rawRows as $row => $data) {
            try {
                $normalized = $this->normalizeStudentData($data, $academicYear);
                $studentData = $normalized['student'] ?? [];
                $studentYearData = $normalized['year'] ?? [];

                $name = trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['last_name'] ?? ''));
                $warnings = $this->detectRowAnomalies($row, $data, $studentData, $duplicateCfs, $homonymNames, $report);

                $existing = $this->findExistingStudent($studentData);
                $action = $existing ? 'updated' : 'created';

                if (!$dryRun) {
                    if ($existing) {
                        $existing->update($studentData);
                        $student = $existing;
                    } else {
                        $student = Student::create($studentData);
                    }

                    if ($student && !empty($studentYearData)) {
                        StudentYear::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'academic_year_id' => $academicYear->id,
                            ],
                            $studentYearData
                        );
                    }

                    $this->importGuardians($student, $data);
                }

                $report[$action === 'created' ? 'created' : 'updated']++;
                $report['rows'][] = [
                    'row'      => $row,
                    'name'     => $name,
                    'tax_code' => $studentData['tax_code'] ?? null,
                    'action'   => $action,
                    'warnings' => $warnings,
                ];
            } catch (\Throwable $e) {
                $report['skipped']++;
                $msg = "Riga {$row}: " . $e->getMessage();
                $report['rows'][] = [
                    'row'      => $row,
                    'name'     => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                    'tax_code' => $this->cleanTaxCode($data['tax_code'] ?? '') ?: null,
                    'action'   => 'error',
                    'warnings' => [$e->getMessage()],
                ];
                if (count($report['errors']) < 50) {
                    $report['errors'][] = $msg . " (Dati: " . substr(json_encode($data), 0, 120) . ")";
                }
            }
        }

        // retro-compatibilità con i chiamanti esistenti
        $report['imported'] = $report['created'] + $report['updated'];

        return $report;
    }

    /**
     * Configurazione alias header -> campo. Per ogni campo:
     *   'any' = elenco di sottostringhe (su header normalizzato) di cui basta una;
     *   'not' = sottostringhe che escludono il match (disambigua omografie tipo
     *           "cognome" che contiene "nome", o "allievo" vs "genitore").
     * L'ordine conta: i campi piu' specifici (genitori) vanno risolti prima e le
     * colonne gia' assegnate non vengono riusate.
     */
    protected const HEADER_ALIASES = [
        // --- Genitori (risolti per primi: header lunghi e specifici) ---
        'guardian1_last'  => ['any' => ['cognome genitore 1'], 'not' => []],
        'guardian1_first' => ['any' => ['nome genitore 1'], 'not' => ['cognome']],
        'guardian2_last'  => ['any' => ['cognome genitore 2'], 'not' => []],
        'guardian2_first' => ['any' => ['nome genitore 2'], 'not' => ['cognome']],
        'guardian1_phone' => ['any' => ['cellulare genitore 1', 'cell 1', 'cell madre'], 'not' => []],
        'guardian2_phone' => ['any' => ['cellulare genitore 2', 'cell 2', 'cell padre'], 'not' => []],
        'guardian1_email' => ['any' => ['mail genitore 1', 'mail 1'], 'not' => []],
        'guardian2_email' => ['any' => ['mail genitore 2', 'mail 2'], 'not' => []],

        // --- Allievo ---
        'last_name'   => ['any' => ['cognome allievo', 'cognome'], 'not' => ['genitore']],
        'first_name'  => ['any' => ['nome allievo', 'nome'], 'not' => ['genitore', 'cognome']],
        'tax_code'    => ['any' => ['codice fiscale', 'cod fiscale'], 'not' => ['genitore']],
        'birth_date'  => ['any' => ['data di nascita', 'nato il', 'nascita'], 'not' => []],
        'age'         => ['any' => ['eta'], 'not' => []],
        'address'     => ['any' => ['indirizzo'], 'not' => ['genitore']],
        'postal_code' => ['any' => ['cap'], 'not' => []],
        'city'        => ['any' => ['citta'], 'not' => []],
        'phone'       => ['any' => ['cellulare allievo', 'cell 3'], 'not' => ['genitore']],
        'email'       => ['any' => ['mail allievo'], 'not' => ['genitore']],

        // --- Anno / iscrizione / note ---
        'first_contact_date' => ['any' => ['data di arrivo', 'data arrivo', 'informazioni cronologiche'], 'not' => []],
        'source'             => ['any' => ['come e venuto a sapere'], 'not' => []],
        'notes'              => ['any' => ['note prove', 'vuoi dirci qualcos'], 'not' => []],
        'admin_notes'        => ['any' => ['note varie'], 'not' => []],
        'last_contact_date'  => ['any' => ['data ultimo'], 'not' => []],
        'status'             => ['any' => ['stato'], 'not' => []],
        'enrollment_code'    => ['any' => ['n iscritto'], 'not' => []],
    ];

    /**
     * Costruisce mappa campo -> lettera colonna dall'header (riga 1), con
     * matching alias-based normalizzato (tollerante ad accenti, spazi, "e- mail",
     * varianti "allievo/genitore").
     */
    protected function buildColumnMap($sheet)
    {
        $headerRow = 1;
        $highestColIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        // lettera colonna -> header normalizzato
        $headers = [];
        for ($c = 1; $c <= min($highestColIndex, 200); $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $raw = trim((string) $sheet->getCell($colLetter . $headerRow)->getValue());
            if ($raw !== '') {
                $headers[$colLetter] = $this->normalizeHeader($raw);
            }
        }

        $colMap = [];
        $used = [];
        foreach (self::HEADER_ALIASES as $field => $rules) {
            foreach ($headers as $colLetter => $normHeader) {
                if (isset($used[$colLetter])) {
                    continue;
                }
                foreach ($rules['not'] as $exclude) {
                    if (strpos($normHeader, $exclude) !== false) {
                        continue 2; // questa colonna e' esclusa per questo campo
                    }
                }
                foreach ($rules['any'] as $needle) {
                    if (strpos($normHeader, $needle) !== false) {
                        $colMap[$field] = $colLetter;
                        $used[$colLetter] = true;
                        continue 3; // campo risolto, passa al successivo
                    }
                }
            }
        }

        return $colMap;
    }

    /**
     * Normalizza un header: minuscolo, accenti rimossi, non-alfanumerici -> spazio.
     */
    protected function normalizeHeader($value)
    {
        $value = mb_strtolower(trim($value));
        $value = strtr($value, [
            'à' => 'a', 'á' => 'a', 'è' => 'e', 'é' => 'e',
            'ì' => 'i', 'í' => 'i', 'ò' => 'o', 'ó' => 'o',
            'ù' => 'u', 'ú' => 'u',
        ]);
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim($value);
    }

    /**
     * Cerca uno studente esistente con strategia a cascata sicura:
     * 1) CF + nome + cognome (il foglio ha CF duplicati: il CF da solo non basta)
     * 2) nome + cognome + data nascita
     * 3) nome + cognome
     */
    protected function findExistingStudent($studentData)
    {
        if (!empty($studentData['tax_code'])
            && !empty($studentData['first_name'])
            && !empty($studentData['last_name'])) {
            $student = Student::where('tax_code', $studentData['tax_code'])
                ->whereRaw('LOWER(first_name) = ?', [mb_strtolower($studentData['first_name'])])
                ->whereRaw('LOWER(last_name) = ?', [mb_strtolower($studentData['last_name'])])
                ->first();
            if ($student) {
                return $student;
            }
        }

        if (!empty($studentData['first_name'])
            && !empty($studentData['last_name'])
            && !empty($studentData['birth_date'])) {
            $student = Student::where('first_name', $studentData['first_name'])
                ->where('last_name', $studentData['last_name'])
                ->whereDate('birth_date', $studentData['birth_date'])
                ->first();
            if ($student) {
                return $student;
            }
        }

        if (!empty($studentData['first_name']) && !empty($studentData['last_name'])) {
            return Student::where('first_name', $studentData['first_name'])
                ->where('last_name', $studentData['last_name'])
                ->first();
        }

        return null;
    }

    /**
     * Registra le anomalie della riga nel report e ritorna l'elenco warning testuali.
     */
    protected function detectRowAnomalies($row, $data, $studentData, $duplicateCfs, $homonymNames, &$report)
    {
        $warnings = [];
        $cf = $studentData['tax_code'] ?? null;

        if (empty($cf)) {
            $report['anomalies']['missing_tax_code'][] = $row;
            $warnings[] = 'CF mancante';
        } elseif (!preg_match(self::TAX_CODE_REGEX, $cf)) {
            $report['anomalies']['invalid_tax_code'][] = ['row' => $row, 'tax_code' => $cf];
            $warnings[] = "CF malformato ({$cf})";
        } elseif (isset($duplicateCfs[$cf])) {
            $report['anomalies']['duplicate_tax_code'][] = ['row' => $row, 'tax_code' => $cf, 'owners' => $duplicateCfs[$cf]];
            $warnings[] = "CF condiviso da nominativi diversi ({$cf})";
        }

        $nameKey = mb_strtolower(trim(($data['last_name'] ?? '') . '|' . ($data['first_name'] ?? '')));
        if (isset($homonymNames[$nameKey])) {
            $report['anomalies']['homonyms'][] = ['row' => $row, 'name' => trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['last_name'] ?? ''))];
            $warnings[] = 'Omonimo (stesso nome/cognome su più righe)';
        }

        // Note che contengono dati strutturati (telefono/email/data) finiti nel campo libero
        foreach (['notes' => 'note prove', 'admin_notes' => 'note varie'] as $field => $label) {
            if (!empty($data[$field]) && $this->notesContainStructuredData($data[$field])) {
                $report['anomalies']['notes_with_data'][] = ['row' => $row, 'field' => $label, 'excerpt' => mb_substr($data[$field], 0, 80)];
                $warnings[] = "Dati strutturati nel campo «{$label}»";
            }
        }

        // Email allievo non valida (presente ma non parsabile)
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $report['anomalies']['invalid_email'][] = ['row' => $row, 'email' => $data['email']];
            $warnings[] = "Email non valida ({$data['email']})";
        }

        // Date presenti ma non parsabili
        foreach (['birth_date' => 'nato il', 'first_contact_date' => 'data arrivo', 'last_contact_date' => 'data ultimo'] as $field => $label) {
            if (!empty($data[$field]) && $this->parseDate($data[$field]) === null) {
                $report['anomalies']['unparsable_date'][] = ['row' => $row, 'field' => $label, 'value' => $data[$field]];
                $warnings[] = "Data «{$label}» non interpretabile ({$data[$field]})";
            }
        }

        return $warnings;
    }

    /**
     * Rileva se un campo note contiene email, numeri di telefono o date (dati che
     * dovrebbero stare in colonne dedicate, non in note libere).
     */
    protected function notesContainStructuredData($value)
    {
        $value = (string) $value;

        if (preg_match('/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/', $value)) {
            return true;
        }
        // sequenza di almeno 6 cifre (telefono) eventualmente con separatori
        if (preg_match('/(?:\d[\s.\-\/]?){6,}/', $value)) {
            return true;
        }
        // data esplicita gg/mm/aaaa o gg-mm-aaaa
        if (preg_match('/\b\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}\b/', $value)) {
            return true;
        }

        return false;
    }

    protected function readRowByMap($sheet, $row, $colMap)
    {
        $data = [];
        foreach ($colMap as $key => $col) {
            if (empty($col)) {
                continue;
            }
            try {
                $cell = $sheet->getCell($col . $row);
                $value = $cell->getValue();
                $data[$key] = $value !== null ? trim((string) $value) : '';
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
        if (!empty($data['age']) && is_numeric($data['age'])) {
            $age = (int) $data['age'];
        } elseif ($birthDate) {
            $age = $birthDate->diffInYears(now());
        }

        $status = 'interested';
        if (!empty($data['status'])) {
            $statusStr = mb_strtolower($data['status']);
            if (strpos($statusStr, 'iscritto') !== false || strpos($statusStr, 'attivo') !== false) {
                $status = 'enrolled';
            } elseif (strpos($statusStr, 'interessato') !== false) {
                $status = 'interested';
            }
        }

        $lastContactDate = !empty($data['last_contact_date'])
            ? $this->parseDate($data['last_contact_date'])
            : null;

        // address, postal_code, city, phone, email non esistono sulla tabella students:
        // confluiscono nelle note dell'anno per non perdere il dato.
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
            'student' => [
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? '',
                'birth_date' => $birthDate,
                'age' => $age,
                'tax_code' => $this->cleanTaxCode($data['tax_code'] ?? '') ?: null,
            ],
            'year' => [
                'code' => !empty($data['enrollment_code']) ? $data['enrollment_code'] : null,
                'status' => $status,
                'how_know_us' => $data['source'] ?? null,
                'last_contact_date' => $lastContactDate,
                'notes' => trim($notes),
                'admin_notes' => $data['admin_notes'] ?? null,
                'preferences' => null,
                'school_origin' => null,
                'privacy_consent' => false,
                'photo_consent' => false,
            ],
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
                    ? (filter_var($data['guardian1_email'], FILTER_VALIDATE_EMAIL)
                        ? $data['guardian1_email']
                        : null)
                    : null,
                'relationship' => 'other',
            ]);

            if ($guardian1 && !$student->guardians()->where('guardian_id', $guardian1->id)->exists()) {
                $student->guardians()->attach($guardian1->id, [
                    'relationship_type' => 'mother',
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
                    ? (filter_var($data['guardian2_email'], FILTER_VALIDATE_EMAIL)
                        ? $data['guardian2_email']
                        : null)
                    : null,
                'relationship' => 'other',
            ]);

            if ($guardian2 && !$student->guardians()->where('guardian_id', $guardian2->id)->exists()) {
                $student->guardians()->attach($guardian2->id, [
                    'relationship_type' => 'father',
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

    /**
     * Normalizza un CF: maiuscolo, senza spazi. Ritorna stringa vuota se assente.
     */
    protected function cleanTaxCode($value)
    {
        if (empty($value)) {
            return '';
        }
        return preg_replace('/\s+/', '', strtoupper((string) $value));
    }

    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value) && (float) $value > 20000) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
            } catch (\Throwable $e) {
                return null;
            }
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];

        foreach ($formats as $format) {
            $date = Carbon::createFromFormat($format, $value);
            if ($date !== false && Carbon::getLastErrors()['error_count'] === 0 && Carbon::getLastErrors()['warning_count'] === 0) {
                return $date;
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
        if (empty($value)) {
            return null;
        }

        $value = str_replace(['€', ' ', '.'], '', (string) $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }
}
