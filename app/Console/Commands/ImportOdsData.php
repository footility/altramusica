<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OdsImportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ImportOdsData extends Command
{
    protected $signature = 'ods:import
                            {file : Nome del file ODS da importare}
                            {--type= : Tipo di import (students, contracts, invoices, instruments, teachers, calendar)}
                            {--dry-run : Esegue solo l\'analisi senza importare}
                            {--sheet= : Nome del foglio specifico da importare}
                            {--report= : Path file JSON dove salvare il report dettagliato}';

    protected $description = 'Importa dati da file ODS nel database';

    public function handle()
    {
        $fileName = $this->argument('file');
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');
        $sheetName = $this->option('sheet');

        // Accetta sia nome relativo (in docs/materiale cliente) sia path assoluto.
        $filePath = file_exists($fileName)
            ? $fileName
            : base_path('docs/materiale cliente') . '/' . $fileName;

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
            return $this->importByType($spreadsheet, $type, $dryRun, $sheetName, $filePath);

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

    protected function importByType($spreadsheet, $type, $dryRun, $sheetName, $filePath = null)
    {
        $this->info("Importazione tipo: {$type}");

        if ($dryRun) {
            $this->warn("DRY RUN - Nessun dato verrà importato");
        }

        switch ($type) {
            case 'students':
                return $this->importStudents($filePath, $dryRun, $sheetName);
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

    /**
     * Import studenti: delega tutta la logica (matching, anomalie, dry-run) a
     * OdsImportService — unica fonte di verità — e stampa il report.
     */
    protected function importStudents($filePath, $dryRun, $sheetName)
    {
        $this->info("Importazione studenti dal file gestionale...");

        if (!\App\Models\AcademicYear::where('is_active', true)->exists()) {
            $this->error("Nessun anno accademico attivo trovato. Creane uno prima di importare.");
            return 1;
        }

        $service = new OdsImportService();

        try {
            $report = $service->importStudents($filePath, $sheetName ?: 'dati', null, (bool) $dryRun);
        } catch (\Throwable $e) {
            $this->error("Errore import: " . $e->getMessage());
            return 1;
        }

        $this->renderReport($report);

        if ($reportPath = $this->option('report')) {
            file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("Report JSON salvato in: {$reportPath}");
        }

        return 0;
    }

    /**
     * Stampa a console il report restituito dal service.
     */
    protected function renderReport(array $report)
    {
        $this->newLine();
        $this->info($report['dry_run'] ? "ANALISI (dry-run) completata" : "Importazione completata!");

        $this->table(
            ['Risultato', 'Conteggio'],
            [
                ['Righe totali', $report['total_rows']],
                ['Creati', $report['created']],
                ['Aggiornati', $report['updated']],
                ['Saltati/errore', $report['skipped']],
            ]
        );

        // Riepilogo anomalie
        $anomalies = $report['anomalies'] ?? [];
        $labels = [
            'missing_tax_code'   => 'CF mancanti',
            'invalid_tax_code'   => 'CF malformati',
            'duplicate_tax_code' => 'CF su nominativi diversi',
            'homonyms'           => 'Omonimi (nome+cognome ripetuti)',
            'notes_with_data'    => 'Note con dati strutturati',
            'invalid_email'      => 'Email non valide',
            'unparsable_date'    => 'Date non interpretabili',
        ];

        $anomalyRows = [];
        foreach ($labels as $key => $label) {
            $count = count($anomalies[$key] ?? []);
            if ($count > 0) {
                $anomalyRows[] = [$label, $count];
            }
        }

        if ($anomalyRows) {
            $this->newLine();
            $this->warn("Anomalie rilevate:");
            $this->table(['Anomalia', 'Righe'], $anomalyRows);

            // Dettaglio righe con warning (max 30 a video)
            $rowsWithWarnings = array_values(array_filter(
                $report['rows'] ?? [],
                fn ($r) => !empty($r['warnings'])
            ));

            if ($rowsWithWarnings) {
                $this->newLine();
                $this->line("Dettaglio righe con anomalie (prime 30):");
                $detail = array_map(fn ($r) => [
                    $r['row'],
                    $r['name'] ?: '—',
                    $r['action'],
                    implode('; ', $r['warnings']),
                ], array_slice($rowsWithWarnings, 0, 30));
                $this->table(['Riga', 'Nominativo', 'Azione', 'Anomalie'], $detail);

                $extra = count($rowsWithWarnings) - 30;
                if ($extra > 0) {
                    $this->line("... e altre {$extra} righe con anomalie (usa --report per il dettaglio completo).");
                }
            }
        } else {
            $this->info("Nessuna anomalia rilevata.");
        }

        if (!empty($report['errors'])) {
            $this->newLine();
            $this->error("Errori bloccanti:");
            foreach ($report['errors'] as $err) {
                $this->line(" - {$err}");
            }
        }
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
