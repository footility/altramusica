<?php

return [
    'dashboard' => [
        'label' => 'Panoramica',
        'icon' => 'bi-grid-1x2',
        'home_route' => 'admin.dashboard',
        'patterns' => ['admin.dashboard'],
        'items' => [
            ['label' => 'Dashboard', 'icon' => 'bi-house-door', 'route' => 'admin.dashboard', 'patterns' => ['admin.dashboard']],
        ],
    ],
    'people' => [
        'label' => 'Persone',
        'icon' => 'bi-people',
        'home_route' => 'admin.students.index',
        'patterns' => ['admin.students.*', 'admin.guardians.*', 'admin.teachers.*', 'admin.academic-years.*', 'admin.family-requests.*'],
        'items' => [
            ['label' => 'Studenti', 'icon' => 'bi-people', 'route' => 'admin.students.index', 'patterns' => ['admin.students.*']],
            ['label' => 'Genitori/Tutori', 'icon' => 'bi-person-badge', 'route' => 'admin.guardians.index', 'patterns' => ['admin.guardians.*']],
            ['label' => 'Richieste famiglie', 'icon' => 'bi-chat-left-dots', 'route' => 'admin.family-requests.index', 'patterns' => ['admin.family-requests.*']],
            ['label' => 'Docenti', 'icon' => 'bi-person-workspace', 'route' => 'admin.teachers.index', 'patterns' => ['admin.teachers.*']],
            ['label' => 'Anni scolastici', 'icon' => 'bi-calendar-range', 'route' => 'admin.academic-years.index', 'patterns' => ['admin.academic-years.*']],
        ],
    ],
    'teaching' => [
        'label' => 'Didattica',
        'icon' => 'bi-music-note-beamed',
        'home_route' => 'admin.courses.index',
        'patterns' => [
            'admin.courses.*',
            'admin.enrollments.*',
            'admin.calendar.*',
            'admin.lessons.*',
            'admin.exams.*',
            'admin.classrooms.*',
            'admin.student-availability.*',
            'admin.teacher-availability.*',
        ],
        'items' => [
            ['label' => 'Corsi', 'icon' => 'bi-book', 'route' => 'admin.courses.index', 'patterns' => ['admin.courses.*']],
            ['label' => 'Iscrizioni', 'icon' => 'bi-clipboard-check', 'route' => 'admin.enrollments.index', 'patterns' => ['admin.enrollments.*']],
            ['label' => 'Calendario annuale', 'icon' => 'bi-calendar3', 'route' => 'admin.calendar.index', 'patterns' => ['admin.calendar.*']],
            ['label' => 'Calendario lezioni', 'icon' => 'bi-calendar-event', 'route' => 'admin.lessons.calendar', 'patterns' => ['admin.lessons.*']],
            ['label' => 'Disponibilità studenti', 'icon' => 'bi-clock', 'route' => 'admin.student-availability.index', 'patterns' => ['admin.student-availability.*']],
            ['label' => 'Disponibilità docenti', 'icon' => 'bi-clock-history', 'route' => 'admin.teacher-availability.index', 'patterns' => ['admin.teacher-availability.*']],
            ['label' => 'Aule', 'icon' => 'bi-door-open', 'route' => 'admin.classrooms.index', 'patterns' => ['admin.classrooms.*']],
            ['label' => 'Esami', 'icon' => 'bi-clipboard-data', 'route' => 'admin.exams.index', 'patterns' => ['admin.exams.*']],
        ],
    ],
    'accounting' => [
        'label' => 'Amministrazione',
        'icon' => 'bi-receipt',
        'home_route' => 'admin.contracts.index',
        'patterns' => ['admin.contracts.*', 'admin.invoices.*', 'admin.payment-plans.*', 'admin.accounting.*', 'admin.documents.*'],
        'items' => [
            ['label' => 'Contratti', 'icon' => 'bi-file-earmark-text', 'route' => 'admin.contracts.index', 'patterns' => ['admin.contracts.*']],
            ['label' => 'Fatture', 'icon' => 'bi-receipt', 'route' => 'admin.invoices.index', 'patterns' => ['admin.invoices.*'], 'permission' => 'invoices.view'],
            ['label' => 'Scadenzario rate', 'icon' => 'bi-calendar-check', 'route' => 'admin.payment-plans.index', 'patterns' => ['admin.payment-plans.*'], 'permission' => 'payment-plans.view'],
            ['label' => 'Crediti/Debiti', 'icon' => 'bi-cash-coin', 'route' => 'admin.accounting.balances', 'patterns' => ['admin.accounting.*'], 'permission' => 'accounting.view'],
            ['label' => 'Documenti', 'icon' => 'bi-folder2-open', 'route' => 'admin.documents.index', 'patterns' => ['admin.documents.*']],
        ],
    ],
    'materials' => [
        'label' => 'Materiali',
        'icon' => 'bi-box-seam',
        'home_route' => 'admin.instruments.index',
        'patterns' => ['admin.instruments.*', 'admin.instrument-rentals.*', 'admin.books.*', 'admin.book-distributions.*'],
        'items' => [
            ['label' => 'Strumenti', 'icon' => 'bi-music-note', 'route' => 'admin.instruments.index', 'patterns' => ['admin.instruments.*']],
            ['label' => 'Noleggi strumenti', 'icon' => 'bi-box-seam', 'route' => 'admin.instrument-rentals.index', 'patterns' => ['admin.instrument-rentals.*']],
            ['label' => 'Libri', 'icon' => 'bi-journal-bookmark', 'route' => 'admin.books.index', 'patterns' => ['admin.books.*']],
            ['label' => 'Distribuzioni libri', 'icon' => 'bi-journal-arrow-up', 'route' => 'admin.book-distributions.index', 'patterns' => ['admin.book-distributions.*']],
        ],
    ],
    'settings' => [
        'label' => 'Configurazione',
        'icon' => 'bi-gear',
        'home_route' => 'admin.acl.index',
        'patterns' => ['admin.acl.*', 'admin.login-logs.*'],
        'permission' => 'acl.view',
        'items' => [
            ['label' => 'Ruoli e permessi', 'icon' => 'bi-shield-lock', 'route' => 'admin.acl.index', 'patterns' => ['admin.acl.*'], 'permission' => 'acl.view'],
            ['label' => 'Log accessi', 'icon' => 'bi-clock-history', 'route' => 'admin.login-logs.index', 'patterns' => ['admin.login-logs.*'], 'permission' => 'acl.view'],
        ],
    ],
];
