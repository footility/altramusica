<?php

/**
 * Script per:
 * 1. Analizzare ODS (basato su documentazione esistente)
 * 2. Creare E/R models ottimizzati
 * 3. Creare modelli E/R in Footility come entity
 * 4. Rinominare attività da "CRUD Completo" a "Migrazione"
 * 5. Ricalcolare DEV UNIT considerando interdipendenze intelligenti
 */

chdir('/Users/mistre/develop/footility/footility');
require __DIR__ . '/../../footility/footility/vendor/autoload.php';

$app = require_once __DIR__ . '/../../footility/footility/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Analisi ODS e creazione E/R + Migrazione in Footility...\n\n";

$projectId = 13;
$quotationId = 1;

// 1. Leggi attività FASE 1
$activitiesData = require __DIR__ . '/../scripts/footility_activities_data.php';
$phase1Activities = array_slice($activitiesData, 0, 24);

// 2. Ottieni attività da Footility
$phase1Id = \DB::table('quotation_phases')
    ->where('quotation_id', $quotationId)
    ->where('order', 1)
    ->value('id');

$quotationActivities = \DB::table('quotation_activities')
    ->where('quotation_phase_id', $phase1Id)
    ->orderBy('order')
    ->get();

echo "📋 Elaborazione " . count($phase1Activities) . " attività FASE 1...\n\n";

foreach ($phase1Activities as $index => $activityData) {
    $quotationActivity = $quotationActivities[$index] ?? null;
    if (!$quotationActivity) continue;
    
    $activityId = $quotationActivity->activity_id;
    $oldTitle = $quotationActivity->title;
    
    // 3. Rinomina da "CRUD Completo" a "Migrazione"
    $newTitle = str_replace(' - CRUD Completo', ' - Migrazione', $oldTitle);
    
    \DB::table('quotation_activities')
        ->where('id', $quotationActivity->id)
        ->update(['title' => $newTitle]);
    
    echo "📋 {$newTitle}\n";
    
    // 4. Crea E/R model come entity in Footility (se presente)
    if (isset($activityData['er_model'])) {
        $erModel = $activityData['er_model'];
        $entityName = $erModel['entity'];
        
        // Crea entity in dev_unit_definitions
        $existingEntity = \DB::table('dev_unit_definitions')
            ->where('project_id', $projectId)
            ->where('type', 'entity')
            ->where('semantic_key', $entityName)
            ->first();
        
        if (!$existingEntity) {
            \DB::table('dev_unit_definitions')->insert([
                'project_id' => $projectId,
                'semantic_key' => $entityName,
                'type' => 'entity',
                'table_name' => strtolower($entityName) . 's',
                'description' => "Entità {$entityName} - Migrazione da ODS",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "   📊 E/R Entity creata: {$entityName}\n";
        }
        
        // Crea devunit per l'entity
        $entityDef = \DB::table('dev_unit_definitions')
            ->where('project_id', $projectId)
            ->where('type', 'entity')
            ->where('semantic_key', $entityName)
            ->first();
        
        if ($entityDef) {
            $existingDevunit = \DB::table('devunits')
                ->where('project_id', $projectId)
                ->where('dev_unit_definition_id', $entityDef->id)
                ->where('semantic_path', $entityName)
                ->first();
            
            if (!$existingDevunit) {
                $devunitId = \DB::table('devunits')->insertGetId([
                    'project_id' => $projectId,
                    'dev_unit_definition_id' => $entityDef->id,
                    'category' => 'database',
                    'semantic_path' => $entityName,
                    'source_file_path' => strtolower($entityName) . 's',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Collega entity all'attività
                \DB::table('activity_devunit')->insert([
                    'activity_id' => $activityId,
                    'devunit_id' => $devunitId,
                    'state' => 'added',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    // 5. Ricalcola DEV UNIT considerando interdipendenze intelligenti
    $devUnits = recalculateIntelligentDevUnits($activityData, $activityId, $projectId);
    
    // Aggiorna attività con nuovi DEV UNIT
    \DB::table('quotation_activities')
        ->where('id', $quotationActivity->id)
        ->update([
            'estimated_dev_units' => $devUnits['total'],
            'estimated_hours' => round($devUnits['total'] * 0.2 * 60), // minuti
            'updated_at' => now(),
        ]);
    
    echo "   ✅ DEV UNIT: {$devUnits['total']} (DB: {$devUnits['db_campi']}+{$devUnits['db_relazioni']}, CRUD: {$devUnits['crud']}, Workflow: {$devUnits['workflow']}, UI: {$devUnits['ui_form']}+{$devUnits['ui_lista']})\n\n";
}

// 6. Ricalcola costi
$quotation = \App\Models\Quotation::find($quotationId);
$hourlyRate = $quotation->hourly_rate;

$activities = \App\Models\QuotationActivity::where('quotation_phase_id', $phase1Id)->get();
foreach ($activities as $activity) {
    $activity->calculateCost($hourlyRate);
}

echo "✅ Completato!\n";
echo "   📊 E/R models creati come entity\n";
echo "   📝 Attività rinominate in 'Migrazione'\n";
echo "   💡 Costi ricalcolati automaticamente\n";

/**
 * Ricalcola DEV UNIT considerando interdipendenze intelligenti
 */
function recalculateIntelligentDevUnits($activityData, $activityId, $projectId) {
    $baseDevUnits = $activityData['dev_units'] ?? [];
    $erModel = $activityData['er_model'] ?? null;
    
    // DB: campi e relazioni (già corrette)
    $dbCampi = $baseDevUnits['db_campi'] ?? 0;
    $dbRelazioni = $baseDevUnits['db_relazioni'] ?? 0;
    
    // BE LOGIC: CRUD base (7) + workflow intelligente
    $crud = 7;
    $workflow = calculateIntelligentWorkflow($activityData, $erModel);
    
    // UI: Form considerando campi interdipendenti
    $uiForm = calculateIntelligentForm($activityData, $erModel);
    
    // UI: Lista considerando filtri e colonne
    $uiLista = calculateIntelligentLista($activityData, $erModel);
    
    // UI: Stampa (0 per FASE 1 - solo migrazione)
    $uiStampa = 0;
    
    $total = $dbCampi + $dbRelazioni + $crud + $workflow + $uiForm + $uiLista + $uiStampa;
    
    return [
        'db_campi' => $dbCampi,
        'db_relazioni' => $dbRelazioni,
        'crud' => $crud,
        'workflow' => $workflow,
        'ui_form' => $uiForm,
        'ui_lista' => $uiLista,
        'ui_stampa' => $uiStampa,
        'total' => $total,
    ];
}

function calculateIntelligentWorkflow($activityData, $erModel) {
    $base = 1; // Import ODS base
    
    if ($erModel && isset($erModel['relationships'])) {
        $relationships = $erModel['relationships'];
        
        // Per ogni belongsTo, aggiungi validazione relazione + gestione dipendenza
        if (isset($relationships['belongsTo'])) {
            $base += count($relationships['belongsTo']) * 2; // Validazione + gestione dipendenza
        }
        
        // Per ogni belongsToMany, aggiungi gestione pivot + validazione
        if (isset($relationships['belongsToMany'])) {
            $base += count($relationships['belongsToMany']) * 3; // Validazione + gestione pivot + sincronizzazione
        }
        
        // Per ogni hasMany, aggiungi validazione esistenza
        if (isset($relationships['hasMany'])) {
            $base += count($relationships['hasMany']); // Validazione esistenza
        }
    }
    
    return $base;
}

function calculateIntelligentForm($activityData, $erModel) {
    $base = $activityData['dev_units']['ui_form'] ?? 0;
    
    if ($erModel && isset($erModel['relationships'])) {
        $relationships = $erModel['relationships'];
        
        // Per ogni belongsTo, aggiungi select/dropdown con validazione (2 DEV UNIT)
        if (isset($relationships['belongsTo'])) {
            $base += count($relationships['belongsTo']) * 2; // Select + validazione
        }
        
        // Per ogni belongsToMany, aggiungi multi-select o checkbox group con gestione (3 DEV UNIT)
        if (isset($relationships['belongsToMany'])) {
            $base += count($relationships['belongsToMany']) * 3; // Multi-select + validazione + gestione
        }
        
        // Campi calcolati/derivati (es. età da data nascita) = 2 DEV UNIT (campo + logica)
        $attributes = $erModel['attributes'] ?? [];
        if (in_array('age', $attributes) && in_array('birth_date', $attributes)) {
            $base += 2; // Campo età calcolato + logica calcolo
        }
        
        // Campi condizionali basati su relazioni (es. se studente minore → mostra genitore)
        if (isset($relationships['belongsTo'])) {
            $base += count($relationships['belongsTo']); // Logica condizionale
        }
    }
    
    return $base;
}

function calculateIntelligentLista($activityData, $erModel) {
    $base = $activityData['dev_units']['ui_lista'] ?? 0;
    
    if ($erModel && isset($erModel['relationships'])) {
        $relationships = $erModel['relationships'];
        
        // Per ogni relazione, aggiungi colonna relazionale (1 DEV UNIT)
        $relCount = 0;
        if (isset($relationships['belongsTo'])) {
            $relCount += count($relationships['belongsTo']);
        }
        if (isset($relationships['belongsToMany'])) {
            $relCount += count($relationships['belongsToMany']);
        }
        if (isset($relationships['hasMany'])) {
            $relCount += count($relationships['hasMany']);
        }
        
        $base += $relCount; // Colonne per relazioni
        
        // Filtri per relazioni (2 DEV UNIT per filtro: filtro + validazione)
        $base += $relCount * 2;
        
        // Ordinamento per relazioni (1 DEV UNIT per relazione)
        $base += $relCount;
    }
    
    return $base;
}
