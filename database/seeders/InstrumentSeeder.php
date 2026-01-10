<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instrument;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Strumenti ===');
        
        $imported = $this->importFromGestionale();
        $imported += $this->importFromAccessori();
        
        $this->command->info("✓ Totale strumenti importati: {$imported}");
    }
    
    protected function importFromGestionale()
    {
        $this->command->info('Importazione strumenti da file gestionale...');
        
        $filePath = base_path('docs/materiale cliente/db 2025-26 gestionale.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato");
            return 0;
        }
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheetByName('dati');
            
            if (!$sheet) {
                return 0;
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
            
            // Cerca colonne strumento
            $instrumentCols = [
                'fornitore strumento' => 'supplier',
                'noleggio/proprietà' => 'rental_type',
                'provenienza' => 'origin',
                'tipo' => 'type',
                'marca' => 'brand',
                'mod' => 'model',
                'misura' => 'size',
                'cod' => 'code',
            ];
            
            $colMap = [];
            foreach ($instrumentCols as $headerKey => $field) {
                if (isset($headerMap[$headerKey])) {
                    $colMap[$field] = $headerMap[$headerKey];
                }
            }
            
            if (empty($colMap)) {
                $this->command->warn("Colonne strumento non trovate");
                return 0;
            }
            
            $highestRow = $sheet->getHighestRow();
            $imported = 0;
            
            // Trova colonna studente
            $studentCol = $headerMap['cognome'] ?? null;
            $studentNameCol = $headerMap['nome'] ?? null;
            
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $cognome = trim((string)$sheet->getCell($studentCol . $row)->getValue());
                    $nome = trim((string)$sheet->getCell($studentNameCol . $row)->getValue());
                    
                    if (empty($cognome) && empty($nome)) {
                        continue;
                    }
                    
                    // Trova studente
                    $student = Student::whereRaw('LOWER(last_name) = ?', [strtolower($cognome)])
                        ->whereRaw('LOWER(first_name) = ?', [strtolower($nome)])
                        ->first();
                    
                    if (!$student) {
                        continue;
                    }
                    
                    // Leggi dati strumento
                    $type = !empty($colMap['type']) ? trim((string)$sheet->getCell($colMap['type'] . $row)->getValue()) : null;
                    
                    if (empty($type)) {
                        continue;
                    }
                    
                    // Crea strumento
                    $instrument = Instrument::firstOrCreate(
                        [
                            'name' => $type,
                            'code' => !empty($colMap['code']) ? trim((string)$sheet->getCell($colMap['code'] . $row)->getValue()) : null,
                        ],
                        [
                            'brand' => !empty($colMap['brand']) ? trim((string)$sheet->getCell($colMap['brand'] . $row)->getValue()) : null,
                            'model' => !empty($colMap['model']) ? trim((string)$sheet->getCell($colMap['model'] . $row)->getValue()) : null,
                            'size' => !empty($colMap['size']) ? trim((string)$sheet->getCell($colMap['size'] . $row)->getValue()) : null,
                            'status' => 'available',
                        ]
                    );
                    
                    $imported++;
                    
                } catch (\Exception $e) {
                    // Ignora errori
                }
            }
            
            $this->command->info("  Importati {$imported} strumenti da gestionale");
            return $imported;
            
        } catch (\Exception $e) {
            $this->command->warn("Errore: " . $e->getMessage());
            return 0;
        }
    }
    
    protected function importFromAccessori()
    {
        $this->command->info('Importazione strumenti da file accessori...');
        
        $filePath = base_path('docs/materiale cliente/Db Accessori 2025-26.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato");
            return 0;
        }
        
        // TODO: Implementare importazione da file accessori (noleggi dettagliati)
        $this->command->info("  (Da implementare - noleggi dettagliati)");
        return 0;
    }
}
