<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AnalyzeOdsFiles extends Command
{
    protected $signature = 'ods:analyze {file?}';
    protected $description = 'Analizza file ODS per mappare struttura dati';

    public function handle()
    {
        $file = $this->argument('file');
        $docsPath = base_path('docs/materiale cliente');

        if ($file) {
            $files = [$file];
        } else {
            $files = [
                'db 2025-26 gestionale.ods',
                'Db Contratti 25-26.ods',
                'Db Contabile 2025-26.ods',
                'Db Accessori 2025-26.ods',
                'dati lavoratori 25-26.ods',
                'Calendario 2025-26.ods',
            ];
        }

        foreach ($files as $fileName) {
            $filePath = $docsPath . '/' . $fileName;
            
            if (!file_exists($filePath)) {
                $this->warn("File non trovato: {$fileName}");
                continue;
            }

            $this->info("\n=== Analizzando: {$fileName} ===");
            
            try {
                $spreadsheet = IOFactory::load($filePath);
                $sheetCount = $spreadsheet->getSheetCount();
                
                $this->info("Fogli trovati: {$sheetCount}");
                
                for ($i = 0; $i < $sheetCount; $i++) {
                    $sheet = $spreadsheet->getSheet($i);
                    $sheetName = $sheet->getTitle();
                    $this->info("\n--- Foglio: {$sheetName} ---");
                    
                    // Leggi prime 5 righe per capire struttura
                    $maxRow = min(5, $sheet->getHighestRow());
                    $maxCol = min(20, $sheet->getHighestColumn());
                    
                    $headers = [];
                    for ($col = 'A'; $col <= $maxCol; $col++) {
                        $value = $sheet->getCell($col . '1')->getValue();
                        if ($value) {
                            $headers[$col] = $value;
                        }
                    }
                    
                    $this->table(
                        ['Colonna', 'Header'],
                        array_map(fn($col, $header) => [$col, $header], array_keys($headers), $headers)
                    );
                    
                    // Mostra esempio dati
                    if ($maxRow > 1) {
                        $this->info("Esempio dati (riga 2):");
                        $sample = [];
                        foreach (array_keys($headers) as $col) {
                            $value = $sheet->getCell($col . '2')->getValue();
                            $sample[] = $value ? substr((string)$value, 0, 30) : '';
                        }
                        $this->line(implode(' | ', $sample));
                    }
                }
            } catch (\Exception $e) {
                $this->error("Errore leggendo {$fileName}: " . $e->getMessage());
            }
        }

        $this->info("\nAnalisi completata!");
        return 0;
    }
}
