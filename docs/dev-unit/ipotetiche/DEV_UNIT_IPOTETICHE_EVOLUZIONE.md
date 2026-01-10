# DEV UNIT IPOTETICHE - Funzionalità Evolutive

**Data:** Dicembre 2024  
**Scopo:** Calcolo DEV UNIT per le funzionalità di evoluzione richieste (oltre la semplice migrazione ODS)  
**Metodologia:** Footility - ogni elemento implementabile = 1 DEV UNIT

> **IMPORTANTE:** Questo conteggio include SOLO le funzionalità nuove/evolutive richieste, NON la migrazione base dei dati.

---

## METODOLOGIA APPLICATA

- **DEV_DB_campi**: ogni campo database aggiuntivo per funzionalità evolutiva = 1 DEV UNIT
- **DEV_DB_relazioni**: ogni relazione FK aggiuntiva = 1 DEV UNIT
- **DEV_LOGIC_CRUD**: CRUD completo per nuove entità = 7 DEV UNIT
- **DEV_LOGIC_workflow**: ogni stato/transizione workflow evolutivo = 1 DEV UNIT
- **DEV_UI_form**: ogni campo form per nuove funzionalità = 1 DEV UNIT
- **DEV_UI_lista**: ogni colonna lista + filtro = 1 DEV UNIT
- **DEV_UI_stampa**: ogni campo PDF + tipo stampa = 1 DEV UNIT

---

## 1. PRIMO CONTATTO / PROSPECT (FASE 1 - Nuova Funzione)

### Campi Database Aggiuntivi

1. `id`
2. `first_name`
3. `last_name`
4. `birth_date`
5. `phone`
6. `email`
7. `notes`
8. `source`
9. `status` (prospect/interessato/convertito)
10. `converted_to_student_id` (riferimento quando convertito)
11. `created_at`
12. `updated_at`

**TOTALE: 12 DEV UNIT**

### Relazioni Database

1. FirstContact → Student (one-to-one, quando convertito)
2. FirstContact → Communication (one-to-many)

**TOTALE: 2 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Evolutivo

1. Conversione prospect → studente (workflow manuale)

**TOTALE: 1 DEV UNIT**

### UI Form (12 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 12 DEV UNIT**

### UI Lista (8 colonne + 3 filtri)

**Colonne:**
1. Nome
2. Cognome
3. Data nascita
4. Telefono
5. Email
6. Fonte
7. Stato
8. Azioni

**Filtri:**
1. Ricerca testo
2. Filtro stato
3. Filtro fonte

**TOTALE: 11 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE PRIMO CONTATTO: 12 + 2 + 7 + 1 + 12 + 11 + 0 = 45 DEV UNIT**

---

## 2. GESTIONE UTENZE (FASE 2 - Nuova Funzione)

### Campi Database

1. `id`
2. `email` (username)
3. `password` (hash)
4. `name`
5. `role` (admin/segreteria/insegnante/supplente)
6. `permissions` (JSON permessi granulari)
7. `status` (attivo/sospeso)
8. `last_login_at`
9. `created_at`
10. `updated_at`

**TOTALE: 10 DEV UNIT**

### Relazioni Database

1. User → Teacher (one-to-one, opzionale)
2. User → Activity (one-to-many, log attività)

**TOTALE: 2 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Evolutivo

1. Creazione account → invio credenziali → attivazione

**TOTALE: 1 DEV UNIT**

### UI Form (10 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 10 DEV UNIT**

### UI Lista (7 colonne + 4 filtri)

**Colonne:**
1. Nome
2. Email
3. Ruolo
4. Permessi
5. Stato
6. Ultimo accesso
7. Azioni

**Filtri:**
1. Ricerca nome/email
2. Filtro ruolo
3. Filtro stato
4. Filtro permessi

**TOTALE: 11 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE UTENZE: 10 + 2 + 7 + 1 + 10 + 11 + 0 = 41 DEV UNIT**

---

## 3. CONTROLLO ACCESSI E AUTORIZZAZIONI (FASE 2 - Nuova Funzione)

### Campi Database

1. `id`
2. `permission_name`
3. `resource` (student/invoice/contract)
4. `action` (create/read/update/delete)
5. `created_at`
6. `updated_at`

**TOTALE: 6 DEV UNIT**

### Relazioni Database

1. Permission → Role (many-to-many)
2. Permission → User (many-to-many, permessi custom)

**TOTALE: 2 DEV UNIT**

### CRUD Base (configurazione, no CRUD standard)

0 actions (configurazione sistema)

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo

1. Controllo permessi middleware
2. Log accessi negati

**TOTALE: 2 DEV UNIT**

### UI Form (0)

Nessun form (configurazione)

**TOTALE: 0 DEV UNIT**

### UI Lista (0)

Nessuna lista (configurazione)

**TOTALE: 0 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE CONTROLLO ACCESSI: 6 + 2 + 0 + 2 + 0 + 0 + 0 = 10 DEV UNIT**

---

## 4. CONTRATTI DA ISCRIZIONI (FASE 2 - Nuova Funzione)

### Campi Database Aggiuntivi (oltre storici)

1. `contract_number` (generazione automatica)
2. `student_id`
3. `academic_year_id`
4. `type` (regolare/breve/tempo estivo)
5. `created_at`
6. `sent_date`
7. `viewed_date`
8. `signed_date`
9. `status` (draft/sent/viewed/accepted/rejected)
10. `total_amount`
11. `payment_installments`
12. `terms` (testo regolamento)
13. `notes`
14. `created_at`
15. `updated_at`

**TOTALE: 15 DEV UNIT**

### Relazioni Database Aggiuntive

1. Contract → PaymentPlan (one-to-many)
2. Contract → Invoice (one-to-many, generazione)
3. Contract → ContractItem (one-to-many, prodotti)

**TOTALE: 3 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Evolutivo (4 stati/transizioni)

1. draft → sent
2. sent → viewed
3. viewed → accepted
4. viewed → rejected

**TOTALE: 4 DEV UNIT**

### UI Form (15 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 15 DEV UNIT**

### UI Lista (11 colonne + 5 filtri)

**Colonne:**
1. Numero contratto
2. Studente
3. Tipo
4. Data creazione
5. Data invio
6. Data visualizzazione
7. Data accettazione
8. Stato
9. Importo totale
10. N. rate
11. Azioni

**Filtri:**
1. Ricerca studente/numero
2. Filtro stato
3. Filtro tipo
4. Filtro anno scolastico
5. Filtro data range

**TOTALE: 16 DEV UNIT**

### UI Stampa (3 tipi PDF + 15 campi per tipo)

**Tipi PDF:**
1. Contratto regolare
2. Contratto breve
3. Contratto tempo estivo

**Campi per PDF (15 campi × 3 tipi = 45 DEV UNIT):**
1. Numero contratto
2. Data contratto
3. Dati studente (nome, cognome, CF, data nascita)
4. Dati genitore principale
5. Corso/i
6. Data inizio
7. Importo totale
8. Rate (importi e scadenze)
9. Note regolamento
10. Privacy
11. Firma studente/genitore
12. Data firma
13. Luogo
14. Testo contrattuale
15. Note aggiuntive

**TOTALE: 3 + 45 = 48 DEV UNIT**

**TOTALE CONTRATTI EVOLUTIVI: 15 + 3 + 7 + 4 + 15 + 16 + 48 = 108 DEV UNIT**

---

## 5. MODELLI STANDARD DI CONTRATTO (FASE 2 - Nuova Funzione)

### Campi Database

1. `id`
2. `name` (Nome modello)
3. `type` (regolare/breve/tempo estivo/noleggio)
4. `template_content` (Contenuto template)
5. `is_active`
6. `notes`
7. `created_at`
8. `updated_at`

**TOTALE: 8 DEV UNIT**

### Relazioni Database

1. ContractTemplate → Contract (one-to-many)

**TOTALE: 1 DEV UNIT**

### CRUD Base (gestione template)

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Evolutivo

1. Selezione modello → precompilazione contratto

**TOTALE: 1 DEV UNIT**

### UI Form (8 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 8 DEV UNIT**

### UI Lista (6 colonne + 3 filtri)

**Colonne:**
1. Nome modello
2. Tipo
3. Contenuto (anteprima)
4. Attivo
5. Note
6. Azioni

**Filtri:**
1. Ricerca nome
2. Filtro tipo
3. Filtro attivo

**TOTALE: 9 DEV UNIT**

### UI Stampa (0)

Nessuna stampa (è un template)

**TOTALE: 0 DEV UNIT**

**TOTALE MODELLI CONTRATTO: 8 + 1 + 7 + 1 + 8 + 9 + 0 = 34 DEV UNIT**

---

## 6. GENERAZIONE PDF CONTRATTI (FASE 2 - Nuova Funzione)

### Campi Database (0)

Nessun campo (generazione da Contract)

**TOTALE: 0 DEV UNIT**

### Relazioni Database (0)

Nessuna relazione

**TOTALE: 0 DEV UNIT**

### CRUD Base (0)

Nessun CRUD

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo

1. Generazione PDF da template + dati contratto

**TOTALE: 1 DEV UNIT**

### UI Form (0)

Nessun form

**TOTALE: 0 DEV UNIT**

### UI Lista (0)

Nessuna lista

**TOTALE: 0 DEV UNIT**

### UI Stampa (3 tipi PDF + 15 campi per tipo)

Stesso calcolo del punto 4 (Contratti da iscrizioni)

**TOTALE: 48 DEV UNIT**

**TOTALE GENERAZIONE PDF: 0 + 0 + 0 + 1 + 0 + 0 + 48 = 49 DEV UNIT**

---

## 7. GESTIONE FATTURAZIONE EVOLUTIVA (FASE 2 - Nuova Funzione)

### Campi Database Aggiuntivi (oltre storici)

1. `invoice_number` (generazione automatica)
2. `student_id`
3. `academic_year_id`
4. `contract_id` (riferimento)
5. `issue_date`
6. `due_date`
7. `total_amount`
8. `paid_amount`
9. `status` (draft/emessa/pagata/parzialmente pagata/scaduta)
10. `sdi_code` (codice SDI fatturazione elettronica)
11. `notes`
12. `created_at`
13. `updated_at`

**TOTALE: 13 DEV UNIT**

### Relazioni Database Aggiuntive

1. Invoice → Contract (many-to-one)
2. Invoice → PaymentPlan (one-to-many)
3. Invoice → Payment (one-to-many)
4. Invoice → CreditNote (one-to-many)
5. Invoice → InvoiceItem (one-to-many)

**TOTALE: 5 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Evolutivo (3 workflow)

1. Emissione fattura (da contratto accettato)
2. Registrazione pagamento
3. Chiusura fattura (pagamento completo)

**TOTALE: 3 DEV UNIT**

### UI Form (13 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 13 DEV UNIT**

### UI Lista (14 colonne + 6 filtri)

**Colonne:**
1. Numero fattura
2. Studente
3. Data emissione
4. Data scadenza
5. Importo totale
6. Importo pagato
7. Residuo
8. Stato
9. Metodo pagamento
10. Data pagamento
11. Note
12. Anno scolastico
13. Rate
14. Azioni

**Filtri:**
1. Ricerca studente/numero
2. Filtro stato
3. Filtro scadute
4. Filtro anno scolastico
5. Filtro data range
6. Filtro metodo pagamento

**TOTALE: 20 DEV UNIT**

### UI Stampa (1 tipo PDF + 12 campi)

**Tipo PDF:**
1. Fattura elettronica

**Campi per PDF (12 campi):**
1. Numero fattura
2. Data emissione
3. Dati studente
4. Dati genitore (fatturazione)
5. Descrizione servizi
6. Importo totale
7. IVA (se applicabile)
8. Scadenza
9. Metodo pagamento
10. Note
11. Codice SDI
12. QR code

**TOTALE: 1 + 12 = 13 DEV UNIT**

**TOTALE FATTURAZIONE EVOLUTIVA: 13 + 5 + 7 + 3 + 13 + 20 + 13 = 74 DEV UNIT**

---

## 8. PIANI DI PAGAMENTO FLESSIBILI (FASE 2 - Nuova Funzione)

### Campi Database

1. `id`
2. `contract_id`
3. `installment_number` (Numero rata)
4. `amount` (Importo rata)
5. `due_date` (Data scadenza)
6. `paid_amount` (Importo pagato)
7. `status` (da pagare/parzialmente pagata/pagata)
8. `created_at`
9. `updated_at`

**TOTALE: 9 DEV UNIT**

### Relazioni Database

1. PaymentPlan → Contract (many-to-one)
2. PaymentPlan → Payment (one-to-many)

**TOTALE: 2 DEV UNIT**

### CRUD Base (gestito da Contract, no CRUD separato)

0 actions

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo (2 workflow)

1. Calcolo automatico rate da calendario
2. Aggiornamento stato da pagamenti

**TOTALE: 2 DEV UNIT**

### UI Form (9 campi - visualizzato in Contract)

Ogni campo = 1 DEV UNIT

**TOTALE: 9 DEV UNIT**

### UI Lista (0 - visualizzato in Contract)

Nessuna lista separata

**TOTALE: 0 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE PIANI PAGAMENTO: 9 + 2 + 0 + 2 + 9 + 0 + 0 = 22 DEV UNIT**

---

## 9. AUTOMAZIONI SOLLECITO (FASE 2 - Nuova Funzione)

### Campi Database

1. `id`
2. `invoice_id`
3. `reminder_type` (primo/secondo/terzo)
4. `sent_date`
5. `notes`
6. `created_at`
7. `updated_at`

**TOTALE: 7 DEV UNIT**

### Relazioni Database

1. Reminder → Invoice (many-to-one)
2. Reminder → Communication (one-to-one)

**TOTALE: 2 DEV UNIT**

### CRUD Base (automatico, no CRUD manuale)

0 actions

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo (2 workflow)

1. Invio automatico sollecito (job schedulato)
2. Sospensione automatismi per casi particolari

**TOTALE: 2 DEV UNIT**

### UI Form (0)

Nessun form (automatico)

**TOTALE: 0 DEV UNIT**

### UI Lista (6 colonne + 3 filtri)

**Colonne:**
1. Fattura
2. Studente
3. Tipo sollecito
4. Data invio
5. Stato
6. Azioni

**Filtri:**
1. Filtro tipo sollecito
2. Filtro data range
3. Filtro stato

**TOTALE: 9 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE AUTOMAZIONI SOLLECITO: 7 + 2 + 0 + 2 + 0 + 9 + 0 = 20 DEV UNIT**

---

## 10. FORNITORI (FASE 2 - Nuova Funzione)

### Campi Database

1. `id`
2. `company_name` (Ragione sociale)
3. `vat_number` (Partita IVA)
4. `tax_code` (Codice fiscale)
5. `address` (Indirizzo)
6. `postal_code` (CAP)
7. `city` (Città)
8. `iban` (IBAN)
9. `expense_category` (Categoria spesa)
10. `notes`
11. `created_at`
12. `updated_at`

**TOTALE: 12 DEV UNIT**

### Relazioni Database

1. Supplier → Invoice (one-to-many, fatture passive)
2. Supplier → Instrument (one-to-many)

**TOTALE: 2 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Evolutivo (0)

Nessun workflow

**TOTALE: 0 DEV UNIT**

### UI Form (12 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 12 DEV UNIT**

### UI Lista (9 colonne + 3 filtri)

**Colonne:**
1. Ragione sociale
2. P. IVA
3. Cod. Fiscale
4. Indirizzo
5. IBAN
6. Categoria spesa
7. N. fatture
8. Note
9. Azioni

**Filtri:**
1. Ricerca testo
2. Filtro categoria
3. Filtro P. IVA

**TOTALE: 12 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FORNITORI: 12 + 2 + 7 + 0 + 12 + 12 + 0 = 45 DEV UNIT**

---

## 11. REGISTRO ELETTRONICO EVOLUTO (FASE 3 - Nuova Funzione)

### Campi Database Aggiuntivi (oltre presenze base)

1. `lesson_id`
2. `course_id`
3. `date`
4. `time_start`
5. `time_end`
6. `classroom_id`
7. `teacher_id`
8. `substitute_teacher_id` (supplente)
9. `type` (regolare/recupero/lezione libera)
10. `status` (programmata/effettuata/cancellata)
11. `notes`
12. `created_at`
13. `updated_at`

**TOTALE: 13 DEV UNIT**

### Relazioni Database Aggiuntive

1. Lesson → Course (many-to-one)
2. Lesson → Classroom (many-to-one)
3. Lesson → Teacher (many-to-one)
4. Lesson → Substitute (many-to-one, supplente)
5. Lesson → Attendance (one-to-many)

**TOTALE: 5 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Evolutivo (2 workflow)

1. Generazione automatica lezioni da calendario
2. Aggiornamento stato (programmata → effettuata)

**TOTALE: 2 DEV UNIT**

### UI Form (13 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 13 DEV UNIT**

### UI Lista (11 colonne + 5 filtri)

**Colonne:**
1. Data
2. Corso
3. Insegnante
4. Aula
5. Orario
6. Tipo
7. Stato
8. N. presenti
9. N. assenti
10. Note
11. Azioni

**Filtri:**
1. Filtro corso
2. Filtro insegnante
3. Filtro data range
4. Filtro tipo
5. Filtro stato

**TOTALE: 16 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE REGISTRO EVOLUTO: 13 + 5 + 7 + 2 + 13 + 16 + 0 = 56 DEV UNIT**

---

## 12. GESTIONE RECUPERI LEZIONI (FASE 3 - Nuova Funzione)

### Campi Database

1. `id`
2. `original_lesson_id` (Lezione originale)
3. `recovery_lesson_id` (Lezione recupero)
4. `recovery_date` (Data recupero)
5. `recovery_reason` (Motivo recupero)
6. `status` (programmato/effettuato/cancellato)
7. `notes`
8. `approved_by` (Approvato da)
9. `created_at`
10. `updated_at`

**TOTALE: 10 DEV UNIT**

### Relazioni Database

1. LessonRecovery → Lesson (many-to-one, originale)
2. LessonRecovery → Lesson (many-to-one, recupero)
3. LessonRecovery → User (many-to-one, approvato da)

**TOTALE: 3 DEV UNIT**

### CRUD Base (gestito da Lesson, no CRUD separato)

0 actions

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo (2 workflow)

1. Richiesta recupero → approvazione
2. Programmazione recupero → effettuazione

**TOTALE: 2 DEV UNIT**

### UI Form (10 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 10 DEV UNIT**

### UI Lista (9 colonne + 4 filtri)

**Colonne:**
1. Lezione originale
2. Data originale
3. Lezione recupero
4. Data recupero
5. Motivo
6. Stato
7. Approvato da
8. Note
9. Azioni

**Filtri:**
1. Filtro studente
2. Filtro data range
3. Filtro stato
4. Filtro insegnante

**TOTALE: 13 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE RECUPERI: 10 + 3 + 0 + 2 + 10 + 13 + 0 = 38 DEV UNIT**

---

## 13. CONTO ORARIO DOCENTI COMPLETO (FASE 3 - Nuova Funzione)

### Campi Database

1. `id`
2. `teacher_id`
3. `academic_year_id`
4. `total_hours` (Ore totali)
5. `regular_hours` (Ore regolari)
6. `recovery_hours` (Ore recupero)
7. `substitute_hours` (Ore supplenze)
8. `hourly_rate` (Valore orario)
9. `bonus_amount` (Bonus)
10. `forfait_amount` (Forfait)
11. `total_amount` (Importo totale)
12. `notes`
13. `created_at`
14. `updated_at`

**TOTALE: 14 DEV UNIT**

### Relazioni Database

1. TeacherHour → Teacher (many-to-one)
2. TeacherHour → AcademicYear (many-to-one)
3. TeacherHour → TeacherPayment (one-to-many)

**TOTALE: 3 DEV UNIT**

### CRUD Base (calcolo automatico, no CRUD manuale)

0 actions

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo (2 workflow)

1. Calcolo automatico ore da presenze
2. Calcolo importi (ore × tariffa + bonus + forfait)

**TOTALE: 2 DEV UNIT**

### UI Form (0)

Nessun form (calcolo automatico)

**TOTALE: 0 DEV UNIT**

### UI Lista (11 colonne + 4 filtri)

**Colonne:**
1. Insegnante
2. Anno scolastico
3. Ore totali
4. Ore regolari
5. Ore recupero
6. Ore supplenze
7. Valore orario
8. Bonus
9. Forfait
10. Importo totale
11. Azioni

**Filtri:**
1. Filtro insegnante
2. Filtro anno scolastico
3. Filtro mese
4. Filtro importo

**TOTALE: 15 DEV UNIT**

### UI Stampa (1 tipo PDF + 12 campi)

**Tipo PDF:**
1. Prospetto conto orario

**Campi per PDF (12 campi):**
1. Dati insegnante
2. Anno scolastico
3. Ore totali
4. Dettaglio ore (regolari/recupero/supplenze)
5. Valore orario
6. Importo ore
7. Bonus
8. Forfait
9. Importo totale
10. Rate pagamenti
11. Note
12. Firma

**TOTALE: 1 + 12 = 13 DEV UNIT**

**TOTALE CONTO ORARIO: 14 + 3 + 0 + 2 + 0 + 15 + 13 = 47 DEV UNIT**

---

## 14. GESTIONE SUPPLENZE (FASE 3 - Nuova Funzione)

### Campi Database

1. `id`
2. `lesson_id`
3. `original_teacher_id` (Insegnante originale)
4. `substitute_teacher_id` (Supplente)
5. `substitute_date` (Data supplenza)
6. `reason` (Motivo)
7. `status` (programmata/effettuata/cancellata)
8. `notes`
9. `created_at`
10. `updated_at`

**TOTALE: 10 DEV UNIT**

### Relazioni Database

1. Substitute → Lesson (many-to-one)
2. Substitute → Teacher (many-to-one, originale)
3. Substitute → Teacher (many-to-one, supplente)

**TOTALE: 3 DEV UNIT**

### CRUD Base (gestito da Lesson, no CRUD separato)

0 actions

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo (2 workflow)

1. Assegnazione supplente → trasferimento lezione
2. Aggiornamento conto orario (togli originale, aggiungi supplente)

**TOTALE: 2 DEV UNIT**

### UI Form (0)

Nessun form separato

**TOTALE: 0 DEV UNIT**

### UI Lista (9 colonne + 4 filtri)

**Colonne:**
1. Data lezione
2. Corso
3. Insegnante originale
4. Supplente
5. Motivo
6. Stato
7. Data supplenza
8. Note
9. Azioni

**Filtri:**
1. Filtro insegnante originale
2. Filtro supplente
3. Filtro data range
4. Filtro stato

**TOTALE: 13 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE SUPPLENZE: 10 + 3 + 0 + 2 + 0 + 13 + 0 = 28 DEV UNIT**

---

## 15. ATTIVITÀ EXTRA EVOLUTE (FASE 3 - Nuova Funzione)

### Campi Database Aggiuntivi (oltre base)

1. `schedule_dates` (Calendario 12 date/anno - JSON)
2. `level_filter` (Filtro livello)
3. `instrument_filter` (Filtro strumento)
4. `convocation_sent_date` (Data invio convocazione)

**TOTALE: 4 DEV UNIT aggiuntivi (oltre i 13 base = 17 totali)**

### Relazioni Database Aggiuntive

1. ExtraActivity → Attendance (one-to-many, presenze prove)

**TOTALE: 1 DEV UNIT aggiuntiva (oltre le 3 base = 4 totali)**

### CRUD Base

Già contato in base (7 DEV UNIT)

**TOTALE: 0 DEV UNIT aggiuntivi**

### Workflow Evolutivo (2 workflow)

1. Composizione automatica gruppi (filtri livello/strumento)
2. Convocazioni automatiche (12 date/anno)

**TOTALE: 2 DEV UNIT**

### UI Form (4 campi aggiuntivi)

Ogni campo = 1 DEV UNIT

**TOTALE: 4 DEV UNIT**

### UI Lista (stessa lista base)

Già contata in base

**TOTALE: 0 DEV UNIT aggiuntivi**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE ATTIVITÀ EXTRA EVOLUTE: 4 + 1 + 0 + 2 + 4 + 0 + 0 = 11 DEV UNIT aggiuntivi**

---

## 16. GESTIONE ESAMI (FASE 3 - Nuova Funzione)

### Campi Database

1. `id`
2. `name` (Nome esame)
3. `type` (interno/esterno)
4. `academic_year_id`
5. `exam_date` (Data esame)
6. `cost` (Costo iscrizione)
7. `level` (Livello ABRSM)
8. `instrument` (Strumento)
9. `notes`
10. `status` (programmato/effettuato/cancellato)
11. `created_at`
12. `updated_at`

**TOTALE: 12 DEV UNIT**

### Relazioni Database

1. Exam → AcademicYear (many-to-one)
2. Exam → Student (many-to-many, iscritti)
3. Exam → Teacher (many-to-many, esaminatori)

**TOTALE: 3 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Evolutivo (1 workflow)

1. Iscrizione esame → pagamento → svolgimento

**TOTALE: 1 DEV UNIT**

### UI Form (12 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 12 DEV UNIT**

### UI Lista (9 colonne + 5 filtri)

**Colonne:**
1. Nome esame
2. Tipo
3. Data
4. Livello
5. Strumento
6. Costo
7. N. iscritti
8. Stato
9. Azioni

**Filtri:**
1. Filtro tipo
2. Filtro livello
3. Filtro strumento
4. Filtro data range
5. Filtro stato

**TOTALE: 14 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE ESAMI: 12 + 3 + 7 + 1 + 12 + 14 + 0 = 49 DEV UNIT**

---

## 17. GENERAZIONE ATTESTATI DI FREQUENZA (FASE 3 - Nuova Funzione)

### Campi Database

1. `id`
2. `student_id`
3. `course_id`
4. `issue_date` (Data emissione)
5. `content` (Contenuto attestato)
6. `created_at`
7. `updated_at`

**TOTALE: 7 DEV UNIT**

### Relazioni Database

1. Certificate → Student (many-to-one)

**TOTALE: 1 DEV UNIT**

### CRUD Base (generazione automatica, no CRUD manuale)

0 actions

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo (1 workflow)

1. Generazione automatica da presenze/corsi frequentati

**TOTALE: 1 DEV UNIT**

### UI Form (0)

Nessun form (generazione automatica)

**TOTALE: 0 DEV UNIT**

### UI Lista (0)

Nessuna lista (visualizzato in studente)

**TOTALE: 0 DEV UNIT**

### UI Stampa (1 tipo PDF + 8 campi)

**Tipo PDF:**
1. Attestato frequenza

**Campi per PDF (8 campi):**
1. Numero attestato
2. Data emissione
3. Dati studente
4. Corso/i frequentato/i
5. Periodo frequenza
6. Presenze totali
7. Firma direttore
8. Timbro scuola

**TOTALE: 1 + 8 = 9 DEV UNIT**

**TOTALE ATTESTATI: 7 + 1 + 0 + 1 + 0 + 0 + 9 = 18 DEV UNIT**

---

## 18. COMUNICAZIONE MAIL/SMS EVOLUTA (FASE 3 - Nuova Funzione)

### Campi Database

1. `id`
2. `recipient_type` (studente/genitore)
3. `recipient_id`
4. `type` (email/SMS/WhatsApp)
5. `subject` (Oggetto)
6. `content` (Contenuto)
7. `sent_at` (Data invio)
8. `status` (inviata/consegnata/errore)
9. `template_id` (Template utilizzato)
10. `created_at`
11. `updated_at`

**TOTALE: 11 DEV UNIT**

### Relazioni Database

1. Communication → Student (many-to-one, opzionale)
2. Communication → Guardian (many-to-one, opzionale)
3. Communication → Template (many-to-one)

**TOTALE: 3 DEV UNIT**

### CRUD Base (invio automatico, no CRUD manuale)

0 actions

**TOTALE: 0 DEV UNIT**

### Workflow Evolutivo (2 workflow)

1. Selezione destinatari (filtri) → invio
2. Invio multi-canale (email + SMS)

**TOTALE: 2 DEV UNIT**

### UI Form (11 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 11 DEV UNIT**

### UI Lista (8 colonne + 5 filtri)

**Colonne:**
1. Destinatario
2. Tipo
3. Oggetto
4. Data invio
5. Stato
6. Template
7. Canale
8. Azioni

**Filtri:**
1. Filtro tipo comunicazione
2. Filtro canale
3. Filtro stato
4. Filtro data range
5. Filtro destinatario

**TOTALE: 13 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE COMUNICAZIONI: 11 + 3 + 0 + 2 + 11 + 13 + 0 = 40 DEV UNIT**

---

## RIEPILOGO DEV UNIT IPOTETICHE (Funzionalità Evolutive)

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
| **TOTALE** | **174** | **46** | **49** | **31** | **119** | **161** | **131** | **710** |

**TOTALE GENERALE DEV UNIT IPOTETICHE: 710 DEV UNIT**

---

## NOTE METODOLOGICHE

1. **Solo funzionalità evolutive**: Questo conteggio include SOLO le funzionalità nuove/evolutive richieste, NON la migrazione base.

2. **Workflow avanzati**: Include automazioni, generazione PDF, integrazioni, ecc.

3. **UI completa**: Include form complessi, liste avanzate, PDF generation.

4. **Stampa evolutiva**: Include generazione PDF contratti, fatture, attestati con tutti i campi.


