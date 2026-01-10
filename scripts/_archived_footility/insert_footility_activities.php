<?php

/**
 * Script per inserire le macro attività di L'Altramusica in Footility
 * 
 * Uso:
 * php scripts/insert_footility_activities.php
 */

$baseUrl = 'https://footility.test/api/v1';
$projectId = 13;
$token = getenv('FOOTILITY_API_TOKEN');

if (!$token) {
    echo "❌ Errore: FOOTILITY_API_TOKEN non impostato\n";
    echo "Genera un token con: php artisan api:generate-token paolo.mistretta@footility.com --name=\"cursor-ai\"\n";
    exit(1);
}

// Macro attività da inserire
$activities = [
    [
        'title' => 'Macro 1: Infrastruttura Base',
        'description' => "Setup progetto e ambiente (16h)\nGestione utenze (24h)\nControllo accessi (16h)\nSicurezza GDPR (20h)\n\nTotale: 76h\nScadenza: Fine Agosto 2026\nPriorità: CRITICA",
        'estimated_duration' => 76 * 60, // 4560 minuti
        'status' => 'standby',
    ],
    [
        'title' => 'Macro 2: Anagrafiche',
        'description' => "Gestione Esercizio/Anno Scolastico (20h)\nGestione Studenti (32h)\nGestione Genitori/Tutore (20h)\nAnagrafica Fornitori e Clienti (16h)\nGestione Docenti (24h)\n\nTotale: 132h\nScadenza: Fine Agosto 2026\nPriorità: CRITICA\nDipende da: Macro 1",
        'estimated_duration' => 132 * 60, // 7920 minuti
        'status' => 'standby',
    ],
    [
        'title' => 'Macro 3: Primo Contatto e Iscrizioni',
        'description' => "Primo Contatto (24h)\nCalendario Lezioni (20h)\nIscrizione e Corsi (40h)\n\nTotale: 84h\nScadenza: Fine Agosto 2026\nPriorità: CRITICA\nDipende da: Macro 2",
        'estimated_duration' => 84 * 60, // 5040 minuti
        'status' => 'standby',
    ],
    [
        'title' => 'Macro 4: Contratti e Fatturazione',
        'description' => "Gestione Contratti (40h)\nGestione Fatturazione (48h)\nGestione Pagamenti (32h)\nRecupero Crediti (24h)\n\nTotale: 144h\nScadenza: Fine Agosto 2026\nPriorità: CRITICA\nDipende da: Macro 3",
        'estimated_duration' => 144 * 60, // 8640 minuti
        'status' => 'standby',
    ],
    [
        'title' => 'Macro 5: Proposta Oraria',
        'description' => "Sistema composizione orari (32h)\n\nTotale: 32h\nScadenza: Primi Settembre 2026\nPriorità: CRITICA\nDipende da: Macro 3",
        'estimated_duration' => 32 * 60, // 1920 minuti
        'status' => 'standby',
    ],
    [
        'title' => 'Macro 6: Didattica e Registro',
        'description' => "Registro Elettronico (32h)\nGestione Presenze (20h)\nConto Orario Insegnanti (40h)\nGestione Supplenti (24h)\nGestione Aule (16h)\n\nTotale: 132h\nScadenza: Fine Settembre 2026\nPriorità: ALTA\nDipende da: Macro 5",
        'estimated_duration' => 132 * 60, // 7920 minuti
        'status' => 'standby',
    ],
    [
        'title' => 'Macro 7: Attività Extra e Comunicazioni',
        'description' => "Attività Extra (Orchestra/Coro) (32h)\nGenerazione Attestati (16h)\nComunicazione e Privacy (40h)\n\nTotale: 88h\nScadenza: Fine Ottobre 2026\nPriorità: ALTA\nDipende da: Macro 6",
        'estimated_duration' => 88 * 60, // 5280 minuti
        'status' => 'standby',
    ],
    [
        'title' => 'Macro 8: Integrazioni e Reportistica',
        'description' => "Integrazione Cassetto Fiscale (32h)\nFlusso di Cassa (24h)\nPianificazione Annuale e Reportistica (40h)\nPreiscrizioni (20h)\n\nTotale: 116h\nScadenza: Dopo Ottobre 2026\nPriorità: MEDIA\nDipende da: Macro 4",
        'estimated_duration' => 116 * 60, // 6960 minuti
        'status' => 'standby',
    ],
    [
        'title' => 'Macro 9: Magazzino e Altro',
        'description' => "Gestione Accessori e Strumenti (32h)\nGestione Esami (16h)\n\nTotale: 48h\nScadenza: Dopo Ottobre 2026\nPriorità: MEDIA",
        'estimated_duration' => 48 * 60, // 2880 minuti
        'status' => 'standby',
    ],
];

echo "🚀 Inserimento attività in Footility (Progetto ID: {$projectId})\n";
echo "📊 Totale attività da inserire: " . count($activities) . "\n";
$totalMinutes = array_sum(array_column($activities, 'estimated_duration'));
$totalHours = $totalMinutes / 60;
echo "⏱️  Totale ore: " . $totalHours . "h\n\n";

$created = 0;
$errors = 0;

foreach ($activities as $index => $activity) {
    $activityData = [
        'project_id' => $projectId,
        'title' => $activity['title'],
        'description' => $activity['description'],
        'estimated_duration' => $activity['estimated_duration'],
        'status' => $activity['status'],
    ];

    $ch = curl_init("{$baseUrl}/activities");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($activityData),
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 201 || $httpCode === 200) {
        $created++;
        $activityId = $result['data']['id'] ?? 'N/A';
        $total = count($activities);
        echo "✅ [{$created}/{$total}] {$activity['title']} (ID: {$activityId})\n";
    } else {
        $errors++;
        $errorMsg = $result['message'] ?? 'Errore sconosciuto';
        $total = count($activities);
        $num = $index + 1;
        echo "❌ [{$num}/{$total}] {$activity['title']}\n";
        echo "   Errore: {$errorMsg} (HTTP {$httpCode})\n";
        if (isset($result['errors'])) {
            print_r($result['errors']);
        }
    }
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Riepilogo:\n";
echo "   ✅ Create: {$created}\n";
echo "   ❌ Errori: {$errors}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if ($errors > 0) {
    exit(1);
}

