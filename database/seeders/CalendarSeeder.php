<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\CalendarLesson;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CalendarSeeder extends Seeder
{
    private array $importedDates = [];

    private array $dayRows = [
        7 => 'monday',
        9 => 'tuesday',
        11 => 'wednesday',
        13 => 'thursday',
        15 => 'friday',
        17 => 'saturday',
    ];

    private array $months = [
        'AGOSTO' => 8,
        'SETTEMBRE' => 9,
        'OTTOBRE' => 10,
        'NOVEMBRE' => 11,
        'DICEMBRE' => 12,
        'GENNAIO' => 1,
        'FEBBRAIO' => 2,
        'MARZO' => 3,
        'APRILE' => 4,
        'MAGGIO' => 5,
        'GIUGNO' => 6,
        'LUGLIO' => 7,
    ];

    public function run(): void
    {
        $this->command->info('=== Importazione Calendario ===');

        $academicYear = AcademicYear::where('is_active', true)->first();
        $filePath = base_path('docs/materiale cliente/Calendario 2025-26.ods');

        if (!$academicYear || !file_exists($filePath)) {
            $this->command->warn('Anno accademico o file calendario non disponibile');
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Sheet1') ?? $spreadsheet->getSheet(0);
        $monthByColumn = $this->monthByColumn($sheet);
        $activeColumns = $this->cycleColumns($sheet);
        $freeLessonColumns = array_merge(range(4, 10), range(56, 64)); // D:J e BD:BL

        CalendarLesson::where('academic_year_id', $academicYear->id)->delete();

        $importedCycles = $this->importColumns(
            $sheet,
            $academicYear,
            $activeColumns,
            $monthByColumn,
            fn (int $week) => $this->cycleNote($week)
        );
        $importedFree = $this->importColumns(
            $sheet,
            $academicYear,
            $freeLessonColumns,
            $monthByColumn,
            fn () => 'Lezione libera da calendario ODS'
        );

        $this->command->info("✓ Giornate ciclo importate: {$importedCycles}");
        $this->command->info("✓ Giornate lezioni libere importate: {$importedFree}");
    }

    private function cycleColumns($sheet): array
    {
        $columns = [];
        $highestColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        for ($index = 1; $index <= $highestColumn; $index++) {
            $column = Coordinate::stringFromColumnIndex($index);
            $value = $this->calculatedValue($sheet, $column . '6');
            if (is_numeric($value) && (int) $value > 0) {
                $columns[$index] = (int) $value;
            }
        }

        return $columns;
    }

    private function monthByColumn($sheet): array
    {
        $mapped = [];
        $currentMonth = null;
        $highestColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        for ($index = 1; $index <= $highestColumn; $index++) {
            $column = Coordinate::stringFromColumnIndex($index);
            $label = strtoupper(trim((string) $sheet->getCell($column . '5')->getValue()));
            if (isset($this->months[$label])) {
                $currentMonth = $this->months[$label];
            }
            $mapped[$index] = $currentMonth;
        }

        return $mapped;
    }

    private function importColumns(
        $sheet,
        AcademicYear $academicYear,
        array $columns,
        array $monthByColumn,
        callable $noteForColumn
    ): int {
        $count = 0;

        foreach ($columns as $columnIndex => $columnValue) {
            $month = $monthByColumn[$columnIndex] ?? null;
            if (!$month) {
                continue;
            }

            $year = $month >= (int) $academicYear->start_date->format('n')
                ? (int) $academicYear->start_date->format('Y')
                : (int) $academicYear->end_date->format('Y');
            $column = Coordinate::stringFromColumnIndex($columnIndex);

            foreach ($this->dayRows as $row => $dayOfWeek) {
                $day = $this->calculatedValue($sheet, $column . $row);
                if (!is_numeric($day) || (int) $day < 1) {
                    continue;
                }

                try {
                    $date = Carbon::create($year, $month, (int) $day)->startOfDay();
                } catch (\Throwable) {
                    continue;
                }

                if ($date->lt($academicYear->start_date) || $date->gt($academicYear->end_date)) {
                    continue;
                }

                $dateKey = $date->format('Y-m-d');
                if (isset($this->importedDates[$dateKey])) {
                    continue;
                }

                CalendarLesson::create([
                    'academic_year_id' => $academicYear->id,
                    'date' => $dateKey,
                    'day_of_week' => $dayOfWeek,
                    'is_active' => true,
                    'notes' => $noteForColumn($columnValue),
                ]);
                $this->importedDates[$dateKey] = true;
                $count++;
            }
        }

        return $count;
    }

    private function cycleNote(int $week): string
    {
        $cycle = $week <= 11 ? 1 : ($week <= 22 ? 2 : 3);

        return "{$cycle}° ciclo - settimana {$week}";
    }

    private function calculatedValue($sheet, string $cell)
    {
        try {
            return $sheet->getCell($cell)->getCalculatedValue();
        } catch (\Throwable) {
            return $sheet->getCell($cell)->getValue();
        }
    }
}
