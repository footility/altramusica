# Riepilogo DEV UNIT - L'Altramusica

**Data:** Dicembre 2024  
**Metodologia:** Footility - ogni elemento implementabile = 1 DEV UNIT

---

## ANALISI 1: DEV UNIT REALI (Trasformazione 1:1 ODS → Laravel)

**Scopo:** Calcolo DEV UNIT per la trasformazione diretta 1:1 dei file ODS in database Laravel relazionale ottimizzato.

**Include:** Solo migrazione/import dei dati esistenti dagli ODS, senza funzionalità evolutive.

| Funzionalità | DB_campi | DB_rel | CRUD | Workflow | UI_form | UI_lista | UI_stampa | TOTALE |
|--------------|----------|--------|------|----------|---------|----------|-----------|--------|
| Studenti | 29 | 7 | 7 | 1 | 29 | 20 | 0 | 93 |
| Genitori/Tutori | 22 | 2 | 7 | 1 | 22 | 16 | 0 | 70 |
| Docenti | 26 | 3 | 7 | 1 | 26 | 18 | 0 | 81 |
| Anno Scolastico | 8 | 5 | 7 | 1 | 8 | 8 | 0 | 37 |
| Corsi/Iscrizioni | 29 | 5 | 7 | 1 | 29 | 17 | 0 | 88 |
| Calendario | 7 | 2 | 7 | 1 | 7 | 10 | 0 | 34 |
| Contratti Storici | 27 | 3 | 1 | 1 | 0 | 16 | 0 | 48 |
| Fatture Storiche | 12 | 3 | 1 | 1 | 0 | 15 | 0 | 32 |
| Pagamenti Storici | 9 | 2 | 1 | 1 | 0 | 12 | 0 | 25 |
| Attività Extra | 13 | 3 | 7 | 1 | 13 | 14 | 0 | 51 |
| Presenze Minimo | 8 | 3 | 7 | 1 | 8 | 13 | 0 | 40 |
| Note Operative | 7 | 2 | 0 | 0 | 7 | 0 | 0 | 16 |
| Strumenti | 19 | 3 | 7 | 1 | 19 | 16 | 0 | 65 |
| Disponibilità | 11 | 1 | 7 | 1 | 11 | 11 | 0 | 42 |
| **TOTALE REALI** | **226** | **43** | **67** | **13** | **191** | **190** | **0** | **730** |

**📄 Dettaglio completo:** `reali/DEV_UNIT_REALI_ODS_LARAVEL.md`

---

## ANALISI 2: DEV UNIT IPOTETICHE (Funzionalità Evolutive)

**Scopo:** Calcolo DEV UNIT per le funzionalità di evoluzione richieste (oltre la semplice migrazione ODS).

**Include:** Solo funzionalità nuove/evolutive, workflow avanzati, automazioni, PDF generation.

| Funzionalità | DB_campi | DB_rel | CRUD | Workflow | UI_form | UI_lista | UI_stampa | TOTALE |
|--------------|----------|--------|------|----------|---------|----------|-----------|--------|
| Primo contatto | 12 | 2 | 7 | 1 | 12 | 11 | 0 | 45 |
| Gestione utenze | 10 | 2 | 7 | 1 | 10 | 11 | 0 | 41 |
| Controllo accessi | 6 | 2 | 0 | 2 | 0 | 0 | 0 | 10 |
| Contratti da iscrizioni | 15 | 3 | 7 | 4 | 15 | 16 | 48 | 108 |
| Modelli contratto | 8 | 1 | 7 | 1 | 8 | 9 | 0 | 34 |
| Generazione PDF contratti | 0 | 0 | 0 | 1 | 0 | 0 | 48 | 49 |
| Fatturazione evolutiva | 13 | 5 | 7 | 3 | 13 | 20 | 13 | 74 |
| Piani pagamento | 9 | 2 | 0 | 2 | 9 | 0 | 0 | 22 |
| Automazioni sollecito | 7 | 2 | 0 | 2 | 0 | 9 | 0 | 20 |
| Fornitori | 12 | 2 | 7 | 0 | 12 | 12 | 0 | 45 |
| Registro evoluto | 13 | 5 | 7 | 2 | 13 | 16 | 0 | 56 |
| Recuperi lezioni | 10 | 3 | 0 | 2 | 10 | 13 | 0 | 38 |
| Conto orario docenti | 14 | 3 | 0 | 2 | 0 | 15 | 13 | 47 |
| Gestione supplenze | 10 | 3 | 0 | 2 | 0 | 13 | 0 | 28 |
| Attività extra evolute | 4 | 1 | 0 | 2 | 4 | 0 | 0 | 11 |
| Gestione esami | 12 | 3 | 7 | 1 | 12 | 14 | 0 | 49 |
| Attestati frequenza | 7 | 1 | 0 | 1 | 0 | 0 | 9 | 18 |
| Comunicazioni evolute | 11 | 3 | 0 | 2 | 11 | 13 | 0 | 40 |
| **TOTALE IPOTETICHE** | **174** | **46** | **49** | **31** | **119** | **161** | **131** | **710** |

**📄 Dettaglio completo:** `ipotetiche/DEV_UNIT_IPOTETICHE_EVOLUZIONE.md`

---

## CONFRONTO TOTALE

| Analisi | DB_campi | DB_rel | CRUD | Workflow | UI_form | UI_lista | UI_stampa | TOTALE |
|---------|----------|--------|------|----------|---------|----------|-----------|--------|
| **REALI** (1:1 ODS) | 226 | 43 | 67 | 13 | 191 | 190 | 0 | **730** |
| **IPOTETICHE** (Evoluzione) | 174 | 46 | 49 | 31 | 119 | 161 | 131 | **710** |
| **TOTALE PROGETTO** | **400** | **89** | **116** | **44** | **310** | **351** | **131** | **1440** |

---

## NOTE METODOLOGICHE

### DEV UNIT REALI
- **Solo trasformazione 1:1**: Migrazione dati esistenti dagli ODS in database Laravel
- **Nessuna funzionalità evolutiva**: No workflow avanzati, automazioni, PDF generation
- **CRUD base**: Solo operazioni base per consultare/modificare dati importati
- **UI minimale**: Solo form e liste necessarie per visualizzare/modificare dati
- **Nessuna stampa**: Solo visualizzazione dati, no PDF

### DEV UNIT IPOTETICHE
- **Solo funzionalità evolutive**: Funzionalità nuove/evolutive richieste
- **Workflow avanzati**: Automazioni, generazione PDF, integrazioni
- **UI completa**: Form complessi, liste avanzate, PDF generation
- **Stampa evolutiva**: Generazione PDF contratti, fatture, attestati

---

## BREAKDOWN PER FASE

### FASE 1 - Migrazione (DEV UNIT REALI)
- Anagrafiche base: Studenti, Genitori, Docenti
- Anno scolastico, Corsi/Iscrizioni, Calendario
- Storici: Contratti, Fatture, Pagamenti
- Base: Attività Extra, Presenze, Note, Strumenti, Disponibilità
- **TOTALE FASE 1: 730 DEV UNIT**

### FASE 2 - Evoluzione Amministrativa (DEV UNIT IPOTETICHE)
- Utenze e Controllo Accessi
- Contratti evolutivi, Modelli, PDF
- Fatturazione evolutiva, Piani pagamento
- Automazioni sollecito, Fornitori
- **TOTALE FASE 2: ~350 DEV UNIT**

### FASE 3 - Evoluzione Didattica (DEV UNIT IPOTETICHE)
- Registro evoluto, Recuperi, Supplenze
- Conto orario docenti
- Attività extra evolute
- Esami, Attestati
- Comunicazioni evolute
- **TOTALE FASE 3: ~360 DEV UNIT**

---

## FILE DI RIFERIMENTO

- **DEV UNIT REALI:** `dev-unit/reali/DEV_UNIT_REALI_ODS_LARAVEL.md`
- **DEV UNIT IPOTETICHE:** `dev-unit/ipotetiche/DEV_UNIT_IPOTETICHE_EVOLUZIONE.md`
- **Matrice Ipotetica (deprecata):** `DEV_UNIT_MATRICE_IPOTETICA.md`
- **Calcolo Dettagliato (deprecato):** `DEV_UNIT_CALCOLO_DETTAGLIATO.md`


