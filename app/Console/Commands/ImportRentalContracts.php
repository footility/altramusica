<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InstrumentRental;
use App\Models\Student;
use App\Models\Instrument;

class ImportRentalContracts extends Command
{
    protected $signature = 'import:rental-contracts {--path=}';
    protected $description = 'Importa contratti noleggio strumenti da PDF';

    public function handle()
    {
        $this->info('=== Importazione Contratti Noleggio ===');
        
        $path = $this->option('path') ?: base_path('docs/materiale cliente/Contratti Noleggi');
        
        if (!is_dir($path)) {
            $this->error("Directory non trovata: {$path}");
            return 1;
        }
        
        $files = glob($path . '/*.pdf');
        
        if (empty($files)) {
            $this->warn("Nessun file PDF trovato in {$path}");
            return 0;
        }
        
        $this->info("Trovati " . count($files) . " file PDF");
        
        // TODO: Implementare parsing PDF
        // Richiede libreria esterna: composer require smalot/pdfparser
        // Esempio:
        // $parser = new \Smalot\PdfParser\Parser();
        // $pdf = $parser->parseFile($file);
        // $text = $pdf->getText();
        // Estrai: studente, strumento, date, cauzione
        
        $this->warn("Importazione PDF richiede libreria smalot/pdfparser");
        $this->info("Esegui: composer require smalot/pdfparser");
        
        return 0;
    }
}
