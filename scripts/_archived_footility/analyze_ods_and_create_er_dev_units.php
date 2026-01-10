<?php

/**
 * Script per analizzare ODS e creare:
 * 1. Modello E/R ottimizzato
 * 2. DEV UNIT intelligenti (considerando interdipendenze)
 * 
 * Uso:
 * php scripts/analyze_ods_and_create_er_dev_units.php
 */

require __DIR__ . '/../footility/footility/vendor/autoload.php';

$app = require_once __DIR__ . '/../footility/footility/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Analisi ODS e creazione E/R + DEV UNIT intelligenti...\n\n";

// Leggi attività FASE 1 (solo migrazione)
$activities = require __DIR__ . '/footility_activities_data.php';
$phase1Activities = array_slice($activities, 0, 24); // Prime 24 = FASE 1

$erModels = [];
$devUnits = [];

foreach ($phase1Activities as $index => $activity) {
    echo "📋 Analisi: {$activity['title']}\n";
    
    // Estrai E/R model se presente
    if (isset($activity['er_model'])) {
        $erModels[] = [
            'activity_id' => 2576 + $index, // ID attività in Footility
            'activity_title' => $activity['title'],
            'er_model' => $activity['er_model'],
        ];
    }
    
    // Analizza DEV UNIT considerando interdipendenze
    $devUnit = analyzeDevUnits($activity);
    
    $devUnits[] = [
        'activity_id' => 2576 + $index,
        'activity_title' => $activity['title'],
        'dev_units' => $devUnit,
    ];
    
    echo "   ✅ E/R e DEV UNIT analizzati\n";
}

// Salva E/R models
file_put_contents(
    __DIR__ . '/../docs/dev-unit/ER_MODELS_FASE1.json',
    json_encode(['er_models' => $erModels], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

// Salva DEV UNIT
file_put_contents(
    __DIR__ . '/../docs/dev-unit/DEV_UNITS_FASE1_INTELLIGENTI.json',
    json_encode(['dev_units' => $devUnits], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "\n✅ Analisi completata!\n";
echo "   📄 E/R Models: docs/dev-unit/ER_MODELS_FASE1.json\n";
echo "   📄 DEV UNIT: docs/dev-unit/DEV_UNITS_FASE1_INTELLIGENTI.json\n";

/**
 * Analizza DEV UNIT considerando interdipendenze
 */
function analyzeDevUnits($activity) {
    $devUnits = $activity['dev_units'] ?? [];
    $erModel = $activity['er_model'] ?? null;
    
    // DB: campi e relazioni (già corrette)
    $dbCampi = $devUnits['db_campi'] ?? 0;
    $dbRelazioni = $devUnits['db_relazioni'] ?? 0;
    
    // BE LOGIC: CRUD base (7) + workflow import (1) + workflow interdipendenze
    $crud = 7; // Sempre 7 per CRUD base
    $workflow = calculateWorkflowDevUnits($activity, $erModel);
    
    // UI: Form considerando campi interdipendenti
    $uiForm = calculateFormDevUnits($activity, $erModel);
    
    // UI: Lista considerando filtri e colonne
    $uiLista = calculateListaDevUnits($activity, $erModel);
    
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

/**
 * Calcola DEV UNIT workflow considerando interdipendenze
 */
function calculateWorkflowDevUnits($activity, $erModel) {
    $base = 1; // Import ODS base
    
    // Se ci sono relazioni, aggiungi workflow per validazione interdipendenze
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
function calculateFormDevUnits($activity, $erModel) {
    $base = $activity['dev_units']['ui_form'] ?? 0;
    
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
function calculateListaDevUnits($activity, $erModel) {
    $base = $activity['dev_units']['ui_lista'] ?? 0;
    
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
