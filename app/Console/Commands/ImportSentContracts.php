<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;

class ImportSentContracts extends Command
{
    protected $signature = 'import:sent-contracts {--path=}';
    protected $description = 'Importa dati contratti inviati da PDF';

    public function handle()
    {
        $this->info('=== Importazione Contratti Inviati ===');
        
        $path = $this->option('path') ?: base_path('docs/materiale cliente/Contratti inviati');
        
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
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($files as $file) {
            try {
                // TODO: Implementare parsing PDF
                // Richiede libreria esterna: composer require smalot/pdfparser
                // Estrai: numero contratto, data invio
                // Aggiorna Contract esistente:
                // $contract = Contract::where('contract_number', $numero)->first();
                // if ($contract) {
                //     $contract->update(['status' => 'sent', 'sent_at' => $dataInvio]);
                //     $imported++;
                // }
                
                $skipped++;
            } catch (\Exception $e) {
                $this->warn("Errore file {$file}: " . $e->getMessage());
                $skipped++;
            }
        }
        
        $this->warn("Importazione PDF richiede libreria smalot/pdfparser");
        $this->info("Esegui: composer require smalot/pdfparser");
        $this->info("✓ Importati {$imported} contratti");
        $this->info("  Saltati: {$skipped}");
        
        return 0;
    }
}
