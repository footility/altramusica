# Analisi ODS - Estrazione Manuale DEV UNIT per Footility

**Data:** Dicembre 2024  
**Obiettivo:** Analizzare gli ODS nel dettaglio e calcolare manualmente le DEV UNIT per ogni attività/funzionalità presente  
**Destinazione:** Inserimento in Footility per generazione preventivi (bilanciamento DEV UNIT + COSMIC)

---

## METODOLOGIA DEV UNIT

Per ogni attività/funzionalità presente negli ODS, calcolare:

- **DEV_DB_campi:** Ogni colonna ODS → campo database = 1 DEV UNIT
- **DEV_DB_relazioni:** Ogni relazione FK necessaria = 1 DEV UNIT
- **DEV_LOGIC_CRUD:** CRUD base = 7 DEV UNIT (index, create, store, show, edit, update, destroy)
- **DEV_LOGIC_workflow:** Workflow import/validazione = 1 DEV UNIT
- **DEV_UI_form:** Ogni campo form = 1 DEV UNIT
- **DEV_UI_lista:** Ogni colonna lista + filtro = 1 DEV UNIT
- **DEV_UI_stampa:** Ogni campo export/stampa = 1 DEV UNIT (se necessario)

---

## FILE ODS DA ANALIZZARE

1. **`db 2025-26 gestionale.ods`** (file principale)
2. **`Db Contratti 25-26.ods`**
3. **`Db Contabile 2025-26.ods`**
4. **`Db Accessori 2025-26.ods`**
5. **`Calendario 2025-26.ods`**
6. **`dati lavoratori 25-26.ods`**

---

## ANALISI FILE PRINCIPALE: `db 2025-26 gestionale.ods`

### Foglio: `dati`
- **Righe:** 485
- **Colonne totali:** 1024
- **Colonne con header:** ~200+

### ATTIVITÀ 1: STUDENTI

**Colonne ODS identificate:**
- A: Richiesta pagamento
- B: Pagato
- C: Conto orario
- D: Contratto
- E: Stato
- F: Data di arrivo
- G: Cognome
- H: Nome
- I: Info
- J: Come è venuto a sapere di noi
- K: A che corso sei interessato?
- L: Ti piacerebbe provare anche altri strumenti?
- M: Note prove e didattiche
- N: Note varie
- O: Data ultimo
- P: Disponibilità
- Q: Lu (Lunedì)
- R: Ma (Martedì)
- S: Me (Mercoledì)
- T: Gio (Giovedì)
- U: Ve (Venerdì)
- V: Sab (Sabato)
- W: Lab (Laboratorio)
- X: Note disponibilità
- Y: Fornitore strumento
- Z: Noleggio/proprietà
- AA: Provenienza
- AB: Tipo strumento
- AC: Marca
- AD: Modello
- AE: Misura
- AF: Cod
- AG: Da cambiare
- AH: Note strumento
- AI: Livello
- AJ: Livello str.
- AK: Livello teoria
- AL: Musica di insieme
- AM: Conf (Conferma orchestra)
- AN: Orch 1
- AO: PYO
- AP: Conf coro
- AQ: Coro
- AR: Data ultima convocazione
- AS: Note orchestra
- AT: Dati
- AU: Età
- AV: Nato il
- AW: Minore
- AX: Cognome Genitore 1
- (altre colonne genitori...)

**Calcolo DEV UNIT STUDENTI:**

**DEV_DB_campi:**
- Campi anagrafici base: ~15 campi (cognome, nome, cod. fiscale, nato il, età, minore, data arrivo, stato, etc.)
- Campi disponibilità: 9 campi (P-X)
- Campi strumenti: 10 campi (Y-AH) - da collegare a tabella separata
- Campi livelli: 4 campi (AI-AL) - da collegare a tabella separata
- Campi orchestra/coro: 7 campi (AM-AS) - da collegare a tabella separata
- Campi genitori: 8 campi (AX-...) - da collegare a tabella separata
- Campi corsi/iscrizioni: 3 campi (I, K, L)
- Campi contratti/pagamenti: 4 campi (A-D) - da collegare a tabelle separate
- Campi Laravel standard: 4 campi (id, created_at, updated_at, deleted_at)
- **TOTALE: ~64 campi = 64 DEV UNIT**

**DEV_DB_relazioni:**
1. Student → AcademicYear (many-to-one)
2. Student → Guardian (many-to-many, pivot)
3. Student → Enrollment (one-to-many)
4. Student → Contract (one-to-many)
5. Student → Invoice (one-to-many)
6. Student → StudentAvailability (one-to-many)
7. Student → StudentLevel (one-to-many)
8. Student → InstrumentRental (one-to-many)
9. Student → ExtraActivityEnrollment (one-to-many)
10. Student → Communication (one-to-many)
- **TOTALE: 10 relazioni = 10 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions standard = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import da ODS = 1 DEV UNIT
- Validazione dati = 1 DEV UNIT
- **TOTALE: 2 DEV UNIT**

**DEV_UI_form:**
- Form anagrafica base: ~15 campi
- Form disponibilità: 9 campi
- Form livelli: 4 campi
- Form orchestra/coro: 7 campi
- **TOTALE: ~35 campi form = 35 DEV UNIT**

**DEV_UI_lista:**
- Colonne lista: ~20 colonne (cognome, nome, cod. fiscale, età, telefono, email, stato, data arrivo, livello, etc.)
- Filtri: ~8 filtri (ricerca, stato, anno scolastico, nuovo/vecchio, minore/maggiore, livello, disponibilità, orchestra/coro)
- **TOTALE: 28 DEV UNIT**

**DEV_UI_stampa:**
- Export base: 0 DEV UNIT (Fase 1 non include stampa)

**TOTALE ATTIVITÀ STUDENTI: 64 + 10 + 7 + 2 + 35 + 28 + 0 = 146 DEV UNIT**

---

### ATTIVITÀ 2: GENITORI/TUTORI

**Colonne ODS identificate:**
- AX: Cognome Genitore 1
- (altre colonne genitori...)

**Calcolo DEV UNIT GENITORI:**

**DEV_DB_campi:**
- Campi anagrafici: ~15 campi (cognome, nome, cod. fiscale, telefono casa, cell 1-4, email 1-3, indirizzo, città, cap, paese, consenso privacy)
- Campi relazione: 3 campi (tipo relazione, genitore principale, contatto fatturazione) - in pivot
- Campi Laravel standard: 4 campi
- **TOTALE: ~22 campi = 22 DEV UNIT**

**DEV_DB_relazioni:**
1. Guardian → Student (many-to-many, pivot)
2. Guardian → Communication (one-to-many)
- **TOTALE: 2 relazioni = 2 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import da ODS = 1 DEV UNIT

**DEV_UI_form:**
- ~22 campi form = 22 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~12 colonne
- Filtri: ~4 filtri
- **TOTALE: 16 DEV UNIT**

**TOTALE ATTIVITÀ GENITORI: 22 + 2 + 7 + 1 + 22 + 16 + 0 = 70 DEV UNIT**

---

### ATTIVITÀ 3: DISPONIBILITÀ STUDENTI

**Colonne ODS:**
- P: Disponibilità
- Q: Lu (Lunedì)
- R: Ma (Martedì)
- S: Me (Mercoledì)
- T: Gio (Giovedì)
- U: Ve (Venerdì)
- V: Sab (Sabato)
- W: Lab (Laboratorio)
- X: Note disponibilità

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~10 campi (student_id, disponibilità, lun-mar-mer-gio-ven-sab, lab, note)
- Campi Laravel: 3 campi
- **TOTALE: 13 campi = 13 DEV UNIT**

**DEV_DB_relazioni:**
1. StudentAvailability → Student (many-to-one)
- **TOTALE: 1 relazione = 1 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~10 campi = 10 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~11 colonne
- Filtri: ~3 filtri
- **TOTALE: 14 DEV UNIT**

**TOTALE ATTIVITÀ DISPONIBILITÀ: 13 + 1 + 7 + 1 + 10 + 14 + 0 = 46 DEV UNIT**

---

### ATTIVITÀ 4: STRUMENTI (collegati a Studenti)

**Colonne ODS:**
- Y: Fornitore strumento
- Z: Noleggio/proprietà
- AA: Provenienza
- AB: Tipo
- AC: Marca
- AD: Modello
- AE: Misura
- AF: Cod
- AG: Da cambiare
- AH: Note strumento

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~15 campi (student_id, fornitore, noleggio/proprietà, provenienza, tipo, marca, modello, misura, cod, da_cambiare, note, data_inizio, data_fine, costo)
- Campi Laravel: 3 campi
- **TOTALE: 18 campi = 18 DEV UNIT**

**DEV_DB_relazioni:**
1. InstrumentRental → Student (many-to-one)
2. InstrumentRental → Instrument (many-to-one)
- **TOTALE: 2 relazioni = 2 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~15 campi = 15 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~12 colonne
- Filtri: ~4 filtri
- **TOTALE: 16 DEV UNIT**

**TOTALE ATTIVITÀ STRUMENTI STUDENTI: 18 + 2 + 7 + 1 + 15 + 16 + 0 = 59 DEV UNIT**

---

### ATTIVITÀ 5: LIVELLI STUDENTI

**Colonne ODS:**
- AI: Livello
- AJ: Livello str.
- AK: Livello teoria
- AL: Musica di insieme

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~8 campi (student_id, livello, livello_strumento, livello_teoria, musica_insieme, data_assegnazione, data_aggiornamento, note)
- Campi Laravel: 3 campi
- **TOTALE: 11 campi = 11 DEV UNIT**

**DEV_DB_relazioni:**
1. StudentLevel → Student (many-to-one)
- **TOTALE: 1 relazione = 1 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~8 campi = 8 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~10 colonne
- Filtri: ~3 filtri
- **TOTALE: 13 DEV UNIT**

**TOTALE ATTIVITÀ LIVELLI: 11 + 1 + 7 + 1 + 8 + 13 + 0 = 41 DEV UNIT**

---

### ATTIVITÀ 6: ORCHESTRA/CORO (collegati a Studenti)

**Colonne ODS:**
- AM: Conf (Conferma orchestra)
- AN: Orch 1
- AO: PYO
- AP: Conf coro
- AQ: Coro
- AR: Data ultima convocazione
- AS: Note orchestra

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~10 campi (student_id, conferma_orchestra, orchestra_1, pyo, conferma_coro, coro, data_ultima_convocazione, note, data_iscrizione)
- Campi Laravel: 3 campi
- **TOTALE: 13 campi = 13 DEV UNIT**

**DEV_DB_relazioni:**
1. ExtraActivityEnrollment → Student (many-to-one)
2. ExtraActivityEnrollment → ExtraActivity (many-to-one)
- **TOTALE: 2 relazioni = 2 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~10 campi = 10 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~11 colonne
- Filtri: ~3 filtri
- **TOTALE: 14 DEV UNIT**

**TOTALE ATTIVITÀ ORCHESTRA/CORO: 13 + 2 + 7 + 1 + 10 + 14 + 0 = 47 DEV UNIT**

---

## ANALISI FILE: `Db Contratti 25-26.ods`

**Righe:** 416  
**Colonne:** Multiple

### ATTIVITÀ 7: CONTRATTI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~25 campi (student_id, numero_contratto, data_contratto, data_inizio, data_fine, tipo_contratto, corso_id, prezzo, sconto, totale, stato, note, token, data_invio, data_firma, etc.)
- Campi Laravel: 3 campi
- **TOTALE: 28 campi = 28 DEV UNIT**

**DEV_DB_relazioni:**
1. Contract → Student (many-to-one)
2. Contract → Course (many-to-one)
3. Contract → Invoice (one-to-many)
- **TOTALE: 3 relazioni = 3 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT
- Workflow invio/firma = 1 DEV UNIT
- **TOTALE: 2 DEV UNIT**

**DEV_UI_form:**
- ~25 campi = 25 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~16 colonne
- Filtri: ~5 filtri
- **TOTALE: 21 DEV UNIT**

**TOTALE ATTIVITÀ CONTRATTI: 28 + 3 + 7 + 2 + 25 + 21 + 0 = 86 DEV UNIT**

---

## ANALISI FILE: `Db Contabile 2025-26.ods`

### ATTIVITÀ 8: FATTURE

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~15 campi (student_id, numero_fattura, data_fattura, scadenza, subtotale, sconto, iva, totale, stato, note, etc.)
- Campi Laravel: 3 campi
- **TOTALE: 18 campi = 18 DEV UNIT**

**DEV_DB_relazioni:**
1. Invoice → Student (many-to-one)
2. Invoice → InvoiceItem (one-to-many)
3. Invoice → Payment (one-to-many)
- **TOTALE: 3 relazioni = 3 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~15 campi = 15 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~15 colonne
- Filtri: ~5 filtri
- **TOTALE: 20 DEV UNIT**

**TOTALE ATTIVITÀ FATTURE: 18 + 3 + 7 + 1 + 15 + 20 + 0 = 64 DEV UNIT**

---

### ATTIVITÀ 9: PAGAMENTI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~10 campi (invoice_id, data_pagamento, importo, metodo_pagamento, riferimento, note)
- Campi Laravel: 3 campi
- **TOTALE: 13 campi = 13 DEV UNIT**

**DEV_DB_relazioni:**
1. Payment → Invoice (many-to-one)
- **TOTALE: 1 relazione = 1 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~10 campi = 10 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~12 colonne
- Filtri: ~4 filtri
- **TOTALE: 16 DEV UNIT**

**TOTALE ATTIVITÀ PAGAMENTI: 13 + 1 + 7 + 1 + 10 + 16 + 0 = 48 DEV UNIT**

---

### ATTIVITÀ 10: FATTURE ACCESSORI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~12 campi (student_id, tipo_accessorio, descrizione, quantità, prezzo_unitario, totale, data)
- Campi Laravel: 3 campi
- **TOTALE: 15 campi = 15 DEV UNIT**

**DEV_DB_relazioni:**
1. InvoiceItem → Invoice (many-to-one)
- **TOTALE: 1 relazione = 1 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~12 campi = 12 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~13 colonne
- Filtri: ~4 filtri
- **TOTALE: 17 DEV UNIT**

**TOTALE ATTIVITÀ FATTURE ACCESSORI: 15 + 1 + 7 + 1 + 12 + 17 + 0 = 53 DEV UNIT**

---

## ANALISI FILE: `Db Accessori 2025-26.ods`

### ATTIVITÀ 11: ESAMI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~12 campi (student_id, tipo_esame, data_esame, livello, risultato, note, esaminatore)
- Campi Laravel: 3 campi
- **TOTALE: 15 campi = 15 DEV UNIT**

**DEV_DB_relazioni:**
1. Exam → Student (many-to-one)
- **TOTALE: 1 relazione = 1 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~12 campi = 12 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~14 colonne
- Filtri: ~4 filtri
- **TOTALE: 18 DEV UNIT**

**TOTALE ATTIVITÀ ESAMI: 15 + 1 + 7 + 1 + 12 + 18 + 0 = 54 DEV UNIT**

---

### ATTIVITÀ 12: ACCESSORI 1-7

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~10 campi per accessorio × 7 = ~70 campi (ma normalizzati in tabella)
- Tabella normalizzata: ~12 campi (student_id, tipo_accessorio, descrizione, quantità, prezzo, data)
- Campi Laravel: 3 campi
- **TOTALE: 15 campi = 15 DEV UNIT**

**DEV_DB_relazioni:**
1. Accessory → Student (many-to-one)
- **TOTALE: 1 relazione = 1 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~12 campi = 12 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~13 colonne
- Filtri: ~4 filtri
- **TOTALE: 17 DEV UNIT**

**TOTALE ATTIVITÀ ACCESSORI: 15 + 1 + 7 + 1 + 12 + 17 + 0 = 53 DEV UNIT**

---

### ATTIVITÀ 13: LIBRI 1-15

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- Tabella normalizzata: ~12 campi (student_id, libro_id, quantità, data_distribuzione, prezzo, note)
- Campi Laravel: 3 campi
- **TOTALE: 15 campi = 15 DEV UNIT**

**DEV_DB_relazioni:**
1. BookDistribution → Student (many-to-one)
2. BookDistribution → Book (many-to-one)
- **TOTALE: 2 relazioni = 2 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~12 campi = 12 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~13 colonne
- Filtri: ~4 filtri
- **TOTALE: 17 DEV UNIT**

**TOTALE ATTIVITÀ LIBRI: 15 + 2 + 7 + 1 + 12 + 17 + 0 = 54 DEV UNIT**

---

## ANALISI FILE: `Calendario 2025-26.ods`

### ATTIVITÀ 14: CALENDARIO LEZIONI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~10 campi (data, corso_id, docente_id, ora_inizio, ora_fine, aula_id, note, sospesa)
- Campi Laravel: 3 campi
- **TOTALE: 13 campi = 13 DEV UNIT**

**DEV_DB_relazioni:**
1. CalendarLesson → Course (many-to-one)
2. CalendarLesson → Teacher (many-to-one)
3. CalendarLesson → Classroom (many-to-one)
- **TOTALE: 3 relazioni = 3 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT
- Generazione calendario = 1 DEV UNIT
- **TOTALE: 2 DEV UNIT**

**DEV_UI_form:**
- ~10 campi = 10 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~10 colonne
- Filtri: ~5 filtri
- **TOTALE: 15 DEV UNIT**

**TOTALE ATTIVITÀ CALENDARIO: 13 + 3 + 7 + 2 + 10 + 15 + 0 = 50 DEV UNIT**

---

### ATTIVITÀ 15: SOSPENSIONI CALENDARIO

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~6 campi (data_inizio, data_fine, motivo, note)
- Campi Laravel: 3 campi
- **TOTALE: 9 campi = 9 DEV UNIT**

**DEV_DB_relazioni:**
- 0 relazioni (tabella indipendente)
- **TOTALE: 0 relazioni**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~6 campi = 6 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~10 colonne
- Filtri: ~3 filtri
- **TOTALE: 13 DEV UNIT**

**TOTALE ATTIVITÀ SOSPENSIONI: 9 + 0 + 7 + 1 + 6 + 13 + 0 = 36 DEV UNIT**

---

## ANALISI FILE: `dati lavoratori 25-26.ods`

### ATTIVITÀ 16: DOCENTI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~25 campi (cognome, nome, cod. fiscale, p.iva, carta identità, indirizzo domicilio, indirizzo residenza, iban, telefono, cell, email, ruolo, inquadramento, compenso_orario, note)
- Campi Laravel: 3 campi
- **TOTALE: 28 campi = 28 DEV UNIT**

**DEV_DB_relazioni:**
1. Teacher → Course (one-to-many)
2. Teacher → Lesson (one-to-many)
3. Teacher → TeacherHour (one-to-many)
- **TOTALE: 3 relazioni = 3 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~25 campi = 25 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~18 colonne
- Filtri: ~4 filtri
- **TOTALE: 22 DEV UNIT**

**TOTALE ATTIVITÀ DOCENTI: 28 + 3 + 7 + 1 + 25 + 22 + 0 = 86 DEV UNIT**

---

### ATTIVITÀ 17: DISPONIBILITÀ DOCENTI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~10 campi (teacher_id, giorno_settimana, ora_inizio, ora_fine, disponibile, note)
- Campi Laravel: 3 campi
- **TOTALE: 13 campi = 13 DEV UNIT**

**DEV_DB_relazioni:**
1. TeacherAvailability → Teacher (many-to-one)
- **TOTALE: 1 relazione = 1 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~10 campi = 10 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~11 colonne
- Filtri: ~3 filtri
- **TOTALE: 14 DEV UNIT**

**TOTALE ATTIVITÀ DISPONIBILITÀ DOCENTI: 13 + 1 + 7 + 1 + 10 + 14 + 0 = 46 DEV UNIT**

---

### ATTIVITÀ 18: CONTRATTI DOCENTI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~15 campi (teacher_id, tipo_contratto, data_inizio, data_fine, compenso, note)
- Campi Laravel: 3 campi
- **TOTALE: 18 campi = 18 DEV UNIT**

**DEV_DB_relazioni:**
1. TeacherContract → Teacher (many-to-one)
- **TOTALE: 1 relazione = 1 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~15 campi = 15 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~13 colonne
- Filtri: ~4 filtri
- **TOTALE: 17 DEV UNIT**

**TOTALE ATTIVITÀ CONTRATTI DOCENTI: 18 + 1 + 7 + 1 + 15 + 17 + 0 = 59 DEV UNIT**

---

## ATTIVITÀ AGGIUNTIVE (da ODS o necessarie)

### ATTIVITÀ 19: CORSI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~15 campi (course_type_id, teacher_id, codice, nome, descrizione, data_inizio, data_fine, giorno_settimana, ora_inizio, ora_fine, max_studenti, prezzo_lezione, etc.)
- Campi Laravel: 3 campi
- **TOTALE: 18 campi = 18 DEV UNIT**

**DEV_DB_relazioni:**
1. Course → CourseType (many-to-one)
2. Course → Teacher (many-to-one)
3. Course → Enrollment (one-to-many)
- **TOTALE: 3 relazioni = 3 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~15 campi = 15 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~17 colonne
- Filtri: ~5 filtri
- **TOTALE: 22 DEV UNIT**

**TOTALE ATTIVITÀ CORSI: 18 + 3 + 7 + 1 + 15 + 22 + 0 = 66 DEV UNIT**

---

### ATTIVITÀ 20: ISCRIZIONI

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~12 campi (student_id, course_id, data_iscrizione, data_inizio, data_fine, prezzo, sconto, totale, stato, note)
- Campi Laravel: 3 campi
- **TOTALE: 15 campi = 15 DEV UNIT**

**DEV_DB_relazioni:**
1. Enrollment → Student (many-to-one)
2. Enrollment → Course (many-to-one)
- **TOTALE: 2 relazioni = 2 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~12 campi = 12 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~14 colonne
- Filtri: ~4 filtri
- **TOTALE: 18 DEV UNIT**

**TOTALE ATTIVITÀ ISCRIZIONI: 15 + 2 + 7 + 1 + 12 + 18 + 0 = 55 DEV UNIT**

---

### ATTIVITÀ 21: ANNO SCOLASTICO

**Calcolo DEV UNIT:**

**DEV_DB_campi:**
- ~8 campi (nome, data_inizio, data_fine, attivo, note)
- Campi Laravel: 3 campi
- **TOTALE: 11 campi = 11 DEV UNIT**

**DEV_DB_relazioni:**
1. AcademicYear → Student (one-to-many)
2. AcademicYear → Invoice (one-to-many)
- **TOTALE: 2 relazioni = 2 DEV UNIT**

**DEV_LOGIC_CRUD:**
- 7 actions = 7 DEV UNIT

**DEV_LOGIC_workflow:**
- Import = 1 DEV UNIT

**DEV_UI_form:**
- ~8 campi = 8 DEV UNIT

**DEV_UI_lista:**
- Colonne: ~8 colonne
- Filtri: ~2 filtri
- **TOTALE: 10 DEV UNIT**

**TOTALE ATTIVITÀ ANNO SCOLASTICO: 11 + 2 + 7 + 1 + 8 + 10 + 0 = 39 DEV UNIT**

---

## RIEPILOGO TOTALE DEV UNIT FASE 1

| # | Attività | DEV UNIT |
|---|----------|----------|
| 1 | Studenti | 146 |
| 2 | Genitori/Tutori | 70 |
| 3 | Disponibilità Studenti | 46 |
| 4 | Strumenti Studenti | 59 |
| 5 | Livelli Studenti | 41 |
| 6 | Orchestra/Coro | 47 |
| 7 | Contratti | 86 |
| 8 | Fatture | 64 |
| 9 | Pagamenti | 48 |
| 10 | Fatture Accessori | 53 |
| 11 | Esami | 54 |
| 12 | Accessori | 53 |
| 13 | Libri | 54 |
| 14 | Calendario Lezioni | 50 |
| 15 | Sospensioni Calendario | 36 |
| 16 | Docenti | 86 |
| 17 | Disponibilità Docenti | 46 |
| 18 | Contratti Docenti | 59 |
| 19 | Corsi | 66 |
| 20 | Iscrizioni | 55 |
| 21 | Anno Scolastico | 39 |
| **TOTALE FASE 1** | | **1.251 DEV UNIT** |

---

## PROSSIMI PASSI

1. ⏳ Verificare analisi ODS completa (potrebbero esserci altre colonne/attività)
2. ⏳ Preparare formato dati per inserimento in Footility
3. ⏳ Inserire attività + DEV UNIT in Footility
4. ⏳ Footility bilancerà con COSMIC per generare preventivi

---

**Documento creato:** Dicembre 2024  
**Status:** Analisi iniziale completata, da verificare con ODS reali
