<?php

/*
|--------------------------------------------------------------------------
| ACL — mappa sezioni / entità / azioni
|--------------------------------------------------------------------------
| Sorgente unica di verità per i permessi Spatie e per la griglia checkbox
| configurabile dall'amministratore (Admin → Ruoli e permessi).
|
| Permesso = "{entita}.{azione}" (es. "students.create").
| Per aggiungere una nuova entità basta inserirla qui: permessi e griglia
| si aggiornano da soli (poi `php artisan acl:sync`).
*/

return [

    // Azioni CRUD standard mostrate come colonne nella griglia.
    'actions' => [
        'view'   => 'Vedi',
        'create' => 'Crea',
        'update' => 'Modifica',
        'delete' => 'Elimina',
    ],

    // Sezioni (righe raggruppate) → entità gestite.
    'sections' => [
        'Anagrafiche' => [
            'students'  => 'Studenti',
            'guardians' => 'Tutori',
            'teachers'  => 'Docenti',
        ],
        'Didattica' => [
            'courses'             => 'Corsi',
            'classrooms'          => 'Aule',
            'instruments'         => 'Strumenti',
            'exams'               => 'Esami',
            'calendar'            => 'Calendario',
            'student-availability' => 'Disponibilità studenti',
            'teacher-availability' => 'Disponibilità docenti',
        ],
        'Iscrizioni & Contratti' => [
            'enrollments' => 'Iscrizioni',
            'contracts'   => 'Contratti',
        ],
        'Amministrazione' => [
            'invoices'      => 'Fatture',
            'payment-plans' => 'Piani di pagamento',
            'accounting'    => 'Contabilità / saldi',
        ],
        'Materiali' => [
            'books'              => 'Libri',
            'book-distributions' => 'Distribuzione libri',
            'instrument-rentals' => 'Noleggio strumenti',
            'documents'          => 'Documenti',
        ],
        'Famiglie' => [
            'family-requests' => 'Richieste famiglie',
        ],
        'Configurazione' => [
            'academic-years' => 'Anni accademici',
            'settings'       => 'Impostazioni',
            'acl'            => 'Ruoli e permessi',
        ],
    ],

    // Ruoli di base creati al sync. 'admin' riceve sempre tutti i permessi
    // (gestito nel seeder), gli altri partono da un set ragionevole e poi
    // sono modificabili dalla griglia.
    'default_roles' => [
        'admin',
        'segreteria',
        'teacher',
        // R13 — area famiglie: ruolo SENZA permessi backoffice. Serve solo da gate
        // per le rotte `famiglia.*` (middleware role:family), mai per `admin.*`.
        'family',
    ],
];
