# DEV UNIT REALI - Trasformazione 1:1 ODS → Laravel

**Data:** Dicembre 2024  
**Scopo:** Calcolo DEV UNIT per la trasformazione diretta 1:1 dei file ODS in database Laravel relazionale ottimizzato  
**Metodologia:** Footility - ogni elemento implementabile = 1 DEV UNIT

> **IMPORTANTE:** Questo conteggio include SOLO la migrazione/import dei dati esistenti dagli ODS, senza funzionalità evolutive.

---

## METODOLOGIA APPLICATA

- **DEV_DB_campi**: ogni colonna ODS → campo database = 1 DEV UNIT
- **DEV_DB_relazioni**: ogni relazione FK necessaria per normalizzazione = 1 DEV UNIT
- **DEV_LOGIC_CRUD**: CRUD base per consultazione/modifica dati importati = 7 DEV UNIT (index, create, store, show, edit, update, destroy)
- **DEV_LOGIC_workflow**: workflow import/validazione dati = 1 DEV UNIT per workflow
- **DEV_UI_form**: ogni campo form per inserimento/modifica = 1 DEV UNIT
- **DEV_UI_lista**: ogni colonna lista + filtro = 1 DEV UNIT
- **DEV_UI_stampa**: ogni campo export/stampa = 1 DEV UNIT

---

## 1. STUDENTI (da db 2025-26 gestionale.ods)

### Colonne ODS → Campi Database

**Colonne ODS mappate:**
1. G (Cognome) → `last_name`
2. H (Nome) → `first_name`
3. v (cod. fiscale allievo) → `tax_code`
4. p (nato il) → `birth_date`
5. o (età) → `age` (calcolato, ma campo per cache)
6. q (minore) → `is_minor`
7. ~ (Indirizzo Allievo) → `address`
8.  (cap) → `postal_code`
9. **** (Città) → `city`
10. y (Cell 3/ allievo) → `phone`
11. } (Mail allievo) → `email`
12. F (data di arrivo) → `first_contact_date`
13. J (come è venuto a sapere di noi) → `source`
14. M (note prove e didattiche) → `notes_didattiche`
15. N (note varie) → `notes_varie`
16. O (data ultimo) → `data_ultimo_contatto`
17. E (stato) → `status`
18. **** (iscrizione) → `is_enrolled` (flag)
19. **** (nuovo iscritto) → `is_new_student` (flag)
20. **** (n. iscritto) → `enrollment_number`
21. **** (€ iscrizione) → `enrollment_fee`
22. **** (sconto) → `discount_percentage`
23. AI (livello) → `level` (riferimento StudentLevel)
24. AJ (livello str.) → `level_strumento` (riferimento StudentLevel)
25. AK (livello teoria) → `level_teoria` (riferimento StudentLevel)

**Campi aggiuntivi Laravel (standard):**
26. `id` (primary key)
27. `created_at`
28. `updated_at`
29. `deleted_at` (soft delete)

**TOTALE: 29 DEV UNIT (campi database)**

### Relazioni Database

1. Student → Guardian (many-to-many, pivot: `student_guardian`)
2. Student → Enrollment (one-to-many)
3. Student → Contract (one-to-many)
4. Student → Invoice (one-to-many)
5. Student → StudentAvailability (one-to-many)
6. Student → StudentLevel (one-to-many)
7. Student → AcademicYear (many-to-one)

**TOTALE: 7 DEV UNIT (relazioni)**

### CRUD Base

7 actions standard:
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import da ODS → validazione → inserimento database

**TOTALE: 1 DEV UNIT**

### UI Form (29 campi)

Ogni campo = 1 DEV UNIT (stessi 29 campi sopra)

**TOTALE: 29 DEV UNIT**

### UI Lista (15 colonne + 5 filtri)

**Colonne:**
1. Cognome
2. Nome
3. Cod. Fiscale
4. Data nascita
5. Età
6. Telefono
7. Email
8. Stato
9. Data arrivo
10. N. iscritto
11. Note didattiche
12. Note varie
13. Data ultimo contatto
14. Livello
15. Azioni

**Filtri:**
1. Ricerca testo
2. Filtro stato
3. Filtro anno scolastico
4. Filtro nuovo/vecchio
5. Filtro minore/maggiore

**TOTALE: 20 DEV UNIT**

### UI Stampa/Export (0)

Nessuna stampa per import base

**TOTALE: 0 DEV UNIT**

**TOTALE STUDENTI: 29 + 7 + 7 + 1 + 29 + 20 + 0 = 113 DEV UNIT**

---

## 2. GENITORI/TUTORI (da db 2025-26 gestionale.ods)

### Colonne ODS → Campi Database

**Colonne ODS mappate:**
1. r (cognome genitore 1) → `last_name` (Guardian 1)
2. s (nome genitore 1) → `first_name` (Guardian 1)
3. t (cognome genitore 2) → `last_name` (Guardian 2)
4. u (nome genitore 2) → `first_name` (Guardian 2)
5. w (Cell 1 /madre) → `phone_mobile_1` (Guardian 1)
6. x (Cell 2/padre) → `phone_mobile_2` (Guardian 2)
7. { (Mail 1) → `email_1` (Guardian 1)
8. | (Mail 2) → `email_2` (Guardian 2)

**Campi aggiuntivi per normalizzazione:**
9. `tax_code` (codice fiscale - da dedurre o richiedere)
10. `relationship_type` (genitore/tutore)
11. `is_primary` (flag genitore principale)
12. `is_billing_contact` (flag contatto fatturazione)
13. `address` (indirizzo - da dedurre da studente o separato)
14. `postal_code`
15. `city`
16. `phone_home` (telefono casa)
17. `phone_work` (telefono lavoro)
18. `privacy_consent` (consenso privacy)

**Campi Laravel standard:**
19. `id`
20. `created_at`
21. `updated_at`
22. `deleted_at`

**TOTALE: 22 DEV UNIT (campi database)**

### Relazioni Database

1. Guardian → Student (many-to-many, pivot: `student_guardian`)
2. Guardian → Communication (one-to-many)

**TOTALE: 2 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import da ODS → deduzione genitore 1/2 → inserimento

**TOTALE: 1 DEV UNIT**

### UI Form (22 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 22 DEV UNIT**

### UI Lista (12 colonne + 4 filtri)

**Colonne:**
1. Cognome
2. Nome
3. Cod. Fiscale
4. Tipo relazione
5. Telefono principale
6. Email principale
7. Indirizzo
8. Genitore principale
9. Contatto fatturazione
10. N. studenti collegati
11. Data ultimo contatto
12. Azioni

**Filtri:**
1. Ricerca testo
2. Filtro tipo relazione
3. Filtro genitore principale
4. Filtro contatto fatturazione

**TOTALE: 16 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE GENITORI: 22 + 2 + 7 + 1 + 22 + 16 + 0 = 70 DEV UNIT**

---

## 3. DOCENTI (da dati lavoratori 25-26.ods)

### Colonne ODS → Campi Database

**Colonne ODS mappate:**
1. D (Cognome) → `last_name`
2. E (Nome) → `first_name`
3. K (nato il) → `birth_date`
4. M (cod. fisc.) → `tax_code`
5. N (P. IVA) → `vat_number`
6. O (Carta Identità) → `id_number`
7. P (data rilascio) → `id_issue_date`
8. Q (ente rilascio) → `id_issuer`
9. R (Domicilio Indirizzo) → `address`
10. S (domicilio cap) → `postal_code`
11. T (domicilio città) → `city`
12. U (Residenza Indirizzo) → `residence_address`
13. V (residenza cap) → `residence_postal_code`
14. W (residenza città) → `residence_city`
15. X (IBAN) → `iban`
16. Z (tel abitazione) → `phone_home`
17. [ (Cell 1) → `phone_mobile`
18. \ (E-mail 1) → `email`
19. B (ruolo) → `role` (socio/non socio)
20. H (inquadramento) → `employment_type`

**Campi aggiuntivi:**
21. `hourly_rate` (compenso orario base)
22. `notes_contrattuali` (note contrattuali)

**Campi Laravel standard:**
23. `id`
24. `created_at`
25. `updated_at`
26. `deleted_at`

**TOTALE: 26 DEV UNIT (campi database)**

### Relazioni Database

1. Teacher → Course (one-to-many)
2. Teacher → Lesson (one-to-many)
3. Teacher → TeacherPayment (one-to-many)

**TOTALE: 3 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import da ODS → validazione → inserimento

**TOTALE: 1 DEV UNIT**

### UI Form (26 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 26 DEV UNIT**

### UI Lista (14 colonne + 4 filtri)

**Colonne:**
1. Cognome
2. Nome
3. Cod. Fiscale
4. P. IVA
5. Ruolo
6. Inquadramento
7. IBAN
8. Telefono
9. Email
10. Compenso orario
11. N. corsi assegnati
12. N. lezioni/mese
13. Note contrattuali
14. Azioni

**Filtri:**
1. Ricerca testo
2. Filtro ruolo
3. Filtro inquadramento
4. Filtro anno scolastico

**TOTALE: 18 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE DOCENTI: 26 + 3 + 7 + 1 + 26 + 18 + 0 = 81 DEV UNIT**

---

## 4. ANNO SCOLASTICO (da deduzione ODS)

### Campi Database

1. `id`
2. `name` (es. "2024-2025")
3. `start_date` (1 settembre)
4. `end_date` (31 agosto)
5. `is_active` (stato)
6. `year` (anno riferimento)
7. `created_at`
8. `updated_at`

**TOTALE: 8 DEV UNIT**

### Relazioni Database

1. AcademicYear → Student (one-to-many)
2. AcademicYear → Enrollment (one-to-many)
3. AcademicYear → Contract (one-to-many)
4. AcademicYear → Invoice (one-to-many)
5. AcademicYear → Lesson (one-to-many)

**TOTALE: 5 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Deduzione da date corsi → creazione anno

**TOTALE: 1 DEV UNIT**

### UI Form (8 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 8 DEV UNIT**

### UI Lista (6 colonne + 2 filtri)

**Colonne:**
1. Nome
2. Data inizio
3. Data fine
4. Anno riferimento
5. Stato
6. Azioni

**Filtri:**
1. Filtro stato
2. Switch anno corrente

**TOTALE: 8 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE ANNO SCOLASTICO: 8 + 5 + 7 + 1 + 8 + 8 + 0 = 37 DEV UNIT**

---

## 5. CORSI E ISCRIZIONI (da db 2025-26 gestionale.ods)

### Colonne ODS → Campi Database

**Per Enrollment (Corso 1, 2, 3 per studente):**
1. **** (Corso 1) → `course_id` (riferimento)
2. **** (Docente strumento/canto) → `teacher_id` (riferimento)
3. **** (Aula strumento/canto) → `classroom_id` (riferimento)
4. **** (giorno strum) → `day_of_week`
5. **** (ora strumento/canto) → `time_start`
6. **** (docente lab) → `lab_teacher_id` (riferimento)
7. **** (giorno lab) → `lab_day`
8. **** (ora lab) → `lab_time`
9. **** (n. sett/anno) → `weeks_per_year`
10. **** (data inizio corso 1) → `start_date`
11. **** (sigla corso 1) → `course.code` (riferimento)
12. **** (descrizione corso 1) → `course.description` (riferimento)
13. **** (tipologia corso 1) → `course_type_id` (riferimento)
14. **** (€ lez) → `price_per_lesson`
15. **** (€ tot anno corso 1) → `total_amount`

**Rate (per ogni corso):**
16. **** (sett 1° rata corso 1) → `installment_1_weeks`
17. **** (€ tot 1° rata corso 1) → `installment_1_amount`
18. **** (sett 2° rata corso 1) → `installment_2_weeks`
19. **** (€ tot 2° rata corso 1) → `installment_2_amount`
20. **** (sett 3° rata corso 1) → `installment_3_weeks`
21. **** (€ tot 3° rata corso 1) → `installment_3_amount`

**Campi aggiuntivi:**
22. `student_id` (riferimento)
23. `end_date` (data fine)
24. `status` (attiva/sospesa/chiusa)
25. `notes`

**Campi Laravel standard:**
26. `id`
27. `created_at`
28. `updated_at`
29. `deleted_at`

**TOTALE: 29 DEV UNIT (campi database)**

### Relazioni Database

1. Enrollment → Student (many-to-one)
2. Enrollment → Course (many-to-one)
3. Enrollment → Teacher (many-to-one)
4. Enrollment → Classroom (many-to-one)
5. Course → CourseType (many-to-one)

**TOTALE: 5 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import da ODS → creazione Course se non esiste → creazione Enrollment

**TOTALE: 1 DEV UNIT**

### UI Form (29 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 29 DEV UNIT**

### UI Lista (12 colonne + 5 filtri)

**Colonne:**
1. Studente
2. Corso
3. Data inizio
4. Data fine
5. Settimane
6. Lezioni
7. Importo totale
8. Stato
9. Giorno
10. Orario
11. Note
12. Azioni

**Filtri:**
1. Ricerca studente
2. Filtro corso
3. Filtro stato
4. Filtro anno scolastico
5. Filtro data inizio

**TOTALE: 17 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE CORSI/ISCRIZIONI: 29 + 5 + 7 + 1 + 29 + 17 + 0 = 88 DEV UNIT**

---

## 6. CALENDARIO LEZIONI (da Calendario 2025-26.ods)

### Campi Database

1. `id`
2. `academic_year_id`
3. `date` (Data)
4. `type` (giornata attiva/sospensione/festività)
5. `notes`
6. `created_at`
7. `updated_at`

**TOTALE: 7 DEV UNIT**

### Relazioni Database

1. Calendar → AcademicYear (many-to-one)
2. Calendar → Lesson (one-to-many)

**TOTALE: 2 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import da ODS → inserimento date

**TOTALE: 1 DEV UNIT**

### UI Form (7 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 7 DEV UNIT**

### UI Lista (6 colonne + 4 filtri)

**Colonne:**
1. Data
2. Tipo
3. Note
4. Anno scolastico
5. Giorno settimana
6. Azioni

**Filtri:**
1. Filtro anno scolastico
2. Filtro tipo
3. Filtro mese
4. Filtro data range

**TOTALE: 10 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE CALENDARIO: 7 + 2 + 7 + 1 + 7 + 10 + 0 = 34 DEV UNIT**

---

## 7. CONTRATTI STORICI (da Db Contratti 25-26.ods)

### Colonne ODS → Campi Database

**Colonne ODS mappate:**
1. A (codice contratto) → `contract_number`
2. B (stato) → `status`
3. C (Cognome) → `student.last_name` (riferimento)
4. D (Nome) → `student.first_name` (riferimento)
5. E (data di nascita) → `student.birth_date` (riferimento)
6. U (data inizio corso 1) → `start_date`
7. V (Corso 1) → `courses` (riferimento)
8. W (descrizione) → `terms`
9. X (tipologia) → `type`
10. Y (1 rata) → `installment_1_amount`
11. Z (2 rata) → `installment_2_amount`
12. [ (3 rata) → `installment_3_amount`
13. \ (tot corso1) → `total_course_1`
14. p (Totale anno corsi) → `total_amount`
15. q (Orchestra 1) → `orchestra` (riferimento)
16. r (costo orch 1) → `orchestra_cost`
17. w (data invio/consegna) → `sent_date`
18. x (data invio sollecito) → `reminder_sent_date`
19. y (data ritorno) → `signed_date`
20. z (privacy) → `privacy_consent`
21. { (note) → `notes`

**Campi aggiuntivi:**
22. `student_id` (riferimento)
23. `academic_year_id` (riferimento)

**Campi Laravel standard:**
24. `id`
25. `created_at`
26. `updated_at`
27. `deleted_at`

**TOTALE: 27 DEV UNIT (campi database)**

### Relazioni Database

1. Contract → Student (many-to-one)
2. Contract → AcademicYear (many-to-one)
3. Contract → Course (many-to-many, pivot)

**TOTALE: 3 DEV UNIT**

### CRUD Base (solo lettura per storici)

1. index (solo visualizzazione)

**TOTALE: 1 DEV UNIT**

### Workflow Import

1. Import da ODS → matching studente → inserimento

**TOTALE: 1 DEV UNIT**

### UI Form (0)

Nessun form (solo visualizzazione)

**TOTALE: 0 DEV UNIT**

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

### UI Stampa (0)

Nessuna stampa (solo consultazione)

**TOTALE: 0 DEV UNIT**

**TOTALE CONTRATTI STORICI: 27 + 3 + 1 + 1 + 0 + 16 + 0 = 48 DEV UNIT**

---

## 8. FATTURE STORICHE (da Db Contabile 2025-26.ods)

### Campi Database (stimati da struttura contabile)

1. `id`
2. `invoice_number`
3. `student_id`
4. `academic_year_id`
5. `issue_date`
6. `due_date`
7. `total_amount`
8. `paid_amount`
9. `status`
10. `notes`
11. `created_at`
12. `updated_at`

**TOTALE: 12 DEV UNIT**

### Relazioni Database

1. Invoice → Student (many-to-one)
2. Invoice → AcademicYear (many-to-one)
3. Invoice → Payment (one-to-many)

**TOTALE: 3 DEV UNIT**

### CRUD Base (solo lettura)

1. index

**TOTALE: 1 DEV UNIT**

### Workflow Import

1. Import da ODS → inserimento

**TOTALE: 1 DEV UNIT**

### UI Form (0)

Nessun form

**TOTALE: 0 DEV UNIT**

### UI Lista (10 colonne + 5 filtri)

**Colonne:**
1. Numero fattura
2. Studente
3. Data emissione
4. Data scadenza
5. Importo totale
6. Importo pagato
7. Residuo
8. Stato
9. Anno scolastico
10. Azioni

**Filtri:**
1. Ricerca studente/numero
2. Filtro stato
3. Filtro scadute
4. Filtro anno scolastico
5. Filtro data range

**TOTALE: 15 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FATTURE STORICHE: 12 + 3 + 1 + 1 + 0 + 15 + 0 = 32 DEV UNIT**

---

## 9. PAGAMENTI STORICI (da Db Contabile 2025-26.ods)

### Campi Database

1. `id`
2. `invoice_id` (riferimento)
3. `amount` (importo pagato)
4. `payment_date` (data pagamento)
5. `payment_method` (metodo)
6. `reference` (riferimento)
7. `notes`
8. `created_at`
9. `updated_at`

**TOTALE: 9 DEV UNIT**

### Relazioni Database

1. Payment → Invoice (many-to-one)
2. Payment → PaymentPlan (many-to-one, opzionale)

**TOTALE: 2 DEV UNIT**

### CRUD Base (solo lettura)

1. index

**TOTALE: 1 DEV UNIT**

### Workflow Import

1. Import da ODS → matching fattura → inserimento

**TOTALE: 1 DEV UNIT**

### UI Form (0)

Nessun form

**TOTALE: 0 DEV UNIT**

### UI Lista (8 colonne + 4 filtri)

**Colonne:**
1. Fattura
2. Studente
3. Importo
4. Data pagamento
5. Metodo
6. Riferimento
7. Note
8. Azioni

**Filtri:**
1. Ricerca fattura/studente
2. Filtro metodo pagamento
3. Filtro data range
4. Filtro anno scolastico

**TOTALE: 12 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE PAGAMENTI STORICI: 9 + 2 + 1 + 1 + 0 + 12 + 0 = 25 DEV UNIT**

---

## 10. ATTIVITÀ EXTRA BASE (da db 2025-26 gestionale.ods)

### Colonne ODS → Campi Database

**Colonne ODS mappate:**
1. h (Orch 1) → `name` (ExtraActivity)
2. i (PYO) → `name` (ExtraActivity)
3. k (coro) → `name` (ExtraActivity)
4. f (musica di insieme) → `type` (flag)

**Campi aggiuntivi:**
5. `id`
6. `academic_year_id`
7. `cost`
8. `max_participants`
9. `current_participants`
10. `status`
11. `notes`
12. `created_at`
13. `updated_at`

**TOTALE: 13 DEV UNIT**

### Relazioni Database

1. ExtraActivity → AcademicYear (many-to-one)
2. ExtraActivity → ExtraActivityEnrollment (one-to-many)
3. ExtraActivity → Student (many-to-many, pivot)

**TOTALE: 3 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import da ODS → creazione attività → collegamento studenti

**TOTALE: 1 DEV UNIT**

### UI Form (13 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 13 DEV UNIT**

### UI Lista (10 colonne + 4 filtri)

**Colonne:**
1. Nome
2. Tipo
3. Anno scolastico
4. Costo
5. Partecipanti (attuali/massimi)
6. Date programmate
7. Filtri composizione
8. Stato
9. Note
10. Azioni

**Filtri:**
1. Filtro tipo
2. Filtro anno scolastico
3. Filtro stato
4. Ricerca nome

**TOTALE: 14 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE ATTIVITÀ EXTRA: 13 + 3 + 7 + 1 + 13 + 14 + 0 = 51 DEV UNIT**

---

## 11. REGISTRO PRESENZE MINIMO (da struttura base)

### Campi Database

1. `id`
2. `lesson_id` (riferimento)
3. `student_id` (riferimento)
4. `is_present` (Presente sì/no)
5. `registered_at` (Data registrazione)
6. `registered_by` (Registrato da)
7. `created_at`
8. `updated_at`

**TOTALE: 8 DEV UNIT**

### Relazioni Database

1. Attendance → Lesson (many-to-one)
2. Attendance → Student (many-to-one)
3. Attendance → User (many-to-one, registrato da)

**TOTALE: 3 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import presenze da fogli cartacei (se disponibili)

**TOTALE: 1 DEV UNIT**

### UI Form (8 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 8 DEV UNIT**

### UI Lista (8 colonne + 5 filtri)

**Colonne:**
1. Data lezione
2. Corso
3. Studente
4. Presente
5. Data registrazione
6. Registrato da
7. Note
8. Azioni

**Filtri:**
1. Filtro studente
2. Filtro corso
3. Filtro data range
4. Filtro presente/assente
5. Filtro insegnante

**TOTALE: 13 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE PRESENZE: 8 + 3 + 7 + 1 + 8 + 13 + 0 = 40 DEV UNIT**

---

## 12. NOTE OPERATIVE (da struttura base)

### Campi Database

1. `id`
2. `student_id` (opzionale)
3. `guardian_id` (opzionale)
4. `note_type` (telefonata/email/colloquio)
5. `content` (Contenuto nota)
6. `created_at`
7. `updated_at`

**TOTALE: 7 DEV UNIT**

### Relazioni Database

1. Note → Student (many-to-one, opzionale)
2. Note → Guardian (many-to-one, opzionale)

**TOTALE: 2 DEV UNIT**

### CRUD Base (note inline, no CRUD separato)

0 actions (gestite inline in form studenti/genitori)

**TOTALE: 0 DEV UNIT**

### Workflow Import

0 workflow

**TOTALE: 0 DEV UNIT**

### UI Form (7 campi - inline)

Ogni campo = 1 DEV UNIT

**TOTALE: 7 DEV UNIT**

### UI Lista (0)

Nessuna lista (visualizzato inline)

**TOTALE: 0 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE NOTE: 7 + 2 + 0 + 0 + 7 + 0 + 0 = 16 DEV UNIT**

---

## 13. STRUMENTI (da db 2025-26 gestionale.ods)

### Colonne ODS → Campi Database

**Colonne ODS mappate:**
1. Y (fornitore strumento) → `supplier_id` (riferimento)
2. Z (noleggio/proprietà) → `rental_type`
3. [ (provenienza) → `source`
4. \ (tipo) → `type`
5. ] (marca) → `brand`
6. ^ (mod) → `model`
7. _ (misura) → `size`
8. ` (cod) → `serial_number`
9. AG (Da cambiare) → `needs_replacement` (flag)
10. AH (Note strumento) → `notes`

**Campi aggiuntivi:**
11. `id`
12. `code` (es. "VL 20")
13. `purchase_date`
14. `purchase_price`
15. `current_value`
16. `status` (disponibile/noleggiato/venduto)
17. `student_id` (riferimento noleggio)
18. `created_at`
19. `updated_at`

**TOTALE: 19 DEV UNIT**

### Relazioni Database

1. Instrument → Supplier (many-to-one)
2. Instrument → Student (many-to-one, noleggio)
3. Instrument → InstrumentRental (one-to-many)

**TOTALE: 3 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import da ODS → inserimento

**TOTALE: 1 DEV UNIT**

### UI Form (19 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 19 DEV UNIT**

### UI Lista (12 colonne + 4 filtri)

**Colonne:**
1. Codice
2. Tipo
3. Marca
4. Modello
5. Misura
6. Fornitore
7. Stato
8. Noleggiato a
9. Valore acquisto
10. Valore attuale
11. Note
12. Azioni

**Filtri:**
1. Ricerca codice/tipo
2. Filtro stato
3. Filtro fornitore
4. Filtro noleggio

**TOTALE: 16 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE STRUMENTI: 19 + 3 + 7 + 1 + 19 + 16 + 0 = 65 DEV UNIT**

---

## 14. DISPONIBILITÀ STUDENTI (da Anagrafica e disponibilità a.s. 2025_26.xlsx)

### Campi Database

1. `id`
2. `student_id`
3. `monday` (lu)
4. `tuesday` (ma)
5. `wednesday` (me)
6. `thursday` (gio)
7. `friday` (ve)
8. `saturday` (sab)
9. `notes` (note disponibilità)
10. `created_at`
11. `updated_at`

**TOTALE: 11 DEV UNIT**

### Relazioni Database

1. StudentAvailability → Student (many-to-one)

**TOTALE: 1 DEV UNIT**

### CRUD Base

7 actions standard

**TOTALE: 7 DEV UNIT**

### Workflow Import

1. Import da Excel → inserimento

**TOTALE: 1 DEV UNIT**

### UI Form (11 campi)

Ogni campo = 1 DEV UNIT

**TOTALE: 11 DEV UNIT**

### UI Lista (8 colonne + 3 filtri)

**Colonne:**
1. Studente
2. Lunedì
3. Martedì
4. Mercoledì
5. Giovedì
6. Venerdì
7. Sabato
8. Azioni

**Filtri:**
1. Ricerca studente
2. Filtro giorno
3. Filtro disponibilità

**TOTALE: 11 DEV UNIT**

### UI Stampa (0)

Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE DISPONIBILITÀ: 11 + 1 + 7 + 1 + 11 + 11 + 0 = 42 DEV UNIT**

---

## RIEPILOGO DEV UNIT REALI (Trasformazione 1:1 ODS → Laravel)

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
| **TOTALE** | **226** | **43** | **67** | **13** | **191** | **190** | **0** | **730** |

**TOTALE GENERALE DEV UNIT REALI: 730 DEV UNIT**

---

## NOTE METODOLOGICHE

1. **Solo trasformazione 1:1**: Questo conteggio include SOLO la migrazione dei dati esistenti dagli ODS in database Laravel relazionale ottimizzato.

2. **Nessuna funzionalità evolutiva**: Non include workflow avanzati, automazioni, PDF generation, integrazioni esterne, ecc.

3. **CRUD base**: Include solo le operazioni base per consultare e modificare i dati importati.

4. **UI minimale**: Include solo form e liste necessarie per visualizzare/modificare i dati importati, senza funzionalità avanzate.

5. **Nessuna stampa**: Per import base non servono PDF o stampe, solo visualizzazione dati.


