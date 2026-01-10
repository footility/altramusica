# Analisi Funzionale Data-Centrica - Sistema Gestionale L'Altramusica

**Data:** Dicembre 2024  
**Versione:** 1.0  
**Metodologia:** DEV UNIT / COSMIC-like  
**Scopo:** Documento per stima progetto software basata su modello data-centrico

---

## 1. CONTESTO DEL PROGETTO

### 1.1 Obiettivo del Sistema

Sistema gestionale integrato per la gestione completa di una scuola di musica cooperativa, che sostituisce l'attuale sistema basato su fogli Excel multipli e database Access. Il sistema deve gestire l'intero ciclo di vita: dal primo contatto con i prospect fino alla gestione contabile e didattica degli allievi iscritti.

**Valore Aggiunto:**
- Unificazione dati dispersi in più fogli Excel
- Automatizzazione processi manuali (fatturazione, solleciti, conto orario)
- Visione d'insieme economica e didattica
- Tracciamento completo storico operazioni
- Comunicazioni integrate multi-canale

### 1.2 Utenti Coinvolti

**Amministratori:**
- Direttrice (Barbara Tripodi) - gestione completa, configurazione, reportistica
- Personale segreteria (Giovanni, Giulia) - inserimento dati, gestione iscrizioni, fatturazione

**Utenti Operativi:**
- Insegnanti (22 docenti) - accesso registro elettronico, presenze, visualizzazione conto orario
- Supplenti (3-4 settimanali) - accesso temporaneo registro, presenze
- Genitori/Allievi (300+ allievi) - area personale futura (non in prima fase)

**Utenti Esterni:**
- Commercialista - accesso dati contabili (futuro)
- Sistema Cassetto Fiscale - integrazione automatica fatture

### 1.3 Confini del Sistema

**Incluso:**
- Gestione anagrafica completa (studenti, genitori, insegnanti, fornitori)
- Gestione iscrizioni e corsi
- Fatturazione elettronica e pagamenti
- Registro elettronico e presenze
- Conto orario insegnanti
- Comunicazioni (email, SMS)
- Magazzino strumenti e libri
- Reportistica e statistiche

**Escluso:**
- Sistema di bilancio contabile completo (gestito da commercialista esterno)
- Area personale allievi/genitori (futura)
- App mobile dedicata
- Integrazione pagamenti online (futura)
- Sistema di prenotazione aule avanzato

**Interfacce Esterne:**
- Cassetto Fiscale (import fatture attive/passive)
- Sistema SMS gateway (invio comunicazioni)
- Sistema email (invio contratti, fatture, comunicazioni)

---

## 2. ENTITÀ DATI

### 2.1 AcademicYear (Anno Scolastico)

**Descrizione Funzionale:** Rappresenta l'esercizio scolastico (1 settembre - 31 agosto). Ogni operazione è associata a un anno scolastico. Il sistema supporta multi-esercizio con switch tra anni.

**Campi Principali:**
- Nome identificativo (es. "2024-2025")
- Data inizio (1 settembre)
- Data fine (31 agosto)
- Stato (attivo/archiviato)
- Anno di riferimento

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Entità master che filtra tutte le operazioni

**Relazioni:**
- Collegata a tutte le entità principali (Student, Enrollment, Invoice, Contract, Lesson)

---

### 2.2 Student (Allievo)

**Descrizione Funzionale:** Anagrafica completa dell'allievo. Gestisce il ciclo di vita: prospect → interessato → iscritto → ex-iscritto. Include dati anagrafici, didattici, amministrativi.

**Campi Principali:**
- Codice identificativo univoco
- Nome, cognome
- Data di nascita, età calcolata
- Codice fiscale (obbligatorio)
- Stato (prospect/interessato/iscritto/ex-iscritto)
- Scuola di provenienza
- Come ci ha conosciuto
- Note primo contatto
- Note didattiche
- Note amministrative
- Consensi privacy e foto
- Data ultimo contatto

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Entità centrale del sistema

**Relazioni:**
- Molti-a-molti con Guardian (genitori/tutori)
- Uno-a-molti con Enrollment (iscrizioni)
- Uno-a-molti con Contract (contratti)
- Uno-a-molti con Invoice (fatture)
- Uno-a-molti con Lesson (lezioni)
- Uno-a-molti con StudentAvailability (disponibilità oraria)
- Uno-a-molti con StudentLevel (livelli ABRSM)

---

### 2.3 Guardian (Genitore/Tutore)

**Descrizione Funzionale:** Anagrafica genitori/tutori. Supporta primo e secondo genitore. Gestisce relazioni multiple (separati, divorziati). Include dati contatto e fiscali.

**Campi Principali:**
- Nome, cognome
- Codice fiscale
- Tipo relazione (genitore/tutore)
- Telefoni (casa, lavoro, cellulari multipli)
- Email multiple (fino a 3)
- Indirizzo completo
- Consenso privacy
- Flag genitore principale
- Flag contatto fatturazione

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Dati fiscali e contatti critici

**Relazioni:**
- Molti-a-molti con Student (tramite pivot con relationship_type, is_primary, is_billing_contact)
- Uno-a-molti con Communication (comunicazioni)

---

### 2.4 Teacher (Insegnante)

**Descrizione Funzionale:** Anagrafica insegnanti. Gestisce categorizzazione soci/non soci, contratti, documenti fiscali. Base per calcolo conto orario.

**Campi Principali:**
- Nome, cognome
- Codice fiscale
- Partita IVA (se applicabile)
- IBAN
- Tipo contratto (partita IVA/Co.co.co/ritenuta d'acconto)
- Flag socio cooperativa
- Compenso orario base
- Note contrattuali
- Documenti allegati

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Dati fiscali e contrattuali critici

**Relazioni:**
- Uno-a-molti con Course (corsi assegnati)
- Uno-a-molti con Lesson (lezioni tenute)
- Uno-a-molti con TeacherPayment (pagamenti)

---

### 2.5 CourseType (Tipo Corso)

**Descrizione Funzionale:** Catalogo tipologie corsi offerti (circa 10 tipi fissi). Definisce caratteristiche standard: strumento, durata lezione, tipo (individuale/collettivo).

**Campi Principali:**
- Nome tipo corso
- Strumento associato
- Durata lezione standard
- Tipo (individuale/collettivo)
- Prezzo unitario lezione
- Descrizione

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Media - Dati di configurazione

**Relazioni:**
- Uno-a-molti con Course (istanze corsi)

---

### 2.6 Course (Corso)

**Descrizione Funzionale:** Istanza concreta di corso per un anno scolastico. Include scheduling (giorno, orario, aula), insegnante assegnato, numero studenti. Modificabile durante l'anno.

**Campi Principali:**
- Codice corso
- Nome descrittivo
- Tipo corso (riferimento)
- Insegnante assegnato
- Data inizio, data fine
- Giorno settimana
- Orario inizio, orario fine
- Numero massimo studenti
- Numero studenti attuali
- Stato (attivo/sospeso/chiuso)
- Prezzo per lezione
- Lezioni per settimana
- Settimane per anno

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Entità operativa centrale

**Relazioni:**
- Molti-a-uno con CourseType
- Molti-a-uno con Teacher
- Uno-a-molti con Enrollment (iscrizioni)
- Uno-a-molti con Lesson (lezioni programmate)

---

### 2.7 Enrollment (Iscrizione)

**Descrizione Funzionale:** Associazione studente-corso. Include periodo iscrizione, stato, date. Base per calcolo costi e fatturazione.

**Campi Principali:**
- Studente (riferimento)
- Corso (riferimento)
- Data inizio iscrizione
- Data fine iscrizione
- Numero settimane
- Numero lezioni totali
- Stato (attiva/sospesa/chiusa)
- Note

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Base per fatturazione e didattica

**Relazioni:**
- Molti-a-uno con Student
- Molti-a-uno con Course
- Uno-a-molti con Contract (può generare contratto)

---

### 2.8 Contract (Contratto)

**Descrizione Funzionale:** Contratto di iscrizione. Workflow: draft → inviato → visualizzato → accettato. Include prodotti acquistati, rate, scadenze. Genera fatture.

**Campi Principali:**
- Numero contratto
- Studente (riferimento)
- Anno scolastico (riferimento)
- Tipo contratto (regolare/breve/tempo estivo)
- Data creazione
- Data invio
- Data visualizzazione
- Data accettazione
- Stato (draft/sent/viewed/accepted/rejected)
- Importo totale
- Numero rate
- Note regolamento

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Documento legale e base fatturazione

**Relazioni:**
- Molti-a-uno con Student
- Uno-a-molti con ContractItem (prodotti nel contratto)
- Uno-a-molti con PaymentPlan (rate)
- Uno-a-molti con Invoice (fatture generate)

---

### 2.9 PaymentPlan (Piano di Pagamento)

**Descrizione Funzionale:** Rateizzazione del contratto. Rate flessibili (non solo 3), con scadenze personalizzate basate su calendario lezioni.

**Campi Principali:**
- Contratto (riferimento)
- Numero rata
- Importo rata
- Data scadenza
- Stato (da pagare/parzialmente pagata/pagata)
- Importo pagato
- Note

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Gestione crediti critica

**Relazioni:**
- Molti-a-uno con Contract
- Uno-a-molti con Payment (pagamenti ricevuti)

---

### 2.10 Invoice (Fattura)

**Descrizione Funzionale:** Fattura elettronica emessa solo a pagamento avvenuto. Include importi, scadenze, stato pagamento. Collegata a contratti e pagamenti.

**Campi Principali:**
- Numero fattura
- Studente (riferimento)
- Anno scolastico (riferimento)
- Data emissione
- Data scadenza
- Importo totale
- Importo pagato
- Stato (draft/emessa/pagata/parzialmente pagata/scaduta)
- Note

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Documento fiscale

**Relazioni:**
- Molti-a-uno con Student
- Uno-a-molti con InvoiceItem (righe fattura)
- Uno-a-molti con Payment (pagamenti)
- Uno-a-molti con CreditNote (note di credito)

---

### 2.11 Payment (Pagamento)

**Descrizione Funzionale:** Tracciamento pagamenti ricevuti. Supporta pagamenti parziali, eccessivi, multipli. Importazione da estratti conto CSV.

**Campi Principali:**
- Fattura (riferimento)
- Piano pagamento (riferimento)
- Importo pagato
- Data pagamento
- Metodo pagamento (contanti/bonifico/Satispay/altro)
- Note
- Riferimento estratto conto

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Dati contabili critici

**Relazioni:**
- Molti-a-uno con Invoice
- Molti-a-uno con PaymentPlan

---

### 2.12 Lesson (Lezione)

**Descrizione Funzionale:** Lezione programmata o effettuata. Include presenze, recuperi, utilizzo aule. Base per conto orario insegnanti.

**Campi Principali:**
- Corso (riferimento)
- Data lezione
- Orario inizio, orario fine
- Aula (riferimento)
- Insegnante (riferimento)
- Tipo (regolare/recupero/lezione libera)
- Stato (programmata/effettuata/cancellata)
- Note

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Base conto orario e didattica

**Relazioni:**
- Molti-a-uno con Course
- Molti-a-uno con Classroom
- Molti-a-uno con Teacher
- Uno-a-molti con Attendance (presenze)

---

### 2.13 Attendance (Presenza)

**Descrizione Funzionale:** Presenza allievo a lezione. Registrata da insegnante/supplente. Base per countdown lezioni e conto orario.

**Campi Principali:**
- Lezione (riferimento)
- Studente (riferimento)
- Presente (sì/no)
- Data registrazione
- Registrato da (insegante/supplente)

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Dati didattici e contabili

**Relazioni:**
- Molti-a-uno con Lesson
- Molti-a-uno con Student

---

### 2.14 TeacherPayment (Pagamento Insegnante)

**Descrizione Funzionale:** Pagamento insegnante basato su conto orario. Include rateizzazione flessibile, voci forfettarie, bonus a consuntivo.

**Campi Principali:**
- Insegnante (riferimento)
- Anno scolastico (riferimento)
- Tipo voce (conto orario/forfait/bonus)
- Importo
- Data pagamento
- Numero rata
- Note

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Dati contabili e contrattuali

**Relazioni:**
- Molti-a-uno con Teacher

---

### 2.15 Classroom (Aula)

**Descrizione Funzionale:** Spazi fisici della scuola. Gestione disponibilità per lezioni e recuperi.

**Campi Principali:**
- Nome aula
- Codice identificativo
- Capacità
- Strumenti disponibili
- Note

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Media - Supporto operativo

**Relazioni:**
- Uno-a-molti con Lesson

---

### 2.16 Instrument (Strumento Musicale)

**Descrizione Funzionale:** Inventario strumenti musicali. Gestione cespiti (valore bilancio), noleggio, vendita.

**Campi Principali:**
- Codice strumento (es. "VL 20")
- Tipo strumento
- Valore acquisto
- Data acquisto
- Stato (disponibile/noleggiato/venduto/ritirato)
- Cliente noleggio (riferimento)
- Note manutenzione

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Media - Dati inventario e bilancio

**Relazioni:**
- Uno-a-molti con InstrumentRental (noleggi)

---

### 2.17 Book (Libro Didattico)

**Descrizione Funzionale:** Catalogo libri didattici. Tracking vendite e rimanenze.

**Campi Principali:**
- Titolo
- Autore
- ISBN
- Editore
- Prezzo
- Quantità stock
- Fornitore

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Bassa - Supporto operativo

**Relazioni:**
- Uno-a-molti con BookDistribution (distribuzioni)

---

### 2.18 ExtraActivity (Attività Extra)

**Descrizione Funzionale:** Attività extracurriculari (orchestra, coro). Gestione iscrizioni, convocazioni, presenze.

**Campi Principali:**
- Nome attività
- Tipo (orchestra/coro/altro)
- Anno scolastico (riferimento)
- Calendario date (12 date/anno)
- Costo partecipazione
- Filtri composizione (livello, strumento)

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Media - Supporto didattico

**Relazioni:**
- Uno-a-molti con ExtraActivityEnrollment (iscrizioni)

---

### 2.19 Communication (Comunicazione)

**Descrizione Funzionale:** Tracciamento comunicazioni inviate (email, SMS). Log per audit e follow-up.

**Campi Principali:**
- Destinatario (studente/genitore)
- Tipo (email/SMS/WhatsApp)
- Oggetto
- Contenuto
- Data invio
- Stato (inviata/consegnata/errore)
- Template utilizzato

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Media - Supporto operativo e compliance

**Relazioni:**
- Molti-a-uno con Student o Guardian

---

### 2.20 Calendar (Calendario Lezioni)

**Descrizione Funzionale:** Calendario annuale con giornate attive e sospensioni. Base per calcolo settimane e scadenze rate.

**Campi Principali:**
- Anno scolastico (riferimento)
- Data
- Tipo (giornata attiva/sospensione/festività)
- Note

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Base calcoli temporali

**Relazioni:**
- Molti-a-uno con AcademicYear

---

### 2.21 Supplier (Fornitore)

**Descrizione Funzionale:** Anagrafica fornitori. Integrazione cassetto fiscale per fatture passive.

**Campi Principali:**
- Ragione sociale
- Partita IVA
- Codice fiscale
- Indirizzo
- IBAN
- Categoria spesa
- Note

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Media - Supporto contabile

**Relazioni:**
- Uno-a-molti con Invoice (fatture passive)

---

### 2.22 User (Utente Sistema)

**Descrizione Funzionale:** Utenti del sistema con autenticazione e autorizzazioni.

**Campi Principali:**
- Email (username)
- Password (hash)
- Nome
- Ruolo (admin/segreteria/insegnante/supplente)
- Permessi granulari
- Stato (attivo/sospeso)

**Persistenza:** Sì (tabella dedicata)

**Criticità:** Alta - Sicurezza sistema

**Relazioni:**
- Collegato a Teacher (se insegnante)

---

## 3. FUNZIONALITÀ

### 3.1 Gestione Anagrafica Studenti

**Descrizione:** CRUD completo anagrafica studenti. Gestione ciclo di vita: prospect → interessato → iscritto. Filtri avanzati per ricerca e categorizzazione.

**Entità Coinvolte:**
- Student (create, read, update, delete)
- Guardian (read, update - relazioni)
- AcademicYear (read - filtro)

**Tipo Operazione:** Create, Read, Update, Delete

**Attore:** Segreteria, Admin

**Output Visibile:** Sì - Liste, form, dettaglio studente

**Movimenti Dati:**
- Input: Dati anagrafici, note, consensi
- Output: Scheda studente completa, liste filtrate
- Storage: Persistenza completa con storico

---

### 3.2 Primo Contatto

**Descrizione:** Form pubblico per raccolta dati iniziali prospect. Link precompilabili. Conversione prospect → studente.

**Entità Coinvolte:**
- Student (create - stato prospect)
- Communication (create - invio link)

**Tipo Operazione:** Create, Read

**Attore:** Prospect (esterno), Segreteria

**Output Visibile:** Sì - Form pubblico, dashboard prospect

**Movimenti Dati:**
- Input: Dati primo contatto (nome, data nascita, telefono, email, note)
- Output: Record prospect creato, link generato
- Storage: Persistenza temporanea fino a conversione

---

### 3.3 Gestione Iscrizioni

**Descrizione:** Creazione iscrizione studente-corso. Calcolo automatico settimane e costi basato su calendario. Gestione modifiche durante anno.

**Entità Coinvolte:**
- Enrollment (create, read, update)
- Student (read)
- Course (read)
- Calendar (read - calcolo settimane)
- AcademicYear (read)

**Tipo Operazione:** Create, Read, Update

**Attore:** Segreteria

**Output Visibile:** Sì - Form iscrizione, lista iscrizioni, dettaglio

**Movimenti Dati:**
- Input: Studente, corso, date inizio/fine
- Calcolo: Numero settimane da calendario, costi
- Output: Iscrizione creata con dati calcolati
- Storage: Persistenza completa

---

### 3.4 Generazione Contratto

**Descrizione:** Creazione contratto da iscrizione. Workflow: draft → invio → visualizzazione → accettazione. Generazione PDF, link precompilato.

**Entità Coinvolte:**
- Contract (create, read, update)
- Enrollment (read)
- Student (read)
- Guardian (read)
- PaymentPlan (create - rate)

**Tipo Operazione:** Create, Read, Update

**Attore:** Segreteria, Studente/Genitore (visualizzazione/accettazione)

**Output Visibile:** Sì - PDF contratto, link web, dashboard stato

**Movimenti Dati:**
- Input: Iscrizione, prodotti, rateizzazione scelta
- Calcolo: Importi, scadenze rate basate su calendario
- Output: PDF contratto, link web, email invio
- Storage: Persistenza completa con tracking stato

---

### 3.5 Fatturazione

**Descrizione:** Generazione fattura elettronica solo a pagamento avvenuto. Calcolo automatico importi da contratti. Gestione proforme.

**Entità Coinvolte:**
- Invoice (create, read, update)
- Contract (read)
- PaymentPlan (read)
- Payment (read - verifica pagamento)

**Tipo Operazione:** Create, Read, Update

**Attore:** Segreteria, Sistema (automatico)

**Output Visibile:** Sì - Fattura PDF, lista fatture, stato pagamento

**Movimenti Dati:**
- Input: Contratto accettato, pagamento ricevuto
- Calcolo: Importi da rate, totali
- Output: Fattura elettronica XML/PDF, invio SDI
- Storage: Persistenza completa

---

### 3.6 Gestione Pagamenti

**Descrizione:** Registrazione pagamenti ricevuti. Importazione estratti conto CSV. Gestione crediti, acconti, compensazioni tra fratelli.

**Entità Coinvolte:**
- Payment (create, read, update)
- Invoice (read, update - aggiornamento stato)
- PaymentPlan (read, update - aggiornamento stato)

**Tipo Operazione:** Create, Read, Update

**Attore:** Segreteria

**Output Visibile:** Sì - Form pagamento, lista pagamenti, saldi

**Movimenti Dati:**
- Input: Importo, data, metodo pagamento, riferimento fattura
- Calcolo: Saldi residui, crediti/debiti
- Output: Pagamento registrato, fatture aggiornate
- Storage: Persistenza completa

---

### 3.7 Recupero Crediti

**Descrizione:** Sistema solleciti automatici configurabili. Monitoraggio pagamenti in ritardo. Comunicazioni massive.

**Entità Coinvolte:**
- Invoice (read - filtri scadute)
- PaymentPlan (read - filtri scadute)
- Communication (create - invio solleciti)
- Student (read - dati contatto)

**Tipo Operazione:** Read, Create (comunicazioni)

**Attore:** Sistema (automatico), Segreteria (monitoraggio)

**Output Visibile:** Sì - Liste scadenze, report recupero crediti

**Movimenti Dati:**
- Input: Filtri scadenze, configurazione solleciti
- Calcolo: Rate scadute, giorni di ritardo
- Output: Solleciti inviati, report
- Storage: Log comunicazioni

---

### 3.8 Registro Elettronico

**Descrizione:** Sistema registro per presenze lezioni. Accesso insegnanti/supplenti. Visualizzazione calendario lezioni.

**Entità Coinvolte:**
- Lesson (read)
- Attendance (create, read, update)
- Student (read)
- Course (read)

**Tipo Operazione:** Create, Read, Update

**Attore:** Insegnante, Supplente

**Output Visibile:** Sì - Calendario lezioni, form presenze, storico

**Movimenti Dati:**
- Input: Presenze allievi, note lezione
- Calcolo: Countdown lezioni rimanenti
- Output: Presenze registrate, statistiche frequenza
- Storage: Persistenza completa

---

### 3.9 Conto Orario Insegnanti

**Descrizione:** Calcolo automatico conto orario basato su lezioni effettuate. Prospetto previsionale. Gestione pagamenti rateizzati, compensi differenziati, voci forfettarie.

**Entità Coinvolte:**
- Teacher (read)
- Lesson (read - lezioni effettuate)
- Attendance (read - presenze)
- TeacherPayment (create, read, update)

**Tipo Operazione:** Read, Create, Update

**Attore:** Segreteria, Admin

**Output Visibile:** Sì - Prospetto conto orario, storico pagamenti

**Movimenti Dati:**
- Input: Lezioni effettuate, presenze, voci forfettarie
- Calcolo: Ore totali, importi, rateizzazione
- Output: Prospetto conto orario, fatture insegnanti
- Storage: Persistenza completa

---

### 3.10 Gestione Supplenti

**Descrizione:** Sistema supplenze con accesso registro temporaneo. Trasferimento lezione insegnante → supplente per calcolo pagamenti.

**Entità Coinvolte:**
- Lesson (update - cambio insegnante)
- Teacher (read - supplente)
- Attendance (create - presenze supplente)
- User (create - account temporaneo)

**Tipo Operazione:** Create, Read, Update

**Attore:** Segreteria, Supplente

**Output Visibile:** Sì - Form supplenza, registro supplente

**Movimenti Dati:**
- Input: Insegnante assente, supplente, data
- Calcolo: Trasferimento lezione, ritenute d'acconto
- Output: Lezione trasferita, accesso supplente
- Storage: Persistenza con storico

---

### 3.11 Proposta Oraria

**Descrizione:** Sistema composizione orari. Matching disponibilità allievi/insegnanti. Proposta definitiva con giorno/orario/classe/insegnante precisi.

**Entità Coinvolte:**
- Student (read - disponibilità)
- StudentAvailability (read)
- Teacher (read - disponibilità)
- Course (create, update - scheduling)
- Classroom (read - disponibilità)

**Tipo Operazione:** Read, Create, Update

**Attore:** Segreteria

**Output Visibile:** Sì - Griglia orari, proposta visualizzata

**Movimenti Dati:**
- Input: Disponibilità allievi, insegnanti, aule
- Calcolo: Matching ottimale, conflitti
- Output: Proposta orario, corsi schedulati
- Storage: Persistenza scheduling

---

### 3.12 Attività Extra (Orchestra)

**Descrizione:** Gestione attività extracurriculari. Filtri composizione gruppi (livello, strumento, iscrizione). Convocazioni con calendario 12 date/anno.

**Entità Coinvolte:**
- ExtraActivity (create, read, update)
- ExtraActivityEnrollment (create, read)
- Student (read - filtri)
- StudentLevel (read - livelli)
- Communication (create - convocazioni)

**Tipo Operazione:** Create, Read, Update

**Attore:** Segreteria

**Output Visibile:** Sì - Liste attività, composizione gruppi, calendario convocazioni

**Movimenti Dati:**
- Input: Attività, filtri composizione, calendario date
- Calcolo: Gruppi formati, convocazioni
- Output: Liste partecipanti, convocazioni inviate
- Storage: Persistenza completa

---

### 3.13 Integrazione Cassetto Fiscale

**Descrizione:** Importazione automatica fatture attive e passive da cassetto fiscale. Incrocio e categorizzazione spese.

**Entità Coinvolte:**
- Invoice (create - fatture passive)
- Supplier (create, read - da fatture)
- Teacher (read - fatture insegnanti)

**Tipo Operazione:** Read (esterno), Create

**Attore:** Sistema (automatico)

**Output Visibile:** Sì - Liste fatture importate, report spese

**Movimenti Dati:**
- Input: Fatture da cassetto fiscale (API esterna)
- Calcolo: Categorizzazione, matching fornitori
- Output: Fatture importate, spese categorizzate
- Storage: Persistenza completa

---

### 3.14 Flusso di Cassa

**Descrizione:** Visualizzazione flusso di cassa entrate/uscite. Inserimento pagamenti cassa. Importazione estratti conto. Riepiloghi mensili.

**Entità Coinvolte:**
- Payment (read - entrate)
- Invoice (read - uscite previste)
- Supplier (read - fornitori)

**Tipo Operazione:** Read, Create (pagamenti cassa)

**Attore:** Segreteria, Admin

**Output Visibile:** Sì - Dashboard flusso cassa, grafici, report mensili

**Movimenti Dati:**
- Input: Pagamenti cassa, estratti conto CSV
- Calcolo: Totali entrate/uscite, saldi, previsioni
- Output: Dashboard, grafici, report
- Storage: Persistenza pagamenti

---

### 3.15 Reportistica e Statistiche

**Descrizione:** Dashboard statistiche (nuovi/vecchi iscritti, preiscrizioni, rinnovi, distribuzione età, partecipazione orchestra). Export dati. Confronto multi-anno.

**Entità Coinvolte:**
- Student (read - aggregazioni)
- Enrollment (read - aggregazioni)
- Invoice (read - aggregazioni)
- AcademicYear (read - filtri multi-anno)

**Tipo Operazione:** Read (aggregazioni)

**Attore:** Admin, Segreteria

**Output Visibile:** Sì - Dashboard, grafici, report tabellari, export

**Movimenti Dati:**
- Input: Filtri periodo, tipo statistiche
- Calcolo: Aggregazioni, percentuali, confronti
- Output: Dashboard, grafici, report, file export
- Storage: Query on-demand (non persistenza)

---

## 4. REQUISITI TRASVERSALI

### 4.1 Sicurezza

**Autenticazione:**
- Sistema login con email/password
- Hash password sicuro (bcrypt)
- Sessioni gestite
- Logout automatico inattività

**Autorizzazioni:**
- Ruoli: Admin, Segreteria, Insegnante, Supplente
- Permessi granulari per area funzionale
- Middleware controllo accessi
- Insegnanti: solo registro, no dati economici
- Supplenti: accesso temporaneo limitato

**Protezione Dati:**
- Crittografia dati sensibili (codici fiscali, IBAN)
- HTTPS obbligatorio
- Validazione input (XSS, SQL injection)
- CSRF protection

**Criticità:** Alta - Dati personali e fiscali sensibili

---

### 4.2 Logging e Audit

**Log Operazioni:**
- Tracciamento modifiche dati critici (fatture, contratti, pagamenti)
- Log accessi utenti
- Log invio comunicazioni
- Log importazioni dati

**Audit Trail:**
- Chi ha modificato cosa e quando
- Storico stati (contratti, fatture)
- Storico pagamenti
- Storico modifiche corsi

**Retention:**
- Log conservati per compliance GDPR
- Storico completo per audit contabile

**Criticità:** Alta - Compliance e audit

---

### 4.3 Test

**Test Unitari:**
- Modelli e logica business
- Calcoli (settimane, costi, conto orario)
- Validazioni

**Test Funzionali:**
- Workflow completi (iscrizione → contratto → fattura → pagamento)
- Integrazioni (cassetto fiscale, SMS)
- Autorizzazioni e permessi

**Test Integrazione:**
- Import dati
- Export report
- Comunicazioni esterne

**Criticità:** Media - Qualità e affidabilità

---

### 4.4 Integrazioni

**Cassetto Fiscale:**
- API import fatture attive/passive
- Sincronizzazione periodica
- Matching automatico fornitori

**SMS Gateway:**
- Invio SMS comunicazioni
- Tracking consegne
- Gestione costi

**Email:**
- Invio contratti, fatture, comunicazioni
- Template personalizzabili
- Tracking apertura (futuro)

**Criticità:** Media - Funzionalità core dipendenti

---

### 4.5 UI Complexity

**Interfacce Principali:**
- Dashboard amministrativa (statistiche, alert)
- Liste con filtri avanzati (studenti, iscrizioni, fatture)
- Form complessi (iscrizione, contratto, fatturazione)
- Calendario interattivo (lezioni, convocazioni)

**Responsive Design:**
- Desktop-first (uso principale)
- Tablet support (insegnanti registro)
- Mobile base (visualizzazione)

**UX Requirements:**
- Navigazione intuitiva
- Feedback operazioni (successo/errore)
- Conferme azioni critiche
- Help contestuale

**Criticità:** Media - Usabilità sistema

---

## 5. ASSUNZIONI E LIMITI

### 5.1 Esclusioni Esplicite

**Non Incluso:**
- Sistema bilancio contabile completo (gestito da commercialista esterno)
- Area personale allievi/genitori con accesso web (futura)
- App mobile dedicata
- Integrazione pagamenti online (stripe, paypal) - futura
- Sistema prenotazione aule avanzato con calendario visuale
- Gestione inventario libri dettagliata (solo tracking vendite)
- Sistema di valutazioni/esami completo (solo base)

**Motivazione:** Focus su funzionalità core per prima fase. Estensioni future valutabili.

---

### 5.2 Semplificazioni

**Rateizzazione:**
- Rate flessibili ma calcolo manuale per casi complessi (es. compensazioni tra fratelli)
- Non automazione completa riconciliazione pagamenti parziali/eccessivi

**Conto Orario:**
- Calcolo automatico base, ma voci forfettarie e bonus gestiti manualmente
- Rateizzazione insegnanti configurabile ma non completamente automatizzata

**Comunicazioni:**
- Template base, personalizzazione avanzata futura
- Tracking apertura email non in prima fase

**Reportistica:**
- Dashboard base, analisi avanzate future
- Export base (CSV, Excel), formati avanzati futuri

**Motivazione:** Priorità funzionalità core. Raffinamenti incrementali.

---

### 5.3 Limitazioni Tecniche

**Performance:**
- Sistema progettato per ~300 allievi attivi
- Scalabilità futura valutabile

**Backup:**
- Backup automatici giornalieri
- Retention 30 giorni (configurabile)

**Manutenzione:**
- Aggiornamenti framework periodici
- Supporto browser moderni (ultimi 2 versioni)

---

### 5.4 Dipendenze Esterne

**Servizi Richiesti:**
- Hosting web con PHP/Laravel
- Database MySQL/PostgreSQL
- Servizio SMS gateway (costo per SMS)
- Accesso cassetto fiscale (se disponibile API)

**Servizi Opzionali:**
- CDN per asset statici
- Servizio backup cloud

---

## 6. METRICHE PER STIMA

### 6.1 Entità Dati

**Totale Entità:** 22 entità principali

**Distribuzione Complessità:**
- Alta criticità: 12 entità (Student, Guardian, Teacher, Course, Enrollment, Contract, Invoice, Payment, Lesson, Attendance, TeacherPayment, User)
- Media criticità: 7 entità (CourseType, Classroom, Instrument, Book, ExtraActivity, Communication, Supplier)
- Bassa criticità: 3 entità (Book, Calendar, altri supporto)

**Campi Totali Stimati:** ~250-300 campi distribuiti tra entità

---

### 6.2 Movimenti Dati

**Operazioni CRUD:**
- Create: ~15 funzionalità principali
- Read: ~20 funzionalità (liste, dettagli, report)
- Update: ~12 funzionalità (modifiche durante ciclo vita)
- Delete: ~5 funzionalità (soft delete principalmente)

**Calcoli Complessi:**
- Calcolo settimane da calendario
- Calcolo costi e rate da contratti
- Calcolo conto orario insegnanti
- Matching disponibilità orarie
- Aggregazioni statistiche

**Integrazioni Esterne:**
- Cassetto Fiscale (import)
- SMS Gateway (invio)
- Email (invio)

---

### 6.3 Complessità Trasversali

**Sicurezza:**
- 4 ruoli utente
- Permessi granulari per area
- Crittografia dati sensibili

**Logging:**
- Audit trail completo operazioni critiche
- Log comunicazioni
- Storico modifiche

**UI:**
- ~25 interfacce principali
- Filtri avanzati multipli
- Form complessi (10+ campi)
- Dashboard con grafici

---

**Fine Documento**

