<?php

/**
 * Script per eliminare attività esistenti progetto 13 e importare nuove
 * 
 * Uso:
 * cd /Users/mistre/develop/footility/footility
 * php ../../gestionale-laltramusica/scripts/footility_delete_and_import_activities.php
 */

// Cambia directory a Footility
chdir('/Users/mistre/develop/footility/footility');

require __DIR__ . '/../../footility/footility/vendor/autoload.php';

$app = require_once __DIR__ . '/../../footility/footility/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$projectId = 13;
$jsonFile = '/Users/mistre/develop/gestionale-laltramusica/docs/dev-unit/FOOTILITY_ATTIVITA_IMPORT_CLEAN.json';

echo "🗑️  Eliminazione attività esistenti progetto {$projectId}...\n";

// Elimina attività esistenti
$deleted = \DB::table('activities')
    ->where('project_id', $projectId)
    ->delete();

echo "✅ Eliminate {$deleted} attività esistenti\n\n";

// Leggi JSON
if (!file_exists($jsonFile)) {
    echo "❌ File JSON non trovato: {$jsonFile}\n";
    exit(1);
}

$json = json_decode(file_get_contents($jsonFile), true);
$activities = $json['activities'] ?? [];
$total = count($activities);

echo "📥 Importazione {$total} nuove attività...\n\n";

$imported = 0;
$errors = 0;

foreach ($activities as $index => $activity) {
    try {
        $activityId = \DB::table('activities')->insertGetId([
            'project_id' => $projectId,
            'title' => $activity['title'],
            'description' => $activity['description'] ?? '',
            'status' => $activity['status'] ?? 'standby',
            'estimated_duration' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $imported++;
        $num = $index + 1;
        echo "✅ [{$num}/{$total}] {$activity['title']} (ID: {$activityId})\n";
    } catch (\Exception $e) {
        $errors++;
        $num = $index + 1;
        echo "❌ [{$num}/{$total}] {$activity['title']}\n";
        echo "   Errore: {$e->getMessage()}\n";
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Riepilogo:\n";
echo "   ✅ Importate: {$imported}\n";
echo "   ❌ Errori: {$errors}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
