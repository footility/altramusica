<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\BookDistribution;
use App\Models\Exam;
use App\Models\Instrument;
use App\Models\InstrumentRental;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\StudentYear;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AccessorySeeder extends Seeder
{
    private int $createdProspects = 0;

    private int $unmatchedRows = 0;

    private array $bookColumns = [
        'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ',
        'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ',
    ];

    private array $examColumns = [
        ['amount' => 'AT', 'detail' => 'AU'],
        ['amount' => 'AV', 'detail' => 'AW'],
        ['amount' => 'AX', 'detail' => 'AY'],
        ['amount' => 'AZ', 'detail' => 'BA'],
        ['amount' => 'BB', 'detail' => 'BC'],
    ];

    private array $accessoryColumns = [
        ['amount' => 'K', 'detail' => 'L', 'label' => 'Accessorio 1'],
        ['amount' => 'M', 'detail' => 'N', 'label' => 'Accessorio 2'],
        ['amount' => 'O', 'detail' => 'P', 'label' => 'Accessorio 3'],
        ['amount' => 'Q', 'detail' => 'R', 'label' => 'Accessorio 4'],
        ['amount' => 'S', 'detail' => 'T', 'label' => 'Accessorio 5'],
        ['amount' => 'U', 'detail' => 'V', 'label' => 'Accessorio 6'],
        ['amount' => 'W', 'detail' => 'X', 'label' => 'Accessorio 7'],
        ['amount' => 'Y', 'detail' => 'Z', 'label' => 'Acquisto strumento'],
    ];

    public function run(): void
    {
        $this->command->info('=== Importazione Accessori / Noleggi / Libri / Esami ===');

        $academicYear = AcademicYear::where('is_active', true)->first();
        $filePath = base_path('docs/materiale cliente/Db Accessori 2025-26.ods');

        if (!$academicYear || !file_exists($filePath)) {
            $this->command->warn('Anno accademico o file accessori non disponibile');
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('accessori');
        $linkedDataSheet = collect($spreadsheet->getWorksheetIterator())
            ->first(fn ($worksheet) => str_contains(strtolower($worksheet->getTitle()), '#dati'));

        if (!$sheet) {
            $this->command->warn("Foglio 'accessori' non trovato");
            return;
        }

        $stats = ['rentals' => 0, 'books' => 0, 'exams' => 0, 'charges' => 0];

        for ($row = 2; $row <= $sheet->getHighestDataRow(); $row++) {
            if (!$this->hasImportableData($sheet, $row)) {
                continue;
            }

            $student = $this->findStudentForRow($sheet, $linkedDataSheet, $row, $academicYear);
            if (!$student) {
                $this->unmatchedRows++;
                continue;
            }

            $invoiceLines = [];
            $rental = $this->importRental($sheet, $row, $student, $academicYear);
            if ($rental) {
                $stats['rentals']++;
            }

            foreach ($this->accessoryColumns as $column) {
                $amount = $this->amount($sheet->getCell($column['amount'] . $row)->getValue());
                if ($amount <= 0) {
                    continue;
                }

                $detail = $this->text($sheet->getCell($column['detail'] . $row)->getValue());
                $invoiceLines[] = [
                    'item_type' => $rental && $column['amount'] === 'K' ? 'instrument_rental' : 'other',
                    'item_id' => $rental && $column['amount'] === 'K' ? $rental->id : null,
                    'description' => $detail ?: $column['label'],
                    'amount' => $amount,
                ];
            }

            foreach ($this->bookColumns as $index => $column) {
                $amount = $this->amount($sheet->getCell($column . $row)->getValue());
                if ($amount <= 0) {
                    continue;
                }

                $book = Book::firstOrCreate(
                    ['title' => 'Libro da registro ODS - voce ' . ($index + 1)],
                    ['price' => 0, 'stock_quantity' => 0]
                );

                BookDistribution::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'book_id' => $book->id,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'distribution_date' => $academicYear->start_date,
                        'quantity' => 1,
                        'price_paid' => $amount,
                    ]
                );

                $invoiceLines[] = [
                    'item_type' => 'book',
                    'item_id' => $book->id,
                    'description' => $book->title,
                    'amount' => $amount,
                ];
                $stats['books']++;
            }

            foreach ($this->examColumns as $index => $column) {
                $amount = $this->amount($sheet->getCell($column['amount'] . $row)->getValue());
                if ($amount <= 0) {
                    continue;
                }

                $detail = $this->text($sheet->getCell($column['detail'] . $row)->getValue());
                $exam = Exam::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'notes' => 'Import ODS accessori - riga ' . $row . ' - esame ' . ($index + 1) . ($detail ? ': ' . $detail : ''),
                    ],
                    [
                        'exam_type' => 'other',
                        'subject' => 'instrument',
                        'registration_fee' => $amount,
                        'result' => 'pending',
                    ]
                );

                $invoiceLines[] = [
                    'item_type' => 'exam',
                    'item_id' => $exam->id,
                    'description' => $detail ?: 'Esame ' . ($index + 1),
                    'amount' => $amount,
                ];
                $stats['exams']++;
            }

            $stampAmount = $this->amount($sheet->getCell('BD' . $row)->getValue());
            if ($stampAmount > 0) {
                $invoiceLines[] = [
                    'item_type' => 'other',
                    'item_id' => null,
                    'description' => 'Bollo esame',
                    'amount' => $stampAmount,
                ];
            }

            if ($invoiceLines) {
                $stats['charges'] += count($invoiceLines);
                $this->storeCharges($sheet, $row, $student, $academicYear, $invoiceLines);
            }
        }

        $this->command->info("✓ Noleggi importati: {$stats['rentals']}");
        $this->command->info("✓ Distribuzioni libri importate: {$stats['books']}");
        $this->command->info("✓ Esami importati: {$stats['exams']}");
        $this->command->info("✓ Righe addebito accessori/libri/esami: {$stats['charges']}");
        $this->command->info("  Prospect creati dal registro accessori: {$this->createdProspects}");
        $this->command->info("  Righe con importi senza nominativo associabile: {$this->unmatchedRows}");
    }

    private function importRental($sheet, int $row, Student $student, AcademicYear $academicYear): ?InstrumentRental
    {
        $monthlyFee = $this->amount($sheet->getCell('I' . $row)->getValue());
        $deposit = $this->amount($sheet->getCell('F' . $row)->getValue());
        $months = $this->amount($sheet->getCell('H' . $row)->getValue());
        $detail = $this->text($sheet->getCell('G' . $row)->getValue());

        if ($monthlyFee <= 0 && $deposit <= 0 && $months <= 0 && !$detail) {
            return null;
        }

        $rental = InstrumentRental::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->first();

        $instrument = $rental?->instrument ?? Instrument::updateOrCreate(
            ['serial_number' => 'ODS-NOLEGGIO-' . $row],
            [
                'type' => $detail ?: 'Strumento a noleggio',
                'status' => 'rented',
                'notes' => 'Creato dal registro accessori ODS',
            ]
        );

        $endDate = $this->date($sheet->getCell('J' . $row)->getValue());
        $notes = array_filter([
            $detail,
            $months > 0 ? 'Mesi noleggio da ODS: ' . (int) $months : null,
        ]);

        $rental = InstrumentRental::updateOrCreate(
            [
                'student_id' => $student->id,
                'instrument_id' => $instrument->id,
                'academic_year_id' => $academicYear->id,
            ],
            [
                'start_date' => $academicYear->start_date,
                'end_date' => $endDate,
                'monthly_fee' => $monthlyFee,
                'deposit' => $deposit,
                'status' => 'active',
                'notes' => implode(' | ', $notes) ?: null,
            ]
        );

        $instrument->update(['status' => 'rented']);

        return $rental;
    }

    private function storeCharges(
        $sheet,
        int $row,
        Student $student,
        AcademicYear $academicYear,
        array $lines
    ): void {
        $total = array_sum(array_column($lines, 'amount'));
        $dueDate = $this->date($sheet->getCell('J' . $row)->getValue()) ?? $academicYear->end_date;
        $invoice = Invoice::updateOrCreate(
            ['invoice_number' => "ODS-ACC-{$student->id}-{$academicYear->id}"],
            [
                'academic_year_id' => $academicYear->id,
                'student_id' => $student->id,
                'invoice_date' => $academicYear->start_date,
                'due_date' => $dueDate,
                'subtotal' => $total,
                'total_amount' => $total,
                'status' => 'draft',
                'notes' => 'Addebiti da registro accessori ODS; fatturazione e incassi da riconciliare.',
            ]
        );

        $invoice->items()->delete();

        foreach ($lines as $line) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => $line['item_type'],
                'item_id' => $line['item_id'],
                'description' => $line['description'],
                'quantity' => 1,
                'unit_price' => $line['amount'],
                'total_price' => $line['amount'],
            ]);
        }
    }

    private function findStudentForRow($sheet, $linkedDataSheet, int $row, AcademicYear $academicYear): ?Student
    {
        $firstName = $this->text($sheet->getCell('D' . $row)->getValue());
        $lastName = $this->text($sheet->getCell('C' . $row)->getValue());

        if ((!$firstName || !$lastName) && $linkedDataSheet) {
            $sourceRow = $this->referencedRow($sheet->getCell('C' . $row)->getValue())
                ?? $this->referencedRow($sheet->getCell('D' . $row)->getValue())
                ?? $row;
            $lastName = $this->text($linkedDataSheet->getCell('G' . $sourceRow)->getValue());
            $firstName = $this->text($linkedDataSheet->getCell('H' . $sourceRow)->getValue());
        }

        if (!$firstName || !$lastName) {
            return null;
        }

        $student = Student::whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
            ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
            ->first();

        if (!$student) {
            $student = Student::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
            StudentYear::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
                'status' => 'prospect',
                'notes' => 'Importato dal registro accessori; non presente nel gestionale principale.',
            ]);
            $this->createdProspects++;
        }

        return $student;
    }

    private function hasImportableData($sheet, int $row): bool
    {
        $columns = ['F', 'H', 'I', 'K', 'M', 'O', 'Q', 'S', 'U', 'W', 'Y', 'BD'];
        $columns = array_merge($columns, $this->bookColumns, array_column($this->examColumns, 'amount'));

        foreach ($columns as $column) {
            if ($this->amount($sheet->getCell($column . $row)->getValue()) > 0) {
                return true;
            }
        }

        return $this->text($sheet->getCell('G' . $row)->getValue()) !== null;
    }

    private function referencedRow($value): ?int
    {
        if (is_string($value) && preg_match('/![\$A-Z]+(\d+)/', $value, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function amount($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = trim((string) $value);
        if ($value === '' || str_starts_with($value, '=')) {
            return 0.0;
        }

        $value = str_replace(['EUR', '€', ' ', '.'], '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function text($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' || str_starts_with($value, '=') ? null : $value;
    }

    private function date($value): ?Carbon
    {
        if (is_numeric($value) && $value > 20000) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($value));
        }

        $value = $this->text($value);
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
