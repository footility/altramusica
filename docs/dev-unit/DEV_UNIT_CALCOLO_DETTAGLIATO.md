# Calcolo Dettagliato DEV UNIT - L'Altramusica

**Metodologia Footility applicata:**
- Ogni campo database = 1 DEV UNIT
- Ogni foreign key/relazione = 1 DEV UNIT  
- Ogni action controller (index, create, store, show, edit, update, destroy) = 1 DEV UNIT
- Ogni metodo model custom = 1 DEV UNIT
- Ogni stato/transizione workflow = 1 DEV UNIT
- Ogni campo input nel form = 1 DEV UNIT
- Ogni colonna nella lista = 1 DEV UNIT
- Ogni filtro = 1 DEV UNIT
- Ogni campo nel PDF = 1 DEV UNIT
- Ogni tipo di stampa = 1 DEV UNIT

---

## 1. ANAGRAFICA STUDENTI

### DEV_DB_campi (25 campi)
1. id
2. first_name (Nome)
3. last_name (Cognome)
4. tax_code (Cod. fiscale)
5. birth_date (Nato il)
6. age (Età - calcolato)
7. is_minor (Minore)
8. address (Indirizzo)
9. postal_code (CAP)
10. city (Città)
11. phone (Cell allievo)
12. email (Mail allievo)
13. first_contact_date (Data arrivo)
14. source (Come ci ha conosciuto)
15. notes_didattiche (Note prove e didattiche)
16. notes_varie (Note varie)
17. data_ultimo_contatto (Data ultimo)
18. status (Stato)
19. enrollment_number (N. iscritto)
20. enrollment_fee (€ iscrizione)
21. discount_percentage (Sconto)
22. is_new_student (Nuovo iscritto)
23. privacy_consent (Consenso privacy)
24. photo_consent (Consenso foto)
25. school (Scuola provenienza)

**TOTALE: 25 DEV UNIT**

### DEV_DB_relazioni (6 relazioni)
1. Student -> Guardian (many-to-many)
2. Student -> Enrollment (one-to-many)
3. Student -> Contract (one-to-many)
4. Student -> Invoice (one-to-many)
5. Student -> Lesson (one-to-many)
6. Student -> StudentAvailability (one-to-many)

**TOTALE: 6 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow per anagrafica base

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (25 campi input)
Ogni campo del form = 1 DEV UNIT (stessi 25 campi sopra)

**TOTALE: 25 DEV UNIT**

### DEV_UI_lista (15 colonne + 5 filtri)
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
14. Scuola
15. Azioni

**Filtri:**
1. Ricerca testo
2. Filtro stato
3. Filtro anno scolastico
4. Filtro nuovo/vecchio
5. Filtro minore/maggiore

**TOTALE: 20 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa per anagrafica studenti

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 25 + 6 + 7 + 0 + 25 + 20 + 0 = 83 DEV UNIT**

---

## 2. ANAGRAFICA GENITORI/TUTORI

### DEV_DB_campi (18 campi)
1. id
2. first_name (Nome)
3. last_name (Cognome)
4. tax_code (Cod. fiscale)
5. relationship_type (Tipo relazione: genitore/tutore)
6. phone_home (Tel casa)
7. phone_work (Tel lavoro)
8. phone_mobile_1 (Cell 1)
9. phone_mobile_2 (Cell 2)
10. email_1 (Mail 1)
11. email_2 (Mail 2)
12. email_3 (Mail 3)
13. address (Indirizzo)
14. postal_code (CAP)
15. city (Città)
16. is_primary (Flag genitore principale)
17. is_billing_contact (Flag contatto fatturazione)
18. privacy_consent (Consenso privacy)

**TOTALE: 18 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. Guardian -> Student (many-to-many)
2. Guardian -> Communication (one-to-many)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (18 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 18 DEV UNIT**

### DEV_UI_lista (12 colonne + 4 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 18 + 2 + 7 + 0 + 18 + 16 + 0 = 61 DEV UNIT**

---

## 3. ANAGRAFICA DOCENTI

### DEV_DB_campi (20 campi)
1. id
2. first_name (Nome)
3. last_name (Cognome)
4. birth_date (Nato il)
5. tax_code (Cod. fiscale)
6. vat_number (P. IVA)
7. id_number (Carta Identità)
8. id_issue_date (Data rilascio)
9. id_issuer (Ente rilascio)
10. address (Domicilio Indirizzo)
11. postal_code (Domicilio CAP)
12. city (Domicilio città)
13. residence_address (Residenza Indirizzo)
14. residence_postal_code (Residenza CAP)
15. residence_city (Residenza città)
16. iban (IBAN)
17. phone_home (Tel abitazione)
18. phone_mobile (Cell 1)
19. email (E-mail 1)
20. role (Ruolo: socio/non socio)
21. employment_type (Inquadramento)
22. hourly_rate (Compenso orario base)
23. notes_contrattuali (Note contrattuali)

**TOTALE: 23 DEV UNIT** (ho contato anche i campi aggiuntivi)

### DEV_DB_relazioni (3 relazioni)
1. Teacher -> Course (one-to-many)
2. Teacher -> Lesson (one-to-many)
3. Teacher -> TeacherPayment (one-to-many)

**TOTALE: 3 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (23 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 23 DEV UNIT**

### DEV_UI_lista (14 colonne + 4 filtri)
**Colonne:**
1. Cognome
2. Nome
3. Cod. Fiscale
4. P. IVA
5. Ruolo (socio/non socio)
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
2. Filtro ruolo (socio/non socio)
3. Filtro inquadramento
4. Filtro anno scolastico

**TOTALE: 18 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 23 + 3 + 7 + 0 + 23 + 18 + 0 = 74 DEV UNIT**

---

## 4. GESTIONE ANNO SCOLASTICO

### DEV_DB_campi (5 campi)
1. id
2. name (Nome: "2024-2025")
3. start_date (Data inizio: 1 settembre)
4. end_date (Data fine: 31 agosto)
5. is_active (Stato: attivo/archiviato)
6. year (Anno di riferimento)

**TOTALE: 6 DEV UNIT**

### DEV_DB_relazioni (5 relazioni)
1. AcademicYear -> Student (one-to-many)
2. AcademicYear -> Enrollment (one-to-many)
3. AcademicYear -> Contract (one-to-many)
4. AcademicYear -> Invoice (one-to-many)
5. AcademicYear -> Lesson (one-to-many)

**TOTALE: 5 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (6 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 6 DEV UNIT**

### DEV_UI_lista (6 colonne + 2 filtri)
**Colonne:**
1. Nome
2. Data inizio
3. Data fine
4. Anno riferimento
5. Stato
6. Azioni

**Filtri:**
1. Filtro stato (attivo/archiviato)
2. Switch anno corrente

**TOTALE: 8 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 6 + 5 + 7 + 0 + 6 + 8 + 0 = 32 DEV UNIT**

---

## 5. CORSI E ISCRIZIONI

### DEV_DB_campi (15 campi per Enrollment)
1. id
2. student_id
3. course_id
4. start_date (Data inizio iscrizione)
5. end_date (Data fine iscrizione)
6. weeks_count (Numero settimane)
7. lessons_count (Numero lezioni totali)
8. status (attiva/sospesa/chiusa)
9. notes
10. enrollment_fee (€ iscrizione)
11. discount_percentage
12. total_amount (€ tot anno)
13. price_per_lesson (€ lez)
14. day_of_week (giorno strum)
15. time_start (ora strumento/canto)

**TOTALE: 15 DEV UNIT**

### DEV_DB_relazioni (4 relazioni)
1. Enrollment -> Student (many-to-one)
2. Enrollment -> Course (many-to-one)
3. Enrollment -> Contract (one-to-many)
4. Course -> CourseType (many-to-one)

**TOTALE: 4 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (1 workflow)
1. Calcolo automatico settimane da calendario

**TOTALE: 1 DEV UNIT**

### DEV_UI_form (15 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 15 DEV UNIT**

### DEV_UI_lista (12 colonne + 5 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 15 + 4 + 7 + 1 + 15 + 17 + 0 = 59 DEV UNIT**

---

## 6. CALENDARIO LEZIONI

### DEV_DB_campi (5 campi)
1. id
2. academic_year_id
3. date (Data)
4. type (giornata attiva/sospensione/festività)
5. notes

**TOTALE: 5 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. Calendar -> AcademicYear (many-to-one)
2. Calendar -> Lesson (one-to-many)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (1 workflow)
1. Generazione automatica calendario da date inizio/fine

**TOTALE: 1 DEV UNIT**

### DEV_UI_form (5 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 5 DEV UNIT**

### DEV_UI_lista (6 colonne + 4 filtri)
**Colonne:**
1. Data
2. Tipo
3. Note
4. Anno scolastico
5. Giorno settimana
6. Azioni

**Filtri:**
1. Filtro anno scolastico
2. Filtro tipo (attiva/sospensione)
3. Filtro mese
4. Filtro data range

**TOTALE: 10 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 5 + 2 + 7 + 1 + 5 + 10 + 0 = 30 DEV UNIT**

---

## 7. PRIMO CONTATTO / PROSPECT

### DEV_DB_campi (8 campi)
1. id
2. first_name (Nome)
3. last_name (Cognome)
4. birth_date (Data nascita)
5. phone (Telefono)
6. email (Email)
7. notes (Note primo contatto)
8. source (Come ci ha conosciuto)
9. status (prospect/interessato/convertito)

**TOTALE: 9 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. FirstContact -> Student (one-to-one, quando convertito)
2. FirstContact -> Communication (one-to-many)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow (conversione manuale)

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (9 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 9 DEV UNIT**

### DEV_UI_lista (8 colonne + 3 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 9 + 2 + 7 + 0 + 9 + 11 + 0 = 38 DEV UNIT**

---

## 8. CONTRATTI DA ISCRIZIONI

### DEV_DB_campi (12 campi)
1. id
2. contract_number (Numero contratto)
3. student_id
4. academic_year_id
5. type (regolare/breve/tempo estivo)
6. created_at (Data creazione)
7. sent_date (Data invio)
8. viewed_date (Data visualizzazione)
9. signed_date (Data accettazione)
10. status (draft/sent/viewed/accepted/rejected)
11. total_amount (Importo totale)
12. payment_installments (Numero rate)
13. notes (Note regolamento)

**TOTALE: 13 DEV UNIT**

### DEV_DB_relazioni (4 relazioni)
1. Contract -> Student (many-to-one)
2. Contract -> AcademicYear (many-to-one)
3. Contract -> PaymentPlan (one-to-many)
4. Contract -> Invoice (one-to-many)

**TOTALE: 4 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (4 stati/transizioni)
1. draft → sent
2. sent → viewed
3. viewed → accepted
4. viewed → rejected

**TOTALE: 4 DEV UNIT**

### DEV_UI_form (13 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 13 DEV UNIT**

### DEV_UI_lista (11 colonne + 5 filtri)
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

### DEV_UI_stampa (3 tipi PDF + 15 campi per PDF)
**Tipi PDF:**
1. Contratto regolare
2. Contratto breve
3. Contratto tempo estivo

**Campi per PDF (15 campi):**
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

**TOTALE: 3 + (15 × 3) = 48 DEV UNIT**

**TOTALE FUNZIONALITÀ: 13 + 4 + 7 + 4 + 13 + 16 + 48 = 105 DEV UNIT**

---

## 9. FATTURAZIONE / PAGAMENTI / RECUPERO CREDITI

### DEV_DB_campi (18 campi totali)
**Invoice (10 campi):**
1. id
2. invoice_number
3. student_id
4. academic_year_id
5. issue_date
6. due_date
7. total_amount
8. paid_amount
9. status (draft/emessa/pagata/parzialmente pagata/scaduta)
10. notes

**Payment (8 campi):**
11. id
12. invoice_id
13. payment_plan_id
14. amount
15. payment_date
16. payment_method (contanti/bonifico/Satispay)
17. reference
18. notes

**TOTALE: 18 DEV UNIT**

### DEV_DB_relazioni (5 relazioni)
1. Invoice -> Student (many-to-one)
2. Invoice -> AcademicYear (many-to-one)
3. Invoice -> Payment (one-to-many)
4. Payment -> PaymentPlan (many-to-one)
5. Invoice -> CreditNote (one-to-many)

**TOTALE: 5 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (3 workflow)
1. Emissione fattura (da contratto accettato)
2. Registrazione pagamento
3. Chiusura fattura (pagamento completo)

**TOTALE: 3 DEV UNIT**

### DEV_UI_form (18 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 18 DEV UNIT**

### DEV_UI_lista (14 colonne + 6 filtri)
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

### DEV_UI_stampa (1 tipo PDF + 12 campi)
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

**TOTALE FUNZIONALITÀ: 18 + 5 + 7 + 3 + 18 + 20 + 13 = 84 DEV UNIT**

---

## 10. PIANI DI PAGAMENTO FLESSIBILI

### DEV_DB_campi (7 campi)
1. id
2. contract_id
3. installment_number (Numero rata)
4. amount (Importo rata)
5. due_date (Data scadenza)
6. paid_amount (Importo pagato)
7. status (da pagare/parzialmente pagata/pagata)

**TOTALE: 7 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. PaymentPlan -> Contract (many-to-one)
2. PaymentPlan -> Payment (one-to-many)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (0 - gestito da Contract)
Nessun CRUD separato

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Calcolo automatico rate da calendario
2. Aggiornamento stato da pagamenti

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (7 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 7 DEV UNIT**

### DEV_UI_lista (0 - visualizzato in Contract)
Nessuna lista separata

**TOTALE: 0 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 7 + 2 + 0 + 2 + 7 + 0 + 0 = 18 DEV UNIT**

---

## 11. AUTOMAZIONI SOLLECITO

### DEV_DB_campi (4 campi)
1. id
2. invoice_id
3. reminder_type (primo/secondo/terzo)
4. sent_date
5. notes

**TOTALE: 5 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. Reminder -> Invoice (many-to-one)
2. Reminder -> Communication (one-to-one)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (0 - automatico)
Nessun CRUD manuale

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Invio automatico sollecito (job schedulato)
2. Sospensione automatismi per casi particolari

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (0)
Nessun form (automatico)

**TOTALE: 0 DEV UNIT**

### DEV_UI_lista (6 colonne + 3 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 5 + 2 + 0 + 2 + 0 + 9 + 0 = 18 DEV UNIT**

---

## 12. FORNITORI

### DEV_DB_campi (10 campi)
1. id
2. company_name (Ragione sociale)
3. vat_number (Partita IVA)
4. tax_code (Codice fiscale)
5. address (Indirizzo)
6. postal_code (CAP)
7. city (Città)
8. iban (IBAN)
9. expense_category (Categoria spesa)
10. notes

**TOTALE: 10 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. Supplier -> Invoice (one-to-many, fatture passive)
2. Supplier -> Instrument (one-to-many)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (10 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 10 DEV UNIT**

### DEV_UI_lista (9 colonne + 3 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 10 + 2 + 7 + 0 + 10 + 12 + 0 = 41 DEV UNIT**

---

## 13. REGISTRO ELETTRONICO EVOLUTO

### DEV_DB_campi (10 campi per Lesson)
1. id
2. course_id
3. date (Data lezione)
4. time_start (Orario inizio)
5. time_end (Orario fine)
6. classroom_id (Aula)
7. teacher_id (Insegnante)
8. type (regolare/recupero/lezione libera)
9. status (programmata/effettuata/cancellata)
10. notes

**TOTALE: 10 DEV UNIT**

### DEV_DB_relazioni (4 relazioni)
1. Lesson -> Course (many-to-one)
2. Lesson -> Classroom (many-to-one)
3. Lesson -> Teacher (many-to-one)
4. Lesson -> Attendance (one-to-many)

**TOTALE: 4 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Generazione automatica lezioni da calendario
2. Aggiornamento stato (programmata → effettuata)

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (10 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 10 DEV UNIT**

### DEV_UI_lista (11 colonne + 5 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 10 + 4 + 7 + 2 + 10 + 16 + 0 = 49 DEV UNIT**

---

## 14. GESTIONE RECUPERI LEZIONI

### DEV_DB_campi (8 campi)
1. id
2. original_lesson_id (Lezione originale)
3. recovery_lesson_id (Lezione recupero)
4. recovery_date (Data recupero)
5. recovery_reason (Motivo recupero)
6. status (programmato/effettuato/cancellato)
7. notes
8. approved_by (Approvato da)

**TOTALE: 8 DEV UNIT**

### DEV_DB_relazioni (3 relazioni)
1. LessonRecovery -> Lesson (many-to-one, originale)
2. LessonRecovery -> Lesson (many-to-one, recupero)
3. LessonRecovery -> User (many-to-one, approvato da)

**TOTALE: 3 DEV UNIT**

### DEV_LOGIC_CRUD (0 - gestito da Lesson)
Nessun CRUD separato

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Richiesta recupero → approvazione
2. Programmazione recupero → effettuazione

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (8 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 8 DEV UNIT**

### DEV_UI_lista (9 colonne + 4 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 8 + 3 + 0 + 2 + 8 + 13 + 0 = 34 DEV UNIT**

---

## 15. CONTO ORARIO DOCENTI

### DEV_DB_campi (12 campi)
1. id
2. teacher_id
3. academic_year_id
4. total_hours (Ore totali)
5. regular_hours (Ore regolari)
6. recovery_hours (Ore recupero)
7. substitute_hours (Ore supplenze)
8. hourly_rate (Valore orario)
9. bonus_amount (Bonus)
10. forfait_amount (Forfait)
11. total_amount (Importo totale)
12. notes

**TOTALE: 12 DEV UNIT**

### DEV_DB_relazioni (3 relazioni)
1. TeacherHour -> Teacher (many-to-one)
2. TeacherHour -> AcademicYear (many-to-one)
3. TeacherHour -> TeacherPayment (one-to-many)

**TOTALE: 3 DEV UNIT**

### DEV_LOGIC_CRUD (0 - calcolo automatico)
Nessun CRUD manuale

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Calcolo automatico ore da presenze
2. Calcolo importi (ore × tariffa + bonus + forfait)

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (0)
Nessun form (calcolo automatico)

**TOTALE: 0 DEV UNIT**

### DEV_UI_lista (11 colonne + 4 filtri)
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

### DEV_UI_stampa (1 tipo PDF + 12 campi)
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

**TOTALE FUNZIONALITÀ: 12 + 3 + 0 + 2 + 0 + 15 + 13 = 45 DEV UNIT**

---

## 16. GESTIONE SUPPLENZE

### DEV_DB_campi (8 campi)
1. id
2. lesson_id
3. original_teacher_id (Insegnante originale)
4. substitute_teacher_id (Supplente)
5. substitute_date (Data supplenza)
6. reason (Motivo)
7. status (programmata/effettuata/cancellata)
8. notes

**TOTALE: 8 DEV UNIT**

### DEV_DB_relazioni (3 relazioni)
1. Substitute -> Lesson (many-to-one)
2. Substitute -> Teacher (many-to-one, originale)
3. Substitute -> Teacher (many-to-one, supplente)

**TOTALE: 3 DEV UNIT**

### DEV_LOGIC_CRUD (0 - gestito da Lesson)
Nessun CRUD separato

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Assegnazione supplente → trasferimento lezione
2. Aggiornamento conto orario (togli originale, aggiungi supplente)

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (0)
Nessun form separato

**TOTALE: 0 DEV UNIT**

### DEV_UI_lista (9 colonne + 4 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 8 + 3 + 0 + 2 + 0 + 13 + 0 = 26 DEV UNIT**

---

## 17. ATTIVITÀ EXTRA (ORCHESTRA/CORO) BASE

### DEV_DB_campi (12 campi)
1. id
2. name (Nome attività)
3. type (orchestra/coro/altro)
4. academic_year_id
5. cost (Costo partecipazione)
6. max_participants (Numero massimo)
7. current_participants (Numero attuale)
8. schedule_dates (Calendario 12 date/anno)
9. level_filter (Filtro livello)
10. instrument_filter (Filtro strumento)
11. notes
12. status (attiva/chiusa)

**TOTALE: 12 DEV UNIT**

### DEV_DB_relazioni (3 relazioni)
1. ExtraActivity -> AcademicYear (many-to-one)
2. ExtraActivity -> ExtraActivityEnrollment (one-to-many)
3. ExtraActivity -> Communication (one-to-many, convocazioni)

**TOTALE: 3 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow base

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (12 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 12 DEV UNIT**

### DEV_UI_lista (10 colonne + 4 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa base

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 12 + 3 + 7 + 0 + 12 + 14 + 0 = 48 DEV UNIT**

---

## 18. ATTIVITÀ EXTRA EVOLUTE

### DEV_DB_campi (12 campi - stessi di base + gestione presenze)
Stessi 12 campi + gestione presenze prove

**TOTALE: 12 DEV UNIT**

### DEV_DB_relazioni (4 relazioni)
1. ExtraActivity -> AcademicYear (many-to-one)
2. ExtraActivity -> ExtraActivityEnrollment (one-to-many)
3. ExtraActivity -> Communication (one-to-many)
4. ExtraActivity -> Attendance (one-to-many, presenze prove)

**TOTALE: 4 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Composizione automatica gruppi (filtri livello/strumento)
2. Convocazioni automatiche (12 date/anno)

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (12 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 12 DEV UNIT**

### DEV_UI_lista (10 colonne + 4 filtri)
Stessa lista base

**TOTALE: 14 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 12 + 4 + 7 + 2 + 12 + 14 + 0 = 51 DEV UNIT**

---

## 19. REGISTRO PRESENZE MINIMO

### DEV_DB_campi (6 campi)
1. id
2. lesson_id
3. student_id
4. is_present (Presente sì/no)
5. registered_at (Data registrazione)
6. registered_by (Registrato da: insegnante/supplente)

**TOTALE: 6 DEV UNIT**

### DEV_DB_relazioni (3 relazioni)
1. Attendance -> Lesson (many-to-one)
2. Attendance -> Student (many-to-one)
3. Attendance -> User (many-to-one, registrato da)

**TOTALE: 3 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow base

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (6 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 6 DEV UNIT**

### DEV_UI_lista (8 colonne + 5 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 6 + 3 + 7 + 0 + 6 + 13 + 0 = 35 DEV UNIT**

---

## 20. GESTIONE ESAMI

### DEV_DB_campi (10 campi)
1. id
2. name (Nome esame)
3. type (interno/esterno)
4. academic_year_id
5. exam_date (Data esame)
6. cost (Costo iscrizione)
7. level (Livello ABRSM)
8. instrument (Strumento)
9. notes
10. status (programmato/effettuato/cancellato)

**TOTALE: 10 DEV UNIT**

### DEV_DB_relazioni (3 relazioni)
1. Exam -> AcademicYear (many-to-one)
2. Exam -> Student (many-to-many, iscritti)
3. Exam -> Teacher (many-to-many, esaminatori)

**TOTALE: 3 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (1 workflow)
1. Iscrizione esame → pagamento → svolgimento

**TOTALE: 1 DEV UNIT**

### DEV_UI_form (10 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 10 DEV UNIT**

### DEV_UI_lista (9 colonne + 5 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 10 + 3 + 7 + 1 + 10 + 14 + 0 = 45 DEV UNIT**

---

## 21. GENERAZIONE ATTESTATI

### DEV_DB_campi (4 campi)
1. id
2. student_id
3. course_id
4. issue_date (Data emissione)
5. content (Contenuto attestato)

**TOTALE: 5 DEV UNIT**

### DEV_DB_relazioni (1 relazione)
1. Certificate -> Student (many-to-one)

**TOTALE: 1 DEV UNIT**

### DEV_LOGIC_CRUD (0 - generazione automatica)
Nessun CRUD manuale

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (1 workflow)
1. Generazione automatica da presenze/corsi frequentati

**TOTALE: 1 DEV UNIT**

### DEV_UI_form (0)
Nessun form (generazione automatica)

**TOTALE: 0 DEV UNIT**

### DEV_UI_lista (0)
Nessuna lista (visualizzato in studente)

**TOTALE: 0 DEV UNIT**

### DEV_UI_stampa (1 tipo PDF + 8 campi)
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

**TOTALE FUNZIONALITÀ: 5 + 1 + 0 + 1 + 0 + 0 + 9 = 16 DEV UNIT**

---

## 22. COMUNICAZIONI MAIL/SMS EVOLUTE

### DEV_DB_campi (8 campi)
1. id
2. recipient_type (studente/genitore)
3. recipient_id
4. type (email/SMS/WhatsApp)
5. subject (Oggetto)
6. content (Contenuto)
7. sent_at (Data invio)
8. status (inviata/consegnata/errore)
9. template_id (Template utilizzato)

**TOTALE: 9 DEV UNIT**

### DEV_DB_relazioni (3 relazioni)
1. Communication -> Student (many-to-one, opzionale)
2. Communication -> Guardian (many-to-one, opzionale)
3. Communication -> Template (many-to-one)

**TOTALE: 3 DEV UNIT**

### DEV_LOGIC_CRUD (0 - invio automatico)
Nessun CRUD manuale

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Selezione destinatari (filtri) → invio
2. Invio multi-canale (email + SMS)

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (9 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 9 DEV UNIT**

### DEV_UI_lista (8 colonne + 5 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 9 + 3 + 0 + 2 + 9 + 13 + 0 = 36 DEV UNIT**

---

## 23. NOTE OPERATIVE / COMUNICAZIONI MANUALI

### DEV_DB_campi (6 campi)
1. id
2. student_id (opzionale)
3. guardian_id (opzionale)
4. note_type (telefonata/email/colloquio)
5. content (Contenuto nota)
6. created_at

**TOTALE: 6 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. Note -> Student (many-to-one, opzionale)
2. Note -> Guardian (many-to-one, opzionale)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (0 - note inline)
Nessun CRUD separato (note inline in form)

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (0)
Nessun workflow

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (6 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 6 DEV UNIT**

### DEV_UI_lista (0)
Nessuna lista (visualizzato in studente/genitore)

**TOTALE: 0 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 6 + 2 + 0 + 0 + 6 + 0 + 0 = 14 DEV UNIT**

---

## 24. GESTIONE UTENZE

### DEV_DB_campi (8 campi)
1. id
2. email (username)
3. password (hash)
4. name (Nome)
5. role (admin/segreteria/insegnante/supplente)
6. permissions (Permessi granulari - JSON)
7. status (attivo/sospeso)
8. last_login_at

**TOTALE: 8 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. User -> Teacher (one-to-one, opzionale)
2. User -> Activity (one-to-many, log attività)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (7 actions)
1. index
2. create
3. store
4. show
5. edit
6. update
7. destroy

**TOTALE: 7 DEV UNIT**

### DEV_LOGIC_workflow (1 workflow)
1. Creazione account → invio credenziali → attivazione

**TOTALE: 1 DEV UNIT**

### DEV_UI_form (8 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 8 DEV UNIT**

### DEV_UI_lista (7 colonne + 4 filtri)
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

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 8 + 2 + 7 + 1 + 8 + 11 + 0 = 37 DEV UNIT**

---

## 25. CONTROLLO ACCESSI E AUTORIZZAZIONI

### DEV_DB_campi (4 campi)
1. id
2. permission_name (Nome permesso)
3. resource (Risorsa: student/invoice/contract)
4. action (Azione: create/read/update/delete)

**TOTALE: 4 DEV UNIT**

### DEV_DB_relazioni (2 relazioni)
1. Permission -> Role (many-to-many)
2. Permission -> User (many-to-many, permessi custom)

**TOTALE: 2 DEV UNIT**

### DEV_LOGIC_CRUD (0 - configurazione)
Nessun CRUD (configurazione sistema)

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (2 workflow)
1. Controllo permessi middleware
2. Log accessi negati

**TOTALE: 2 DEV UNIT**

### DEV_UI_form (0)
Nessun form (configurazione)

**TOTALE: 0 DEV UNIT**

### DEV_UI_lista (0)
Nessuna lista (configurazione)

**TOTALE: 0 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 4 + 2 + 0 + 2 + 0 + 0 + 0 = 8 DEV UNIT**

---

## 26. MODELLI STANDARD DI CONTRATTO

### DEV_DB_campi (6 campi)
1. id
2. name (Nome modello)
3. type (regolare/breve/tempo estivo/noleggio)
4. template_content (Contenuto template)
5. is_active
6. notes

**TOTALE: 6 DEV UNIT**

### DEV_DB_relazioni (1 relazione)
1. ContractTemplate -> Contract (one-to-many)

**TOTALE: 1 DEV UNIT**

### DEV_LOGIC_CRUD (0 - template)
Nessun CRUD (gestione template)

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (1 workflow)
1. Selezione modello → precompilazione contratto

**TOTALE: 1 DEV UNIT**

### DEV_UI_form (6 campi input)
Ogni campo = 1 DEV UNIT

**TOTALE: 6 DEV UNIT**

### DEV_UI_lista (0)
Nessuna lista (configurazione)

**TOTALE: 0 DEV UNIT**

### DEV_UI_stampa (0)
Nessuna stampa (è un template)

**TOTALE: 0 DEV UNIT**

**TOTALE FUNZIONALITÀ: 6 + 1 + 0 + 1 + 6 + 0 + 0 = 14 DEV UNIT**

---

## 27. GENERAZIONE PDF CONTRATTI

### DEV_DB_campi (0)
Nessun campo (generazione da Contract)

**TOTALE: 0 DEV UNIT**

### DEV_DB_relazioni (0)
Nessuna relazione

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_CRUD (0)
Nessun CRUD

**TOTALE: 0 DEV UNIT**

### DEV_LOGIC_workflow (1 workflow)
1. Generazione PDF da template + dati contratto

**TOTALE: 1 DEV UNIT**

### DEV_UI_form (0)
Nessun form

**TOTALE: 0 DEV UNIT**

### DEV_UI_lista (0)
Nessuna lista

**TOTALE: 0 DEV UNIT**

### DEV_UI_stampa (3 tipi PDF + 15 campi per tipo)
**Tipi PDF:**
1. Contratto regolare
2. Contratto breve
3. Contratto tempo estivo

**Campi per PDF (15 campi × 3 tipi = 45 DEV UNIT):**
Stessi 15 campi del punto 8 (Contratti)

**TOTALE: 3 + 45 = 48 DEV UNIT**

**TOTALE FUNZIONALITÀ: 0 + 0 + 0 + 1 + 0 + 0 + 48 = 49 DEV UNIT**

---

## 28. CONTRATTI STORICI / FATTURE STORICHE / PAGAMENTI STORICI

Queste sono solo visualizzazioni (read-only), quindi:

### DEV_DB_campi
Stessi campi delle entità principali (già contati)

**TOTALE: 0 DEV UNIT** (già contati)

### DEV_DB_relazioni
Stesse relazioni (già contate)

**TOTALE: 0 DEV UNIT** (già contate)

### DEV_LOGIC_CRUD (1 action)
1. index (solo lettura)

**TOTALE: 1 DEV UNIT per tipo**

### DEV_LOGIC_workflow (0)
Nessun workflow

**TOTALE: 0 DEV UNIT**

### DEV_UI_form (0)
Nessun form (solo visualizzazione)

**TOTALE: 0 DEV UNIT**

### DEV_UI_lista (stesse colonne + filtri)
Stesse colonne delle liste principali + filtri aggiuntivi per storico

**TOTALE: ~15 DEV UNIT per tipo** (colonne + filtri)

### DEV_UI_stampa (0)
Nessuna stampa

**TOTALE: 0 DEV UNIT**

**TOTALE PER OGNI STORICO: 0 + 0 + 1 + 0 + 0 + 15 + 0 = 16 DEV UNIT**

**TOTALE 3 STORICI: 16 × 3 = 48 DEV UNIT**

---

## RIEPILOGO TOTALE

| Funzionalità | DB_campi | DB_rel | CRUD | Workflow | UI_form | UI_lista | UI_stampa | TOTALE |
|--------------|----------|--------|------|----------|---------|----------|-----------|--------|
| Anagrafica studenti | 25 | 6 | 7 | 0 | 25 | 20 | 0 | 83 |
| Anagrafica genitori | 18 | 2 | 7 | 0 | 18 | 16 | 0 | 61 |
| Anagrafica docenti | 23 | 3 | 7 | 0 | 23 | 18 | 0 | 74 |
| Gestione anno scolastico | 6 | 5 | 7 | 0 | 6 | 8 | 0 | 32 |
| Corsi e iscrizioni | 15 | 4 | 7 | 1 | 15 | 17 | 0 | 59 |
| Calendario lezioni | 5 | 2 | 7 | 1 | 5 | 10 | 0 | 30 |
| Primo contatto | 9 | 2 | 7 | 0 | 9 | 11 | 0 | 38 |
| Contratti da iscrizioni | 13 | 4 | 7 | 4 | 13 | 16 | 48 | 105 |
| Fatturazione/pagamenti | 18 | 5 | 7 | 3 | 18 | 20 | 13 | 84 |
| Piani pagamento | 7 | 2 | 0 | 2 | 7 | 0 | 0 | 18 |
| Automazioni sollecito | 5 | 2 | 0 | 2 | 0 | 9 | 0 | 18 |
| Fornitori | 10 | 2 | 7 | 0 | 10 | 12 | 0 | 41 |
| Registro elettronico | 10 | 4 | 7 | 2 | 10 | 16 | 0 | 49 |
| Recuperi lezioni | 8 | 3 | 0 | 2 | 8 | 13 | 0 | 34 |
| Conto orario docenti | 12 | 3 | 0 | 2 | 0 | 15 | 13 | 45 |
| Gestione supplenze | 8 | 3 | 0 | 2 | 0 | 13 | 0 | 26 |
| Attività extra base | 12 | 3 | 7 | 0 | 12 | 14 | 0 | 48 |
| Attività extra evolute | 12 | 4 | 7 | 2 | 12 | 14 | 0 | 51 |
| Presenze minimo | 6 | 3 | 7 | 0 | 6 | 13 | 0 | 35 |
| Gestione esami | 10 | 3 | 7 | 1 | 10 | 14 | 0 | 45 |
| Attestati frequenza | 5 | 1 | 0 | 1 | 0 | 0 | 9 | 16 |
| Comunicazioni evolute | 9 | 3 | 0 | 2 | 9 | 13 | 0 | 36 |
| Note operative | 6 | 2 | 0 | 0 | 6 | 0 | 0 | 14 |
| Gestione utenze | 8 | 2 | 7 | 1 | 8 | 11 | 0 | 37 |
| Controllo accessi | 4 | 2 | 0 | 2 | 0 | 0 | 0 | 8 |
| Modelli contratto | 6 | 1 | 0 | 1 | 6 | 0 | 0 | 14 |
| Generazione PDF contratti | 0 | 0 | 0 | 1 | 0 | 0 | 48 | 49 |
| Storici (3 tipi) | 0 | 0 | 3 | 0 | 0 | 45 | 0 | 48 |
| **TOTALE GENERALE** | **280** | **68** | **98** | **32** | **223** | **309** | **131** | **1141** |

