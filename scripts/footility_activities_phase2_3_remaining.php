<?php
/**
 * Attività rimanenti Fase 2 e 3 da aggiungere
 * Questo file viene incluso nel file principale
 */

return [
    // FASE 2 - RIMANENTI (8 attività)
    
    // GRUPPO 3: FATTURAZIONE EVOLUTIVA
    [
        'title' => 'Fatturazione da Contratti',
        'description' => 'Generazione automatica fatture da contratti firmati con workflow Laravel',
        'dev_units' => ['db_campi' => 8, 'db_relazioni' => 2, 'crud' => 0, 'workflow' => 3, 'ui_form' => 0, 'ui_lista' => 15, 'ui_stampa' => 0, 'total' => 50],
        'tasks' => [
            ['title' => 'Workflow Generazione Fatture', 'description' => 'Implementazione generazione automatica', 'dev_units' => ['workflow' => 3, 'db_campi' => 8, 'db_relazioni' => 2], 'subtasks' => [
                ['title' => 'Migration campi collegamento contratto-fattura', 'dev_units' => ['db_campi' => 8]],
                ['title' => 'Relazioni Invoice → Contract', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni Invoice → Student', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Job Queue generazione fatture', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Calcolo importi da contratto', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Validazione dati fattura', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Lista Fatture Generate', 'description' => 'Lista fatture generate automaticamente', 'dev_units' => ['ui_lista' => 15], 'subtasks' => [
                ['title' => 'Lista fatture (15 elementi)', 'dev_units' => ['ui_lista' => 15]],
            ]],
        ],
        'er_model' => ['entity' => 'Invoice', 'attributes' => ['id', 'contract_id', 'auto_generated', 'generated_at'], 'relationships' => ['belongsTo' => ['Contract']]],
    ],
    
    [
        'title' => 'Piani Pagamento',
        'description' => 'Gestione rateizzazione flessibile fatture con piani personalizzati',
        'dev_units' => ['db_campi' => 10, 'db_relazioni' => 2, 'crud' => 0, 'workflow' => 2, 'ui_form' => 10, 'ui_lista' => 0, 'ui_stampa' => 0, 'total' => 40],
        'tasks' => [
            ['title' => 'Modello Database Piani Pagamento', 'description' => 'Creazione modello PaymentPlan', 'dev_units' => ['db_campi' => 10, 'db_relazioni' => 2], 'subtasks' => [
                ['title' => 'Migration tabella payment_plans', 'dev_units' => ['db_campi' => 10]],
                ['title' => 'Relazioni PaymentPlan → Invoice', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni PaymentPlan → Payment', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'Workflow Gestione Rate', 'description' => 'Implementazione gestione rate', 'dev_units' => ['workflow' => 2], 'subtasks' => [
                ['title' => 'Calcolo rate automatico', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Gestione scadenze rate', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Form Piani Pagamento', 'description' => 'Form per creazione piani', 'dev_units' => ['ui_form' => 10], 'subtasks' => [
                ['title' => 'Form piani (10 campi)', 'dev_units' => ['ui_form' => 10]],
            ]],
        ],
        'er_model' => ['entity' => 'PaymentPlan', 'attributes' => ['id', 'invoice_id', 'installments', 'amount_per_installment', 'start_date'], 'relationships' => ['belongsTo' => ['Invoice'], 'hasMany' => ['Payment']]],
    ],
    
    [
        'title' => 'Generazione PDF Fatture',
        'description' => 'Generazione PDF fatture con template personalizzabili',
        'dev_units' => ['db_campi' => 0, 'db_relazioni' => 0, 'crud' => 0, 'workflow' => 1, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 29, 'total' => 30],
        'tasks' => [
            ['title' => 'Workflow Generazione PDF Fatture', 'description' => 'Implementazione generazione PDF', 'dev_units' => ['workflow' => 1, 'ui_stampa' => 29], 'subtasks' => [
                ['title' => 'Template Blade PDF fattura', 'dev_units' => ['ui_stampa' => 20]],
                ['title' => 'Download PDF fattura', 'dev_units' => ['ui_stampa' => 9]],
            ]],
        ],
        'er_model' => ['entity' => 'Invoice', 'attributes' => ['id', 'pdf_path'], 'relationships' => []],
    ],
    
    [
        'title' => 'Automazioni Solleciti',
        'description' => 'Sistema automatico solleciti pagamenti scaduti con email/SMS',
        'dev_units' => ['db_campi' => 8, 'db_relazioni' => 2, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 10, 'ui_stampa' => 0, 'total' => 40],
        'tasks' => [
            ['title' => 'Modello Database Solleciti', 'description' => 'Creazione modello Reminder', 'dev_units' => ['db_campi' => 8, 'db_relazioni' => 2], 'subtasks' => [
                ['title' => 'Migration tabella reminders', 'dev_units' => ['db_campi' => 8]],
                ['title' => 'Relazioni Reminder → Invoice', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni Reminder → Student', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'Workflow Automazioni', 'description' => 'Implementazione automazioni', 'dev_units' => ['workflow' => 2], 'subtasks' => [
                ['title' => 'Job schedulato controllo scadenze', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Invio solleciti automatici', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Lista Solleciti', 'description' => 'Lista solleciti inviati', 'dev_units' => ['ui_lista' => 10], 'subtasks' => [
                ['title' => 'Lista solleciti (10 elementi)', 'dev_units' => ['ui_lista' => 10]],
            ]],
        ],
        'er_model' => ['entity' => 'Reminder', 'attributes' => ['id', 'invoice_id', 'student_id', 'sent_at', 'reminder_type'], 'relationships' => ['belongsTo' => ['Invoice', 'Student']]],
    ],
    
    [
        'title' => 'Recupero Crediti',
        'description' => 'Gestione avanzata recupero crediti con workflow',
        'dev_units' => ['db_campi' => 8, 'db_relazioni' => 1, 'crud' => 7, 'workflow' => 1, 'ui_form' => 8, 'ui_lista' => 5, 'ui_stampa' => 0, 'total' => 30],
        'tasks' => [
            ['title' => 'Modello Database Recupero Crediti', 'description' => 'Creazione modello CreditRecovery', 'dev_units' => ['db_campi' => 8, 'db_relazioni' => 1], 'subtasks' => [
                ['title' => 'Migration tabella credit_recoveries', 'dev_units' => ['db_campi' => 8]],
                ['title' => 'Relazioni CreditRecovery → Invoice', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'CRUD Controller Recupero Crediti', 'description' => 'Implementazione CRUD', 'dev_units' => ['crud' => 7], 'subtasks' => [
                ['title' => 'CreditRecoveryController CRUD', 'dev_units' => ['crud' => 7]],
            ]],
            ['title' => 'Workflow Recupero', 'description' => 'Workflow recupero crediti', 'dev_units' => ['workflow' => 1], 'subtasks' => [
                ['title' => 'Workflow recupero crediti', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Form e Lista', 'description' => 'Form e lista recupero crediti', 'dev_units' => ['ui_form' => 8, 'ui_lista' => 5], 'subtasks' => [
                ['title' => 'Form recupero (8 campi)', 'dev_units' => ['ui_form' => 8]],
                ['title' => 'Lista recuperi (5 elementi)', 'dev_units' => ['ui_lista' => 5]],
            ]],
        ],
        'er_model' => ['entity' => 'CreditRecovery', 'attributes' => ['id', 'invoice_id', 'status', 'recovery_date'], 'relationships' => ['belongsTo' => ['Invoice']]],
    ],
    
    // GRUPPO 4: INTEGRAZIONI ESTERNE
    [
        'title' => 'Integrazione SDI Fatturazione Elettronica',
        'description' => 'Integrazione Sistema di Interscambio per fatturazione elettronica (XML, invio, ricezione)',
        'dev_units' => ['db_campi' => 10, 'db_relazioni' => 1, 'crud' => 0, 'workflow' => 3, 'ui_form' => 0, 'ui_lista' => 15, 'ui_stampa' => 0, 'total' => 80],
        'tasks' => [
            ['title' => 'Modello Database SDI', 'description' => 'Creazione modello per tracking SDI', 'dev_units' => ['db_campi' => 10, 'db_relazioni' => 1], 'subtasks' => [
                ['title' => 'Migration tabella sdi_submissions', 'dev_units' => ['db_campi' => 10]],
                ['title' => 'Relazioni SdiSubmission → Invoice', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'Workflow Integrazione SDI', 'description' => 'Implementazione integrazione', 'dev_units' => ['workflow' => 3], 'subtasks' => [
                ['title' => 'Generazione XML fattura elettronica', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Invio a SDI via API', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Ricezione notifiche SDI', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Lista Invii SDI', 'description' => 'Lista invii e stato', 'dev_units' => ['ui_lista' => 15], 'subtasks' => [
                ['title' => 'Lista invii SDI (15 elementi)', 'dev_units' => ['ui_lista' => 15]],
            ]],
        ],
        'er_model' => ['entity' => 'SdiSubmission', 'attributes' => ['id', 'invoice_id', 'xml_path', 'sent_at', 'status', 'response'], 'relationships' => ['belongsTo' => ['Invoice']]],
    ],
    
    [
        'title' => 'Integrazione Cassetto Fiscale',
        'description' => 'Integrazione cassetto fiscale per categorizzazione spese e entrate',
        'dev_units' => ['db_campi' => 15, 'db_relazioni' => 2, 'crud' => 0, 'workflow' => 3, 'ui_form' => 0, 'ui_lista' => 20, 'ui_stampa' => 0, 'total' => 100],
        'tasks' => [
            ['title' => 'Modello Database Cassetto Fiscale', 'description' => 'Creazione modelli per categorizzazione', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 2], 'subtasks' => [
                ['title' => 'Migration tabella fiscal_categories', 'dev_units' => ['db_campi' => 8]],
                ['title' => 'Migration tabella fiscal_transactions', 'dev_units' => ['db_campi' => 7]],
                ['title' => 'Relazioni FiscalTransaction → Invoice', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni FiscalTransaction → FiscalCategory', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'Workflow Integrazione', 'description' => 'Implementazione integrazione', 'dev_units' => ['workflow' => 3], 'subtasks' => [
                ['title' => 'Sincronizzazione categorie fiscali', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Categorizzazione automatica transazioni', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Export dati per cassetto fiscale', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Lista Transazioni Fiscali', 'description' => 'Lista transazioni categorizzate', 'dev_units' => ['ui_lista' => 20], 'subtasks' => [
                ['title' => 'Lista transazioni (20 elementi)', 'dev_units' => ['ui_lista' => 20]],
            ]],
        ],
        'er_model' => ['entity' => 'FiscalTransaction', 'attributes' => ['id', 'invoice_id', 'category_id', 'amount', 'date'], 'relationships' => ['belongsTo' => ['Invoice', 'FiscalCategory']]],
    ],
    
    [
        'title' => 'Fornitori',
        'description' => 'CRUD completo gestione fornitori per integrazione contabilità',
        'dev_units' => ['db_campi' => 15, 'db_relazioni' => 2, 'crud' => 7, 'workflow' => 0, 'ui_form' => 15, 'ui_lista' => 11, 'ui_stampa' => 0, 'total' => 50],
        'tasks' => [
            ['title' => 'Modello Database Fornitori', 'description' => 'Creazione modello Supplier', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 2], 'subtasks' => [
                ['title' => 'Migration tabella suppliers', 'dev_units' => ['db_campi' => 15]],
                ['title' => 'Relazioni Supplier → Invoice (fornitore)', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni Supplier → Payment', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'CRUD Controller Fornitori', 'description' => 'Implementazione CRUD', 'dev_units' => ['crud' => 7], 'subtasks' => [
                ['title' => 'SupplierController CRUD', 'dev_units' => ['crud' => 7]],
            ]],
            ['title' => 'UI Form e Lista Fornitori', 'description' => 'Form e lista fornitori', 'dev_units' => ['ui_form' => 15, 'ui_lista' => 11], 'subtasks' => [
                ['title' => 'Form fornitori (15 campi)', 'dev_units' => ['ui_form' => 15]],
                ['title' => 'Lista fornitori (11 elementi)', 'dev_units' => ['ui_lista' => 11]],
            ]],
        ],
        'er_model' => ['entity' => 'Supplier', 'attributes' => ['id', 'name', 'vat_number', 'address', 'email', 'phone'], 'relationships' => ['hasMany' => ['Invoice', 'Payment']]],
    ],
    
    // FASE 3 - TUTTE LE 20 ATTIVITÀ (in formato compatto)
    
    // GRUPPO 1: PRIMO CONTATTO E PREISCRIZIONI
    [
        'title' => 'Primo Contatto Pubblico',
        'description' => 'Form pubblico per primo contatto con generazione link precompilati',
        'dev_units' => ['db_campi' => 15, 'db_relazioni' => 2, 'crud' => 7, 'workflow' => 1, 'ui_form' => 15, 'ui_lista' => 12, 'ui_stampa' => 0, 'total' => 60],
        'tasks' => [
            ['title' => 'Modello Database Prospect', 'description' => 'Creazione modello Prospect', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 2], 'subtasks' => [
                ['title' => 'Migration tabella prospects', 'dev_units' => ['db_campi' => 15]],
                ['title' => 'Relazioni Prospect → Course (interesse)', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni Prospect → Student (conversione)', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'CRUD Controller Prospect', 'description' => 'CRUD pubblico e admin', 'dev_units' => ['crud' => 7], 'subtasks' => [
                ['title' => 'ProspectController CRUD', 'dev_units' => ['crud' => 7]],
            ]],
            ['title' => 'Workflow Link Precompilati', 'description' => 'Generazione link precompilati', 'dev_units' => ['workflow' => 1], 'subtasks' => [
                ['title' => 'Generazione token link precompilato', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Form Pubblico e Lista Admin', 'description' => 'Form pubblico e lista admin', 'dev_units' => ['ui_form' => 15, 'ui_lista' => 12], 'subtasks' => [
                ['title' => 'Form pubblico primo contatto (15 campi)', 'dev_units' => ['ui_form' => 15]],
                ['title' => 'Lista prospect admin (12 elementi)', 'dev_units' => ['ui_lista' => 12]],
            ]],
        ],
        'er_model' => ['entity' => 'Prospect', 'attributes' => ['id', 'first_name', 'last_name', 'email', 'phone', 'interested_course', 'token'], 'relationships' => ['belongsTo' => ['Course'], 'hasOne' => ['Student']]],
    ],
    
    [
        'title' => 'Conversione Prospect → Studente',
        'description' => 'Workflow conversione primo contatto in studente con validazione dati',
        'dev_units' => ['db_campi' => 0, 'db_relazioni' => 1, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 0, 'total' => 30],
        'tasks' => [
            ['title' => 'Workflow Conversione', 'description' => 'Implementazione conversione', 'dev_units' => ['workflow' => 2, 'db_relazioni' => 1], 'subtasks' => [
                ['title' => 'Relazioni Prospect → Student', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Validazione e conversione dati', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Notifica conversione completata', 'dev_units' => ['workflow' => 1]],
            ]],
        ],
        'er_model' => ['entity' => 'Prospect', 'attributes' => ['id', 'converted_at', 'student_id'], 'relationships' => ['hasOne' => ['Student']]],
    ],
    
    [
        'title' => 'Preiscrizioni',
        'description' => 'Sistema preiscrizioni per anno successivo con workflow approvazione',
        'dev_units' => ['db_campi' => 12, 'db_relazioni' => 2, 'crud' => 7, 'workflow' => 1, 'ui_form' => 12, 'ui_lista' => 17, 'ui_stampa' => 0, 'total' => 50],
        'tasks' => [
            ['title' => 'Modello Database Preiscrizioni', 'description' => 'Creazione modello PreEnrollment', 'dev_units' => ['db_campi' => 12, 'db_relazioni' => 2], 'subtasks' => [
                ['title' => 'Migration tabella pre_enrollments', 'dev_units' => ['db_campi' => 12]],
                ['title' => 'Relazioni PreEnrollment → Student', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni PreEnrollment → Course', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'CRUD Controller Preiscrizioni', 'description' => 'CRUD completo', 'dev_units' => ['crud' => 7], 'subtasks' => [
                ['title' => 'PreEnrollmentController CRUD', 'dev_units' => ['crud' => 7]],
            ]],
            ['title' => 'Workflow Approvazione', 'description' => 'Workflow approvazione preiscrizioni', 'dev_units' => ['workflow' => 1], 'subtasks' => [
                ['title' => 'Workflow approvazione', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Form e Lista Preiscrizioni', 'description' => 'Form e lista preiscrizioni', 'dev_units' => ['ui_form' => 12, 'ui_lista' => 17], 'subtasks' => [
                ['title' => 'Form preiscrizioni (12 campi)', 'dev_units' => ['ui_form' => 12]],
                ['title' => 'Lista preiscrizioni (17 elementi)', 'dev_units' => ['ui_lista' => 17]],
            ]],
        ],
        'er_model' => ['entity' => 'PreEnrollment', 'attributes' => ['id', 'student_id', 'course_id', 'academic_year_id', 'status', 'approved_at'], 'relationships' => ['belongsTo' => ['Student', 'Course']]],
    ],
    
    // GRUPPO 2: PROPOSTA ORARIA
    [
        'title' => 'Proposta Oraria Avanzata',
        'description' => 'Sistema composizione orari con algoritmo matching disponibilità studenti/docenti',
        'dev_units' => ['db_campi' => 15, 'db_relazioni' => 3, 'crud' => 7, 'workflow' => 3, 'ui_form' => 15, 'ui_lista' => 20, 'ui_stampa' => 0, 'total' => 80],
        'tasks' => [
            ['title' => 'Modello Database Proposte Orarie', 'description' => 'Creazione modello ScheduleProposal', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 3], 'subtasks' => [
                ['title' => 'Migration tabella schedule_proposals', 'dev_units' => ['db_campi' => 15]],
                ['title' => 'Relazioni ScheduleProposal → Student', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni ScheduleProposal → Course', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Relazioni ScheduleProposal → Teacher', 'dev_units' => ['db_relazioni' => 1]],
            ]],
            ['title' => 'CRUD Controller Proposte', 'description' => 'CRUD completo', 'dev_units' => ['crud' => 7], 'subtasks' => [
                ['title' => 'ScheduleProposalController CRUD', 'dev_units' => ['crud' => 7]],
            ]],
            ['title' => 'Workflow Algoritmo Matching', 'description' => 'Algoritmo matching disponibilità', 'dev_units' => ['workflow' => 3], 'subtasks' => [
                ['title' => 'Algoritmo matching disponibilità', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Generazione proposte multiple', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Validazione conflitti orari', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Form e Lista Proposte', 'description' => 'Form e lista proposte', 'dev_units' => ['ui_form' => 15, 'ui_lista' => 20], 'subtasks' => [
                ['title' => 'Form proposte (15 campi)', 'dev_units' => ['ui_form' => 15]],
                ['title' => 'Lista proposte (20 elementi)', 'dev_units' => ['ui_lista' => 20]],
            ]],
        ],
        'er_model' => ['entity' => 'ScheduleProposal', 'attributes' => ['id', 'student_id', 'course_id', 'teacher_id', 'proposed_time', 'status'], 'relationships' => ['belongsTo' => ['Student', 'Course', 'Teacher']]],
    ],
    
    [
        'title' => 'Accettazione/Rifiuto Proposte',
        'description' => 'Workflow accettazione/rifiuto proposte orarie con notifiche',
        'dev_units' => ['db_campi' => 5, 'db_relazioni' => 1, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 10, 'ui_stampa' => 0, 'total' => 30],
        'tasks' => [
            ['title' => 'Workflow Accettazione/Rifiuto', 'description' => 'Implementazione workflow', 'dev_units' => ['workflow' => 2, 'db_campi' => 5, 'db_relazioni' => 1], 'subtasks' => [
                ['title' => 'Migration campi status proposta', 'dev_units' => ['db_campi' => 5]],
                ['title' => 'Relazioni ScheduleProposal → Enrollment', 'dev_units' => ['db_relazioni' => 1]],
                ['title' => 'Workflow accettazione proposta', 'dev_units' => ['workflow' => 1]],
                ['title' => 'Notifiche accettazione/rifiuto', 'dev_units' => ['workflow' => 1]],
            ]],
            ['title' => 'UI Lista Proposte da Gestire', 'description' => 'Lista proposte in attesa', 'dev_units' => ['ui_lista' => 10], 'subtasks' => [
                ['title' => 'Lista proposte (10 elementi)', 'dev_units' => ['ui_lista' => 10]],
            ]],
        ],
        'er_model' => ['entity' => 'ScheduleProposal', 'attributes' => ['id', 'status', 'accepted_at', 'rejected_at', 'notes'], 'relationships' => ['hasOne' => ['Enrollment']]],
    ],
    
    // GRUPPO 3: REGISTRO EVOLUTO (6 attività)
    ['title' => 'Registro Elettronico Avanzato', 'description' => 'Registro elettronico completo con accesso docenti', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 5, 'crud' => 7, 'workflow' => 2, 'ui_form' => 15, 'ui_lista' => 24, 'ui_stampa' => 0, 'total' => 70], 'tasks' => [
        ['title' => 'Modello Database Registro', 'description' => 'Creazione modelli per registro', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 5], 'subtasks' => [
            ['title' => 'Migration tabella lesson_records', 'dev_units' => ['db_campi' => 15]],
            ['title' => 'Relazioni multiple (Student, Course, Teacher, etc.)', 'dev_units' => ['db_relazioni' => 5]],
        ]],
        ['title' => 'CRUD Controller Registro', 'description' => 'CRUD completo', 'dev_units' => ['crud' => 7], 'subtasks' => [['title' => 'LessonRecordController CRUD', 'dev_units' => ['crud' => 7]]]],
        ['title' => 'Workflow Accesso Docenti', 'description' => 'Workflow accesso docenti', 'dev_units' => ['workflow' => 2], 'subtasks' => [
            ['title' => 'Autenticazione docenti', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Permessi accesso registro', 'dev_units' => ['workflow' => 1]],
        ]],
        ['title' => 'UI Form e Lista Registro', 'description' => 'Form e lista registro', 'dev_units' => ['ui_form' => 15, 'ui_lista' => 24], 'subtasks' => [
            ['title' => 'Form registro (15 campi)', 'dev_units' => ['ui_form' => 15]],
            ['title' => 'Lista registro (24 elementi)', 'dev_units' => ['ui_lista' => 24]],
        ]],
    ], 'er_model' => ['entity' => 'LessonRecord', 'attributes' => ['id', 'lesson_id', 'student_id', 'teacher_id', 'content', 'date'], 'relationships' => ['belongsTo' => ['Lesson', 'Student', 'Teacher']]]],
    
    ['title' => 'Presenze Avanzate', 'description' => 'Gestione presenze con recuperi e supplenze', 'dev_units' => ['db_campi' => 12, 'db_relazioni' => 3, 'crud' => 7, 'workflow' => 2, 'ui_form' => 12, 'ui_lista' => 18, 'ui_stampa' => 0, 'total' => 60], 'tasks' => [
        ['title' => 'Modello Database Presenze', 'description' => 'Creazione modello Attendance', 'dev_units' => ['db_campi' => 12, 'db_relazioni' => 3], 'subtasks' => [
            ['title' => 'Migration tabella attendances', 'dev_units' => ['db_campi' => 12]],
            ['title' => 'Relazioni Attendance → Student, Lesson, Teacher', 'dev_units' => ['db_relazioni' => 3]],
        ]],
        ['title' => 'CRUD Controller Presenze', 'description' => 'CRUD completo', 'dev_units' => ['crud' => 7], 'subtasks' => [['title' => 'AttendanceController CRUD', 'dev_units' => ['crud' => 7]]]],
        ['title' => 'Workflow Recuperi/Supplenze', 'description' => 'Workflow recuperi e supplenze', 'dev_units' => ['workflow' => 2], 'subtasks' => [
            ['title' => 'Gestione recuperi lezioni', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Gestione supplenze', 'dev_units' => ['workflow' => 1]],
        ]],
        ['title' => 'UI Form e Lista Presenze', 'description' => 'Form e lista presenze', 'dev_units' => ['ui_form' => 12, 'ui_lista' => 18], 'subtasks' => [
            ['title' => 'Form presenze (12 campi)', 'dev_units' => ['ui_form' => 12]],
            ['title' => 'Lista presenze (18 elementi)', 'dev_units' => ['ui_lista' => 18]],
        ]],
    ], 'er_model' => ['entity' => 'Attendance', 'attributes' => ['id', 'student_id', 'lesson_id', 'teacher_id', 'status', 'date'], 'relationships' => ['belongsTo' => ['Student', 'Lesson', 'Teacher']]]],
    
    ['title' => 'Recuperi Lezioni', 'description' => 'Gestione avanzata recuperi lezioni perse', 'dev_units' => ['db_campi' => 12, 'db_relazioni' => 3, 'crud' => 0, 'workflow' => 2, 'ui_form' => 12, 'ui_lista' => 19, 'ui_stampa' => 0, 'total' => 50], 'tasks' => [
        ['title' => 'Modello Database Recuperi', 'description' => 'Creazione modello LessonRecovery', 'dev_units' => ['db_campi' => 12, 'db_relazioni' => 3], 'subtasks' => [
            ['title' => 'Migration tabella lesson_recoveries', 'dev_units' => ['db_campi' => 12]],
            ['title' => 'Relazioni LessonRecovery → Student, Lesson, Teacher', 'dev_units' => ['db_relazioni' => 3]],
        ]],
        ['title' => 'Workflow Gestione Recuperi', 'description' => 'Workflow recuperi', 'dev_units' => ['workflow' => 2], 'subtasks' => [
            ['title' => 'Pianificazione recuperi', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Validazione recuperi', 'dev_units' => ['workflow' => 1]],
        ]],
        ['title' => 'UI Form e Lista Recuperi', 'description' => 'Form e lista recuperi', 'dev_units' => ['ui_form' => 12, 'ui_lista' => 19], 'subtasks' => [
            ['title' => 'Form recuperi (12 campi)', 'dev_units' => ['ui_form' => 12]],
            ['title' => 'Lista recuperi (19 elementi)', 'dev_units' => ['ui_lista' => 19]],
        ]],
    ], 'er_model' => ['entity' => 'LessonRecovery', 'attributes' => ['id', 'student_id', 'original_lesson_id', 'recovery_lesson_id', 'status'], 'relationships' => ['belongsTo' => ['Student', 'Lesson']]]],
    
    ['title' => 'Gestione Supplenti', 'description' => 'Sistema gestione supplenti con account provvisori', 'dev_units' => ['db_campi' => 12, 'db_relazioni' => 3, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 15, 'ui_stampa' => 0, 'total' => 50], 'tasks' => [
        ['title' => 'Modello Database Supplenti', 'description' => 'Creazione modello Substitute', 'dev_units' => ['db_campi' => 12, 'db_relazioni' => 3], 'subtasks' => [
            ['title' => 'Migration tabella substitutes', 'dev_units' => ['db_campi' => 12]],
            ['title' => 'Relazioni Substitute → Teacher, Lesson, Course', 'dev_units' => ['db_relazioni' => 3]],
        ]],
        ['title' => 'Workflow Account Provvisori', 'description' => 'Workflow account supplenti', 'dev_units' => ['workflow' => 2], 'subtasks' => [
            ['title' => 'Creazione account provvisori', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Gestione permessi supplenti', 'dev_units' => ['workflow' => 1]],
        ]],
        ['title' => 'UI Lista Supplenti', 'description' => 'Lista supplenti', 'dev_units' => ['ui_lista' => 15], 'subtasks' => [
            ['title' => 'Lista supplenti (15 elementi)', 'dev_units' => ['ui_lista' => 15]],
        ]],
    ], 'er_model' => ['entity' => 'Substitute', 'attributes' => ['id', 'teacher_id', 'lesson_id', 'course_id', 'start_date', 'end_date'], 'relationships' => ['belongsTo' => ['Teacher', 'Lesson', 'Course']]]],
    
    ['title' => 'Conto Orario Docenti Avanzato', 'description' => 'Calcolo automatico conto orario da presenze con bonus/forfait', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 3, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 20, 'ui_stampa' => 15, 'total' => 70], 'tasks' => [
        ['title' => 'Modello Database Conto Orario Avanzato', 'description' => 'Estensione modello TeacherHour', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 3], 'subtasks' => [
            ['title' => 'Migration campi avanzati conto orario', 'dev_units' => ['db_campi' => 15]],
            ['title' => 'Relazioni TeacherHour → Attendance, Lesson, Teacher', 'dev_units' => ['db_relazioni' => 3]],
        ]],
        ['title' => 'Workflow Calcolo Automatico', 'description' => 'Calcolo automatico da presenze', 'dev_units' => ['workflow' => 2], 'subtasks' => [
            ['title' => 'Calcolo ore da presenze', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Calcolo bonus/forfait', 'dev_units' => ['workflow' => 1]],
        ]],
        ['title' => 'UI Lista e Stampa Conto Orario', 'description' => 'Lista e stampa conto orario', 'dev_units' => ['ui_lista' => 20, 'ui_stampa' => 15], 'subtasks' => [
            ['title' => 'Lista conto orario (20 elementi)', 'dev_units' => ['ui_lista' => 20]],
            ['title' => 'Stampa conto orario PDF', 'dev_units' => ['ui_stampa' => 15]],
        ]],
    ], 'er_model' => ['entity' => 'TeacherHour', 'attributes' => ['id', 'teacher_id', 'hours_worked', 'bonus', 'forfait', 'total'], 'relationships' => ['belongsTo' => ['Teacher'], 'hasMany' => ['Attendance']]]],
    
    ['title' => 'Configurazione Compensi Docenti', 'description' => 'CRUD configurazione compensi personalizzati docenti', 'dev_units' => ['db_campi' => 10, 'db_relazioni' => 1, 'crud' => 7, 'workflow' => 0, 'ui_form' => 10, 'ui_lista' => 12, 'ui_stampa' => 0, 'total' => 40], 'tasks' => [
        ['title' => 'Modello Database Compensi', 'description' => 'Creazione modello TeacherCompensation', 'dev_units' => ['db_campi' => 10, 'db_relazioni' => 1], 'subtasks' => [
            ['title' => 'Migration tabella teacher_compensations', 'dev_units' => ['db_campi' => 10]],
            ['title' => 'Relazioni TeacherCompensation → Teacher', 'dev_units' => ['db_relazioni' => 1]],
        ]],
        ['title' => 'CRUD Controller Compensi', 'description' => 'CRUD completo', 'dev_units' => ['crud' => 7], 'subtasks' => [['title' => 'TeacherCompensationController CRUD', 'dev_units' => ['crud' => 7]]]],
        ['title' => 'UI Form e Lista Compensi', 'description' => 'Form e lista compensi', 'dev_units' => ['ui_form' => 10, 'ui_lista' => 12], 'subtasks' => [
            ['title' => 'Form compensi (10 campi)', 'dev_units' => ['ui_form' => 10]],
            ['title' => 'Lista compensi (12 elementi)', 'dev_units' => ['ui_lista' => 12]],
        ]],
    ], 'er_model' => ['entity' => 'TeacherCompensation', 'attributes' => ['id', 'teacher_id', 'hourly_rate', 'bonus_rate', 'forfait_amount'], 'relationships' => ['belongsTo' => ['Teacher']]]],
    
    // GRUPPO 4: COMUNICAZIONI E ATTIVITÀ (5 attività)
    ['title' => 'Comunicazioni Evolute', 'description' => 'Sistema comunicazioni multi-canale (email, SMS, WhatsApp)', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 3, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 0, 'total' => 80], 'tasks' => [
        ['title' => 'Modello Database Comunicazioni', 'description' => 'Creazione modello Communication', 'dev_units' => ['db_campi' => 15, 'db_relazioni' => 3], 'subtasks' => [
            ['title' => 'Migration tabella communications', 'dev_units' => ['db_campi' => 15]],
            ['title' => 'Relazioni Communication → Student, Guardian, Teacher', 'dev_units' => ['db_relazioni' => 3]],
        ]],
        ['title' => 'Workflow Multi-Canale', 'description' => 'Workflow invio multi-canale', 'dev_units' => ['workflow' => 2], 'subtasks' => [
            ['title' => 'Integrazione email/SMS/WhatsApp', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Gestione code invio', 'dev_units' => ['workflow' => 1]],
        ]],
    ], 'er_model' => ['entity' => 'Communication', 'attributes' => ['id', 'student_id', 'guardian_id', 'teacher_id', 'channel', 'content', 'sent_at'], 'relationships' => ['belongsTo' => ['Student', 'Guardian', 'Teacher']]]],
    
    ['title' => 'Template Comunicazioni', 'description' => 'Editor template comunicazioni personalizzabili', 'dev_units' => ['db_campi' => 10, 'db_relazioni' => 1, 'crud' => 7, 'workflow' => 1, 'ui_form' => 10, 'ui_lista' => 14, 'ui_stampa' => 0, 'total' => 50], 'tasks' => [
        ['title' => 'Modello Database Template', 'description' => 'Creazione modello CommunicationTemplate', 'dev_units' => ['db_campi' => 10, 'db_relazioni' => 1], 'subtasks' => [
            ['title' => 'Migration tabella communication_templates', 'dev_units' => ['db_campi' => 10]],
            ['title' => 'Relazioni CommunicationTemplate → Communication', 'dev_units' => ['db_relazioni' => 1]],
        ]],
        ['title' => 'CRUD Controller Template', 'description' => 'CRUD completo', 'dev_units' => ['crud' => 7], 'subtasks' => [['title' => 'CommunicationTemplateController CRUD', 'dev_units' => ['crud' => 7]]]],
        ['title' => 'Workflow Editor Template', 'description' => 'Editor template', 'dev_units' => ['workflow' => 1], 'subtasks' => [['title' => 'Editor template con variabili', 'dev_units' => ['workflow' => 1]]]],
        ['title' => 'UI Form e Lista Template', 'description' => 'Form e lista template', 'dev_units' => ['ui_form' => 10, 'ui_lista' => 14], 'subtasks' => [
            ['title' => 'Form template (10 campi)', 'dev_units' => ['ui_form' => 10]],
            ['title' => 'Lista template (14 elementi)', 'dev_units' => ['ui_lista' => 14]],
        ]],
    ], 'er_model' => ['entity' => 'CommunicationTemplate', 'attributes' => ['id', 'name', 'content', 'channel', 'variables'], 'relationships' => ['hasMany' => ['Communication']]]],
    
    ['title' => 'Integrazione SMS/WhatsApp', 'description' => 'Integrazione gateway SMS/WhatsApp (Twilio)', 'dev_units' => ['db_campi' => 8, 'db_relazioni' => 1, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 0, 'total' => 60], 'tasks' => [
        ['title' => 'Modello Database SMS/WhatsApp', 'description' => 'Creazione modello per tracking', 'dev_units' => ['db_campi' => 8, 'db_relazioni' => 1], 'subtasks' => [
            ['title' => 'Migration tabella sms_whatsapp_logs', 'dev_units' => ['db_campi' => 8]],
            ['title' => 'Relazioni SmsWhatsappLog → Communication', 'dev_units' => ['db_relazioni' => 1]],
        ]],
        ['title' => 'Workflow Integrazione Twilio', 'description' => 'Integrazione Twilio', 'dev_units' => ['workflow' => 2], 'subtasks' => [
            ['title' => 'Integrazione API Twilio', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Gestione invio SMS/WhatsApp', 'dev_units' => ['workflow' => 1]],
        ]],
    ], 'er_model' => ['entity' => 'SmsWhatsappLog', 'attributes' => ['id', 'communication_id', 'sent_at', 'status', 'response'], 'relationships' => ['belongsTo' => ['Communication']]]],
    
    ['title' => 'Attività Extra Evolute', 'description' => 'Gestione avanzata attività extra (orchestra/coro)', 'dev_units' => ['db_campi' => 8, 'db_relazioni' => 1, 'crud' => 0, 'workflow' => 2, 'ui_form' => 8, 'ui_lista' => 0, 'ui_stampa' => 0, 'total' => 40], 'tasks' => [
        ['title' => 'Modello Database Attività Extra Evolute', 'description' => 'Estensione modello ExtraActivity', 'dev_units' => ['db_campi' => 8, 'db_relazioni' => 1], 'subtasks' => [
            ['title' => 'Migration campi avanzati', 'dev_units' => ['db_campi' => 8]],
            ['title' => 'Relazioni ExtraActivity → Enrollment', 'dev_units' => ['db_relazioni' => 1]],
        ]],
        ['title' => 'Workflow Gestione Avanzata', 'description' => 'Workflow avanzato', 'dev_units' => ['workflow' => 2], 'subtasks' => [
            ['title' => 'Workflow iscrizioni avanzate', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Gestione convocazioni', 'dev_units' => ['workflow' => 1]],
        ]],
        ['title' => 'UI Form Attività Extra', 'description' => 'Form attività extra', 'dev_units' => ['ui_form' => 8], 'subtasks' => [
            ['title' => 'Form attività extra (8 campi)', 'dev_units' => ['ui_form' => 8]],
        ]],
    ], 'er_model' => ['entity' => 'ExtraActivity', 'attributes' => ['id', 'name', 'type', 'schedule', 'notes'], 'relationships' => ['hasMany' => ['ExtraActivityEnrollment']]]],
    
    ['title' => 'Generazione Attestati PDF', 'description' => 'Generazione PDF attestati frequenza', 'dev_units' => ['db_campi' => 0, 'db_relazioni' => 0, 'crud' => 0, 'workflow' => 1, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 39, 'total' => 40], 'tasks' => [
        ['title' => 'Workflow Generazione Attestati', 'description' => 'Generazione PDF attestati', 'dev_units' => ['workflow' => 1, 'ui_stampa' => 39], 'subtasks' => [
            ['title' => 'Template PDF attestato', 'dev_units' => ['ui_stampa' => 25]],
            ['title' => 'Generazione e download PDF', 'dev_units' => ['ui_stampa' => 14]],
        ]],
    ], 'er_model' => ['entity' => 'Certificate', 'attributes' => ['id', 'student_id', 'pdf_path', 'issued_at'], 'relationships' => ['belongsTo' => ['Student']]]],
    
    // GRUPPO 5: REPORTISTICA E ANALISI (4 attività)
    ['title' => 'Reportistica Avanzata', 'description' => 'Reportistica con grafici e confronti multi-anno', 'dev_units' => ['db_campi' => 0, 'db_relazioni' => 0, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 98, 'total' => 100], 'tasks' => [
        ['title' => 'Workflow Reportistica', 'description' => 'Implementazione reportistica', 'dev_units' => ['workflow' => 2, 'ui_stampa' => 98], 'subtasks' => [
            ['title' => 'Query aggregazioni dati', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Generazione grafici (Chart.js)', 'dev_units' => ['ui_stampa' => 50]],
            ['title' => 'Confronti multi-anno', 'dev_units' => ['ui_stampa' => 30]],
            ['title' => 'Export report PDF/Excel', 'dev_units' => ['ui_stampa' => 18]],
        ]],
    ], 'er_model' => ['entity' => 'Report', 'attributes' => ['id', 'type', 'parameters', 'generated_at'], 'relationships' => []]],
    
    ['title' => 'Flusso di Cassa', 'description' => 'Visualizzazione flusso di cassa con aggregazioni', 'dev_units' => ['db_campi' => 0, 'db_relazioni' => 0, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 68, 'total' => 70], 'tasks' => [
        ['title' => 'Workflow Flusso di Cassa', 'description' => 'Implementazione flusso cassa', 'dev_units' => ['workflow' => 2, 'ui_stampa' => 68], 'subtasks' => [
            ['title' => 'Calcolo aggregazioni entrate/uscite', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Visualizzazione grafico flusso', 'dev_units' => ['ui_stampa' => 40]],
            ['title' => 'Export flusso cassa', 'dev_units' => ['ui_stampa' => 28]],
        ]],
    ], 'er_model' => ['entity' => 'CashFlow', 'attributes' => ['id', 'date', 'income', 'expense', 'balance'], 'relationships' => []]],
    
    ['title' => 'Export Excel/CSV Personalizzato', 'description' => 'Export dati personalizzabile in Excel/CSV', 'dev_units' => ['db_campi' => 0, 'db_relazioni' => 0, 'crud' => 0, 'workflow' => 1, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 49, 'total' => 50], 'tasks' => [
        ['title' => 'Workflow Export Personalizzato', 'description' => 'Implementazione export', 'dev_units' => ['workflow' => 1, 'ui_stampa' => 49], 'subtasks' => [
            ['title' => 'Builder query personalizzate', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Export Excel (Laravel Excel)', 'dev_units' => ['ui_stampa' => 25]],
            ['title' => 'Export CSV', 'dev_units' => ['ui_stampa' => 24]],
        ]],
    ], 'er_model' => ['entity' => 'Export', 'attributes' => ['id', 'type', 'parameters', 'file_path'], 'relationships' => []]],
    
    ['title' => 'Statistiche Dashboard', 'description' => 'Dashboard con statistiche avanzate', 'dev_units' => ['db_campi' => 0, 'db_relazioni' => 0, 'crud' => 0, 'workflow' => 2, 'ui_form' => 0, 'ui_lista' => 0, 'ui_stampa' => 58, 'total' => 60], 'tasks' => [
        ['title' => 'Workflow Dashboard', 'description' => 'Implementazione dashboard', 'dev_units' => ['workflow' => 2, 'ui_stampa' => 58], 'subtasks' => [
            ['title' => 'Calcolo statistiche real-time', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Aggiornamento automatico dati', 'dev_units' => ['workflow' => 1]],
            ['title' => 'Widget dashboard (grafici, KPI)', 'dev_units' => ['ui_stampa' => 58]],
        ]],
    ], 'er_model' => ['entity' => 'Dashboard', 'attributes' => ['id', 'widgets', 'layout'], 'relationships' => []]],
    
];
