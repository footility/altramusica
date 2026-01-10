<?php

/**
 * Script per aggiornare attività preventivo con DEV UNIT e calcolare ore/costi
 * 
 * Uso:
 * cd /Users/mistre/develop/footility/footility
 * php ../../gestionale-laltramusica/scripts/footility_update_activities_dev_units.php
 */

chdir('/Users/mistre/develop/footility/footility');

require __DIR__ . '/../../footility/footility/vendor/autoload.php';

$app = require_once __DIR__ . '/../../footility/footility/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$quotationId = 1;

// Leggi hourly_rate dal preventivo
$quotation = \DB::table('quotations')->where('id', $quotationId)->first();
$hourlyRate = $quotation->hourly_rate ?? 50.00;

echo "🔄 Aggiornamento attività preventivo con DEV UNIT...\n\n";

// Leggi DEV UNIT dal file JSON
$devUnitsFile = '/Users/mistre/develop/gestionale-laltramusica/docs/dev-unit/FOOTILITY_DEV_UNITS.json';
if (!file_exists($devUnitsFile)) {
    echo "❌ File DEV UNIT non trovato: {$devUnitsFile}\n";
    exit(1);
}

$devUnitsData = json_decode(file_get_contents($devUnitsFile), true);
$devUnitsByActivity = [];

// Crea mappa activity_title -> dev_units
foreach ($devUnitsData['dev_units'] ?? [] as $du) {
    $devUnitsByActivity[$du['activity_title']] = $du;
}

// Leggi tutte le attività del preventivo
$quotationActivities = \DB::table('quotation_activities')
    ->join('quotation_phases', 'quotation_activities.quotation_phase_id', '=', 'quotation_phases.id')
    ->where('quotation_phases.quotation_id', $quotationId)
    ->select('quotation_activities.*', 'quotation_phases.name as phase_name')
    ->get();

echo "📊 Trovate {$quotationActivities->count()} attività nel preventivo\n\n";

$updated = 0;
$notFound = 0;

foreach ($quotationActivities as $qa) {
    if (!isset($devUnitsByActivity[$qa->title])) {
        $notFound++;
        echo "⚠️  DEV UNIT non trovata per: {$qa->title}\n";
        continue;
    }
    
    $devUnits = $devUnitsByActivity[$qa->title];
    $totalDevUnits = $devUnits['dev_units']['total'] ?? 0;
    
    // Calcola: 1 DEV UNIT = 12 minuti = 0.2 ore
    $estimatedMinutes = $totalDevUnits * 12;
    $estimatedHours = round($totalDevUnits * 0.2, 1);
    $estimatedCost = round($estimatedHours * $hourlyRate, 2);
    
    \DB::table('quotation_activities')
        ->where('id', $qa->id)
        ->update([
            'estimated_hours' => $estimatedMinutes, // In minuti per Footility
            'estimated_cost' => $estimatedCost,
            'estimated_dev_units' => $totalDevUnits,
            'updated_at' => now(),
        ]);
    
    $updated++;
    echo "✅ [{$updated}/{$quotationActivities->count()}] {$qa->title} - {$totalDevUnits} DEV UNIT, {$estimatedHours}h, €{$estimatedCost}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Riepilogo:\n";
echo "   ✅ Aggiornate: {$updated}\n";
echo "   ⚠️  Non trovate: {$notFound}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Ricalcola totali fasi
echo "\n🔄 Ricalcolo totali fasi...\n";
$phases = \DB::table('quotation_phases')->where('quotation_id', $quotationId)->get();

foreach ($phases as $phase) {
    $activities = \DB::table('quotation_activities')->where('quotation_phase_id', $phase->id)->get();
    
    $totalHours = $activities->sum('estimated_hours');
    $totalCost = $activities->sum('estimated_cost');
    $totalDevUnits = $activities->sum('estimated_dev_units');
    
    \DB::table('quotation_phases')
        ->where('id', $phase->id)
        ->update([
            'total_estimated_hours' => $totalHours,
            'total_cost' => $totalCost,
            'total_dev_units' => $totalDevUnits,
            'updated_at' => now(),
        ]);
    
    $hours = round($totalHours / 60, 1);
    echo "✅ {$phase->name}: {$hours}h, €{$totalCost}, {$totalDevUnits} DEV UNIT\n";
}

// Ricalcola totale preventivo
echo "\n🔄 Ricalcolo totale preventivo...\n";
$quotation = \DB::table('quotations')->where('id', $quotationId)->first();
$allPhases = \DB::table('quotation_phases')->where('quotation_id', $quotationId)->get();

$totalHours = $allPhases->sum('total_estimated_hours');
$totalCost = $allPhases->sum('total_cost');
$totalDevUnits = $allPhases->sum('total_dev_units');

\DB::table('quotations')
    ->where('id', $quotationId)
    ->update([
        'total_estimated_hours' => $totalHours,
        'subtotal' => $totalCost,
        'total' => $totalCost,
        'updated_at' => now(),
    ]);

$totalHoursFormatted = round($totalHours / 60, 1);
echo "✅ Preventivo totale: {$totalHoursFormatted}h, €{$totalCost}, {$totalDevUnits} DEV UNIT\n";

echo "\n✅ Completato!\n";
