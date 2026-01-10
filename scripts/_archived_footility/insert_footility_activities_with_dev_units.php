<?php

/**
 * Script per inserire attività L'Altramusica in Footility con:
 * - Task e sottotask
 * - DEV UNIT associate
 * - Modello E/R
 * 
 * Uso:
 * php scripts/insert_footility_activities_with_dev_units.php
 */

$baseUrl = 'https://footility.test/api/v1';
$projectId = 13; // Progetto L'Altramusica
$token = getenv('FOOTILITY_API_TOKEN') ?: 'local-dev-token'; // Token locale per sviluppo

/**
 * Funzione helper per chiamate API
 */
function apiCall($url, $method = 'GET', $data = null) {
    global $token, $baseUrl;
    
    $ch = curl_init($baseUrl . $url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => json_decode($response, true),
    ];
}

/**
 * Carica dati attività da file esterno
 */
$activities = require __DIR__ . '/footility_activities_data.php';

echo "🚀 Inserimento attività in Footility (Progetto ID: {$projectId})\n";
echo "📊 Totale attività da inserire: " . count($activities) . "\n\n";

$createdActivities = 0;
$createdTasks = 0;
$createdSubtasks = 0;
$errors = 0;

foreach ($activities as $activityIndex => $activity) {
    // Calcola estimated_duration da DEV UNIT totali
    // Assumiamo 1 DEV UNIT = ~0.2 ore (12 minuti) come stima base
    $estimatedMinutes = $activity['dev_units']['total'] * 12;
    
    // Inserisci attività principale
    $activityData = [
        'project_id' => $projectId,
        'title' => $activity['title'],
        'description' => $activity['description'] . "\n\nDEV UNIT Totali: " . $activity['dev_units']['total'] . 
                       "\nBreakdown: DB_campi=" . $activity['dev_units']['db_campi'] . 
                       ", DB_relazioni=" . $activity['dev_units']['db_relazioni'] . 
                       ", CRUD=" . $activity['dev_units']['crud'] . 
                       ", Workflow=" . $activity['dev_units']['workflow'] . 
                       ", UI_form=" . $activity['dev_units']['ui_form'] . 
                       ", UI_lista=" . $activity['dev_units']['ui_lista'] . 
                       ", UI_stampa=" . $activity['dev_units']['ui_stampa'],
        'estimated_duration' => $estimatedMinutes,
        'status' => 'standby',
    ];
    
    // Aggiungi modello E/R nella description se disponibile
    if (isset($activity['er_model'])) {
        $activityData['description'] .= "\n\nModello E/R:\n";
        $activityData['description'] .= "Entità: " . $activity['er_model']['entity'] . "\n";
        $activityData['description'] .= "Attributi: " . implode(', ', $activity['er_model']['attributes']) . "\n";
        $activityData['description'] .= "Relazioni: " . json_encode($activity['er_model']['relationships'], JSON_PRETTY_PRINT);
    }
    
    $result = apiCall('/activities', 'POST', $activityData);
    
    if ($result['code'] === 201 || $result['code'] === 200) {
        $createdActivities++;
        $activityId = $result['data']['data']['id'] ?? null;
        
        if ($activityId) {
            echo "✅ Attività creata: {$activity['title']} (ID: {$activityId})\n";
            
            // Inserisci task per questa attività
            foreach ($activity['tasks'] as $taskIndex => $task) {
                $taskDevUnits = array_sum($task['dev_units']);
                $taskMinutes = $taskDevUnits * 12;
                
                $taskData = [
                    'activity_id' => $activityId,
                    'title' => $task['title'],
                    'description' => $task['description'] . "\n\nDEV UNIT: " . json_encode($task['dev_units']),
                    'estimated_duration' => $taskMinutes,
                    'status' => 'standby',
                ];
                
                // Prova endpoint alternativi per task
                $taskResult = apiCall('/tasks', 'POST', $taskData);
                
                // Se fallisce, prova come attività figlia
                if ($taskResult['code'] !== 201 && $taskResult['code'] !== 200) {
                    $taskData['parent_activity_id'] = $activityId;
                    $taskData['activity_id'] = null;
                    $taskResult = apiCall('/activities', 'POST', $taskData);
                }
                
                if ($taskResult['code'] === 201 || $taskResult['code'] === 200) {
                    $createdTasks++;
                    $taskId = $taskResult['data']['data']['id'] ?? null;
                    
                    if ($taskId && isset($task['subtasks'])) {
                        // Inserisci sottotask
                        foreach ($task['subtasks'] as $subtask) {
                            $subtaskDevUnits = array_sum($subtask['dev_units']);
                            $subtaskMinutes = $subtaskDevUnits * 12;
                            
                            $subtaskData = [
                                'title' => $subtask['title'],
                                'description' => "DEV UNIT: " . json_encode($subtask['dev_units']),
                                'estimated_duration' => $subtaskMinutes,
                                'status' => 'standby',
                            ];
                            
                            // Prova endpoint subtask
                            $subtaskData['task_id'] = $taskId;
                            $subtaskResult = apiCall('/subtasks', 'POST', $subtaskData);
                            
                            // Se fallisce, prova come task figlio
                            if ($subtaskResult['code'] !== 201 && $subtaskResult['code'] !== 200) {
                                $subtaskData['parent_activity_id'] = $taskId;
                                $subtaskData['task_id'] = null;
                                $subtaskResult = apiCall('/activities', 'POST', $subtaskData);
                            }
                            
                            if ($subtaskResult['code'] === 201 || $subtaskResult['code'] === 200) {
                                $createdSubtasks++;
                            } else {
                                $errors++;
                                echo "      ❌ Errore sottotask: {$subtask['title']}\n";
                            }
                        }
                    }
                } else {
                    $errors++;
                    echo "   ❌ Errore task: {$task['title']}\n";
                    if (isset($taskResult['data']['message'])) {
                        echo "      Errore: {$taskResult['data']['message']}\n";
                    }
                }
            }
        }
    } else {
        $errors++;
        $errorMsg = $result['data']['message'] ?? 'Errore sconosciuto';
        echo "❌ Errore attività: {$activity['title']}\n";
        echo "   Errore: {$errorMsg} (HTTP {$result['code']})\n";
        if (isset($result['data']['errors'])) {
            print_r($result['data']['errors']);
        }
    }
    
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Riepilogo:\n";
echo "   ✅ Attività create: {$createdActivities}\n";
echo "   ✅ Task creati: {$createdTasks}\n";
echo "   ✅ Subtask creati: {$createdSubtasks}\n";
echo "   ❌ Errori: {$errors}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if ($errors > 0) {
    exit(1);
}
