<?php

/**
 * Script per creare E/R models e DEV UNIT in Footility
 * 
 * Analizza ODS → Crea E/R ottimizzato → Crea DEV UNIT intelligenti → Inserisce in Footility
 * 
 * Uso:
 * php scripts/create_er_and_dev_units_footility.php
 */

chdir('/Users/mistre/develop/footility/footility');
require __DIR__ . '/../../footility/footility/vendor/autoload.php';

$app = require_once __DIR__ . '/../../footility/footility/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Analisi ODS e creazione E/R + DEV UNIT in Footility...\n\n";

$projectId = 13; // L'Altramusica
$quotationId = 1;

// Leggi attività FASE 1
$activitiesData = require __DIR__ . '/../scripts/footility_activities_data.php';
$phase1Activities = array_slice($activitiesData, 0, 24);

// Ottieni ID attività da Footility
$quotationActivities = \DB::table('quotation_activities')
    ->whereIn('quotation_phase_id', function($query) use ($quotationId) {
        $query->select('id')
            ->from('quotation_phases')
            ->where('quotation_id', $quotationId)
            ->where('order', 1); // FASE 1
    })
    ->orderBy('order')
    ->get();

if ($quotationActivities->count() !== count($phase1Activities)) {
    echo "⚠️  ATTENZIONE: Numero attività non corrisponde!\n";
    echo "   Footility: {$quotationActivities->count()}\n";
    echo "   Dati: " . count($phase1Activities) . "\n";
}

echo "📋 Elaborazione " . count($phase1Activities) . " attività FASE 1...\n\n";

$totalDevUnitsCreated = 0;

foreach ($phase1Activities as $index => $activityData) {
    $quotationActivity = $quotationActivities[$index] ?? null;
    if (!$quotationActivity) {
        echo "⚠️  Attività {$index} non trovata in Footility, salto...\n";
        continue;
    }
    
    $activityId = $quotationActivity->activity_id;
    $title = $activityData['title'];
    
    echo "📋 {$title}\n";
    
    // 1. Crea/aggiorna E/R model (se presente)
    if (isset($activityData['er_model'])) {
        $erModel = $activityData['er_model'];
        echo "   📊 E/R Model: {$erModel['entity']}\n";
        // TODO: Salva E/R model (verificare se c'è tabella dedicata o JSON in quotation_activities)
    }
    
    // 2. Analizza e crea DEV UNIT intelligenti
    $devUnits = analyzeAndCreateDevUnits($projectId, $activityId, $activityData);
    
    // 3. Aggiorna estimated_dev_units in quotation_activities
    \DB::table('quotation_activities')
        ->where('id', $quotationActivity->id)
        ->update([
            'estimated_dev_units' => $devUnits['total'],
            'updated_at' => now(),
        ]);
    
    $totalDevUnitsCreated += $devUnits['total'];
    
    echo "   ✅ DEV UNIT: {$devUnits['total']} (DB: {$devUnits['db_campi']}+{$devUnits['db_relazioni']}, CRUD: {$devUnits['crud']}, Workflow: {$devUnits['workflow']}, UI: {$devUnits['ui_form']}+{$devUnits['ui_lista']})\n\n";
}

echo "✅ Completato!\n";
echo "   📊 Totale DEV UNIT create: {$totalDevUnitsCreated}\n";
echo "   💡 Footility calcolerà automaticamente ore e costi\n";

/**
 * Analizza e crea DEV UNIT intelligenti considerando interdipendenze
 */
function analyzeAndCreateDevUnits($projectId, $activityId, $activityData) {
    $baseDevUnits = $activityData['dev_units'] ?? [];
    $erModel = $activityData['er_model'] ?? null;
    
    // DB: campi e relazioni (già corrette)
    $dbCampi = $baseDevUnits['db_campi'] ?? 0;
    $dbRelazioni = $baseDevUnits['db_relazioni'] ?? 0;
    
    // Crea DEV UNIT per ogni campo DB
    if ($erModel && isset($erModel['attributes'])) {
        foreach ($erModel['attributes'] as $attribute) {
            createDevUnitDefinition($projectId, $activityId, [
                'type' => 'field',
                'semantic_key' => "{$erModel['entity']}.{$attribute}",
                'table_name' => strtolower($erModel['entity']) . 's',
                'description' => "Campo {$attribute} in {$erModel['entity']}",
            ]);
        }
    }
    
    // Crea DEV UNIT per ogni relazione
    if ($erModel && isset($erModel['relationships'])) {
        $relationships = $erModel['relationships'];
        
        // belongsTo
        if (isset($relationships['belongsTo'])) {
            foreach ($relationships['belongsTo'] as $related) {
                createDevUnitDefinition($projectId, $activityId, [
                    'type' => 'relationship',
                    'semantic_key' => "{$erModel['entity']}.belongsTo.{$related}",
                    'table_name' => strtolower($erModel['entity']) . 's',
                    'description' => "Relazione {$erModel['entity']} → {$related}",
                ]);
            }
        }
        
        // belongsToMany
        if (isset($relationships['belongsToMany'])) {
            foreach ($relationships['belongsToMany'] as $related) {
                createDevUnitDefinition($projectId, $activityId, [
                    'type' => 'relationship',
                    'semantic_key' => "{$erModel['entity']}.belongsToMany.{$related}",
                    'table_name' => strtolower($erModel['entity']) . 's',
                    'description' => "Relazione many-to-many {$erModel['entity']} ↔ {$related}",
                ]);
            }
        }
        
        // hasMany
        if (isset($relationships['hasMany'])) {
            foreach ($relationships['hasMany'] as $related) {
                createDevUnitDefinition($projectId, $activityId, [
                    'type' => 'relationship',
                    'semantic_key' => "{$erModel['entity']}.hasMany.{$related}",
                    'table_name' => strtolower($erModel['entity']) . 's',
                    'description' => "Relazione {$erModel['entity']} → {$related} (one-to-many)",
                ]);
            }
        }
    }
    
    // BE LOGIC: CRUD base (7) + workflow
    $crud = 7;
    $workflow = calculateWorkflowDevUnits($activityData, $erModel);
    
    // UI: Form considerando campi interdipendenti
    $uiForm = calculateFormDevUnits($activityData, $erModel);
    
    // UI: Lista considerando filtri e colonne
    $uiLista = calculateListaDevUnits($activityData, $erModel);
    
    // UI: Stampa (0 per FASE 1)
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

/**
 * Crea definizione DEV UNIT in Footility
 * Struttura: dev_unit_definitions → devunits → activity_devunit
 */
function createDevUnitDefinition($projectId, $activityId, $data) {
    // 1. Crea/recupera dev_unit_definition
    $definition = \DB::table('dev_unit_definitions')
        ->where('project_id', $projectId)
        ->where('semantic_key', $data['semantic_key'])
        ->first();
    
    if (!$definition) {
        $definitionId = \DB::table('dev_unit_definitions')->insertGetId([
            'project_id' => $projectId,
            'semantic_key' => $data['semantic_key'],
            'type' => $data['type'],
            'table_name' => $data['table_name'],
            'description' => $data['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        $definitionId = $definition->id;
    }
    
    // 2. Crea/recupera devunit (istanza concreta)
    $category = mapTypeToCategory($data['type']);
    $semanticPath = $data['semantic_key'];
    
    $devunit = \DB::table('devunits')
        ->where('project_id', $projectId)
        ->where('dev_unit_definition_id', $definitionId)
        ->where('semantic_path', $semanticPath)
        ->first();
    
    if (!$devunit) {
        $devunitId = \DB::table('devunits')->insertGetId([
            'project_id' => $projectId,
            'dev_unit_definition_id' => $definitionId,
            'category' => $category,
            'semantic_path' => $semanticPath,
            'source_file_path' => $data['table_name'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        $devunitId = $devunit->id;
    }
    
    // 3. Collega devunit all'attività
    $existingLink = \DB::table('activity_devunit')
        ->where('activity_id', $activityId)
        ->where('devunit_id', $devunitId)
        ->first();
    
    if (!$existingLink) {
        \DB::table('activity_devunit')->insert([
            'activity_id' => $activityId,
            'devunit_id' => $devunitId,
            'state' => 'added',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    return $devunitId;
}

/**
 * Mappa tipo DEV UNIT a categoria
 */
function mapTypeToCategory($type) {
    $map = [
        'entity' => 'database',
        'field' => 'database',
        'relationship' => 'database',
    ];
    return $map[$type] ?? 'data';
}

/**
 * Calcola DEV UNIT workflow considerando interdipendenze
 */
function calculateWorkflowDevUnits($activityData, $erModel) {
    $base = 1; // Import ODS base
    
    if ($erModel && isset($erModel['relationships'])) {
        $relationships = $erModel['relationships'];
        
        // Per ogni belongsTo, aggiungi validazione relazione
        if (isset($relationships['belongsTo'])) {
            $base += count($relationships['belongsTo']);
        }
        
        // Per ogni belongsToMany, aggiungi gestione pivot
        if (isset($relationships['belongsToMany'])) {
            $base += count($relationships['belongsToMany']) * 2; // Validazione + gestione pivot
        }
    }
    
    return $base;
}

/**
 * Calcola DEV UNIT form considerando campi interdipendenti
 */
function calculateFormDevUnits($activityData, $erModel) {
    $base = $activityData['dev_units']['ui_form'] ?? 0;
    
    if ($erModel && isset($erModel['relationships'])) {
        // Per ogni belongsTo, aggiungi select/dropdown (1 DEV UNIT)
        if (isset($erModel['relationships']['belongsTo'])) {
            $base += count($erModel['relationships']['belongsTo']);
        }
        
        // Per ogni belongsToMany, aggiungi multi-select o checkbox group (2 DEV UNIT)
        if (isset($erModel['relationships']['belongsToMany'])) {
            $base += count($erModel['relationships']['belongsToMany']) * 2;
        }
        
        // Campi calcolati/derivati (es. età da data nascita) = 1 DEV UNIT
        $attributes = $erModel['attributes'] ?? [];
        if (in_array('age', $attributes) && in_array('birth_date', $attributes)) {
            $base += 1; // Campo età calcolato
        }
    }
    
    return $base;
}

/**
 * Calcola DEV UNIT lista considerando filtri e colonne
 */
function calculateListaDevUnits($activityData, $erModel) {
    $base = $activityData['dev_units']['ui_lista'] ?? 0;
    
    if ($erModel && isset($erModel['relationships'])) {
        // Per ogni relazione, aggiungi colonna relazionale (1 DEV UNIT)
        $relCount = 0;
        if (isset($erModel['relationships']['belongsTo'])) {
            $relCount += count($erModel['relationships']['belongsTo']);
        }
        if (isset($erModel['relationships']['belongsToMany'])) {
            $relCount += count($erModel['relationships']['belongsToMany']);
        }
        if (isset($erModel['relationships']['hasMany'])) {
            $relCount += count($erModel['relationships']['hasMany']);
        }
        
        $base += $relCount; // Colonne per relazioni
        
        // Filtri per relazioni (1 DEV UNIT per filtro)
        $base += $relCount;
    }
    
    return $base;
}
