# Analisi DEV UNITS - Macro Attività Altramusica

**Data:** Gennaio 2026  
**Obiettivo:** Definire attività macro atomiche con DEV UNITS realistiche per categoria (database, be_logic, ui_layout, ecc.)  
**Metodologia:** Analisi ODS + conversazioni + pattern da progetti simili (mscarichi, klabhouse, cactusboard, tastingnotes)

---

## PRINCIPI ORGANIZZAZIONE

1. **Attività macro atomiche:** Ogni attività descrive una funzionalità completa ma atomica (non troppo frammentata)
2. **DEV UNITS realistiche:** Calcolo per tutte le categorie, non solo database
3. **Laravel base:** Navigazioni standard, poco AJAX (logiche da controller a controller)
4. **Raggruppamento IA:** Analisi documentazione, ODS e conversazioni per raggruppare correttamente

---

## CATEGORIE DEV UNITS (Laravel)

- **`database`**: Campi migration (tabelle, colonne, indici)
- **`be_data`**: Campi fillable model (oppure `data`)
- **`data_relation`**: Relazioni Laravel (belongsTo, hasMany, hasOne, belongsToMany)
- **`be_logic`**: Controller actions (7 CRUD), metodi model, accessor, workflow
- **`ui_layout`**: Form inputs, variabili Blade, liste/tabelle
- **`fe_logic`**: JavaScript functions (minimo per Laravel base)

---

## FASE 1: MIGRAZIONE 1:1 ODS → LARAVEL NORMALIZZATO

### GRUPPO 1: INFRASTRUTTURA E ANAGRAFICHE BASE

#### Attività 1.1: Gestione Anno Scolastico/Esercizio
**Descrizione funzionale:** Sistema lavora su esercizio che coincide con anno scolastico (1 settembre - 31 agosto). Switch tra esercizi per visualizzare dati storici. Gestione preiscrizioni che si trasferiscono automaticamente.

**Campi ODS:** Da deduzione (non presente direttamente, ma necessario per contesto)

**DEV UNITS calcolate:**
- **database**: 8 campi (id, name, start_date, end_date, is_active, year, created_at, updated_at) = **8 DEV UNIT**
- **be_data**: 8 campi fillable = **8 DEV UNIT**
- **data_relation**: 5 relazioni (Student, Enrollment, Contract, Invoice, Lesson) = **5 DEV UNIT**
- **be_logic**: 7 CRUD actions + 2 metodi (switchActive, transferPreRegistrations) = **9 DEV UNIT**
- **ui_layout**: 8 campi form + 8 colonne lista + 3 filtri (stato, switch attivo, ricerca) = **19 DEV UNIT**
- **fe_logic**: 0 (Laravel base) = **0 DEV UNIT**

**TOTALE: 49 DEV UNIT**

---

#### Attività 1.2: Gestione Studenti
**Descrizione funzionale:** Gestione completa anagrafica studenti dal primo contatto fino a conversione in clienti. Include informazioni didattiche, personali, disponibilità, livelli, strumenti, orchestra/coro. Categorizzazione per esercizio (attivi, interessati, vecchi iscritti).

**Campi ODS principali:** Cognome, Nome, Cod. Fiscale, Nato il, Età, Minore, Data arrivo, Stato, Telefono, Email, Indirizzo, Note varie/didattiche, Disponibilità (Lun-Sab, Lab), Livelli (0-8 ABRSM), Strumento, Orchestra/Coro, Genitori (relazione)

**DEV UNITS calcolate:**
- **database**: ~35 campi (anagrafica base ~20 + disponibilità 7 + livelli 4 + note 4) = **35 DEV UNIT**
- **be_data**: 35 campi fillable = **35 DEV UNIT**
- **data_relation**: 10 relazioni (Guardian, Enrollment, Contract, Invoice, Availability, Level, InstrumentRental, ExtraActivityEnrollment, AcademicYear, Communication) = **10 DEV UNIT**
- **be_logic**: 7 CRUD + 3 metodi (convertToClient, calculateAge, getStatusLabel) + 1 workflow import = **11 DEV UNIT**
- **ui_layout**: 25 campi form (raggruppati in tabs/sezioni) + 18 colonne lista + 6 filtri (ricerca, stato, anno, nuovo/vecchio, minore/maggiore, livello) = **49 DEV UNIT**
- **fe_logic**: 1 (toggle disponibilità giorni) = **1 DEV UNIT**

**TOTALE: 141 DEV UNIT**

---

#### Attività 1.3: Gestione Genitori/Tutori
**Descrizione funzionale:** Conservazione informazioni genitori/tutori per contatti e transazioni finanziarie. Gestione privacy, anagrafica primo/secondo genitore, delega recupero minori.

**Campi ODS:** Cognome/Nome Genitore 1-2, Cell 1-4, Mail 1-3, Indirizzo, Città, CAP, Telefono casa, Tipo relazione, Genitore principale, Contatto fatturazione, Consenso privacy

**DEV UNITS calcolate:**
- **database**: ~18 campi = **18 DEV UNIT**
- **be_data**: 18 campi fillable = **18 DEV UNIT**
- **data_relation**: 2 relazioni (Student many-to-many, Communication one-to-many) = **2 DEV UNIT**
- **be_logic**: 7 CRUD + 2 metodi (isPrimary, isBillingContact) + 1 workflow import = **10 DEV UNIT**
- **ui_layout**: 18 campi form + 12 colonne lista + 4 filtri (ricerca, tipo relazione, principale, fatturazione) = **34 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 82 DEV UNIT**

---

#### Attività 1.4: Gestione Docenti
**Descrizione funzionale:** Informazioni anagrafiche docenti, contratti, assegnazioni corsi, accesso profili didattici. Categorizzazione soci/non soci, compensi differenziati base.

**Campi ODS (dati lavoratori):** Cognome, Nome, Cod. Fiscale, P. IVA, Carta Identità, Domicilio/Residenza, IBAN, Telefono, Cell, Email, Ruolo (socio/non socio), Inquadramento, Compenso orario base

**DEV UNITS calcolate:**
- **database**: ~22 campi = **22 DEV UNIT**
- **be_data**: 22 campi fillable = **22 DEV UNIT**
- **data_relation**: 3 relazioni (Course one-to-many, Lesson one-to-many, TeacherPayment one-to-many) = **3 DEV UNIT**
- **be_logic**: 7 CRUD + 3 metodi (isPartner, getHourlyRate, calculateMonthlyHours) + 1 workflow import = **11 DEV UNIT**
- **ui_layout**: 22 campi form + 16 colonne lista + 4 filtri (ricerca, ruolo, inquadramento, anno) = **42 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 100 DEV UNIT**

---

### GRUPPO 2: DISPONIBILITÀ E ORARI

#### Attività 1.5: Disponibilità Studenti
**Descrizione funzionale:** Gestione disponibilità oraria studenti per composizione orari. Giorni settimana (Lun-Sab) + laboratorio, note disponibilità.

**Campi ODS:** Disponibilità, Lun, Mar, Mer, Gio, Ven, Sab, Lab, Note

**DEV UNITS calcolate:**
- **database**: 10 campi (student_id, monday, tuesday, wednesday, thursday, friday, saturday, lab_available, notes, created_at, updated_at) = **11 DEV UNIT**
- **be_data**: 10 campi fillable = **10 DEV UNIT**
- **data_relation**: 1 relazione (Student many-to-one) = **1 DEV UNIT**
- **be_logic**: 7 CRUD + 1 metodo (getAvailableDays) + 1 workflow import = **9 DEV UNIT**
- **ui_layout**: 10 campi form (checkbox giorni + textarea note) + 10 colonne lista + 3 filtri (ricerca studente, giorno, disponibilità) = **23 DEV UNIT**
- **fe_logic**: 1 (toggle giorni disponibilità) = **1 DEV UNIT**

**TOTALE: 55 DEV UNIT**

---

#### Attività 1.6: Disponibilità Docenti
**Descrizione funzionale:** Gestione disponibilità oraria docenti per composizione orari corsi.

**DEV UNITS calcolate:**
- **database**: 10 campi (teacher_id, giorno_settimana, ora_inizio, ora_fine, disponibile, note, created_at, updated_at) = **10 DEV UNIT**
- **be_data**: 10 campi fillable = **10 DEV UNIT**
- **data_relation**: 1 relazione (Teacher many-to-one) = **1 DEV UNIT**
- **be_logic**: 7 CRUD + 1 metodo (isAvailableAt) + 1 workflow import = **9 DEV UNIT**
- **ui_layout**: 10 campi form + 10 colonne lista + 3 filtri (ricerca docente, giorno, disponibilità) = **23 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 53 DEV UNIT**

---

#### Attività 1.7: Calendario Lezioni
**Descrizione funzionale:** Calendario lezioni con giornate attive e sospensioni. Calcolo automatico settimane di lezione in base a data inizio e calendario.

**Campi ODS (Calendario 2025-26.ods):** Data, Tipo (attiva/sospensione/festività), Note

**DEV UNITS calcolate:**
- **database**: 8 campi (id, academic_year_id, date, type, notes, created_at, updated_at, is_holiday) = **8 DEV UNIT**
- **be_data**: 8 campi fillable = **8 DEV UNIT**
- **data_relation**: 2 relazioni (AcademicYear many-to-one, Lesson one-to-many) = **2 DEV UNIT**
- **be_logic**: 7 CRUD + 3 metodi (calculateWeeks, getActiveDays, getSuspensions) + 2 workflow (import, generazione) = **12 DEV UNIT**
- **ui_layout**: 8 campi form + 10 colonne lista (data, tipo, note, giorno settimana, mese) + 5 filtri (anno, tipo, mese, range date, ricerca) = **23 DEV UNIT**
- **fe_logic**: 1 (calendario visualizzazione mese) = **1 DEV UNIT**

**TOTALE: 54 DEV UNIT**

---

#### Attività 1.8: Sospensioni Calendario
**Descrizione funzionale:** Gestione sospensioni/festività calendario per allineamento rate.

**DEV UNITS calcolate:**
- **database**: 7 campi (id, academic_year_id, start_date, end_date, motivo, notes, created_at, updated_at) = **8 DEV UNIT**
- **be_data**: 7 campi fillable = **7 DEV UNIT**
- **data_relation**: 1 relazione (AcademicYear many-to-one) = **1 DEV UNIT**
- **be_logic**: 7 CRUD + 1 metodo (getDaysCount) + 1 workflow import = **9 DEV UNIT**
- **ui_layout**: 7 campi form + 10 colonne lista + 3 filtri (anno, motivo, range date) = **20 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 45 DEV UNIT**

---

### GRUPPO 3: CORSI E ISCRIZIONI

#### Attività 1.9: Tipi Corso
**Descrizione funzionale:** Catalogo tipi di corso (strumento individuale, strumento collettivo, laboratorio, teoria, etc.)

**DEV UNITS calcolate:**
- **database**: 10 campi (id, code, name, description, duration_minutes, is_collective, max_students, price_per_lesson, created_at, updated_at) = **10 DEV UNIT**
- **be_data**: 10 campi fillable = **10 DEV UNIT**
- **data_relation**: 1 relazione (Course one-to-many) = **1 DEV UNIT**
- **be_logic**: 7 CRUD + 1 metodo (getDefaultPrice) = **8 DEV UNIT**
- **ui_layout**: 10 campi form + 8 colonne lista + 3 filtri (ricerca, tipo, collettivo) = **21 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 50 DEV UNIT**

---

#### Attività 1.10: Corsi
**Descrizione funzionale:** CRUD corsi con assegnazione docente, aula, giorno, orario, prezzo. Modifiche frequenti (segreteria) per cambi durata/caratteristiche.

**Campi ODS:** Corso 1-3 per studente, Docente, Aula, Giorno, Ora, N. settimane/anno, Data inizio, Sigla, Descrizione, Tipologia, Prezzo lezione, Totale anno, Rate (settimane/importi)

**DEV UNITS calcolate:**
- **database**: ~18 campi (course_type_id, teacher_id, classroom_id, code, name, description, day_of_week, time_start, time_end, max_students, weeks_per_year, price_per_lesson, total_amount, start_date, end_date, status, created_at, updated_at) = **18 DEV UNIT**
- **be_data**: 18 campi fillable = **18 DEV UNIT**
- **data_relation**: 4 relazioni (CourseType many-to-one, Teacher many-to-one, Classroom many-to-one, Enrollment one-to-many) = **4 DEV UNIT**
- **be_logic**: 7 CRUD + 4 metodi (calculateTotal, adjustDuration, getEnrolledCount, isFull) + 1 workflow import = **12 DEV UNIT**
- **ui_layout**: 18 campi form + 15 colonne lista + 5 filtri (ricerca, tipo, docente, giorno, stato) = **38 DEV UNIT**
- **fe_logic**: 1 (calcolo totale dinamico) = **1 DEV UNIT**

**TOTALE: 91 DEV UNIT**

---

#### Attività 1.11: Iscrizioni
**Descrizione funzionale:** Gestione iscrizioni studenti a corsi. Collegamento a contratti e fatturazione. Gestione rate (settimane/importi per ogni rata).

**Campi ODS:** Student_id, Course_id (1-3), Data iscrizione, Data inizio/fine, Prezzo, Sconto, Totale, Stato, Note, Rate (settimane/importi 1-3)

**DEV UNITS calcolate:**
- **database**: ~20 campi (student_id, course_id, enrollment_date, start_date, end_date, price, discount, total, status, notes, installment_1_weeks, installment_1_amount, installment_2_weeks, installment_2_amount, installment_3_weeks, installment_3_amount, created_at, updated_at) = **18 DEV UNIT**
- **be_data**: 18 campi fillable = **18 DEV UNIT**
- **data_relation**: 3 relazioni (Student many-to-one, Course many-to-one, Contract one-to-many) = **3 DEV UNIT**
- **be_logic**: 7 CRUD + 3 metodi (calculateTotal, getRemainingInstallments, linkToContract) + 1 workflow import = **11 DEV UNIT**
- **ui_layout**: 18 campi form + 14 colonne lista + 4 filtri (studente, corso, stato, anno) = **36 DEV UNIT**
- **fe_logic**: 1 (calcolo rate dinamico) = **1 DEV UNIT**

**TOTALE: 87 DEV UNIT**

---

### GRUPPO 4: CONTRATTI E FATTURAZIONE

#### Attività 1.12: Contratti
**Descrizione funzionale:** Gestione contratti dal rilascio alla firma. Tipi: corso regolare (con rate), corso breve (forfettario), tempo estivo, noleggio strumenti. Monitoraggio scadenze, gestione documenti privacy.

**Campi ODS (Db Contratti 25-26.ods):** Numero contratto, Stato, Studente, Data inizio, Corso, Descrizione, Tipologia, Rate (1-3), Totale, Orchestra, Costo orchestra, Date invio/sollecito/ritorno, Privacy, Note

**DEV UNITS calcolate:**
- **database**: ~22 campi (student_id, contract_number, type, status, start_date, end_date, terms, total_amount, installment_count, installment_1_amount, installment_2_amount, installment_3_amount, sent_date, reminder_sent_date, signed_date, privacy_consent, token, notes, academic_year_id, created_at, updated_at) = **21 DEV UNIT**
- **be_data**: 21 campi fillable = **21 DEV UNIT**
- **data_relation**: 3 relazioni (Student many-to-one, AcademicYear many-to-one, Course many-to-many pivot) = **3 DEV UNIT**
- **be_logic**: 7 CRUD + 4 metodi (generateToken, isSigned, isExpired, calculateInstallments) + 2 workflow (import, tracking stato) = **13 DEV UNIT**
- **ui_layout**: 21 campi form + 14 colonne lista + 5 filtri (ricerca, stato, tipo, anno, range date) = **40 DEV UNIT**
- **fe_logic**: 1 (validazione date) = **1 DEV UNIT**

**TOTALE: 99 DEV UNIT**

---

#### Attività 1.13: Fatture
**Descrizione funzionale:** Gestione fatture collegate a contratti. Rate variabili, gestione crediti e acconti, importazione estratto conto.

**Campi ODS (Db Contabile 2025-26.ods):** Numero fattura, Studente, Data emissione, Scadenza, Subtotale, Sconto, IVA, Totale, Stato, Note, Rate (collegate a pagamenti)

**DEV UNITS calcolate:**
- **database**: ~16 campi (student_id, invoice_number, issue_date, due_date, subtotal, discount, vat, total, status, notes, academic_year_id, contract_id, created_at, updated_at) = **14 DEV UNIT**
- **be_data**: 14 campi fillable = **14 DEV UNIT**
- **data_relation**: 4 relazioni (Student many-to-one, AcademicYear many-to-one, Contract many-to-one, Payment one-to-many) = **4 DEV UNIT**
- **be_logic**: 7 CRUD + 5 metodi (generateNumber, calculateTotal, getBalance, isOverdue, linkToContract) + 1 workflow import = **13 DEV UNIT**
- **ui_layout**: 14 campi form + 13 colonne lista + 5 filtri (ricerca, stato, scadute, anno, range date) = **32 DEV UNIT**
- **fe_logic**: 1 (calcolo totale dinamico) = **1 DEV UNIT**

**TOTALE: 78 DEV UNIT**

---

#### Attività 1.14: Pagamenti
**Descrizione funzionale:** Tracciamento pagamenti collegate a fatture. Importazione estratti conto CSV, file cassa e banca.

**Campi ODS:** Fattura, Data pagamento, Importo, Metodo pagamento, Riferimento, Note

**DEV UNITS calcolate:**
- **database**: 10 campi (invoice_id, payment_date, amount, payment_method, reference, notes, imported_from, created_at, updated_at) = **9 DEV UNIT**
- **be_data**: 9 campi fillable = **9 DEV UNIT**
- **data_relation**: 1 relazione (Invoice many-to-one) = **1 DEV UNIT**
- **be_logic**: 7 CRUD + 2 metodi (linkToInvoice, markAsImported) + 1 workflow import CSV = **10 DEV UNIT**
- **ui_layout**: 9 campi form + 11 colonne lista + 4 filtri (fattura, metodo, range date, ricerca) = **24 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 53 DEV UNIT**

---

#### Attività 1.15: Fatture Accessori
**Descrizione funzionale:** Gestione fatture per accessori (libri, materiali) e strumenti.

**DEV UNITS calcolate:**
- **database**: 12 campi (student_id, type, description, quantity, unit_price, total, date, invoice_id, created_at, updated_at) = **10 DEV UNIT**
- **be_data**: 10 campi fillable = **10 DEV UNIT**
- **data_relation**: 2 relazioni (Student many-to-one, Invoice many-to-one) = **2 DEV UNIT**
- **be_logic**: 7 CRUD + 2 metodi (calculateTotal, linkToInvoice) + 1 workflow import = **10 DEV UNIT**
- **ui_layout**: 10 campi form + 12 colonne lista + 4 filtri (studente, tipo, ricerca, range date) = **26 DEV UNIT**
- **fe_logic**: 1 (calcolo totale) = **1 DEV UNIT**

**TOTALE: 59 DEV UNIT**

---

### GRUPPO 5: DIDATTICA E VALUTAZIONE

#### Attività 1.16: Livelli Studenti
**Descrizione funzionale:** Gestione livelli studenti ABRSM (0-8). Livello strumento, teoria, musica di insieme.

**Campi ODS:** Livello, Livello str., Livello teoria, Musica di insieme

**DEV UNITS calcolate:**
- **database**: 9 campi (student_id, level, instrument_level, theory_level, ensemble_level, assignment_date, notes, created_at, updated_at) = **9 DEV UNIT**
- **be_data**: 9 campi fillable = **9 DEV UNIT**
- **data_relation**: 1 relazione (Student many-to-one) = **1 DEV UNIT**
- **be_logic**: 7 CRUD + 2 metodi (getLevelLabel, canAccessOrchestra) + 1 workflow import = **10 DEV UNIT**
- **ui_layout**: 9 campi form + 11 colonne lista + 3 filtri (studente, livello, strumento) = **23 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 52 DEV UNIT**

---

#### Attività 1.17: Esami
**Descrizione funzionale:** Gestione esami con costi iscrizione, pianificazione, livelli ABRSM.

**Campi ODS (Db Accessori 2025-26.ods):** Studente, Tipo esame, Data esame, Livello, Risultato, Note, Esaminatore

**DEV UNITS calcolate:**
- **database**: 11 campi (student_id, exam_type, exam_date, level, result, notes, examiner_id, cost, created_at, updated_at) = **10 DEV UNIT**
- **be_data**: 10 campi fillable = **10 DEV UNIT**
- **data_relation**: 2 relazioni (Student many-to-one, Teacher many-to-one examiner) = **2 DEV UNIT**
- **be_logic**: 7 CRUD + 2 metodi (calculateCost, generateCertificate) + 1 workflow import = **10 DEV UNIT**
- **ui_layout**: 10 campi form + 13 colonne lista + 4 filtri (studente, tipo, livello, range date) = **27 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 59 DEV UNIT**

---

### GRUPPO 6: ATTIVITÀ EXTRA

#### Attività 1.18: Orchestra/Coro
**Descrizione funzionale:** Gestione iscrizioni orchestra/coro. Filtri per composizione gruppi (livello, strumento, iscrizione progetto).

**Campi ODS:** Conf (Conferma orchestra), Orch 1, PYO, Conf coro, Coro, Data ultima convocazione, Note orchestra

**DEV UNITS calcolate:**
- **database**: 11 campi (student_id, extra_activity_id, orchestra_type, pyo, choir_type, last_convocation_date, notes, enrollment_date, created_at, updated_at) = **10 DEV UNIT**
- **be_data**: 10 campi fillable = **10 DEV UNIT**
- **data_relation**: 2 relazioni (Student many-to-one, ExtraActivity many-to-one) = **2 DEV UNIT**
- **be_logic**: 7 CRUD + 3 metodi (filterByLevel, filterByInstrument, getConvocations) + 1 workflow import = **11 DEV UNIT**
- **ui_layout**: 10 campi form + 12 colonne lista + 4 filtri (attività, livello, strumento, ricerca) = **26 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 59 DEV UNIT**

---

### GRUPPO 7: MAGAZZINO

#### Attività 1.19: Strumenti
**Descrizione funzionale:** Catalogo strumenti musicali con codifica, valore acquisto, stato (disponibile/noleggiato/venduto).

**Campi ODS:** Codice, Tipo, Marca, Modello, Misura, Cod seriale, Fornitore, Noleggio/proprietà, Provenienza, Valore acquisto, Stato, Note

**DEV UNITS calcolate:**
- **database**: 15 campi (code, type, brand, model, size, serial_number, supplier_id, rental_type, source, purchase_date, purchase_price, current_value, status, notes, created_at, updated_at) = **16 DEV UNIT**
- **be_data**: 16 campi fillable = **16 DEV UNIT**
- **data_relation**: 2 relazioni (Supplier many-to-one, InstrumentRental one-to-many) = **2 DEV UNIT**
- **be_logic**: 7 CRUD + 3 metodi (isAvailable, calculateDepreciation, linkToRental) + 1 workflow import = **11 DEV UNIT**
- **ui_layout**: 16 campi form + 13 colonne lista + 4 filtri (codice, tipo, stato, fornitore) = **33 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 78 DEV UNIT**

---

#### Attività 1.20: Noleggi Strumenti
**Descrizione funzionale:** Gestione noleggi strumenti agli studenti con contratti, cauzioni, rate.

**Campi ODS:** Studente, Strumento, Fornitore, Noleggio/proprietà, Provenienza, Tipo, Marca, Modello, Misura, Cod, Da cambiare, Note, Data inizio, Data fine, Costo

**DEV UNITS calcolate:**
- **database**: 14 campi (student_id, instrument_id, supplier_id, rental_type, start_date, end_date, deposit, monthly_rate, total_cost, status, notes, created_at, updated_at) = **13 DEV UNIT**
- **be_data**: 13 campi fillable = **13 DEV UNIT**
- **data_relation**: 3 relazioni (Student many-to-one, Instrument many-to-one, Supplier many-to-one) = **3 DEV UNIT**
- **be_logic**: 7 CRUD + 3 metodi (calculateCost, isActive, needsRenewal) + 1 workflow import = **11 DEV UNIT**
- **ui_layout**: 13 campi form + 13 colonne lista + 4 filtri (studente, strumento, stato, range date) = **30 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 70 DEV UNIT**

---

#### Attività 1.21: Accessori e Libri
**Descrizione funzionale:** Gestione accessori (1-7) e libri (1-15) con operazioni tracciamento, acquisto, distribuzione.

**Campi ODS (Db Accessori 2025-26.ods):** Accessori 1-7, Libri 1-15 (descrizione, quantità, prezzo, data distribuzione)

**DEV UNITS calcolate:**
- **database**: 12 campi (student_id, item_type, item_name, quantity, unit_price, total, distribution_date, notes, created_at, updated_at) = **10 DEV UNIT**
- **be_data**: 10 campi fillable = **10 DEV UNIT**
- **data_relation**: 1 relazione (Student many-to-one) = **1 DEV UNIT**
- **be_logic**: 7 CRUD + 2 metodi (calculateTotal, trackInventory) + 1 workflow import = **10 DEV UNIT**
- **ui_layout**: 10 campi form + 13 colonne lista + 4 filtri (studente, tipo, ricerca, range date) = **27 DEV UNIT**
- **fe_logic**: 0 = **0 DEV UNIT**

**TOTALE: 58 DEV UNIT**

---

## RIEPILOGO FASE 1 - DEV UNITS PER CATEGORIA

| Attività | database | be_data | data_relation | be_logic | ui_layout | fe_logic | **TOTALE** |
|----------|----------|---------|---------------|----------|-----------|----------|------------|
| 1.1 Anno Scolastico | 8 | 8 | 5 | 9 | 19 | 0 | **49** |
| 1.2 Studenti | 35 | 35 | 10 | 11 | 49 | 1 | **141** |
| 1.3 Genitori | 18 | 18 | 2 | 10 | 34 | 0 | **82** |
| 1.4 Docenti | 22 | 22 | 3 | 11 | 42 | 0 | **100** |
| 1.5 Disponibilità Studenti | 11 | 10 | 1 | 9 | 23 | 1 | **55** |
| 1.6 Disponibilità Docenti | 10 | 10 | 1 | 9 | 23 | 0 | **53** |
| 1.7 Calendario | 8 | 8 | 2 | 12 | 23 | 1 | **54** |
| 1.8 Sospensioni | 8 | 7 | 1 | 9 | 20 | 0 | **45** |
| 1.9 Tipi Corso | 10 | 10 | 1 | 8 | 21 | 0 | **50** |
| 1.10 Corsi | 18 | 18 | 4 | 12 | 38 | 1 | **91** |
| 1.11 Iscrizioni | 18 | 18 | 3 | 11 | 36 | 1 | **87** |
| 1.12 Contratti | 21 | 21 | 3 | 13 | 40 | 1 | **99** |
| 1.13 Fatture | 14 | 14 | 4 | 13 | 32 | 1 | **78** |
| 1.14 Pagamenti | 9 | 9 | 1 | 10 | 24 | 0 | **53** |
| 1.15 Fatture Accessori | 10 | 10 | 2 | 10 | 26 | 1 | **59** |
| 1.16 Livelli | 9 | 9 | 1 | 10 | 23 | 0 | **52** |
| 1.17 Esami | 10 | 10 | 2 | 10 | 27 | 0 | **59** |
| 1.18 Orchestra/Coro | 10 | 10 | 2 | 11 | 26 | 0 | **59** |
| 1.19 Strumenti | 16 | 16 | 2 | 11 | 33 | 0 | **78** |
| 1.20 Noleggi Strumenti | 13 | 13 | 3 | 11 | 30 | 0 | **70** |
| 1.21 Accessori e Libri | 10 | 10 | 1 | 10 | 27 | 0 | **58** |
| **TOTALE FASE 1** | **258** | **256** | **47** | **200** | **555** | **7** | **1.323 DEV UNIT** |

**Note:**
- Calcolo realistico per tutte le categorie (non solo database)
- Ratio: ~20% database, ~20% be_data, ~4% data_relation, ~15% be_logic, ~42% ui_layout, ~0.5% fe_logic
- Pattern simile a progetti Laravel esistenti (mscarichi, klabhouse)

---

## FASE 2-3: EVOLUZIONI (da semplificare)

Le fasi evolutive saranno analizzate in base alle conversazioni e desiderata utente, ma con approccio semplificato:
- Workflow avanzati: minimi necessari
- Automazioni: solo quelle realmente richieste
- Integrazioni: solo quando chiaramente necessarie
- Reportistica: semplice, senza grafici complessi

### Esempi Evoluzioni Semplificate:

#### Attività 2.1: Primo Contatto Pubblico
**Descrizione:** Form pubblico base per primo contatto, conversione prospect → studente.

**DEV UNITS stimate:** ~45 DEV UNIT (form pubblico, workflow conversione, validazione)

#### Attività 2.2: Proposta Oraria (Semplificata)
**Descrizione:** Composizione orari basata su disponibilità (non algoritmo complesso, ma matching semplice).

**DEV UNITS stimate:** ~55 DEV UNIT (matching base, proposta, accettazione/rifiuto)

#### Attività 2.3: Registro Elettronico Base
**Descrizione:** Registro accessibile docenti, presenze base, countdown lezioni.

**DEV UNITS stimate:** ~70 DEV UNIT (accesso docente, presenze, conteggi)

#### Attività 2.4: Conto Orario Automatico
**Descrizione:** Calcolo automatico da presenze, compensi personalizzati, voci forfettarie.

**DEV UNITS stimate:** ~60 DEV UNIT (calcolo, configurazione compensi, report)

#### Attività 2.5: Comunicazioni Base
**Descrizione:** Sistema comunicazioni email base, template semplici, log invii.

**DEV UNITS stimate:** ~45 DEV UNIT (invio email, template base, log)

**TOTALE STIMATO FASE 2-3:** ~275 DEV UNIT (semplificate)

---

## PROSSIMI PASSI

1. ✅ Verificare questa struttura con documentazione ODS reale
2. ✅ Confrontare con pattern progetti simili per validare ratio categorie
3. ✅ Creare JSON/CSV per inserimento in Footility
4. ✅ Associare attività a DEV UNITS tramite processo guidato

**Documento creato:** Gennaio 2026  
**Status:** Prima bozza - da validare con dati reali e confrontare con progetti esistenti
