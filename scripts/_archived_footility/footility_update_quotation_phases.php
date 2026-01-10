<?php

/**
 * Script per aggiornare preventivo con fasi e attività corrette
 * 
 * Uso:
 * cd /Users/mistre/develop/footility/footility
 * php ../../gestionale-laltramusica/scripts/footility_update_quotation_phases.php
 */

chdir('/Users/mistre/develop/footility/footility');

require __DIR__ . '/../../footility/footility/vendor/autoload.php';

$app = require_once __DIR__ . '/../../footility/footility/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$quotationId = 1;
$projectId = 13;
$hourlyRate = 50.00; // Da quotation esistente

echo "🔄 Aggiornamento preventivo ID {$quotationId} con fasi...\n\n";

// Elimina fasi e attività esistenti
$deletedPhases = \DB::table('quotation_phases')->where('quotation_id', $quotationId)->delete();
$deletedActivities = \DB::table('quotation_activities')->whereIn('quotation_phase_id', 
    \DB::table('quotation_phases')->where('quotation_id', $quotationId)->pluck('id')
)->delete();

echo "🗑️  Eliminate {$deletedPhases} fasi e relative attività\n\n";

// Leggi attività dal progetto
$allActivities = \DB::table('activities')
    ->where('project_id', $projectId)
    ->orderBy('id')
    ->get()
    ->keyBy('id');

// Definisci le fasi con le attività corrispondenti
// FASE 1: Attività 1-24 (ID 2576-2599)
// FASE 2: Attività 25-38 (ID 2600-2613)
// FASE 3: Attività 39-58 (ID 2614-2633)

$phases = [
    [
        'name' => 'FASE 1: Traduzione 1:1 ODS → DB Normalizzato',
        'description' => 'Traduzione completa del database ODS esistente in un database normalizzato e ingegnerizzato con CRUD base per ogni funzionalità esistente. Include: anagrafiche, disponibilità, calendario, corsi, contratti, fatturazione, didattica, magazzino.',
        'order' => 1,
        'activity_ids' => range(2576, 2599), // 24 attività
        'dev_units' => 1398,
    ],
    [
        'name' => 'FASE 2: Evoluzioni Amministrative',
        'description' => 'Evoluzioni avanzate per la gestione amministrativa: workflow contratti avanzati, fatturazione evolutiva, integrazioni esterne (SDI, Cassetto Fiscale), automazioni solleciti.',
        'order' => 2,
        'activity_ids' => range(2600, 2613), // 14 attività
        'dev_units' => 690,
    ],
    [
        'name' => 'FASE 3: Evoluzioni Didattiche',
        'description' => 'Evoluzioni avanzate per la gestione didattica: primo contatto pubblico, proposta oraria avanzata, registro elettronico evoluto, comunicazioni multi-canale, reportistica avanzata.',
        'order' => 3,
        'activity_ids' => range(2614, 2633), // 20 attività
        'dev_units' => 1140,
    ],
];

$createdPhases = 0;
$createdActivities = 0;

foreach ($phases as $phaseData) {
    // Crea fase
    $phaseId = \DB::table('quotation_phases')->insertGetId([
        'quotation_id' => $quotationId,
        'name' => $phaseData['name'],
        'description' => $phaseData['description'],
        'order' => $phaseData['order'],
        'duration_type' => 'weeks',
        'duration_value' => 0, // Da calcolare
        'start_date' => null,
        'end_date' => null,
        'total_estimated_hours' => 0,
        'total_cost' => 0,
        'total_dev_units' => $phaseData['dev_units'],
        'total_cosmic_points' => 0,
        'notes' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $createdPhases++;
    echo "✅ Fase creata: {$phaseData['name']} (ID: {$phaseId})\n";
    
    // Associa attività alla fase
    $order = 1;
    foreach ($phaseData['activity_ids'] as $activityId) {
        if (!isset($allActivities[$activityId])) {
            echo "   ⚠️  Attività ID {$activityId} non trovata\n";
            continue;
        }
        
        $activity = $allActivities[$activityId];
        
        // Calcola ore da DEV UNIT (1 DEV UNIT = 12 minuti = 0.2 ore)
        // Per ora usiamo una stima base, poi verrà aggiornata con DEV UNIT reali
        $estimatedMinutes = 0; // Sarà aggiornato quando associamo DEV UNIT
        $estimatedHours = 0;
        $estimatedCost = 0;
        $estimatedDevUnits = 0;
        
        \DB::table('quotation_activities')->insert([
            'quotation_phase_id' => $phaseId,
            'activity_id' => $activityId,
            'title' => $activity->title,
            'description' => $activity->description ?? '',
            'estimated_hours' => $estimatedHours,
            'estimated_cost' => $estimatedCost,
            'estimated_dev_units' => $estimatedDevUnits,
            'estimated_cosmic_points' => 0,
            'category' => null,
            'order' => $order++,
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $createdActivities++;
    }
    
    echo "   📋 Associate " . count($phaseData['activity_ids']) . " attività\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Riepilogo:\n";
echo "   ✅ Fasi create: {$createdPhases}\n";
echo "   ✅ Attività associate: {$createdActivities}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n⚠️  NOTA: Le ore e i costi verranno calcolati quando assocerai le DEV UNIT alle attività.\n";
