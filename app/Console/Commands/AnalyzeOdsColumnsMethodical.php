<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AnalyzeOdsColumnsMethodical extends Command
{
    protected $signature = 'ods:analyze-columns {file?} {--output=docs/ANALISI_COLONNE_ODS_COMPLETA.md}';
    protected $description = 'Analisi metodica completa colonne ODS - FASE 1 del processo';

    public function handle()
    {
        $file = $this->argument('file');
        $outputFile = $this->option('output');
        $docsPath = base_path('docs/materiale cliente');

        $files = $file ? [$file] : [
            'db 2025-26 gestionale.ods',
            'Db Contratti 25-26.ods',
            'Db Contabile 2025-26.ods',
            'Db Accessori 2025-26.ods',
            'dati lavoratori 25-26.ods',
            'Calendario 2025-26.ods',
        ];

        $analysis = [
            'metadata' => [
                'date' => now()->toDateTimeString(),
                'files_analyzed' => [],
            ],
            'files' => [],
        ];

        foreach ($files as $fileName) {
            $filePath = $docsPath . '/' . $fileName;
            
            if (!file_exists($filePath)) {
                $this->warn("File non trovato: {$fileName}");
                continue;
            }

            $this->info("\n=== Analizzando: {$fileName} ===");
            
            try {
                $spreadsheet = IOFactory::load($filePath);
                $fileAnalysis = $this->analyzeFile($spreadsheet, $fileName);
                $analysis['files'][] = $fileAnalysis;
                $analysis['metadata']['files_analyzed'][] = $fileName;
                
                $this->info("✓ Analizzato: {$fileName} ({$fileAnalysis['sheet_count']} fogli)");
            } catch (\Exception $e) {
                $this->error("Errore leggendo {$fileName}: " . $e->getMessage());
            }
        }

        // Genera documento Markdown
        $this->generateMarkdownDocument($analysis, $outputFile);
        
        $this->info("\n✓ Analisi completata! Documento salvato: {$outputFile}");
        return 0;
    }

    private function analyzeFile($spreadsheet, $fileName): array
    {
        $sheetCount = $spreadsheet->getSheetCount();
        $fileAnalysis = [
            'file_name' => $fileName,
            'sheet_count' => $sheetCount,
            'sheets' => [],
        ];

        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $sheetName = $sheet->getTitle();
            
            $this->line("  Analizzando foglio: {$sheetName}...");
            
            $sheetAnalysis = $this->analyzeSheet($sheet, $sheetName);
            $fileAnalysis['sheets'][] = $sheetAnalysis;
        }

        return $fileAnalysis;
    }

    private function analyzeSheet($sheet, $sheetName): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);
        
        // Cerca riga header (prima riga non vuota con più di 3 colonne)
        $headerRow = $this->findHeaderRow($sheet, min($highestRow, 10), $highestColIndex);
        
        if (!$headerRow) {
            return [
                'sheet_name' => $sheetName,
                'header_row' => null,
                'columns' => [],
                'row_count' => $highestRow,
                'sample_data_rows' => [],
            ];
        }

        // Leggi header
        $columns = [];
        for ($col = 1; $col <= min($highestColIndex, 300); $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $cellValue = $sheet->getCell($colLetter . $headerRow)->getValue();
            
            if ($cellValue !== null && trim((string)$cellValue) !== '') {
                $header = trim((string)$cellValue);
                
                // Leggi tipo dato da righe di esempio
                $dataType = $this->detectDataType($sheet, $colLetter, $headerRow + 1, min($headerRow + 10, $highestRow));
                
                // Leggi esempio valori
                $sampleValues = $this->getSampleValues($sheet, $colLetter, $headerRow + 1, min($headerRow + 5, $highestRow));
                
                $columns[] = [
                    'column_letter' => $colLetter,
                    'column_index' => $col,
                    'header_name' => $header,
                    'data_type' => $dataType,
                    'sample_values' => array_filter($sampleValues), // Rimuovi valori vuoti
                ];
            }
        }

        // Leggi righe esempio dati
        $sampleDataRows = [];
        for ($row = $headerRow + 1; $row <= min($headerRow + 3, $highestRow); $row++) {
            $rowData = [];
            foreach ($columns as $col) {
                $cellValue = $sheet->getCell($col['column_letter'] . $row)->getValue();
                $rowData[$col['header_name']] = $cellValue !== null ? trim((string)$cellValue) : '';
            }
            if (!empty(array_filter($rowData))) {
                $sampleDataRows[] = $rowData;
            }
        }

        return [
            'sheet_name' => $sheetName,
            'header_row' => $headerRow,
            'column_count' => count($columns),
            'row_count' => $highestRow,
            'columns' => $columns,
            'sample_data_rows' => $sampleDataRows,
        ];
    }

    private function findHeaderRow($sheet, $maxRow, $maxCol): ?int
    {
        // Cerca nella prima riga
        $nonEmptyCount = 0;
        for ($col = 1; $col <= min($maxCol, 50); $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $value = $sheet->getCell($colLetter . '1')->getValue();
            if ($value !== null && trim((string)$value) !== '') {
                $nonEmptyCount++;
            }
        }
        
        // Se la prima riga ha almeno 3 valori non vuoti, è probabilmente l'header
        if ($nonEmptyCount >= 3) {
            return 1;
        }
        
        // Altrimenti cerca nelle righe successive
        for ($row = 2; $row <= $maxRow; $row++) {
            $nonEmptyCount = 0;
            for ($col = 1; $col <= min($maxCol, 50); $col++) {
                $colLetter = Coordinate::stringFromColumnIndex($col);
                $value = $sheet->getCell($colLetter . $row)->getValue();
                if ($value !== null && trim((string)$value) !== '') {
                    $nonEmptyCount++;
                }
            }
            if ($nonEmptyCount >= 3) {
                return $row;
            }
        }
        
        return null;
    }

    private function detectDataType($sheet, $colLetter, $startRow, $endRow): string
    {
        $values = [];
        for ($row = $startRow; $row <= $endRow; $row++) {
            $cellValue = $sheet->getCell($colLetter . $row)->getValue();
            if ($cellValue !== null && trim((string)$cellValue) !== '') {
                $values[] = trim((string)$cellValue);
            }
        }
        
        if (empty($values)) {
            return 'unknown';
        }
        
        // Analizza tipo
        $dateCount = 0;
        $numericCount = 0;
        $booleanCount = 0;
        
        foreach ($values as $value) {
            // Data (formati comuni)
            if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}$/', $value) || 
                preg_match('/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/', $value)) {
                $dateCount++;
            }
            // Numero
            elseif (is_numeric(str_replace([',', '.'], '', $value))) {
                $numericCount++;
            }
            // Booleano
            elseif (in_array(strtolower($value), ['si', 'no', 'sì', 'vero', 'falso', 'true', 'false', '1', '0', '✓', '✗'])) {
                $booleanCount++;
            }
        }
        
        $total = count($values);
        if ($dateCount / $total > 0.5) return 'date';
        if ($numericCount / $total > 0.5) return 'number';
        if ($booleanCount / $total > 0.5) return 'boolean';
        
        return 'string';
    }

    private function getSampleValues($sheet, $colLetter, $startRow, $endRow): array
    {
        $values = [];
        for ($row = $startRow; $row <= $endRow; $row++) {
            $cellValue = $sheet->getCell($colLetter . $row)->getValue();
            if ($cellValue !== null) {
                $value = trim((string)$cellValue);
                if ($value !== '') {
                    $values[] = substr($value, 0, 50); // Limita lunghezza
                }
            }
        }
        return array_unique($values);
    }

    private function generateMarkdownDocument($analysis, $outputFile): void
    {
        $outputPath = base_path($outputFile);
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = "# Analisi Metodica Colonne ODS - FASE 1\n\n";
        $content .= "**Data analisi:** {$analysis['metadata']['date']}\n\n";
        $content .= "**File analizzati:** " . count($analysis['metadata']['files_analyzed']) . "\n\n";
        $content .= "---\n\n";

        foreach ($analysis['files'] as $fileAnalysis) {
            $content .= "## File: {$fileAnalysis['file_name']}\n\n";
            $content .= "**Fogli:** {$fileAnalysis['sheet_count']}\n\n";

            foreach ($fileAnalysis['sheets'] as $sheet) {
                $content .= "### Foglio: {$sheet['sheet_name']}\n\n";
                
                if (empty($sheet['columns'])) {
                    $content .= "*Nessuna colonna identificata (foglio vuoto o formato non riconosciuto)*\n\n";
                    continue;
                }

                $content .= "**Riga header:** {$sheet['header_row']}  \n";
                $content .= "**Colonne identificate:** {$sheet['column_count']}  \n";
                $content .= "**Righe dati:** {$sheet['row_count']}\n\n";

                $content .= "#### Colonne\n\n";
                $content .= "| Colonna | Header | Tipo | Esempi |\n";
                $content .= "|---------|--------|------|--------|\n";

                foreach ($sheet['columns'] as $col) {
                    $examples = !empty($col['sample_values']) 
                        ? implode(', ', array_slice($col['sample_values'], 0, 3))
                        : '(nessun esempio)';
                    $examples = str_replace('|', '\\|', $examples); // Escape pipe
                    
                    $content .= "| {$col['column_letter']} ({$col['column_index']}) | {$col['header_name']} | {$col['data_type']} | {$examples} |\n";
                }

                $content .= "\n";

                // Righe esempio
                if (!empty($sheet['sample_data_rows'])) {
                    $content .= "#### Esempio Dati (prime righe)\n\n";
                    foreach ($sheet['sample_data_rows'] as $idx => $row) {
                        $content .= "**Riga " . ($sheet['header_row'] + $idx + 1) . ":**\n\n";
                        $content .= "```\n";
                        foreach ($row as $header => $value) {
                            if (!empty($value)) {
                                $content .= "  {$header}: {$value}\n";
                            }
                        }
                        $content .= "```\n\n";
                    }
                }

                $content .= "---\n\n";
            }
        }

        file_put_contents($outputPath, $content);
        $this->info("Documento generato: {$outputPath}");
    }
}
