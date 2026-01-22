<?php

/**
 * Dati attività L'Altramusica per Footility
 * 
 * Struttura:
 * - Attività = Macro processo di trasformazione
 * - Task = Dettagli implementativi principali
 * - Subtask = Dettagli operativi
 * - DEV UNIT = Associati a ogni livello
 * - Modello E/R = Per ogni entità principale
 */

return [
    // ============================================
    // FASE 1: TRADUZIONE 1:1 ODS → DB NORMALIZZATO
    // ============================================
    
    // GRUPPO 1: ANAGRAFICHE BASE
    [
        'title' => 'Studenti - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS studenti in DB normalizzato con CRUD base. Include: anagrafica, disponibilità, livelli, orchestra/coro, strumenti collegati.',
        'dev_units' => [
            'db_campi' => 64,
            'db_relazioni' => 10,
            'crud' => 7,
            'workflow' => 2,
            'ui_form' => 35,
            'ui_lista' => 28,
            'ui_stampa' => 0,
            'total' => 146,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Studenti',
                'description' => 'Creazione modello Eloquent Student con tutti i campi ODS',
                'dev_units' => ['db_campi' => 64, 'db_relazioni' => 10],
                'subtasks' => [
                    ['title' => 'Migration tabella students', 'dev_units' => ['db_campi' => 64]],
                    ['title' => 'Relazioni Student → AcademicYear', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → Guardian (many-to-many)', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → Enrollment', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → Contract', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → Invoice', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → StudentAvailability', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → StudentLevel', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → InstrumentRental', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → ExtraActivityEnrollment', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Student → Communication', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Studenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'StudentController::index', 'dev_units' => ['crud' => 1]],
                    ['title' => 'StudentController::create', 'dev_units' => ['crud' => 1]],
                    ['title' => 'StudentController::store', 'dev_units' => ['crud' => 1]],
                    ['title' => 'StudentController::show', 'dev_units' => ['crud' => 1]],
                    ['title' => 'StudentController::edit', 'dev_units' => ['crud' => 1]],
                    ['title' => 'StudentController::update', 'dev_units' => ['crud' => 1]],
                    ['title' => 'StudentController::destroy', 'dev_units' => ['crud' => 1]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import e validazione dati da ODS',
                'dev_units' => ['workflow' => 2],
                'subtasks' => [
                    ['title' => 'Import dati da db 2025-26 gestionale.ods', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Validazione dati importati', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form Studenti',
                'description' => 'Form completo per inserimento/modifica tutti i campi ODS',
                'dev_units' => ['ui_form' => 35],
                'subtasks' => [
                    ['title' => 'Form anagrafica base (15 campi)', 'dev_units' => ['ui_form' => 15]],
                    ['title' => 'Form disponibilità (9 campi)', 'dev_units' => ['ui_form' => 9]],
                    ['title' => 'Form livelli (4 campi)', 'dev_units' => ['ui_form' => 4]],
                    ['title' => 'Form orchestra/coro (7 campi)', 'dev_units' => ['ui_form' => 7]],
                ],
            ],
            [
                'title' => 'UI Lista Studenti',
                'description' => 'Lista con colonne e filtri per visualizzazione dati',
                'dev_units' => ['ui_lista' => 28],
                'subtasks' => [
                    ['title' => 'Colonne lista (20 colonne)', 'dev_units' => ['ui_lista' => 20]],
                    ['title' => 'Filtri ricerca (8 filtri)', 'dev_units' => ['ui_lista' => 8]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Student',
            'attributes' => [
                'id', 'academic_year_id', 'code', 'first_name', 'last_name', 'birth_date', 'age',
                'tax_code', 'status', 'school_origin', 'how_know_us', 'preferences', 'notes',
                'admin_notes', 'privacy_consent', 'photo_consent', 'last_contact_date',
            ],
            'relationships' => [
                'belongsTo' => ['AcademicYear'],
                'belongsToMany' => ['Guardian'],
                'hasMany' => [
                    'Enrollment', 'Contract', 'Invoice', 'StudentAvailability', 'StudentLevel',
                    'InstrumentRental', 'ExtraActivityEnrollment', 'Communication', 'Exam',
                ],
            ],
        ],
    ],
    
    [
        'title' => 'Genitori/Tutori - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS genitori in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 22,
            'db_relazioni' => 2,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 22,
            'ui_lista' => 16,
            'ui_stampa' => 0,
            'total' => 70,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Genitori',
                'description' => 'Creazione modello Eloquent Guardian',
                'dev_units' => ['db_campi' => 22, 'db_relazioni' => 2],
                'subtasks' => [
                    ['title' => 'Migration tabella guardians', 'dev_units' => ['db_campi' => 22]],
                    ['title' => 'Migration tabella pivot student_guardian', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Guardian → Communication', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Genitori',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'GuardianController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import genitori da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import genitori da db 2025-26 gestionale.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Genitori',
                'description' => 'Form e lista per gestione genitori',
                'dev_units' => ['ui_form' => 22, 'ui_lista' => 16],
                'subtasks' => [
                    ['title' => 'Form genitori (22 campi)', 'dev_units' => ['ui_form' => 22]],
                    ['title' => 'Lista genitori (16 elementi)', 'dev_units' => ['ui_lista' => 16]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Guardian',
            'attributes' => [
                'id', 'first_name', 'last_name', 'tax_code', 'relationship', 'phone_home',
                'phone_work', 'cell_1', 'cell_2', 'cell_3', 'cell_4', 'email_1', 'email_2',
                'email_3', 'address', 'city', 'postal_code', 'country', 'privacy_consent',
            ],
            'relationships' => [
                'belongsToMany' => ['Student'],
                'hasMany' => ['Communication'],
            ],
        ],
    ],
    
    [
        'title' => 'Docenti - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS docenti (dati lavoratori) in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 28,
            'db_relazioni' => 3,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 25,
            'ui_lista' => 22,
            'ui_stampa' => 0,
            'total' => 86,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Docenti',
                'description' => 'Creazione modello Eloquent Teacher',
                'dev_units' => ['db_campi' => 28, 'db_relazioni' => 3],
                'subtasks' => [
                    ['title' => 'Migration tabella teachers', 'dev_units' => ['db_campi' => 28]],
                    ['title' => 'Relazioni Teacher → Course', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Teacher → Lesson', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Teacher → TeacherHour', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Docenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'TeacherController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import docenti da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import docenti da dati lavoratori 25-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Docenti',
                'description' => 'Form e lista per gestione docenti',
                'dev_units' => ['ui_form' => 25, 'ui_lista' => 22],
                'subtasks' => [
                    ['title' => 'Form docenti (25 campi)', 'dev_units' => ['ui_form' => 25]],
                    ['title' => 'Lista docenti (22 elementi)', 'dev_units' => ['ui_lista' => 22]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Teacher',
            'attributes' => [
                'id', 'first_name', 'last_name', 'birth_date', 'tax_code', 'vat_number',
                'id_number', 'id_issue_date', 'id_issuer', 'address', 'postal_code', 'city',
                'residence_address', 'residence_postal_code', 'residence_city', 'iban',
                'phone_home', 'phone_mobile', 'email', 'role', 'employment_type',
                'hourly_rate', 'notes_contrattuali',
            ],
            'relationships' => [
                'hasMany' => ['Course', 'Lesson', 'TeacherHour'],
            ],
        ],
    ],
    
    [
        'title' => 'Anno Scolastico - CRUD Completo',
        'description' => 'CRUD per gestione anni scolastici/esercizi',
        'dev_units' => [
            'db_campi' => 11,
            'db_relazioni' => 2,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 8,
            'ui_lista' => 10,
            'ui_stampa' => 0,
            'total' => 39,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Anno Scolastico',
                'description' => 'Creazione modello Eloquent AcademicYear',
                'dev_units' => ['db_campi' => 11, 'db_relazioni' => 2],
                'subtasks' => [
                    ['title' => 'Migration tabella academic_years', 'dev_units' => ['db_campi' => 11]],
                    ['title' => 'Relazioni AcademicYear → Student', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni AcademicYear → Invoice', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Anno Scolastico',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'AcademicYearController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Switch Anno Corrente',
                'description' => 'Funzionalità switch anno scolastico corrente',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Metodo setActive per anno corrente', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Anno Scolastico',
                'description' => 'Form e lista per gestione anni scolastici',
                'dev_units' => ['ui_form' => 8, 'ui_lista' => 10],
                'subtasks' => [
                    ['title' => 'Form anno scolastico (8 campi)', 'dev_units' => ['ui_form' => 8]],
                    ['title' => 'Lista anni scolastici (10 elementi)', 'dev_units' => ['ui_lista' => 10]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'AcademicYear',
            'attributes' => [
                'id', 'name', 'start_date', 'end_date', 'active', 'notes',
            ],
            'relationships' => [
                'hasMany' => ['Student', 'Invoice'],
            ],
        ],
    ],
    
    // GRUPPO 2: DISPONIBILITÀ E ORARI
    
    [
        'title' => 'Disponibilità Studenti - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS disponibilità studenti in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 13,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 10,
            'ui_lista' => 14,
            'ui_stampa' => 0,
            'total' => 46,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Disponibilità Studenti',
                'description' => 'Creazione modello Eloquent StudentAvailability',
                'dev_units' => ['db_campi' => 13, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella student_availability', 'dev_units' => ['db_campi' => 13]],
                    ['title' => 'Relazioni StudentAvailability → Student', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Disponibilità Studenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'StudentAvailabilityController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import disponibilità da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import disponibilità da db 2025-26 gestionale.ods (colonne P-X)', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Disponibilità Studenti',
                'description' => 'Form e lista per gestione disponibilità',
                'dev_units' => ['ui_form' => 10, 'ui_lista' => 14],
                'subtasks' => [
                    ['title' => 'Form disponibilità (10 campi)', 'dev_units' => ['ui_form' => 10]],
                    ['title' => 'Lista disponibilità (14 elementi)', 'dev_units' => ['ui_lista' => 14]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'StudentAvailability',
            'attributes' => [
                'id', 'student_id', 'available', 'monday', 'tuesday', 'wednesday', 'thursday',
                'friday', 'saturday', 'laboratory', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Student'],
            ],
        ],
    ],
    
    [
        'title' => 'Disponibilità Docenti - CRUD Completo',
        'description' => 'CRUD per gestione disponibilità oraria docenti',
        'dev_units' => [
            'db_campi' => 13,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 10,
            'ui_lista' => 14,
            'ui_stampa' => 0,
            'total' => 46,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Disponibilità Docenti',
                'description' => 'Creazione modello Eloquent TeacherAvailability',
                'dev_units' => ['db_campi' => 13, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella teacher_availability', 'dev_units' => ['db_campi' => 13]],
                    ['title' => 'Relazioni TeacherAvailability → Teacher', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Disponibilità Docenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'TeacherAvailabilityController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import disponibilità docenti da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import disponibilità da dati lavoratori 25-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Disponibilità Docenti',
                'description' => 'Form e lista per gestione disponibilità docenti',
                'dev_units' => ['ui_form' => 10, 'ui_lista' => 14],
                'subtasks' => [
                    ['title' => 'Form disponibilità docenti (10 campi)', 'dev_units' => ['ui_form' => 10]],
                    ['title' => 'Lista disponibilità docenti (14 elementi)', 'dev_units' => ['ui_lista' => 14]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'TeacherAvailability',
            'attributes' => [
                'id', 'teacher_id', 'day_of_week', 'time_start', 'time_end', 'available', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Teacher'],
            ],
        ],
    ],
    
    [
        'title' => 'Calendario Lezioni - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS calendario lezioni in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 13,
            'db_relazioni' => 3,
            'crud' => 7,
            'workflow' => 2,
            'ui_form' => 10,
            'ui_lista' => 15,
            'ui_stampa' => 0,
            'total' => 50,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Calendario Lezioni',
                'description' => 'Creazione modello Eloquent CalendarLesson',
                'dev_units' => ['db_campi' => 13, 'db_relazioni' => 3],
                'subtasks' => [
                    ['title' => 'Migration tabella calendar_lessons', 'dev_units' => ['db_campi' => 13]],
                    ['title' => 'Relazioni CalendarLesson → Course', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni CalendarLesson → Teacher', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni CalendarLesson → Classroom', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Calendario Lezioni',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'CalendarController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Calendario',
                'description' => 'Import e generazione calendario',
                'dev_units' => ['workflow' => 2],
                'subtasks' => [
                    ['title' => 'Import calendario da Calendario 2025-26.ods', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Generazione calendario automatico', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Calendario Lezioni',
                'description' => 'Form e lista per gestione calendario',
                'dev_units' => ['ui_form' => 10, 'ui_lista' => 15],
                'subtasks' => [
                    ['title' => 'Form calendario (10 campi)', 'dev_units' => ['ui_form' => 10]],
                    ['title' => 'Lista calendario (15 elementi)', 'dev_units' => ['ui_lista' => 15]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'CalendarLesson',
            'attributes' => [
                'id', 'date', 'course_id', 'teacher_id', 'classroom_id', 'time_start', 'time_end',
                'suspended', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Course', 'Teacher', 'Classroom'],
            ],
        ],
    ],
    
    [
        'title' => 'Sospensioni Calendario - CRUD Completo',
        'description' => 'CRUD per gestione sospensioni/festività calendario',
        'dev_units' => [
            'db_campi' => 9,
            'db_relazioni' => 0,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 6,
            'ui_lista' => 13,
            'ui_stampa' => 0,
            'total' => 36,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Sospensioni',
                'description' => 'Creazione modello Eloquent CalendarSuspension',
                'dev_units' => ['db_campi' => 9],
                'subtasks' => [
                    ['title' => 'Migration tabella calendar_suspensions', 'dev_units' => ['db_campi' => 9]],
                ],
            ],
            [
                'title' => 'CRUD Controller Sospensioni',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'CalendarController::suspensions CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import sospensioni da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import sospensioni da Calendario 2025-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Sospensioni',
                'description' => 'Form e lista per gestione sospensioni',
                'dev_units' => ['ui_form' => 6, 'ui_lista' => 13],
                'subtasks' => [
                    ['title' => 'Form sospensioni (6 campi)', 'dev_units' => ['ui_form' => 6]],
                    ['title' => 'Lista sospensioni (13 elementi)', 'dev_units' => ['ui_lista' => 13]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'CalendarSuspension',
            'attributes' => [
                'id', 'start_date', 'end_date', 'reason', 'notes',
            ],
            'relationships' => [],
        ],
    ],
    
    // GRUPPO 3: CORSI E ISCRIZIONI
    
    [
        'title' => 'Tipi Corso - CRUD Completo',
        'description' => 'CRUD per gestione tipi di corso',
        'dev_units' => [
            'db_campi' => 12,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 12,
            'ui_lista' => 7,
            'ui_stampa' => 0,
            'total' => 40,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Tipi Corso',
                'description' => 'Creazione modello Eloquent CourseType',
                'dev_units' => ['db_campi' => 12, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella course_types', 'dev_units' => ['db_campi' => 12]],
                    ['title' => 'Relazioni CourseType → Course', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Tipi Corso',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'CourseTypeController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import tipi corso da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import tipi corso da ODS', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Tipi Corso',
                'description' => 'Form e lista per gestione tipi corso',
                'dev_units' => ['ui_form' => 12, 'ui_lista' => 7],
                'subtasks' => [
                    ['title' => 'Form tipi corso (12 campi)', 'dev_units' => ['ui_form' => 12]],
                    ['title' => 'Lista tipi corso (7 elementi)', 'dev_units' => ['ui_lista' => 7]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'CourseType',
            'attributes' => [
                'id', 'code', 'name', 'description', 'duration_minutes', 'max_students',
                'price_full', 'price_reduced', 'price_annual', 'price_monthly', 'active',
            ],
            'relationships' => [
                'hasMany' => ['Course'],
            ],
        ],
    ],
    
    [
        'title' => 'Corsi - CRUD Completo',
        'description' => 'CRUD per gestione corsi',
        'dev_units' => [
            'db_campi' => 18,
            'db_relazioni' => 3,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 15,
            'ui_lista' => 22,
            'ui_stampa' => 0,
            'total' => 66,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Corsi',
                'description' => 'Creazione modello Eloquent Course',
                'dev_units' => ['db_campi' => 18, 'db_relazioni' => 3],
                'subtasks' => [
                    ['title' => 'Migration tabella courses', 'dev_units' => ['db_campi' => 18]],
                    ['title' => 'Relazioni Course → CourseType', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Course → Teacher', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Course → Enrollment', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Corsi',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'CourseController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import corsi da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import corsi da ODS', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Corsi',
                'description' => 'Form e lista per gestione corsi',
                'dev_units' => ['ui_form' => 15, 'ui_lista' => 22],
                'subtasks' => [
                    ['title' => 'Form corsi (15 campi)', 'dev_units' => ['ui_form' => 15]],
                    ['title' => 'Lista corsi (22 elementi)', 'dev_units' => ['ui_lista' => 22]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Course',
            'attributes' => [
                'id', 'course_type_id', 'teacher_id', 'code', 'name', 'description',
                'start_date', 'end_date', 'day_of_week', 'time_start', 'time_end',
                'max_students', 'current_students', 'status', 'price_per_lesson',
                'lessons_per_week', 'weeks_per_year',
            ],
            'relationships' => [
                'belongsTo' => ['CourseType', 'Teacher'],
                'hasMany' => ['Enrollment', 'Lesson'],
            ],
        ],
    ],
    
    [
        'title' => 'Iscrizioni - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS iscrizioni in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 15,
            'db_relazioni' => 2,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 12,
            'ui_lista' => 18,
            'ui_stampa' => 0,
            'total' => 55,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Iscrizioni',
                'description' => 'Creazione modello Eloquent Enrollment',
                'dev_units' => ['db_campi' => 15, 'db_relazioni' => 2],
                'subtasks' => [
                    ['title' => 'Migration tabella enrollments', 'dev_units' => ['db_campi' => 15]],
                    ['title' => 'Relazioni Enrollment → Student', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Enrollment → Course', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Iscrizioni',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'EnrollmentController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import iscrizioni da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import iscrizioni da db 2025-26 gestionale.ods (colonne I, K, L)', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Iscrizioni',
                'description' => 'Form e lista per gestione iscrizioni',
                'dev_units' => ['ui_form' => 12, 'ui_lista' => 18],
                'subtasks' => [
                    ['title' => 'Form iscrizioni (12 campi)', 'dev_units' => ['ui_form' => 12]],
                    ['title' => 'Lista iscrizioni (18 elementi)', 'dev_units' => ['ui_lista' => 18]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Enrollment',
            'attributes' => [
                'id', 'student_id', 'course_id', 'enrollment_date', 'start_date', 'end_date',
                'price', 'discount', 'total', 'status', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Student', 'Course'],
            ],
        ],
    ],
    
    // GRUPPO 4: CONTRATTI E FATTURAZIONE
    
    [
        'title' => 'Contratti - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS contratti in DB normalizzato con CRUD base (solo visualizzazione/modifica dati esistenti)',
        'dev_units' => [
            'db_campi' => 28,
            'db_relazioni' => 3,
            'crud' => 7,
            'workflow' => 2,
            'ui_form' => 25,
            'ui_lista' => 21,
            'ui_stampa' => 0,
            'total' => 86,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Contratti',
                'description' => 'Creazione modello Eloquent Contract',
                'dev_units' => ['db_campi' => 28, 'db_relazioni' => 3],
                'subtasks' => [
                    ['title' => 'Migration tabella contracts', 'dev_units' => ['db_campi' => 28]],
                    ['title' => 'Relazioni Contract → Student', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Contract → Course', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Contract → Invoice', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Contratti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'ContractController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import contratti da ODS',
                'dev_units' => ['workflow' => 2],
                'subtasks' => [
                    ['title' => 'Import contratti da Db Contratti 25-26.ods', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Import colonna D (Contratto) da db 2025-26 gestionale.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Contratti',
                'description' => 'Form e lista per gestione contratti',
                'dev_units' => ['ui_form' => 25, 'ui_lista' => 21],
                'subtasks' => [
                    ['title' => 'Form contratti (25 campi)', 'dev_units' => ['ui_form' => 25]],
                    ['title' => 'Lista contratti (21 elementi)', 'dev_units' => ['ui_lista' => 21]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Contract',
            'attributes' => [
                'id', 'student_id', 'course_id', 'contract_number', 'contract_date', 'start_date',
                'end_date', 'contract_type', 'price', 'discount', 'total', 'status', 'notes',
                'token', 'sent_at', 'signed_at',
            ],
            'relationships' => [
                'belongsTo' => ['Student', 'Course'],
                'hasMany' => ['Invoice'],
            ],
        ],
    ],
    
    [
        'title' => 'Fatture - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS fatture in DB normalizzato con CRUD base (solo visualizzazione/modifica dati esistenti)',
        'dev_units' => [
            'db_campi' => 18,
            'db_relazioni' => 3,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 15,
            'ui_lista' => 20,
            'ui_stampa' => 0,
            'total' => 64,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Fatture',
                'description' => 'Creazione modello Eloquent Invoice',
                'dev_units' => ['db_campi' => 18, 'db_relazioni' => 3],
                'subtasks' => [
                    ['title' => 'Migration tabella invoices', 'dev_units' => ['db_campi' => 18]],
                    ['title' => 'Relazioni Invoice → Student', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Invoice → InvoiceItem', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Invoice → Payment', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Fatture',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'InvoiceController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import fatture da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import fatture da Db Contabile 2025-26.ods (fatture corsi)', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Fatture',
                'description' => 'Form e lista per gestione fatture',
                'dev_units' => ['ui_form' => 15, 'ui_lista' => 20],
                'subtasks' => [
                    ['title' => 'Form fatture (15 campi)', 'dev_units' => ['ui_form' => 15]],
                    ['title' => 'Lista fatture (20 elementi)', 'dev_units' => ['ui_lista' => 20]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Invoice',
            'attributes' => [
                'id', 'academic_year_id', 'student_id', 'invoice_number', 'invoice_date', 'due_date',
                'subtotal', 'discount_amount', 'tax_amount', 'total_amount', 'status',
                'payment_terms', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['AcademicYear', 'Student'],
                'hasMany' => ['InvoiceItem', 'Payment', 'PaymentPlan', 'CreditNote'],
            ],
        ],
    ],
    
    [
        'title' => 'Fatture Accessori - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS fatture accessori in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 15,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 12,
            'ui_lista' => 17,
            'ui_stampa' => 0,
            'total' => 53,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Fatture Accessori',
                'description' => 'Creazione modello Eloquent InvoiceItem',
                'dev_units' => ['db_campi' => 15, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella invoice_items', 'dev_units' => ['db_campi' => 15]],
                    ['title' => 'Relazioni InvoiceItem → Invoice', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Fatture Accessori',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'InvoiceItemController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import fatture accessori da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import fatture accessori da Db Contabile 2025-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Fatture Accessori',
                'description' => 'Form e lista per gestione fatture accessori',
                'dev_units' => ['ui_form' => 12, 'ui_lista' => 17],
                'subtasks' => [
                    ['title' => 'Form fatture accessori (12 campi)', 'dev_units' => ['ui_form' => 12]],
                    ['title' => 'Lista fatture accessori (17 elementi)', 'dev_units' => ['ui_lista' => 17]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'InvoiceItem',
            'attributes' => [
                'id', 'invoice_id', 'item_type', 'item_id', 'description', 'quantity',
                'unit_price', 'discount_percentage', 'total_price',
            ],
            'relationships' => [
                'belongsTo' => ['Invoice'],
            ],
        ],
    ],
    
    [
        'title' => 'Pagamenti - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS pagamenti in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 13,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 10,
            'ui_lista' => 16,
            'ui_stampa' => 0,
            'total' => 48,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Pagamenti',
                'description' => 'Creazione modello Eloquent Payment',
                'dev_units' => ['db_campi' => 13, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella payments', 'dev_units' => ['db_campi' => 13]],
                    ['title' => 'Relazioni Payment → Invoice', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Pagamenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'PaymentController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import pagamenti da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import pagamenti da Db Contabile 2025-26.ods e colonna B (Pagato) da db 2025-26 gestionale.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Pagamenti',
                'description' => 'Form e lista per gestione pagamenti',
                'dev_units' => ['ui_form' => 10, 'ui_lista' => 16],
                'subtasks' => [
                    ['title' => 'Form pagamenti (10 campi)', 'dev_units' => ['ui_form' => 10]],
                    ['title' => 'Lista pagamenti (16 elementi)', 'dev_units' => ['ui_lista' => 16]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Payment',
            'attributes' => [
                'id', 'invoice_id', 'payment_date', 'amount', 'payment_method', 'reference', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Invoice'],
            ],
        ],
    ],
    
    // GRUPPO 5: DIDATTICA E VALUTAZIONE
    
    [
        'title' => 'Livelli Studenti - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS livelli studenti (ABRSM) in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 11,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 8,
            'ui_lista' => 13,
            'ui_stampa' => 0,
            'total' => 41,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Livelli Studenti',
                'description' => 'Creazione modello Eloquent StudentLevel',
                'dev_units' => ['db_campi' => 11, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella student_levels', 'dev_units' => ['db_campi' => 11]],
                    ['title' => 'Relazioni StudentLevel → Student', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Livelli Studenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'StudentLevelController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import livelli da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import livelli da db 2025-26 gestionale.ods (colonne AI-AL)', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Livelli Studenti',
                'description' => 'Form e lista per gestione livelli',
                'dev_units' => ['ui_form' => 8, 'ui_lista' => 13],
                'subtasks' => [
                    ['title' => 'Form livelli (8 campi)', 'dev_units' => ['ui_form' => 8]],
                    ['title' => 'Lista livelli (13 elementi)', 'dev_units' => ['ui_lista' => 13]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'StudentLevel',
            'attributes' => [
                'id', 'student_id', 'level', 'level_strumento', 'level_teoria', 'musica_insieme',
                'assigned_date', 'updated_date', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Student'],
            ],
        ],
    ],
    
    [
        'title' => 'Esami - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS esami in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 15,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 12,
            'ui_lista' => 18,
            'ui_stampa' => 0,
            'total' => 54,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Esami',
                'description' => 'Creazione modello Eloquent Exam',
                'dev_units' => ['db_campi' => 15, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella exams', 'dev_units' => ['db_campi' => 15]],
                    ['title' => 'Relazioni Exam → Student', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Esami',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'ExamController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import esami da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import esami da Db Accessori 2025-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Esami',
                'description' => 'Form e lista per gestione esami',
                'dev_units' => ['ui_form' => 12, 'ui_lista' => 18],
                'subtasks' => [
                    ['title' => 'Form esami (12 campi)', 'dev_units' => ['ui_form' => 12]],
                    ['title' => 'Lista esami (18 elementi)', 'dev_units' => ['ui_lista' => 18]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Exam',
            'attributes' => [
                'id', 'student_id', 'exam_type', 'exam_date', 'level', 'result', 'notes', 'examiner',
            ],
            'relationships' => [
                'belongsTo' => ['Student'],
            ],
        ],
    ],
    
    // GRUPPO 6: ATTIVITÀ EXTRA
    
    [
        'title' => 'Orchestra/Coro - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS orchestra/coro in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 13,
            'db_relazioni' => 2,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 10,
            'ui_lista' => 14,
            'ui_stampa' => 0,
            'total' => 47,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Orchestra/Coro',
                'description' => 'Creazione modelli Eloquent ExtraActivity e ExtraActivityEnrollment',
                'dev_units' => ['db_campi' => 13, 'db_relazioni' => 2],
                'subtasks' => [
                    ['title' => 'Migration tabella extra_activities', 'dev_units' => ['db_campi' => 6]],
                    ['title' => 'Migration tabella extra_activity_enrollments', 'dev_units' => ['db_campi' => 7]],
                    ['title' => 'Relazioni ExtraActivityEnrollment → Student', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni ExtraActivityEnrollment → ExtraActivity', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Orchestra/Coro',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'ExtraActivityController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import orchestra/coro da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import orchestra/coro da db 2025-26 gestionale.ods (colonne AM-AS)', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Orchestra/Coro',
                'description' => 'Form e lista per gestione orchestra/coro',
                'dev_units' => ['ui_form' => 10, 'ui_lista' => 14],
                'subtasks' => [
                    ['title' => 'Form orchestra/coro (10 campi)', 'dev_units' => ['ui_form' => 10]],
                    ['title' => 'Lista orchestra/coro (14 elementi)', 'dev_units' => ['ui_lista' => 14]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'ExtraActivityEnrollment',
            'attributes' => [
                'id', 'student_id', 'extra_activity_id', 'conferma_orchestra', 'orchestra_1', 'pyo',
                'conferma_coro', 'coro', 'data_ultima_convocazione', 'notes', 'enrollment_date', 'status',
            ],
            'relationships' => [
                'belongsTo' => ['Student', 'ExtraActivity'],
            ],
        ],
    ],
    
    // GRUPPO 7: MAGAZZINO
    
    [
        'title' => 'Strumenti - CRUD Completo',
        'description' => 'CRUD per gestione strumenti (catalogo)',
        'dev_units' => [
            'db_campi' => 15,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 15,
            'ui_lista' => 11,
            'ui_stampa' => 0,
            'total' => 50,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Strumenti',
                'description' => 'Creazione modello Eloquent Instrument',
                'dev_units' => ['db_campi' => 15, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella instruments', 'dev_units' => ['db_campi' => 15]],
                    ['title' => 'Relazioni Instrument → InstrumentRental', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Strumenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'InstrumentController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import strumenti da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import strumenti da Db Accessori 2025-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Strumenti',
                'description' => 'Form e lista per gestione strumenti',
                'dev_units' => ['ui_form' => 15, 'ui_lista' => 11],
                'subtasks' => [
                    ['title' => 'Form strumenti (15 campi)', 'dev_units' => ['ui_form' => 15]],
                    ['title' => 'Lista strumenti (11 elementi)', 'dev_units' => ['ui_lista' => 11]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Instrument',
            'attributes' => [
                'id', 'type', 'brand', 'model', 'size', 'serial_number', 'condition',
                'supplier', 'purchase_date', 'purchase_price', 'current_value', 'status', 'notes',
            ],
            'relationships' => [
                'hasMany' => ['InstrumentRental'],
            ],
        ],
    ],
    
    [
        'title' => 'Noleggi Strumenti - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS noleggi strumenti in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 18,
            'db_relazioni' => 2,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 15,
            'ui_lista' => 16,
            'ui_stampa' => 0,
            'total' => 59,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Noleggi Strumenti',
                'description' => 'Creazione modello Eloquent InstrumentRental',
                'dev_units' => ['db_campi' => 18, 'db_relazioni' => 2],
                'subtasks' => [
                    ['title' => 'Migration tabella instrument_rentals', 'dev_units' => ['db_campi' => 18]],
                    ['title' => 'Relazioni InstrumentRental → Student', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni InstrumentRental → Instrument', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Noleggi Strumenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'InstrumentRentalController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import noleggi da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import noleggi da db 2025-26 gestionale.ods (colonne Y-AH) e Db Accessori 2025-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Noleggi Strumenti',
                'description' => 'Form e lista per gestione noleggi',
                'dev_units' => ['ui_form' => 15, 'ui_lista' => 16],
                'subtasks' => [
                    ['title' => 'Form noleggi (15 campi)', 'dev_units' => ['ui_form' => 15]],
                    ['title' => 'Lista noleggi (16 elementi)', 'dev_units' => ['ui_lista' => 16]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'InstrumentRental',
            'attributes' => [
                'id', 'student_id', 'instrument_id', 'supplier', 'rental_type', 'provenance',
                'type', 'brand', 'model', 'size', 'code', 'needs_replacement', 'notes',
                'start_date', 'end_date', 'cost',
            ],
            'relationships' => [
                'belongsTo' => ['Student', 'Instrument'],
            ],
        ],
    ],
    
    [
        'title' => 'Accessori - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS accessori in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 15,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 12,
            'ui_lista' => 17,
            'ui_stampa' => 0,
            'total' => 53,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Accessori',
                'description' => 'Creazione modello per accessori (tabella normalizzata)',
                'dev_units' => ['db_campi' => 15, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella accessories', 'dev_units' => ['db_campi' => 15]],
                    ['title' => 'Relazioni Accessory → Student', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Accessori',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'AccessoryController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import accessori da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import accessori 1-7 da Db Accessori 2025-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Accessori',
                'description' => 'Form e lista per gestione accessori',
                'dev_units' => ['ui_form' => 12, 'ui_lista' => 17],
                'subtasks' => [
                    ['title' => 'Form accessori (12 campi)', 'dev_units' => ['ui_form' => 12]],
                    ['title' => 'Lista accessori (17 elementi)', 'dev_units' => ['ui_lista' => 17]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Accessory',
            'attributes' => [
                'id', 'student_id', 'accessory_type', 'description', 'quantity', 'price', 'date',
            ],
            'relationships' => [
                'belongsTo' => ['Student'],
            ],
        ],
    ],
    
    [
        'title' => 'Libri - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS libri in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 15,
            'db_relazioni' => 2,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 12,
            'ui_lista' => 17,
            'ui_stampa' => 0,
            'total' => 54,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Libri',
                'description' => 'Creazione modelli Eloquent Book e BookDistribution',
                'dev_units' => ['db_campi' => 15, 'db_relazioni' => 2],
                'subtasks' => [
                    ['title' => 'Migration tabella books', 'dev_units' => ['db_campi' => 8]],
                    ['title' => 'Migration tabella book_distributions', 'dev_units' => ['db_campi' => 7]],
                    ['title' => 'Relazioni BookDistribution → Student', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni BookDistribution → Book', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Libri',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'BookController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import libri da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import libri 1-15 da Db Accessori 2025-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Libri',
                'description' => 'Form e lista per gestione libri',
                'dev_units' => ['ui_form' => 12, 'ui_lista' => 17],
                'subtasks' => [
                    ['title' => 'Form libri (12 campi)', 'dev_units' => ['ui_form' => 12]],
                    ['title' => 'Lista libri (17 elementi)', 'dev_units' => ['ui_lista' => 17]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'BookDistribution',
            'attributes' => [
                'id', 'student_id', 'book_id', 'quantity', 'distribution_date', 'price', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Student', 'Book'],
            ],
        ],
    ],
    
    [
        'title' => 'Contratti Docenti - CRUD Completo',
        'description' => 'Traduzione 1:1 colonne ODS contratti docenti in DB normalizzato con CRUD base',
        'dev_units' => [
            'db_campi' => 18,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 15,
            'ui_lista' => 17,
            'ui_stampa' => 0,
            'total' => 59,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Contratti Docenti',
                'description' => 'Creazione modello Eloquent TeacherContract',
                'dev_units' => ['db_campi' => 18, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella teacher_contracts', 'dev_units' => ['db_campi' => 18]],
                    ['title' => 'Relazioni TeacherContract → Teacher', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Contratti Docenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'TeacherContractController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import contratti docenti da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import contratti docenti da dati lavoratori 25-26.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Contratti Docenti',
                'description' => 'Form e lista per gestione contratti docenti',
                'dev_units' => ['ui_form' => 15, 'ui_lista' => 17],
                'subtasks' => [
                    ['title' => 'Form contratti docenti (15 campi)', 'dev_units' => ['ui_form' => 15]],
                    ['title' => 'Lista contratti docenti (17 elementi)', 'dev_units' => ['ui_lista' => 17]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'TeacherContract',
            'attributes' => [
                'id', 'teacher_id', 'contract_type', 'start_date', 'end_date', 'compensation', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Teacher'],
            ],
        ],
    ],
    
    [
        'title' => 'Conto Orario Docenti - CRUD Completo',
        'description' => 'CRUD per gestione conto orario docenti (solo visualizzazione dati)',
        'dev_units' => [
            'db_campi' => 15,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 12,
            'ui_lista' => 14,
            'ui_stampa' => 0,
            'total' => 50,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Conto Orario Docenti',
                'description' => 'Creazione modello Eloquent TeacherHour',
                'dev_units' => ['db_campi' => 15, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella teacher_hours', 'dev_units' => ['db_campi' => 15]],
                    ['title' => 'Relazioni TeacherHour → Teacher', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Conto Orario Docenti',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'TeacherHourController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Import ODS',
                'description' => 'Import conto orario da ODS',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Import conto orario da dati lavoratori 25-26.ods e colonna C (Conto orario) da db 2025-26 gestionale.ods', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Conto Orario Docenti',
                'description' => 'Form e lista per gestione conto orario',
                'dev_units' => ['ui_form' => 12, 'ui_lista' => 14],
                'subtasks' => [
                    ['title' => 'Form conto orario (12 campi)', 'dev_units' => ['ui_form' => 12]],
                    ['title' => 'Lista conto orario (14 elementi)', 'dev_units' => ['ui_lista' => 14]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'TeacherHour',
            'attributes' => [
                'id', 'teacher_id', 'period_start', 'period_end', 'hours_worked', 'hourly_rate',
                'bonus', 'forfait', 'total_amount', 'status', 'approved_at', 'paid_at', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Teacher'],
            ],
        ],
    ],
    
    // ============================================
    // FASE 2: EVOLUZIONI AMMINISTRATIVE
    // ============================================
    
    // GRUPPO 1: INFRASTRUTTURA AVANZATA
    
    [
        'title' => 'Gestione Utenze Avanzata',
        'description' => 'Sistema completo gestione utenti con ruoli e permessi granulari per Laravel',
        'dev_units' => [
            'db_campi' => 15,
            'db_relazioni' => 3,
            'crud' => 7,
            'workflow' => 2,
            'ui_form' => 15,
            'ui_lista' => 16,
            'ui_stampa' => 0,
            'total' => 60,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Utenze',
                'description' => 'Creazione modelli User, Role, Permission con relazioni',
                'dev_units' => ['db_campi' => 15, 'db_relazioni' => 3],
                'subtasks' => [
                    ['title' => 'Migration tabella users (estesa)', 'dev_units' => ['db_campi' => 8]],
                    ['title' => 'Migration tabella roles', 'dev_units' => ['db_campi' => 4]],
                    ['title' => 'Migration tabella permissions', 'dev_units' => ['db_campi' => 3]],
                    ['title' => 'Relazioni User → Role', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni Role → Permission', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni User → Permission (dirette)', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Utenze',
                'description' => 'Implementazione CRUD completo gestione utenti',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'UserController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Gestione Ruoli',
                'description' => 'Workflow assegnazione/rimozione ruoli e permessi',
                'dev_units' => ['workflow' => 2],
                'subtasks' => [
                    ['title' => 'Assegnazione ruoli agli utenti', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Gestione permessi granulari', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Utenze',
                'description' => 'Form e lista per gestione utenti con ruoli',
                'dev_units' => ['ui_form' => 15, 'ui_lista' => 16],
                'subtasks' => [
                    ['title' => 'Form utenti (15 campi)', 'dev_units' => ['ui_form' => 15]],
                    ['title' => 'Lista utenti con filtri ruoli (16 elementi)', 'dev_units' => ['ui_lista' => 16]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'User',
            'attributes' => [
                'id', 'name', 'email', 'password', 'role_id', 'active', 'last_login', 'notes',
            ],
            'relationships' => [
                'belongsTo' => ['Role'],
                'belongsToMany' => ['Permission'],
            ],
        ],
    ],
    
    [
        'title' => 'Controllo Accessi Granulare',
        'description' => 'Sistema permessi granulari per ogni funzionalità con middleware Laravel',
        'dev_units' => [
            'db_campi' => 10,
            'db_relazioni' => 2,
            'crud' => 0,
            'workflow' => 2,
            'ui_form' => 0,
            'ui_lista' => 0,
            'ui_stampa' => 0,
            'total' => 40,
        ],
        'tasks' => [
            [
                'title' => 'Middleware Permessi',
                'description' => 'Creazione middleware per controllo accessi granulari',
                'dev_units' => ['db_campi' => 10, 'db_relazioni' => 2, 'workflow' => 2],
                'subtasks' => [
                    ['title' => 'Migration tabella permission_routes', 'dev_units' => ['db_campi' => 10]],
                    ['title' => 'Relazioni Permission → Route', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Relazioni User → Permission (check)', 'dev_units' => ['db_relazioni' => 1]],
                    ['title' => 'Middleware CheckPermission', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Policies Laravel per modelli', 'dev_units' => ['workflow' => 1]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Permission',
            'attributes' => [
                'id', 'name', 'slug', 'description', 'resource', 'action',
            ],
            'relationships' => [
                'belongsToMany' => ['Role', 'User'],
            ],
        ],
    ],
    
    // GRUPPO 2: CONTRATTI EVOLUTIVI
    
    [
        'title' => 'Workflow Contratti Avanzato',
        'description' => 'Workflow invio/firma contratti con token e tracking completo',
        'dev_units' => [
            'db_campi' => 8,
            'db_relazioni' => 1,
            'crud' => 0,
            'workflow' => 4,
            'ui_form' => 0,
            'ui_lista' => 15,
            'ui_stampa' => 0,
            'total' => 50,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Workflow Contratti',
                'description' => 'Estensione modello Contract con tracking',
                'dev_units' => ['db_campi' => 8, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration aggiunta campi tracking (token, sent_at, signed_at, etc.)', 'dev_units' => ['db_campi' => 8]],
                    ['title' => 'Relazioni Contract → ContractHistory', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'Workflow Invio/Firma',
                'description' => 'Implementazione workflow completo',
                'dev_units' => ['workflow' => 4],
                'subtasks' => [
                    ['title' => 'Generazione token univoco', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Invio email con link firma', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Tracking stato contratto', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Firma digitale/elettronica', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Lista Tracking Contratti',
                'description' => 'Lista con stato workflow contratti',
                'dev_units' => ['ui_lista' => 15],
                'subtasks' => [
                    ['title' => 'Lista tracking contratti (15 elementi)', 'dev_units' => ['ui_lista' => 15]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Contract',
            'attributes' => [
                'id', 'token', 'status', 'sent_at', 'signed_at', 'expires_at', 'ip_address', 'user_agent',
            ],
            'relationships' => [
                'hasMany' => ['ContractHistory'],
            ],
        ],
    ],
    
    [
        'title' => 'Modelli Contratto',
        'description' => 'Gestione modelli contratto personalizzabili con variabili',
        'dev_units' => [
            'db_campi' => 10,
            'db_relazioni' => 1,
            'crud' => 7,
            'workflow' => 1,
            'ui_form' => 10,
            'ui_lista' => 11,
            'ui_stampa' => 0,
            'total' => 40,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Modelli Contratto',
                'description' => 'Creazione modello ContractTemplate',
                'dev_units' => ['db_campi' => 10, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella contract_templates', 'dev_units' => ['db_campi' => 10]],
                    ['title' => 'Relazioni ContractTemplate → Contract', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'CRUD Controller Modelli Contratto',
                'description' => 'Implementazione CRUD completo',
                'dev_units' => ['crud' => 7],
                'subtasks' => [
                    ['title' => 'ContractTemplateController CRUD completo', 'dev_units' => ['crud' => 7]],
                ],
            ],
            [
                'title' => 'Workflow Editor Template',
                'description' => 'Editor template con variabili dinamiche',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Editor template con variabili ({{student_name}}, etc.)', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Form e Lista Modelli Contratto',
                'description' => 'Form e lista per gestione modelli',
                'dev_units' => ['ui_form' => 10, 'ui_lista' => 11],
                'subtasks' => [
                    ['title' => 'Form modelli (10 campi)', 'dev_units' => ['ui_form' => 10]],
                    ['title' => 'Lista modelli (11 elementi)', 'dev_units' => ['ui_lista' => 11]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'ContractTemplate',
            'attributes' => [
                'id', 'name', 'description', 'content', 'variables', 'active', 'created_at', 'updated_at',
            ],
            'relationships' => [
                'hasMany' => ['Contract'],
            ],
        ],
    ],
    
    [
        'title' => 'Generazione PDF Contratti',
        'description' => 'Generazione PDF contratti da modelli con libreria PDF (DomPDF/Snappy)',
        'dev_units' => [
            'db_campi' => 0,
            'db_relazioni' => 0,
            'crud' => 0,
            'workflow' => 1,
            'ui_form' => 0,
            'ui_lista' => 0,
            'ui_stampa' => 49,
            'total' => 50,
        ],
        'tasks' => [
            [
                'title' => 'Workflow Generazione PDF',
                'description' => 'Implementazione generazione PDF',
                'dev_units' => ['workflow' => 1],
                'subtasks' => [
                    ['title' => 'Integrazione libreria PDF (DomPDF/Snappy)', 'dev_units' => ['workflow' => 1]],
                ],
            ],
            [
                'title' => 'UI Stampa PDF Contratti',
                'description' => 'Template PDF e download',
                'dev_units' => ['ui_stampa' => 49],
                'subtasks' => [
                    ['title' => 'Template Blade per PDF contratto', 'dev_units' => ['ui_stampa' => 25]],
                    ['title' => 'Sostituzione variabili nel template', 'dev_units' => ['ui_stampa' => 12]],
                    ['title' => 'Download PDF e preview', 'dev_units' => ['ui_stampa' => 12]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'Contract',
            'attributes' => [
                'id', 'pdf_path', 'pdf_generated_at',
            ],
            'relationships' => [],
        ],
    ],
    
    [
        'title' => 'Invio Email Contratti',
        'description' => 'Invio automatico email contratti con link firma e tracking',
        'dev_units' => [
            'db_campi' => 5,
            'db_relazioni' => 1,
            'crud' => 0,
            'workflow' => 2,
            'ui_form' => 0,
            'ui_lista' => 0,
            'ui_stampa' => 0,
            'total' => 30,
        ],
        'tasks' => [
            [
                'title' => 'Modello Database Email Tracking',
                'description' => 'Creazione modello per tracking email',
                'dev_units' => ['db_campi' => 5, 'db_relazioni' => 1],
                'subtasks' => [
                    ['title' => 'Migration tabella email_logs', 'dev_units' => ['db_campi' => 5]],
                    ['title' => 'Relazioni EmailLog → Contract', 'dev_units' => ['db_relazioni' => 1]],
                ],
            ],
            [
                'title' => 'Workflow Invio Email',
                'description' => 'Implementazione invio email automatico',
                'dev_units' => ['workflow' => 2],
                'subtasks' => [
                    ['title' => 'Template email con link firma', 'dev_units' => ['workflow' => 1]],
                    ['title' => 'Invio email via Laravel Mail/Queue', 'dev_units' => ['workflow' => 1]],
                ],
            ],
        ],
        'er_model' => [
            'entity' => 'EmailLog',
            'attributes' => [
                'id', 'contract_id', 'sent_at', 'opened_at', 'clicked_at',
            ],
            'relationships' => [
                'belongsTo' => ['Contract'],
            ],
        ],
    ],
    
    // Carica attività rimanenti Fase 2 e 3
    ...array_merge([], require __DIR__ . '/footility_activities_phase2_3_remaining.php'),
    
];
