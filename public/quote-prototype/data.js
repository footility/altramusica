// Dataset iniziale per il preventivo parametrico Altramusica
// Le macro-aree riprendono la struttura funzionale emersa dalle analisi.

window.MACRO_AREAS = [
    "Anagrafiche e anni scolastici",
    "Primo contatto, disponibilità e iscrizioni",
    "Contratti, rate e documenti",
    "Fatturazione, pagamenti, recupero crediti",
    "Registro, lezioni, presenze, conto orario",
    "Attività extra, comunicazioni, attestati",
    "Magazzino e noleggi",
    "Reportistica, integrazioni fiscali, flusso di cassa"
];

// Tag possibili (informativi):
// - trasporto: portare nel gestionale dati e flussi già esistenti
// - evolutiva: miglioramento rispetto alla situazione attuale
// - meccanica: attività ripetitiva, fortemente accelerabile dall'IA
// - pensiero: richiede decisioni, modellazione, confronto
// - integrazione: richiede dialogo con sistemi esterni

window.FUNCTIONS = [
    // --- FASE 1: ANAGRAFICHE E ANNO SCOLASTICO ---
    {
        id: "f_ay_setup",
        title: "Configurazione anno scolastico corrente",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["trasporto", "meccanica"],
        effortPoints: 4,
        dependencies: [],
        groupKey: "anno_scolastico",
        notes: "Definizione anno corrente e periodi base.",
        phaseKey: "phase1"
    },
    {
        id: "f_ay_filtering",
        title: "Filtri per anno scolastico su liste principali",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["evolutiva", "meccanica"],
        effortPoints: 5,
        dependencies: [{ id: "f_ay_setup", type: "hard" }],
        groupKey: "anno_scolastico",
        notes: "Permette di non mescolare anni diversi.",
        phaseKey: "phase1"
    },

    // Studenti
    {
        id: "f_student_model",
        title: "Modello dati studenti",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [{ id: "f_ay_setup", type: "hard" }],
        groupKey: "anagrafica_studenti",
        notes: "Campi allineati agli ODS.",
        phaseKey: "phase1"
    },
    {
        id: "f_student_crud",
        title: "CRUD studenti (crea/vedi/modifica/elimina)",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["trasporto", "meccanica"],
        effortPoints: 8,
        dependencies: [{ id: "f_student_model", type: "hard" }],
        groupKey: "anagrafica_studenti",
        notes: "Schermate base per la segreteria.",
        phaseKey: "phase1"
    },
    {
        id: "f_student_import_ods",
        title: "Import studenti da ODS esistente",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["trasporto", "meccanica"],
        effortPoints: 7,
        dependencies: [{ id: "f_student_model", type: "hard" }],
        groupKey: "anagrafica_studenti",
        notes: "Popola l’anagrafica dal gestionale attuale.",
        phaseKey: "phase1"
    },
    {
        id: "f_student_search_filters",
        title: "Ricerca e filtri studenti (nome, stato, codice)",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["evolutiva", "meccanica"],
        effortPoints: 5,
        dependencies: [{ id: "f_student_crud", type: "hard" }],
        groupKey: "anagrafica_studenti",
        notes: "Evita liste ingestibili a mano.",
        phaseKey: "phase1"
    },

    // Genitori / Tutori
    {
        id: "f_guardian_model",
        title: "Modello dati genitori/tutori",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["trasporto", "meccanica"],
        effortPoints: 5,
        dependencies: [{ id: "f_student_model", type: "hard" }],
        groupKey: "anagrafica_genitori",
        notes: "Collegato agli studenti come nei fogli attuali.",
        phaseKey: "phase1"
    },
    {
        id: "f_guardian_crud",
        title: "CRUD genitori/tutori",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [{ id: "f_guardian_model", type: "hard" }],
        groupKey: "anagrafica_genitori",
        notes: "Gestione madre/padre/altro referente.",
        phaseKey: "phase1"
    },

    // Docenti
    {
        id: "f_teacher_model",
        title: "Modello dati docenti",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["trasporto", "meccanica"],
        effortPoints: 5,
        dependencies: [],
        groupKey: "anagrafica_docenti",
        notes: "Dati personali + elementi contrattuali base.",
        phaseKey: "phase1"
    },
    {
        id: "f_teacher_crud",
        title: "CRUD docenti",
        macroArea: "Anagrafiche e anni scolastici",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [{ id: "f_teacher_model", type: "hard" }],
        groupKey: "anagrafica_docenti",
        notes: "Gestione anagrafe insegnanti.",
        phaseKey: "phase1"
    },

    // --- FASE 1: PRIMO CONTATTO, DISPONIBILITÀ, ISCRIZIONI ---
    {
        id: "f_first_contact_form",
        title: "Form primo contatto pubblico",
        macroArea: "Primo contatto, disponibilità e iscrizioni",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [{ id: "f_student_model", type: "hard" }],
        groupKey: "primo_contatto",
        notes: "Raccoglie i dati iniziali di interessati.",
        phaseKey: "phase1"
    },
    {
        id: "f_first_contact_to_student",
        title: "Conversione primo contatto → studente",
        macroArea: "Primo contatto, disponibilità e iscrizioni",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 6,
        dependencies: [
            { id: "f_first_contact_form", type: "hard" },
            { id: "f_student_crud", type: "hard" }
        ],
        groupKey: "primo_contatto",
        notes: "Trasforma interessati in anagrafiche operative.",
        phaseKey: "phase2"
    },
    {
        id: "f_student_availability",
        title: "Gestione disponibilità orarie studenti",
        macroArea: "Primo contatto, disponibilità e iscrizioni",
        tags: ["trasporto", "meccanica"],
        effortPoints: 7,
        dependencies: [{ id: "f_student_model", type: "hard" }],
        groupKey: "disponibilita",
        notes: "Trascrive le preferenze dai questionari.",
        phaseKey: "phase1"
    },
    {
        id: "f_teacher_availability",
        title: "Gestione disponibilità docenti",
        macroArea: "Primo contatto, disponibilità e iscrizioni",
        tags: ["trasporto", "meccanica"],
        effortPoints: 7,
        dependencies: [{ id: "f_teacher_model", type: "hard" }],
        groupKey: "disponibilita",
        notes: "Centralizza giorni/orari disponibili.",
        phaseKey: "phase1"
    },
    {
        id: "f_calendar_base",
        title: "Calendario lezioni base (giornate attive/sospensioni)",
        macroArea: "Primo contatto, disponibilità e iscrizioni",
        tags: ["trasporto", "meccanica"],
        effortPoints: 8,
        dependencies: [{ id: "f_ay_setup", type: "hard" }],
        groupKey: "calendario",
        notes: "Replica l’informazione dei fogli Calendario.",
        phaseKey: "phase1"
    },
    {
        id: "f_course_model",
        title: "Modello corsi e tipi di corso",
        macroArea: "Primo contatto, disponibilità e iscrizioni",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [],
        groupKey: "corsi_iscrizioni",
        notes: "Base per iscrizioni e lezioni.",
        phaseKey: "phase1"
    },
    {
        id: "f_enrollment_crud",
        title: "Iscrizioni studenti ai corsi",
        macroArea: "Primo contatto, disponibilità e iscrizioni",
        tags: ["trasporto", "meccanica"],
        effortPoints: 8,
        dependencies: [
            { id: "f_student_model", type: "hard" },
            { id: "f_course_model", type: "hard" },
            { id: "f_calendar_base", type: "soft" }
        ],
        groupKey: "corsi_iscrizioni",
        notes: "Riflette i dati di iscrizione già presenti negli ODS.",
        phaseKey: "phase1"
    },

    // --- FASE 1: CONTRATTI/FATTURE/PAGAMENTI STORICO ---
    {
        id: "f_contract_model",
        title: "Modello contratti base",
        macroArea: "Contratti, rate e documenti",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [
            { id: "f_student_model", type: "hard" },
            { id: "f_ay_setup", type: "hard" }
        ],
        groupKey: "contratti_base",
        notes: "Replica i campi principali del file contratti.",
        phaseKey: "phase1"
    },
    {
        id: "f_contract_import_ods",
        title: "Import contratti storici da ODS",
        macroArea: "Contratti, rate e documenti",
        tags: ["trasporto", "meccanica"],
        effortPoints: 7,
        dependencies: [
            { id: "f_contract_model", type: "hard" },
            { id: "f_student_import_ods", type: "soft" }
        ],
        groupKey: "contratti_base",
        notes: "Permette di consultare i contratti già esistenti.",
        phaseKey: "phase1"
    },
    {
        id: "f_invoice_model",
        title: "Modello fatture base",
        macroArea: "Fatturazione, pagamenti, recupero crediti",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [
            { id: "f_student_model", type: "hard" },
            { id: "f_ay_setup", type: "hard" }
        ],
        groupKey: "fatture_base",
        notes: "Campi principali delle fatture attuali.",
        phaseKey: "phase1"
    },
    {
        id: "f_invoice_import_ods",
        title: "Import fatture storiche",
        macroArea: "Fatturazione, pagamenti, recupero crediti",
        tags: ["trasporto", "meccanica"],
        effortPoints: 7,
        dependencies: [
            { id: "f_invoice_model", type: "hard" },
            { id: "f_student_import_ods", type: "soft" }
        ],
        groupKey: "fatture_base",
        notes: "Rende consultabile lo storico nel gestionale.",
        phaseKey: "phase1"
    },
    {
        id: "f_payment_model",
        title: "Modello pagamenti base",
        macroArea: "Fatturazione, pagamenti, recupero crediti",
        tags: ["trasporto", "meccanica"],
        effortPoints: 5,
        dependencies: [{ id: "f_invoice_model", type: "hard" }],
        groupKey: "pagamenti_base",
        notes: "Traccia importi pagati e residui.",
        phaseKey: "phase1"
    },
    {
        id: "f_payment_import_ods",
        title: "Import pagamenti storici",
        macroArea: "Fatturazione, pagamenti, recupero crediti",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [
            { id: "f_payment_model", type: "hard" },
            { id: "f_invoice_import_ods", type: "soft" }
        ],
        groupKey: "pagamenti_base",
        notes: "Permette di vedere situazione pagamenti come oggi.",
        phaseKey: "phase1"
    },

    // --- FASE 1: ATTIVITÀ EXTRA, REGISTRO MINIMO, COMUNICAZIONI MANUALI ---
    {
        id: "f_extra_activity_model",
        title: "Modello attività extra (orchestra/coro)",
        macroArea: "Attività extra, comunicazioni, attestati",
        tags: ["trasporto", "meccanica"],
        effortPoints: 5,
        dependencies: [],
        groupKey: "attivita_extra",
        notes: "Censisce progetti come orchestra e coro.",
        phaseKey: "phase1"
    },
    {
        id: "f_extra_activity_enrollment",
        title: "Iscrizioni a orchestra/coro",
        macroArea: "Attività extra, comunicazioni, attestati",
        tags: ["trasporto", "meccanica"],
        effortPoints: 6,
        dependencies: [
            { id: "f_extra_activity_model", type: "hard" },
            { id: "f_student_model", type: "hard" }
        ],
        groupKey: "attivita_extra",
        notes: "Trasporta le liste di partecipazione attuali.",
        phaseKey: "phase1"
    },
    {
        id: "f_lesson_minimal",
        title: "Registro presenze minimo",
        macroArea: "Registro, lezioni, presenze, conto orario",
        tags: ["trasporto", "meccanica"],
        effortPoints: 7,
        dependencies: [
            { id: "f_course_model", type: "hard" },
            { id: "f_student_model", type: "hard" }
        ],
        groupKey: "registro_minimo",
        notes: "Permette di segnare presenti/assenti in modo essenziale.",
        phaseKey: "phase1"
    },
    {
        id: "f_manual_communication_log",
        title: "Note su comunicazioni manuali",
        macroArea: "Attività extra, comunicazioni, attestati",
        tags: ["evolutiva", "meccanica"],
        effortPoints: 4,
        dependencies: [{ id: "f_student_model", type: "hard" }],
        groupKey: "comunicazioni_base",
        notes: "Permette di annotare telefonate, email, colloqui.",
        phaseKey: "phase1"
    },

    // --- FASE 2: AREA AMMINISTRATIVA EVOLUTIVA ---
    {
        id: "f_contract_from_enrollment",
        title: "Creazione contratti da iscrizioni",
        macroArea: "Contratti, rate e documenti",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 9,
        dependencies: [
            { id: "f_enrollment_crud", type: "hard" },
            { id: "f_contract_model", type: "hard" }
        ],
        groupKey: "contratti_evolutivi",
        notes: "Riduce la duplicazione di dati tra iscrizioni e contratti.",
        phaseKey: "phase2"
    },
    {
        id: "f_contract_pdf",
        title: "Generazione PDF contratti",
        macroArea: "Contratti, rate e documenti",
        tags: ["evolutiva", "meccanica"],
        effortPoints: 8,
        dependencies: [{ id: "f_contract_model", type: "hard" }],
        groupKey: "contratti_evolutivi",
        notes: "Produce documenti coerenti pronti per l’invio.",
        phaseKey: "phase2"
    },
    {
        id: "f_invoice_from_contract",
        title: "Creazione fatture da contratti/pagamenti",
        macroArea: "Fatturazione, pagamenti, recupero crediti",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 9,
        dependencies: [
            { id: "f_contract_model", type: "hard" },
            { id: "f_invoice_model", type: "hard" }
        ],
        groupKey: "fatture_evolutive",
        notes: "Allinea in modo automatico amministrazione e didattica.",
        phaseKey: "phase2"
    },
    {
        id: "f_payment_plan_flexible",
        title: "Piani di pagamento flessibili",
        macroArea: "Fatturazione, pagamenti, recupero crediti",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 8,
        dependencies: [{ id: "f_invoice_model", type: "hard" }],
        groupKey: "fatture_evolutive",
        notes: "Permette rate personalizzate oltre agli schemi fissi.",
        phaseKey: "phase2"
    },
    {
        id: "f_recovery_reminders",
        title: "Solleciti recupero crediti semi-automatici",
        macroArea: "Fatturazione, pagamenti, recupero crediti",
        tags: ["evolutiva", "integrazione"],
        effortPoints: 8,
        dependencies: [
            { id: "f_payment_model", type: "hard" },
            { id: "f_manual_communication_log", type: "soft" }
        ],
        groupKey: "recupero_crediti",
        notes: "Supporta la segreteria nel seguire le scadenze critiche.",
        phaseKey: "phase2"
    },

    // --- FASE 3: DIDATTICA EVOLUTA ---
    {
        id: "f_register_evolved",
        title: "Registro elettronico evoluto",
        macroArea: "Registro, lezioni, presenze, conto orario",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 10,
        dependencies: [
            { id: "f_lesson_minimal", type: "hard" },
            { id: "f_teacher_crud", type: "hard" }
        ],
        groupKey: "registro_evoluto",
        notes: "Visualizzazioni per docente, recuperi, storico.",
        phaseKey: "phase3"
    },
    {
        id: "f_teacher_hours_full",
        title: "Conto orario docenti completo",
        macroArea: "Registro, lezioni, presenze, conto orario",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 9,
        dependencies: [
            { id: "f_register_evolved", type: "hard" },
            { id: "f_teacher_model", type: "hard" }
        ],
        groupKey: "conto_orario",
        notes: "Regole per compensi, bonus e forfait.",
        phaseKey: "phase3"
    },
    {
        id: "f_substitutions",
        title: "Gestione supplenze",
        macroArea: "Registro, lezioni, presenze, conto orario",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 7,
        dependencies: [
            { id: "f_lesson_minimal", type: "hard" },
            { id: "f_teacher_crud", type: "hard" }
        ],
        groupKey: "supplenze",
        notes: "Traccia lezioni svolte da insegnanti sostituti.",
        phaseKey: "phase3"
    },
    {
        id: "f_attestati",
        title: "Generazione attestati di frequenza",
        macroArea: "Attività extra, comunicazioni, attestati",
        tags: ["evolutiva", "meccanica"],
        effortPoints: 7,
        dependencies: [
            { id: "f_register_evolved", type: "hard" },
            { id: "f_extra_activity_enrollment", type: "soft" }
        ],
        groupKey: "attestati",
        notes: "Documenti per crediti scolastici basati su presenze.",
        phaseKey: "phase3"
    },

    // --- FASE 4: INTEGRAZIONI E REPORTISTICA ---
    {
        id: "f_sdi_integration",
        title: "Integrazione SDI fatturazione elettronica",
        macroArea: "Reportistica, integrazioni fiscali, flusso di cassa",
        tags: ["evolutiva", "integrazione"],
        effortPoints: 14,
        dependencies: [
            { id: "f_invoice_model", type: "hard" },
            { id: "f_invoice_from_contract", type: "soft" }
        ],
        groupKey: "integrazioni_fiscali",
        notes: "Invio e gestione fatture elettroniche tramite SDI.",
        phaseKey: "phase4"
    },
    {
        id: "f_taxdrawer_import",
        title: "Import fatture da cassetto fiscale",
        macroArea: "Reportistica, integrazioni fiscali, flusso di cassa",
        tags: ["evolutiva", "integrazione"],
        effortPoints: 10,
        dependencies: [{ id: "f_invoice_model", type: "hard" }],
        groupKey: "integrazioni_fiscali",
        notes: "Recupera fatture passive/attive da sistemi esterni.",
        phaseKey: "phase4"
    },
    {
        id: "f_cashflow_view",
        title: "Vista flusso di cassa semplice",
        macroArea: "Reportistica, integrazioni fiscali, flusso di cassa",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 8,
        dependencies: [
            { id: "f_payment_model", type: "hard" },
            { id: "f_invoice_model", type: "hard" }
        ],
        groupKey: "flusso_cassa",
        notes: "Mostra entrate/uscite e proiezioni di base.",
        phaseKey: "phase4"
    },
    {
        id: "f_reports_advanced",
        title: "Report e cruscotti avanzati",
        macroArea: "Reportistica, integrazioni fiscali, flusso di cassa",
        tags: ["evolutiva", "pensiero"],
        effortPoints: 12,
        dependencies: [
            { id: "f_enrollment_crud", type: "soft" },
            { id: "f_invoice_model", type: "soft" },
            { id: "f_lesson_minimal", type: "soft" }
        ],
        groupKey: "reportistica",
        notes: "Analisi multi-anno, per corso, livello, età.",
        phaseKey: "phase4"
    }
];


