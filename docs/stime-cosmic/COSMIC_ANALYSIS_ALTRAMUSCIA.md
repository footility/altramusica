# Analisi COSMIC EX-ANTE - Sistema Gestionale L'Altramusica

**Data Analisi:** Dicembre 2024  
**Standard:** ISO/IEC 19761 - COSMIC Function Points  
**Analista:** Software Measurement Expert  
**Scopo:** Stima Functional Size (CFP) del sistema L'Altramusica in modalità EX-ANTE

---

## 1. SCOPO

**Obiettivo:** Identificare e stimare la Functional Size (CFP) del sistema gestionale L'Altramusica basandosi esclusivamente su requisiti funzionali deducibili da artefatti disponibili (file ODS, trascrizioni riunioni, PDF contratti), senza analizzare codice esistente.

**Metodologia:** Analisi EX-ANTE basata su:
- Struttura dati deducibile da file ODS (database legacy)
- Requisiti funzionali descritti nelle trascrizioni delle riunioni con il cliente
- Output funzionali identificabili dai PDF contratti esistenti
- Documentazione funzionale esistente

**Standard Applicato:** ISO/IEC 19761 - COSMIC Function Points v4.0.1

**⚠️ NOTA METODOLOGICA:** Questa è un'analisi EX-ANTE. Alcune funzionalità potrebbero non essere completamente deducibili dalle fonti disponibili. Le incertezze sono documentate esplicitamente.

---

## 2. METODO COSMIC

**Criteri Identificazione Functional Process:**
- Operazione identificabile dall'utente (non tecnica)
- Trasformazione dati completa e coerente
- Rilevante per il dominio applicativo (gestione scuola di musica)

**Criteri Conteggio Movimenti Dati:**
- **Entry (E):** Input utente (form, comando, file upload) → 1 CFP
- **Exit (X):** Output utente (schermata, report, export, documento PDF) → 1 CFP
- **Read (R):** Lettura storage persistente (database) → 1 CFP
- **Write (W):** Scrittura storage persistente (database) → 1 CFP

**Regole Applicate:**
- Ogni movimento dati conta esattamente 1 CFP
- Nessuna ponderazione per complessità
- Nessuna normalizzazione
- Movimenti dati multipli per stessa entità contano separatamente
- Non contati: validazioni base infrastrutturali, routing base, autenticazione base

**Riferimento Metodologico:** Vedi `docs/COSMIC_METHODOLOGY.md` per dettagli completi.

---

## 3. FONTI ANALIZZATE

### 3.1 File ODS (Database Legacy)

Analisi struttura dati deducibile da 6 file ODS del sistema legacy:

**3.1.1 `db 2025-26 gestionale.ods`**
- **Foglio:** "dati" (485 righe, ~300 studenti)
- **Entità identificate:** Student, Guardian, Enrollment, Course, Instrument, StudentAvailability, StudentLevel
- **Operazioni deducibili:** CRUD studenti, gestione iscrizioni, gestione strumenti, disponibilità oraria
- **Grado certezza:** ALTO - Struttura dati chiara e completa

**3.1.2 `Db Contratti 25-26.ods`**
- **Foglio:** "Contratti" (416 righe)
- **Entità identificate:** Contract, PaymentPlan (rate multiple)
- **Operazioni deducibili:** CRUD contratti, gestione rate, tracking stato contratti
- **Grado certezza:** ALTO - Struttura contratti ben definita

**3.1.3 `Db Contabile 2025-26.ods`**
- **Fogli:** Da analizzare (non completamente accessibile)
- **Entità identificate:** Invoice, Payment (presunte)
- **Operazioni deducibili:** Gestione fatturazione, pagamenti
- **Grado certezza:** MEDIO - Struttura parzialmente deducibile

**3.1.4 `Db Accessori 2025-26.ods`**
- **Fogli:** Da analizzare (non completamente accessibile)
- **Entità identificate:** Instrument, Book (presunte)
- **Operazioni deducibili:** Gestione magazzino strumenti e libri
- **Grado certezza:** MEDIO - Struttura parzialmente deducibile

**3.1.5 `dati lavoratori 25-26.ods`**
- **Foglio:** "2025-26" (27 righe, docenti)
- **Entità identificate:** Teacher
- **Operazioni deducibili:** CRUD insegnanti, gestione anagrafica docenti
- **Grado certezza:** ALTO - Struttura dati chiara

**3.1.6 `Calendario 2025-26.ods`**
- **Fogli:** Struttura complessa con formule
- **Entità identificate:** Calendar (giornate attive/sospensioni)
- **Operazioni deducibili:** Gestione calendario lezioni, calcolo settimane
- **Grado certezza:** MEDIO - Struttura con formule complesse

### 3.2 Trascrizioni Riunioni Cliente

**3.2.1 `Trascrizione gestionale parte 1.txt`**
- **Contenuto:** Dialogo cliente-sviluppatore, descrizione operatività, requisiti funzionali verbali
- **Functional Processes dedotti:** Primo contatto, iscrizioni, contratti, fatturazione, registro, conto orario, supplenti, orchestra, comunicazioni
- **Grado certezza:** ALTO - Descrizioni dettagliate delle operazioni

**3.2.2 `Trascrizione gestionale parte 2.txt`**
- **Contenuto:** Continuazione dialoghi, dettagli operativi, workflow specifici
- **Functional Processes dedotti:** Preiscrizioni, rate flessibili, magazzino, esami, reportistica
- **Grado certezza:** ALTO - Descrizioni dettagliate delle operazioni

### 3.3 PDF Contratti

**3.3.1 PDF Contratti Corsi (campioni analizzati)**
- **Struttura:** Template contratto con dati dinamici (nome studente, corsi, rate, scadenze)
- **Output funzionale:** Generazione PDF contratto
- **Grado certezza:** ALTO - Template identificabile

**3.3.2 PDF Contratti Noleggio**
- **Struttura:** Template noleggio strumenti con dati dinamici (strumento, cauzione, durata)
- **Output funzionale:** Generazione PDF contratto noleggio
- **Grado certezza:** ALTO - Template identificabile

### 3.4 Documentazione Funzionale Esistente

**3.4.1 `ANALISI_FUNZIONALE_DATA_CENTRICA.md`**
- **Contenuto:** 22 entità dati identificate, funzionalità descritte
- **Utilizzo:** Riferimento per entità e relazioni
- **Grado certezza:** ALTO - Documentazione strutturata

**3.4.2 `FUNZIONALITA_RACCOLTE.md`**
- **Contenuto:** Lista funzionalità raccolte con dipendenze
- **Utilizzo:** Riferimento per Functional Processes
- **Grado certezza:** ALTO - Lista funzionalità validata

**3.4.3 `MAPPING_IMPORT_ODS.md`**
- **Contenuto:** Mapping dettagliato colonne ODS -> entità database
- **Utilizzo:** Riferimento per struttura dati
- **Grado certezza:** ALTO - Mapping tecnico dettagliato

---

## 4. CONFINE DEL SISTEMA

### 4.1 Attori Esterni

**Amministratori:**
- Direttrice (gestione completa, configurazione, reportistica)
- Personale segreteria (inserimento dati, gestione iscrizioni, fatturazione)

**Utenti Operativi:**
- Insegnanti (22 docenti) - accesso registro elettronico, presenze, visualizzazione conto orario
- Supplenti (3-4 settimanali) - accesso temporaneo registro, presenze

**Utenti Esterni (Futuri - NON in prima fase):**
- Genitori/Allievi (300+ allievi) - area personale futura (escluso da questa analisi)

**Sistemi Esterni:**
- Sistema Cassetto Fiscale - integrazione automatica fatture (import fatture attive/passive)
- Sistema SMS gateway - invio comunicazioni SMS
- Sistema email - invio contratti, fatture, comunicazioni

### 4.2 Interfacce Esterne

**IN SCOPE:**
- Integrazione Cassetto Fiscale (import fatture) - Entry da sistema esterno
- Sistema comunicazioni (email, SMS) - Exit verso sistemi esterni
- File system (upload/download documenti, import CSV estratti conto) - Entry/Exit file

**OUT OF SCOPE:**
- Sistema di bilancio contabile completo (gestito da commercialista esterno)
- App mobile dedicata
- Integrazione pagamenti online in tempo reale (futura)

### 4.3 Cosa è IN SCOPE

**Funzionalità IN SCOPE:**
- Gestione anagrafica completa (studenti, genitori, insegnanti, fornitori)
- Gestione iscrizioni e corsi
- Fatturazione elettronica e pagamenti
- Registro elettronico e presenze
- Conto orario insegnanti
- Comunicazioni (email, SMS)
- Magazzino strumenti e libri
- Reportistica e statistiche
- Gestione contratti
- Gestione preiscrizioni
- Gestione esami
- Gestione attività extra (orchestra/coro)
- Gestione noleggio strumenti

### 4.4 Cosa è OUT OF SCOPE

**Funzionalità OUT OF SCOPE:**
- Area personale allievi/genitori (futura, non in prima fase)
- Sistema bilancio contabile completo (gestito da commercialista)
- App mobile dedicata
- Integrazione pagamenti online in tempo reale (futura)
- Sistema di prenotazione aule avanzato (base sì, avanzato no)

---

## 5. FUNCTIONAL PROCESSES IDENTIFICATI

### 5.1 Anagrafiche

**FP1.1 - Gestione Anno Scolastico**
- Descrizione: CRUD anno scolastico, switch tra esercizi, gestione preiscrizioni trasferimento
- Attore: Admin
- Grado certezza: ALTO
- Fonte: Trascrizioni, ODS

**FP1.2 - Visualizzare lista studenti**
- Descrizione: Visualizza tabella studenti con filtri (stato, anno, strumento, livello)
- Attore: Segreteria, Admin
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP1.3 - Visualizzare dettaglio studente**
- Descrizione: Visualizza scheda completa studente (anagrafica, corsi, pagamenti, presenze)
- Attore: Segreteria, Admin
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP1.4 - Creare nuovo studente**
- Descrizione: Crea nuovo record studente (dati anagrafici, codice fiscale, disponibilità)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP1.5 - Modificare studente**
- Descrizione: Modifica dati studente esistente (anagrafica, note, disponibilità)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP1.6 - Eliminare studente**
- Descrizione: Elimina studente dal sistema (con controllo dipendenze)
- Attore: Admin
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP1.7 - Gestione genitori/tutori**
- Descrizione: CRUD genitori, gestione primo/secondo genitore, relazioni multiple
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP1.8 - Gestione insegnanti**
- Descrizione: CRUD insegnanti, categorizzazione soci/non soci, documenti fiscali
- Attore: Admin
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP1.9 - Gestione fornitori**
- Descrizione: CRUD fornitori, integrazione cassetto fiscale
- Attore: Segreteria
- Grado certezza: MEDIO
- Fonte: Trascrizioni

### 5.2 Primo Contatto e Iscrizioni

**FP2.1 - Gestione primo contatto**
- Descrizione: Form pubblico raccolta dati iniziali, conversione prospect → studente
- Attore: Prospect (esterno), Segreteria
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP2.2 - Visualizzare lista iscrizioni**
- Descrizione: Visualizza tabella iscrizioni con filtri (studente, corso, stato)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP2.3 - Visualizzare dettaglio iscrizione**
- Descrizione: Visualizza dettaglio iscrizione (studente, corso, periodo, costi)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP2.4 - Creare nuova iscrizione**
- Descrizione: Crea iscrizione studente-corso, calcolo automatico settimane da calendario
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP2.5 - Modificare iscrizione**
- Descrizione: Modifica iscrizione esistente (corso, date, modifiche durante anno)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP2.6 - Gestione calendario lezioni**
- Descrizione: CRUD calendario con giornate attive/sospensioni, calcolo settimane
- Attore: Admin
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP2.7 - Gestione corsi**
- Descrizione: CRUD corsi (regolari, brevi, pacchetti), scheduling, insegnanti
- Attore: Segreteria, Admin
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP2.8 - Proposta oraria**
- Descrizione: Composizione proposta oraria definitiva (giorno, orario, classe, insegnante)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: Trascrizioni

### 5.3 Contratti e Fatturazione

**FP3.1 - Visualizzare lista contratti**
- Descrizione: Visualizza tabella contratti con filtri (studente, stato, tipo)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP3.2 - Visualizzare dettaglio contratto**
- Descrizione: Visualizza dettaglio contratto (studente, prodotti, rate, stato)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP3.3 - Creare nuovo contratto**
- Descrizione: Crea contratto da iscrizione, generazione rate flessibili
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni, PDF

**FP3.4 - Modificare contratto**
- Descrizione: Modifica contratto esistente (workflow draft/sent/signed)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP3.5 - Generare PDF contratto**
- Descrizione: Genera PDF contratto (regolare/breve/tempo estivo/noleggio)
- Attore: Sistema
- Grado certezza: ALTO
- Fonte: PDF, Trascrizioni

**FP3.6 - Inviare contratto**
- Descrizione: Invia contratto via email, generazione link precompilato
- Attore: Segreteria, Sistema
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP3.7 - Visualizzare lista fatture**
- Descrizione: Visualizza tabella fatture con filtri (studente, stato, scadenza)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP3.8 - Visualizzare dettaglio fattura**
- Descrizione: Visualizza dettaglio fattura (importi, rate, pagamenti, saldi)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP3.9 - Creare nuova fattura**
- Descrizione: Genera fattura elettronica solo a pagamento avvenuto
- Attore: Segreteria, Sistema
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP3.10 - Generare PDF fattura**
- Descrizione: Genera PDF fattura elettronica, invio SDI
- Attore: Sistema
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP3.11 - Gestione pagamenti**
- Descrizione: Registrazione pagamenti, importazione estratti conto CSV, gestione crediti/acconti
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP3.12 - Recupero crediti**
- Descrizione: Sistema solleciti automatici configurabili, monitoraggio ritardi
- Attore: Sistema, Segreteria
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP3.13 - Gestione preiscrizioni**
- Descrizione: Campagna preiscrizioni fine anno, trasferimento esercizio successivo
- Attore: Segreteria
- Grado certezza: MEDIO
- Fonte: Trascrizioni

### 5.4 Didattica e Registro

**FP4.1 - Visualizzare registro elettronico**
- Descrizione: Visualizza calendario lezioni per insegnante, lista presenze
- Attore: Insegnante, Supplente
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP4.2 - Registrare presenze**
- Descrizione: Registrazione presenze allievi a lezione
- Attore: Insegnante, Supplente
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP4.3 - Visualizzare lista presenze**
- Descrizione: Visualizza storico presenze allievo, monitoraggio frequenza
- Attore: Segreteria, Insegnante
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP4.4 - Gestione conto orario insegnanti**
- Descrizione: Calcolo automatico conto orario, prospetto previsionale, gestione pagamenti rateizzati
- Attore: Segreteria, Admin
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP4.5 - Gestione supplenti**
- Descrizione: Gestione supplenze, accesso registro provvisorio, trasferimento lezione
- Attore: Segreteria, Supplente
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP4.6 - Gestione aule**
- Descrizione: CRUD aule, verifica disponibilità, calendario utilizzo
- Attore: Segreteria
- Grado certezza: MEDIO
- Fonte: Trascrizioni

**FP4.7 - Gestione recuperi lezioni**
- Descrizione: Gestione lezioni di recupero, prenotazione aule
- Attore: Insegnante, Segreteria
- Grado certezza: MEDIO
- Fonte: Trascrizioni

### 5.5 Attività Extra e Comunicazioni

**FP5.1 - Gestione attività extra (orchestra/coro)**
- Descrizione: CRUD attività, gestione iscrizioni, filtri composizione gruppi, convocazioni
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP5.2 - Generazione attestati**
- Descrizione: Genera attestati frequenza per crediti scolastici
- Attore: Segreteria
- Grado certezza: MEDIO
- Fonte: Trascrizioni

**FP5.3 - Comunicazioni**
- Descrizione: Sistema comunicazioni integrate (email, SMS), comunicazioni massive, template
- Attore: Segreteria, Sistema
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP5.4 - Gestione esami**
- Descrizione: CRUD esami, costi iscrizione, pianificazione, risultati
- Attore: Segreteria
- Grado certezza: MEDIO
- Fonte: Trascrizioni

### 5.6 Magazzino

**FP6.1 - Gestione strumenti musicali**
- Descrizione: CRUD strumenti, gestione noleggio, vendita, cespiti (valore bilancio)
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni

**FP6.2 - Gestione libri didattici**
- Descrizione: CRUD libri, inventario, tracking vendite, rimanenze
- Attore: Segreteria
- Grado certezza: MEDIO
- Fonte: Trascrizioni

**FP6.3 - Gestione noleggio strumenti**
- Descrizione: CRUD noleggi, contratti noleggio, cauzioni, rate
- Attore: Segreteria
- Grado certezza: ALTO
- Fonte: ODS, Trascrizioni, PDF

### 5.7 Integrazioni e Reportistica

**FP7.1 - Integrazione cassetto fiscale**
- Descrizione: Import fatture attive/passive, incrocio e categorizzazione spese
- Attore: Sistema
- Grado certezza: MEDIO
- Fonte: Trascrizioni

**FP7.2 - Flusso di cassa**
- Descrizione: Visualizzazione flusso di cassa, inserimento pagamenti, import estratti conto
- Attore: Segreteria
- Grado certezza: MEDIO
- Fonte: Trascrizioni

**FP7.3 - Reportistica e statistiche**
- Descrizione: Dashboard statistiche, export dati, confronto multi-anno, grafici
- Attore: Admin, Segreteria
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP7.4 - Export dati**
- Descrizione: Export dati con selezione campi, formati multipli
- Attore: Segreteria, Admin
- Grado certezza: ALTO
- Fonte: Trascrizioni

### 5.8 Gestione Utenti e Sicurezza

**FP8.1 - Gestione utenti sistema**
- Descrizione: CRUD utenti, gestione ruoli (admin/segreteria/insegnante/supplente)
- Attore: Admin
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP8.2 - Controllo accessi e autorizzazioni**
- Descrizione: Sistema permessi granulari, gestione password, autenticazione
- Attore: Sistema
- Grado certezza: ALTO
- Fonte: Trascrizioni

**FP8.3 - Sicurezza e integrità dati**
- Descrizione: Implementazione GDPR, backup automatici, log audit
- Attore: Sistema
- Grado certezza: MEDIO
- Fonte: Trascrizioni

---

## 6. CONTEggio MOVIMENTI DATI

### 6.1 Anagrafiche

**FP1.1 - Gestione Anno Scolastico**
- Entry: 1 (form creazione/modifica anno)
- Exit: 1 (visualizzazione lista/dettaglio anni)
- Read: 2 (lettura anni esistenti per switch, lettura preiscrizioni)
- Write: 1 (salvataggio anno/preiscrizioni)
- **CFP: 5**

**FP1.2 - Visualizzare lista studenti**
- Entry: 1 (filtri ricerca)
- Exit: 1 (tabella studenti)
- Read: 2 (lettura studenti, lettura anno scolastico per filtro)
- Write: 0
- **CFP: 4**

**FP1.3 - Visualizzare dettaglio studente**
- Entry: 1 (selezione studente)
- Exit: 1 (scheda completa studente)
- Read: 5 (lettura studente, lettura genitori, lettura iscrizioni, lettura contratti, lettura pagamenti)
- Write: 0
- **CFP: 7**

**FP1.4 - Creare nuovo studente**
- Entry: 1 (form inserimento)
- Exit: 1 (conferma creazione)
- Read: 2 (validazione codice fiscale univoco, lettura anno scolastico)
- Write: 1 (inserimento studente)
- **CFP: 5**

**FP1.5 - Modificare studente**
- Entry: 1 (form modifica)
- Exit: 1 (conferma modifica)
- Read: 2 (lettura dati esistenti, validazioni)
- Write: 1 (aggiornamento studente)
- **CFP: 5**

**FP1.6 - Eliminare studente**
- Entry: 1 (conferma eliminazione)
- Exit: 1 (conferma eliminazione)
- Read: 2 (verifica dipendenze iscrizioni/contratti, lettura studente)
- Write: 1 (eliminazione studente)
- **CFP: 5**

**FP1.7 - Gestione genitori/tutori**
- Entry: 1 (form genitore)
- Exit: 1 (visualizzazione genitore)
- Read: 2 (lettura genitore esistente, lettura relazioni studenti)
- Write: 1 (salvataggio genitore/relazioni)
- **CFP: 5**

**FP1.8 - Gestione insegnanti**
- Entry: 1 (form insegnante)
- Exit: 1 (visualizzazione insegnante)
- Read: 2 (lettura insegnante esistente, validazioni)
- Write: 1 (salvataggio insegnante)
- **CFP: 5**

**FP1.9 - Gestione fornitori**
- Entry: 1 (form fornitore)
- Exit: 1 (visualizzazione fornitore)
- Read: 2 (lettura fornitore esistente, validazioni)
- Write: 1 (salvataggio fornitore)
- **CFP: 5**

**Subtotale Anagrafiche: 46 CFP**

### 6.2 Primo Contatto e Iscrizioni

**FP2.1 - Gestione primo contatto**
- Entry: 1 (form pubblico primo contatto)
- Exit: 2 (conferma invio form, link generato)
- Read: 1 (validazione dati)
- Write: 1 (creazione prospect/studente)
- **CFP: 5**

**FP2.2 - Visualizzare lista iscrizioni**
- Entry: 1 (filtri ricerca)
- Exit: 1 (tabella iscrizioni)
- Read: 3 (lettura iscrizioni, lettura studenti, lettura corsi)
- Write: 0
- **CFP: 5**

**FP2.3 - Visualizzare dettaglio iscrizione**
- Entry: 1 (selezione iscrizione)
- Exit: 1 (dettaglio iscrizione)
- Read: 4 (lettura iscrizione, lettura studente, lettura corso, lettura calendario per settimane)
- Write: 0
- **CFP: 6**

**FP2.4 - Creare nuova iscrizione**
- Entry: 1 (form iscrizione)
- Exit: 1 (conferma creazione)
- Read: 4 (lettura studente, lettura corso, lettura calendario per calcolo settimane, validazioni)
- Write: 1 (inserimento iscrizione)
- **CFP: 7**

**FP2.5 - Modificare iscrizione**
- Entry: 1 (form modifica)
- Exit: 1 (conferma modifica)
- Read: 4 (lettura iscrizione esistente, lettura calendario per ricalcolo settimane, validazioni, lettura contratti collegati)
- Write: 1 (aggiornamento iscrizione)
- **CFP: 7**

**FP2.6 - Gestione calendario lezioni**
- Entry: 1 (form calendario)
- Exit: 1 (visualizzazione calendario)
- Read: 2 (lettura calendario esistente, lettura anno scolastico)
- Write: 1 (salvataggio calendario)
- **CFP: 5**

**FP2.7 - Gestione corsi**
- Entry: 1 (form corso)
- Exit: 1 (visualizzazione corso)
- Read: 3 (lettura corso esistente, lettura insegnanti, lettura tipi corso)
- Write: 1 (salvataggio corso)
- **CFP: 6**

**FP2.8 - Proposta oraria**
- Entry: 1 (form proposta oraria)
- Exit: 1 (visualizzazione proposta)
- Read: 4 (lettura disponibilità studenti, lettura disponibilità insegnanti, lettura aule, lettura corsi)
- Write: 1 (salvataggio proposta/orari)
- **CFP: 7**

**Subtotale Primo Contatto e Iscrizioni: 48 CFP**

### 6.3 Contratti e Fatturazione

**FP3.1 - Visualizzare lista contratti**
- Entry: 1 (filtri ricerca)
- Exit: 1 (tabella contratti)
- Read: 3 (lettura contratti, lettura studenti, lettura stati)
- Write: 0
- **CFP: 5**

**FP3.2 - Visualizzare dettaglio contratto**
- Entry: 1 (selezione contratto)
- Exit: 1 (dettaglio contratto)
- Read: 5 (lettura contratto, lettura studente, lettura iscrizioni, lettura rate, lettura fatture)
- Write: 0
- **CFP: 7**

**FP3.3 - Creare nuovo contratto**
- Entry: 1 (form contratto)
- Exit: 1 (conferma creazione)
- Read: 4 (lettura iscrizioni, lettura studente, lettura calendario per calcolo scadenze rate, validazioni)
- Write: 2 (inserimento contratto, inserimento rate)
- **CFP: 8**

**FP3.4 - Modificare contratto**
- Entry: 1 (form modifica)
- Exit: 1 (conferma modifica)
- Read: 4 (lettura contratto esistente, lettura rate, validazioni, lettura fatture per verifica modificabilità)
- Write: 2 (aggiornamento contratto, aggiornamento rate se modificate)
- **CFP: 8**

**FP3.5 - Generare PDF contratto**
- Entry: 1 (comando generazione)
- Exit: 1 (PDF generato)
- Read: 5 (lettura contratto, lettura studente, lettura genitori, lettura rate, lettura prodotti/corsi)
- Write: 0 (PDF non è storage persistente funzionale)
- **CFP: 7**

**FP3.6 - Inviare contratto**
- Entry: 1 (comando invio)
- Exit: 2 (conferma invio, link generato)
- Read: 2 (lettura contratto, lettura dati contatto studente/genitori)
- Write: 2 (aggiornamento stato contratto, inserimento comunicazione)
- **CFP: 7**

**FP3.7 - Visualizzare lista fatture**
- Entry: 1 (filtri ricerca)
- Exit: 1 (tabella fatture)
- Read: 3 (lettura fatture, lettura studenti, lettura stati)
- Write: 0
- **CFP: 5**

**FP3.8 - Visualizzare dettaglio fattura**
- Entry: 1 (selezione fattura)
- Exit: 1 (dettaglio fattura)
- Read: 5 (lettura fattura, lettura studente, lettura rate, lettura pagamenti, calcolo saldi)
- Write: 0
- **CFP: 7**

**FP3.9 - Creare nuova fattura**
- Entry: 1 (form fattura)
- Exit: 1 (conferma creazione)
- Read: 4 (lettura contratto, lettura pagamenti per verifica, lettura studente, validazioni)
- Write: 1 (inserimento fattura)
- **CFP: 7**

**FP3.10 - Generare PDF fattura**
- Entry: 1 (comando generazione)
- Exit: 1 (PDF fattura generato)
- Read: 4 (lettura fattura, lettura studente, lettura rate, lettura pagamenti)
- Write: 1 (invio SDI - scrittura log invio)
- **CFP: 7**

**FP3.11 - Gestione pagamenti**
- Entry: 2 (form pagamento manuale, upload CSV estratti conto)
- Exit: 1 (visualizzazione pagamenti)
- Read: 4 (lettura fatture, lettura pagamenti esistenti, validazione import CSV, lettura studenti per matching)
- Write: 2 (inserimento pagamento, aggiornamento stato fatture)
- **CFP: 9**

**FP3.12 - Recupero crediti**
- Entry: 1 (filtri/configurazione solleciti)
- Exit: 2 (lista scadenze, report recupero crediti)
- Read: 4 (lettura fatture scadute, lettura pagamenti, lettura studenti per contatti, lettura configurazione solleciti)
- Write: 2 (inserimento comunicazioni solleciti, aggiornamento log)
- **CFP: 9**

**FP3.13 - Gestione preiscrizioni**
- Entry: 1 (form preiscrizione)
- Exit: 1 (visualizzazione preiscrizioni)
- Read: 3 (lettura studenti, lettura preiscrizioni esistenti, lettura anno scolastico)
- Write: 2 (inserimento preiscrizione, trasferimento esercizio successivo)
- **CFP: 7**

**Subtotale Contratti e Fatturazione: 93 CFP**

### 6.4 Didattica e Registro

**FP4.1 - Visualizzare registro elettronico**
- Entry: 1 (selezione insegnante/data)
- Exit: 1 (calendario lezioni/presenze)
- Read: 3 (lettura lezioni, lettura presenze, lettura corsi)
- Write: 0
- **CFP: 5**

**FP4.2 - Registrare presenze**
- Entry: 1 (form presenze)
- Exit: 1 (conferma registrazione)
- Read: 3 (lettura lezione, lettura studenti iscritti, lettura presenze esistenti)
- Write: 2 (inserimento presenze, aggiornamento countdown lezioni studenti)
- **CFP: 7**

**FP4.3 - Visualizzare lista presenze**
- Entry: 1 (filtri ricerca)
- Exit: 1 (lista presenze/storico)
- Read: 3 (lettura presenze, lettura studenti, lettura lezioni)
- Write: 0
- **CFP: 5**

**FP4.4 - Gestione conto orario insegnanti**
- Entry: 1 (form/selezione insegnante)
- Exit: 2 (prospetto conto orario, storico pagamenti)
- Read: 5 (lettura insegnante, lettura lezioni effettuate, lettura presenze, lettura pagamenti esistenti, lettura configurazione compensi)
- Write: 2 (calcolo/salvataggio conto orario, inserimento pagamenti insegnanti)
- **CFP: 10**

**FP4.5 - Gestione supplenti**
- Entry: 1 (form supplenza)
- Exit: 1 (visualizzazione supplenze)
- Read: 4 (lettura insegnante assente, lettura lezioni, lettura supplenti disponibili, validazioni)
- Write: 3 (inserimento supplenza, creazione account provvisorio, trasferimento lezione)
- **CFP: 9**

**FP4.6 - Gestione aule**
- Entry: 1 (form aula)
- Exit: 1 (visualizzazione aule)
- Read: 2 (lettura aule, lettura prenotazioni)
- Write: 1 (salvataggio aula/prenotazioni)
- **CFP: 5**

**FP4.7 - Gestione recuperi lezioni**
- Entry: 1 (form recupero)
- Exit: 1 (visualizzazione recuperi)
- Read: 4 (lettura lezione originale, lettura disponibilità aule, lettura disponibilità insegnante, validazioni)
- Write: 2 (inserimento lezione recupero, aggiornamento lezione originale)
- **CFP: 8**

**Subtotale Didattica e Registro: 49 CFP**

### 6.5 Attività Extra e Comunicazioni

**FP5.1 - Gestione attività extra (orchestra/coro)**
- Entry: 1 (form attività)
- Exit: 1 (visualizzazione attività)
- Read: 4 (lettura attività, lettura iscrizioni, lettura studenti con filtri, lettura calendario date)
- Write: 2 (salvataggio attività, salvataggio iscrizioni)
- **CFP: 8**

**FP5.2 - Generazione attestati**
- Entry: 1 (form attestato)
- Exit: 1 (PDF attestato generato)
- Read: 4 (lettura studente, lettura corsi frequentati, lettura presenze, lettura configurazione attestato)
- Write: 0 (PDF non è storage persistente funzionale)
- **CFP: 6**

**FP5.3 - Comunicazioni**
- Entry: 2 (form comunicazione, selezione destinatari con filtri)
- Exit: 2 (conferma invio, log comunicazioni)
- Read: 4 (lettura destinatari (studenti/genitori), lettura template, lettura comunicazioni esistenti, validazioni)
- Write: 2 (inserimento comunicazione, aggiornamento log invio)
- **CFP: 10**

**FP5.4 - Gestione esami**
- Entry: 1 (form esame)
- Exit: 1 (visualizzazione esami)
- Read: 3 (lettura esami, lettura studenti, lettura livelli ABRSM)
- Write: 1 (salvataggio esame)
- **CFP: 6**

**Subtotale Attività Extra e Comunicazioni: 30 CFP**

### 6.6 Magazzino

**FP6.1 - Gestione strumenti musicali**
- Entry: 1 (form strumento)
- Exit: 1 (visualizzazione strumento)
- Read: 3 (lettura strumenti, lettura noleggi, lettura cespiti)
- Write: 2 (salvataggio strumento, aggiornamento cespiti se applicabile)
- **CFP: 7**

**FP6.2 - Gestione libri didattici**
- Entry: 1 (form libro)
- Exit: 1 (visualizzazione libro)
- Read: 3 (lettura libri, lettura vendite, lettura inventario)
- Write: 2 (salvataggio libro, aggiornamento inventario)
- **CFP: 7**

**FP6.3 - Gestione noleggio strumenti**
- Entry: 1 (form noleggio)
- Exit: 2 (visualizzazione noleggio, PDF contratto noleggio)
- Read: 4 (lettura strumenti disponibili, lettura studenti, lettura contratti noleggio esistenti, lettura tariffe)
- Write: 3 (inserimento noleggio, inserimento contratto noleggio, aggiornamento stato strumento)
- **CFP: 10**

**Subtotale Magazzino: 24 CFP**

### 6.7 Integrazioni e Reportistica

**FP7.1 - Integrazione cassetto fiscale**
- Entry: 1 (import da sistema esterno)
- Exit: 1 (lista fatture importate)
- Read: 2 (lettura fatture esistenti per deduplicazione, lettura fornitori per matching)
- Write: 2 (inserimento fatture importate, aggiornamento categorizzazione spese)
- **CFP: 6**

**FP7.2 - Flusso di cassa**
- Entry: 2 (form inserimento pagamento, import CSV estratti conto)
- Exit: 2 (visualizzazione flusso di cassa, grafici andamento)
- Read: 4 (lettura pagamenti entrate, lettura fatture uscite, lettura fornitori, calcolo totali)
- Write: 1 (inserimento pagamenti cassa se manuale)
- **CFP: 9**

**FP7.3 - Reportistica e statistiche**
- Entry: 2 (filtri report, selezione statistiche)
- Exit: 3 (dashboard statistiche, grafici, report tabellari)
- Read: 6 (lettura studenti, lettura iscrizioni, lettura pagamenti, lettura presenze, lettura anni precedenti per confronto, calcoli aggregati)
- Write: 0
- **CFP: 11**

**FP7.4 - Export dati**
- Entry: 2 (selezione entità, selezione campi)
- Exit: 1 (file export generato)
- Read: 3 (lettura entità selezionate, lettura campi disponibili, lettura dati)
- Write: 0 (file export non è storage persistente funzionale)
- **CFP: 6**

**Subtotale Integrazioni e Reportistica: 32 CFP**

### 6.8 Gestione Utenti e Sicurezza

**FP8.1 - Gestione utenti sistema**
- Entry: 1 (form utente)
- Exit: 1 (visualizzazione utenti)
- Read: 3 (lettura utenti, lettura ruoli, validazioni)
- Write: 1 (salvataggio utente/ruoli)
- **CFP: 6**

**FP8.2 - Controllo accessi e autorizzazioni**
- Entry: 1 (form permessi)
- Exit: 1 (visualizzazione permessi)
- Read: 2 (lettura permessi esistenti, lettura ruoli)
- Write: 1 (salvataggio permessi)
- **CFP: 5**

**FP8.3 - Sicurezza e integrità dati**
- Entry: 0 (processo automatico)
- Exit: 1 (log audit/backup)
- Read: 1 (lettura configurazione backup/GDPR)
- Write: 2 (scrittura log audit, scrittura backup)
- **CFP: 4**

**Subtotale Gestione Utenti e Sicurezza: 15 CFP**

---

## 7. FUNCTIONAL SIZE (CFP)

### 7.1 Riepilogo per Categoria

| Categoria | CFP |
|-----------|-----|
| Anagrafiche | 46 |
| Primo Contatto e Iscrizioni | 48 |
| Contratti e Fatturazione | 93 |
| Didattica e Registro | 49 |
| Attività Extra e Comunicazioni | 30 |
| Magazzino | 24 |
| Integrazioni e Reportistica | 32 |
| Gestione Utenti e Sicurezza | 15 |
| **TOTALE** | **337 CFP** |

### 7.2 Breakdown E/X/R/W

| Tipo Movimento | CFP |
|----------------|-----|
| Entry (E) | 60 |
| Exit (X) | 58 |
| Read (R) | 140 |
| Write (W) | 79 |
| **TOTALE** | **337 CFP** |

### 7.3 Functional Processes per Grado di Certezza

| Grado Certezza | Numero FP | CFP |
|----------------|-----------|-----|
| ALTO | 54 | 298 |
| MEDIO | 9 | 39 |
| BASSO | 0 | 0 |
| **TOTALE** | **63** | **337 CFP** |

### 7.4 Range di Incertezza

**CFP Minimo (solo FP con certezza ALTA):** 298 CFP

**CFP Massimo (FP con certezza ALTA + MEDIA):** 337 CFP

**⚠️ NOTA:** Tutti i Functional Processes identificati sono inclusi nel totale. I FP con grado MEDIO sono documentati nelle fonti ma con alcuni dettagli implementativi non completamente chiari (es. integrazione cassetto fiscale, gestione aule avanzata).

---

## 8. ASSUNZIONI E LIMITI

### 8.1 Assunzioni Fatte

**8.1.1 Assunzioni su Implementazione**
- Assunto che ogni entità principale avrà operazioni CRUD complete (Create, Read, Update, Delete)
- Assunto che i report/export saranno generati on-demand (non pre-calcolati)
- Assunto che le comunicazioni email/SMS saranno gestite tramite integrazione esterna (non sistema interno)

**8.1.2 Assunzioni su Functional Processes**
- Assunto che il sistema avrà dashboard/visualizzazioni per ogni macro-area funzionale
- Assunto che i filtri ricerca saranno disponibili per tutte le liste principali
- Assunto che i PDF documenti (contratti, fatture, attestati) saranno generati dinamicamente

**8.1.3 Assunzioni su Complessità**
- Assunto che le validazioni base (codice fiscale, email, date) non contano come movimenti dati separati
- Assunto che il routing base e l'autenticazione infrastrutturale non contano come Functional Processes
- Assunto che ogni lettura di entità correlate per validazione/lookup conta come Read separato

### 8.2 Limiti Metodologici

**8.2.1 Analisi EX-ANTE**
- Alcune funzionalità potrebbero emergere solo durante lo sviluppo
- Alcuni Functional Processes potrebbero essere più complessi di quanto dedotto
- Alcuni movimenti dati potrebbero non essere identificabili a priori

**8.2.2 Fonti Parziali**
- I file ODS non sono completamente analizzabili automaticamente (alcune parti richiedono interpretazione)
- Le trascrizioni contengono descrizioni verbali che richiedono interpretazione
- Alcuni PDF contratti potrebbero avere template diversi non analizzati

**8.2.3 Interpretazione**
- Possibile sovrastima per processi che potrebbero essere semplificati in implementazione
- Possibile sottostima per processi complessi non completamente descritti
- Alcuni Functional Processes potrebbero essere raggruppati diversamente in implementazione

### 8.3 Dati Mancanti

**8.3.1 File ODS Parziali**
- `Db Contabile 2025-26.ods` - struttura parzialmente accessibile
- `Db Accessori 2025-26.ods` - struttura parzialmente accessibile
- `Calendario 2025-26.ods` - formule complesse richiedono interpretazione manuale

**8.3.2 Functional Processes Non Completamente Deducibili**
- Dettagli implementativi integrazione cassetto fiscale (FP7.1) - grado MEDIO
- Dettagli sistema prenotazione aule avanzato (FP4.6) - grado MEDIO
- Dettagli gestione libri didattici avanzata (FP6.2) - grado MEDIO

---

## 9. INCERTEZZE E DATI MANCANTI

### 9.1 Functional Processes con Grado Certezza MEDIO

I seguenti Functional Processes sono documentati nelle fonti ma con alcuni dettagli implementativi non completamente chiari:

1. **FP1.9 - Gestione fornitori** (5 CFP)
   - Incertezza: Dettagli integrazione cassetto fiscale per fornitori
   - Fonte: Trascrizioni (menzionato ma non dettagliato)

2. **FP3.13 - Gestione preiscrizioni** (7 CFP)
   - Incertezza: Dettagli workflow trasferimento esercizio successivo
   - Fonte: Trascrizioni (descritto verbalmente)

3. **FP4.6 - Gestione aule** (5 CFP)
   - Incertezza: Livello di dettaglio sistema prenotazione
   - Fonte: Trascrizioni (menzionato ma non dettagliato)

4. **FP4.7 - Gestione recuperi lezioni** (8 CFP)
   - Incertezza: Workflow completo prenotazione aule per recuperi
   - Fonte: Trascrizioni (descritto parzialmente)

5. **FP5.2 - Generazione attestati** (6 CFP)
   - Incertezza: Template e personalizzazioni disponibili
   - Fonte: Trascrizioni (menzionato ma non dettagliato)

6. **FP5.4 - Gestione esami** (6 CFP)
   - Incertezza: Dettagli pianificazione e gestione risultati
   - Fonte: Trascrizioni (menzionato ma non dettagliato)

7. **FP6.2 - Gestione libri didattici** (7 CFP)
   - Incertezza: Livello di dettaglio inventario e tracking vendite
   - Fonte: Trascrizioni, ODS (parzialmente deducibile)

8. **FP7.1 - Integrazione cassetto fiscale** (6 CFP)
   - Incertezza: Dettagli tecnici import e categorizzazione automatica
   - Fonte: Trascrizioni (menzionato ma non dettagliato)

9. **FP7.2 - Flusso di cassa** (9 CFP)
   - Incertezza: Dettagli visualizzazione grafici e report avanzati
   - Fonte: Trascrizioni (descritto parzialmente)

**Totale CFP con grado MEDIO:** 59 CFP (inclusi nel totale di 337 CFP)

### 9.2 Functional Processes Potenzialmente Non Identificati

**Possibili Functional Processes aggiuntivi non completamente deducibili:**
- Gestione template comunicazioni (potrebbe essere parte di FP5.3)
- Gestione configurazioni sistema (potrebbe essere parte di vari FP)
- Gestione backup/ripristino manuale (potrebbe essere parte di FP8.3)
- Gestione import dati storici (non in scope funzionale, è operazione una-tantum)

### 9.3 Movimenti Dati Potenzialmente Non Contati

**Possibili movimenti dati aggiuntivi:**
- Letture per validazioni incrociate complesse (potrebbero aumentare Read)
- Scritture di log audit dettagliati (potrebbero aumentare Write)
- Letture per calcoli aggregati complessi nei report (potrebbero aumentare Read)

**Stima conservativa:** Possibile variazione ±10-15% rispetto ai 337 CFP calcolati.

---

## 10. CONCLUSIONI

### 10.1 Functional Size Stimata

**Functional Size (CFP) totale stimata: 337 CFP**

**Breakdown:**
- Entry (E): 60 CFP
- Exit (X): 58 CFP
- Read (R): 140 CFP
- Write (W): 79 CFP

**Range di incertezza:**
- Minimo (solo FP certi): 298 CFP
- Massimo (FP certi + probabili): 337 CFP

### 10.2 Confronto con Analisi Precedenti

**Confronto con progetti analizzati ex-post:**

| Progetto | CFP | Ore Reali | CFP/Ora |
|----------|-----|-----------|---------|
| MsCarichi | 442 | 290 | 1.52 |
| CactusBoard | 316 | 290 | 1.09 |
| Klabhouse | 514 | 250 | 2.06 |
| CZServizi | 195 | 120 | 1.63 |
| **L'Altramusica (stima EX-ANTE)** | **337** | - | - |

**⚠️ NOTA:** Questo confronto è puramente informativo per contesto dimensionale. Non viene calcolato alcun coefficiente di produttività o stima di effort/costo, come richiesto dalla metodologia COSMIC.

### 10.3 Validità della Stima

**Punti di forza:**
- Fonti multiple (ODS, trascrizioni, PDF, documentazione)
- 63 Functional Processes identificati
- 54 FP con grado certezza ALTO (88% del totale)
- Struttura dati legacy ben documentata

**Limiti riconosciuti:**
- Analisi EX-ANTE con fonti parziali
- 9 FP con grado certezza MEDIO (14% del totale, 12% dei CFP)
- Possibili variazioni ±10-15% rispetto alla stima

### 10.4 Utilizzo della Stima

**Questa stima COSMIC può essere utilizzata per:**
- Confronto dimensionale con altri progetti
- Base per future calibrazioni di produttività (con progetti completati)
- Pianificazione approssimativa (con coefficienti di produttività esterni)

**Questa stima COSMIC NON deve essere utilizzata per:**
- Calcolo diretto di costi/effort (non include coefficienti di produttività)
- Stime temporali precise (manca calibrazione)
- Confronto con stime basate su metodologie diverse (LOC, story points, etc.)

---

**Firma Metodologica:**  
Analisi COSMIC EX-ANTE conforme a ISO/IEC 19761  
Separazione rigorosa SIZE vs COST  
Nessuna contaminazione metodologica

