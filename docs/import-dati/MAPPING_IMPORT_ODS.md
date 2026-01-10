# Mapping Importazione Dati ODS

## File: db 2025-26 gestionale.ods

### Foglio: dati
**Righe:** 485 (circa 300 studenti)

### Mapping Colonne -> Database

#### STUDENTI (Students)
- **G** (Cognome) -> `last_name`
- **H** (Nome) -> `first_name`
- **v** (cod. fiscale allievo) -> `tax_code`
- **p** (nato il) -> `birth_date`
- **o** (età) -> calcolato da birth_date
- **q** (minore) -> `is_minor` (sì/no)
- **~** (Indirizzo Allievo) -> `address`
- **** (cap) -> `postal_code`
- **** (Città) -> `city`
- **y** (Cell 3/ allievo) -> `phone`
- **}** (Mail allievo) -> `email`
- **F** (data di arrivo) -> `first_contact_date`
- **J** (come è venuto a sapere di noi) -> `source` (note)
- **M** (note prove e didattiche) -> `notes`
- **N** (note varie) -> `notes`

#### GENITORI/TUTORI (Guardians)
- **r** (cognome genitore 1) -> `first_name` (Guardian 1)
- **s** (nome genitore 1) -> `last_name` (Guardian 1)
- **t** (cognome genitore 2) -> `first_name` (Guardian 2)
- **u** (nome genitore 2) -> `last_name` (Guardian 2)
- **w** (Cell 1 /madre) -> `cell_1` (Guardian 1)
- **x** (Cell 2/padre) -> `cell_2` (Guardian 2)
- **{** (Mail 1) -> `email_1` (Guardian 1)
- **|** (Mail 2) -> `email_2` (Guardian 2)

#### CORSI/ISCRIZIONI (Enrollments)
Ogni studente può avere fino a 3 corsi (Corso 1, Corso 2, Corso 3)

**Corso 1:**
- **** (Corso 1) -> `course.name` (cercare o creare)
- **** (Docente strumento/canto) -> `teacher` (cercare o creare)
- **** (Aula strumento/canto) -> `classroom`
- **** (giorno strum) -> `day_of_week`
- **** (ora strumento/canto) -> `time_start`
- **** (docente lab) -> `lab_teacher`
- **** (giorno lab) -> `lab_day`
- **** (ora lab) -> `lab_time`
- **** (n. sett/anno) -> `weeks_per_year`
- **** (data inizio corso 1) -> `start_date`
- **** (sigla corso 1) -> `course.code`
- **** (descrizione corso 1) -> `course.description`
- **** (tipologia corso 1) -> `course_type` (cercare o creare)
- **** (€ lez) -> `price_per_lesson`
- **** (€ tot anno corso 1) -> `total_amount`

**Rate:**
- **** (sett 1° rata corso 1) -> settimane prima rata
- **** (€ tot 1° rata corso 1) -> importo prima rata
- **** (sett 2° rata corso 1) -> settimane seconda rata
- **** (€ tot 2° rata corso 1) -> importo seconda rata
- **** (sett 3° rata corso 1) -> settimane terza rata
- **** (€ tot 3° rata corso 1) -> importo terza rata

#### ORCHESTRA/CORO (Extra Activities)
- **h** (Orch 1) -> nome orchestra
- **i** (PYO) -> orchestra
- **k** (coro) -> coro
- **f** (musica di insieme) -> flag generale

#### STRUMENTI (Instruments)
- **Y** (fornitore strumento) -> `supplier`
- **Z** (noleggio/proprietà) -> tipo (rental/owned)
- **[** (provenienza) -> `source`
- **\** (tipo) -> `type`
- **]** (marca) -> `brand`
- **^** (mod) -> `model`
- **_** (misura) -> `size`
- **`** (cod) -> `serial_number`

#### DISPONIBILITÀ (StudentAvailability)
- **P** (disponibilità) -> note generale
- **Q** (lu) -> lunedì
- **R** (ma) -> martedì
- **S** (me) -> mercoledì
- **T** (gio) -> giovedì
- **U** (ve) -> venerdì
- **V** (sab) -> sabato
- **X** (note disponibilità) -> `notes`

#### LIVELLI (StudentLevel)
- **c** (livello) -> livello generale
- **d** (livello str.) -> livello strumento
- **e** (livello teoria) -> livello teoria

#### STATO/ISCRIZIONE
- **E** (stato) -> stato iscrizione (attivo/non attivo)
- **** (iscrizione) -> flag iscritto
- **** (nuovo iscritto) -> flag nuovo
- **** (n. iscritto) -> numero iscrizione
- **** (€ iscrizione) -> importo iscrizione
- **** (sconto) -> `discount_percentage` o `discount_amount`

#### CONTRATTI
- **D** (contratto) -> numero contratto
- **C** (conto orario) -> flag

#### PAGAMENTI
- **A** (Richiesta pagamento) -> flag
- **B** (pagato) -> flag

---

## File: Db Contratti 25-26.ods

### Foglio: Contratti
**Righe:** 416

### Mapping Colonne -> Database

#### CONTRATTI (Contracts)
- **A** (codice contratto) -> `contract_number`
- **B** (stato) -> `status` (draft/sent/signed)
- **C** (Cognome) -> `student.last_name`
- **D** (Nome) -> `student.first_name`
- **E** (data di nascita) -> `student.birth_date`
- **U** (data inizio corso 1) -> `start_date`
- **V** (Corso 1) -> `courses[0]`
- **W** (descrizione) -> `terms`
- **X** (tipologia) -> `type`
- **Y** (1 rata) -> importo prima rata
- **Z** (2 rata) -> importo seconda rata
- **[** (3 rata) -> importo terza rata
- **\** (tot corso1) -> totale corso 1
- **p** (Totale anno corsi) -> totale complessivo
- **q** (Orchestra 1) -> orchestra
- **r** (costo orch 1) -> costo orchestra
- **w** (data invio/consegna) -> `sent_date`
- **x** (data invio sollecito) -> data sollecito
- **y** (data ritorno) -> `signed_date`
- **z** (privacy) -> flag privacy
- **{** (note) -> `notes`

---

## File: dati lavoratori 25-26.ods

### Foglio: 2025-26
**Righe:** 27 (docenti)

### Mapping Colonne -> Database

#### DOCENTI (Teachers)
- **D** (Cognome) -> `last_name`
- **E** (Nome) -> `first_name`
- **K** (nato il) -> `birth_date`
- **M** (cod. fisc.) -> `tax_code`
- **N** (P. IVA) -> `vat_number`
- **O** (Carta Identità) -> `id_number`
- **P** (data rilascio) -> `id_issue_date`
- **Q** (ente rilascio) -> `id_issuer`
- **R** (Domicilio Indirizzo) -> `address`
- **S** (domicilio cap) -> `postal_code`
- **T** (domicilio città) -> `city`
- **U** (Residenza Indirizzo) -> `residence_address`
- **V** (residenza cap) -> `residence_postal_code`
- **W** (residenza città) -> `residence_city`
- **X** (IBAN) -> `iban`
- **Z** (tel abitazione) -> `phone_home`
- **[** (Cell 1) -> `phone_mobile`
- **\** (E-mail 1) -> `email`
- **B** (ruolo) -> `role` (socio/non socio)
- **F** (corso) -> corsi insegnati
- **G** (Lab teoria) -> laboratori
- **H** (inquadramento) -> `employment_type`

---

## File: Db Contabile 2025-26.ods

### Da analizzare

---

## File: Db Accessori 2025-26.ods

### Da analizzare

---

## File: Calendario 2025-26.ods

### Da analizzare

---

## Note Importanti

1. **Relazioni da gestire:**
   - Student -> Guardians (many-to-many)
   - Student -> Enrollments (one-to-many)
   - Enrollment -> Course (many-to-one)
   - Enrollment -> Teacher (many-to-one)
   - Student -> Contracts (one-to-many)
   - Contract -> Invoices (one-to-many)

2. **Valori da normalizzare:**
   - Giorni settimana: "lu", "ma", "me", "gio", "ve", "sab" -> "monday", "tuesday", etc.
   - Date: vari formati -> formato standard
   - Importi: stringhe con € -> decimal
   - Stati: vari formati -> enum standard

3. **Dati da dedurre:**
   - AcademicYear: da data inizio corso (settembre 2025 -> anno 2025-26)
   - CourseType: da tipologia corso
   - Teacher: da nome docente (cercare esistente o creare)

4. **Validazioni:**
   - Codice fiscale: formato e unicità
   - Email: formato valido
   - Date: formato e coerenza
   - Importi: numerici e positivi

