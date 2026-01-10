# Analisi COSMIC - CactusBoard

**Data Analisi:** Dicembre 2024  
**Standard:** ISO/IEC 19761 - COSMIC Function Points  
**Analista:** Software Measurement Expert  
**Scopo:** Misurazione Functional Size (CFP) del sistema CactusBoard

---

## 1. SCOPO

**Obiettivo:** Identificare e misurare tutti i Functional Processes del sistema CactusBoard secondo standard COSMIC ISO/IEC 19761.

**Metodologia:** Analisi codice sorgente (Controllers, Routes, Models) per identificare operazioni utente identificabili.

**Standard Applicato:** ISO/IEC 19761 - COSMIC Function Points v4.0.1

---

## 2. METODO COSMIC

**Criteri Identificazione Functional Process:**
- Operazione identificabile dall'utente (non tecnica)
- Trasformazione dati completa e coerente
- Rilevante per il dominio applicativo

**Criteri Conteggio Movimenti Dati:**
- **Entry (E):** Input utente (form, comando, file upload) → 1 CFP
- **Exit (X):** Output utente (schermata, report, export, messaggio) → 1 CFP
- **Read (R):** Lettura storage persistente (database, file) → 1 CFP
- **Write (W):** Scrittura storage persistente (database, file) → 1 CFP

**Regole Applicate:**
- Ogni movimento dati conta esattamente 1 CFP
- Nessuna ponderazione per complessità
- Nessuna normalizzazione
- Movimenti dati multipli per stessa entità contano separatamente

---

## 3. CONTESTO E CONFINE SISTEMA

### 3.1 Descrizione Sistema

**CactusBoard** è un sistema gestionale per la gestione di progetti, spese, contabilità, estratti conto bancari. Gestisce l'intero ciclo: progetti, spese, documenti, contabilità, abbinamento estratti conto.

**Dominio:** Gestione progetti e contabilità

### 3.2 Utenti Identificati

- **Amministratori:** Gestione completa sistema
- **Operatori:** Gestione progetti, spese, documenti
- **Dipendenti:** Accesso limitato (solo progetti propri)

### 3.3 Confine Sistema

**Incluso:**
- Gestione progetti e categorie progetti
- Gestione spese
- Gestione clienti, fornitori, dipendenti
- Gestione documenti (fatture, preventivi, spese)
- Gestione contabilità (registrazione fatture/spese)
- Gestione estratti conto bancari
- Abbinamento estratti conto con spese
- Gestione metodi di pagamento
- Gestione anni fiscali
- Dashboard
- Gestione utenti e permessi
- Export progetti

**Escluso:**
- Autenticazione base (infrastrutturale)
- Routing base (infrastrutturale)
- Template grafici generici (non funzionali)
- Test automatici (non Functional Process)

**Interfacce Esterne:**
- File system (upload/download documenti)
- Import estratti conto (CSV)

---

## 4. FUNCTIONAL PROCESSES

### 4.1 Gestione Progetti

**FP1.1 - Visualizzare lista progetti**
- Descrizione: Visualizza progetti per categoria e anno fiscale
- Attore: Operatore
- Scopo: Consultazione progetti

**FP1.2 - Visualizzare dettaglio progetto**
- Descrizione: Visualizza dettaglio completo progetto
- Attore: Operatore
- Scopo: Consultazione dati progetto

**FP1.3 - Creare nuovo progetto**
- Descrizione: Crea nuovo progetto
- Attore: Operatore
- Scopo: Inserimento progetto

**FP1.4 - Modificare progetto**
- Descrizione: Modifica progetto esistente
- Attore: Operatore
- Scopo: Aggiornamento progetto

**FP1.5 - Eliminare progetto**
- Descrizione: Elimina progetto
- Attore: Operatore
- Scopo: Rimozione progetto

**FP1.6 - Scaricare documento progetto**
- Descrizione: Download documento associato a progetto
- Attore: Operatore
- Scopo: Recupero file

**FP1.7 - Eliminare documento progetto**
- Descrizione: Elimina documento da progetto
- Attore: Operatore
- Scopo: Rimozione documento

**FP1.8 - Export progetto**
- Descrizione: Esporta dati progetto
- Attore: Operatore
- Scopo: Export dati

### 4.2 Gestione Categorie Progetti

**FP2.1 - Visualizzare lista categorie progetti**
- Descrizione: Visualizza categorie progetti
- Attore: Operatore
- Scopo: Consultazione categorie

**FP2.2 - Visualizzare dettaglio categoria**
- Descrizione: Visualizza dettaglio categoria
- Attore: Operatore
- Scopo: Consultazione dati categoria

**FP2.3 - Creare nuova categoria progetto**
- Descrizione: Crea nuova categoria
- Attore: Operatore
- Scopo: Configurazione categorie

**FP2.4 - Modificare categoria progetto**
- Descrizione: Modifica categoria
- Attore: Operatore
- Scopo: Aggiornamento categoria

**FP2.5 - Eliminare categoria progetto**
- Descrizione: Elimina categoria
- Attore: Operatore
- Scopo: Rimozione categoria

### 4.3 Gestione Spese

**FP3.1 - Visualizzare lista spese**
- Descrizione: Visualizza spese (ricerca)
- Attore: Operatore
- Scopo: Consultazione spese

**FP3.2 - Visualizzare dettaglio spesa**
- Descrizione: Visualizza dettaglio spesa
- Attore: Operatore
- Scopo: Consultazione dati spesa

**FP3.3 - Creare nuova spesa**
- Descrizione: Crea nuova spesa (opzionalmente associata a progetto)
- Attore: Operatore
- Scopo: Inserimento spesa

**FP3.4 - Modificare spesa**
- Descrizione: Modifica spesa esistente
- Attore: Operatore
- Scopo: Aggiornamento spesa

**FP3.5 - Eliminare spesa**
- Descrizione: Elimina spesa
- Attore: Operatore
- Scopo: Rimozione spesa

**FP3.6 - Duplicare spesa**
- Descrizione: Crea copia di spesa esistente
- Attore: Operatore
- Scopo: Creazione rapida spesa simile

**FP3.7 - Scaricare documento spesa**
- Descrizione: Download documento associato a spesa
- Attore: Operatore
- Scopo: Recupero file

**FP3.8 - Eliminare documento spesa**
- Descrizione: Elimina documento da spesa
- Attore: Operatore
- Scopo: Rimozione documento

### 4.4 Gestione Clienti

**FP4.1 - Visualizzare lista clienti**
- Descrizione: Visualizza tabella clienti
- Attore: Operatore
- Scopo: Consultazione clienti

**FP4.2 - Visualizzare dettaglio cliente**
- Descrizione: Visualizza dettaglio cliente
- Attore: Operatore
- Scopo: Consultazione dati cliente

**FP4.3 - Creare nuovo cliente**
- Descrizione: Crea nuovo cliente
- Attore: Operatore
- Scopo: Inserimento cliente

**FP4.4 - Modificare cliente**
- Descrizione: Modifica cliente
- Attore: Operatore
- Scopo: Aggiornamento cliente

**FP4.5 - Eliminare cliente**
- Descrizione: Elimina cliente
- Attore: Operatore
- Scopo: Rimozione cliente

### 4.5 Gestione Fornitori

**FP5.1 - Visualizzare lista fornitori**
- Descrizione: Visualizza tabella fornitori
- Attore: Operatore
- Scopo: Consultazione fornitori

**FP5.2 - Visualizzare dettaglio fornitore**
- Descrizione: Visualizza dettaglio fornitore
- Attore: Operatore
- Scopo: Consultazione dati fornitore

**FP5.3 - Creare nuovo fornitore**
- Descrizione: Crea nuovo fornitore
- Attore: Operatore
- Scopo: Inserimento fornitore

**FP5.4 - Modificare fornitore**
- Descrizione: Modifica fornitore
- Attore: Operatore
- Scopo: Aggiornamento fornitore

**FP5.5 - Eliminare fornitore**
- Descrizione: Elimina fornitore
- Attore: Operatore
- Scopo: Rimozione fornitore

### 4.6 Gestione Dipendenti

**FP6.1 - Visualizzare lista dipendenti**
- Descrizione: Visualizza tabella dipendenti
- Attore: Operatore
- Scopo: Consultazione dipendenti

**FP6.2 - Visualizzare dettaglio dipendente**
- Descrizione: Visualizza dettaglio dipendente
- Attore: Operatore
- Scopo: Consultazione dati dipendente

**FP6.3 - Creare nuovo dipendente**
- Descrizione: Crea nuovo dipendente
- Attore: Operatore
- Scopo: Inserimento dipendente

**FP6.4 - Modificare dipendente**
- Descrizione: Modifica dipendente
- Attore: Operatore
- Scopo: Aggiornamento dipendente

**FP6.5 - Eliminare dipendente**
- Descrizione: Elimina dipendente
- Attore: Operatore
- Scopo: Rimozione dipendente

### 4.7 Gestione Documenti

**FP7.1 - Visualizzare lista fatture**
- Descrizione: Visualizza fatture progetti
- Attore: Operatore
- Scopo: Consultazione fatture

**FP7.2 - Visualizzare lista documenti spese**
- Descrizione: Visualizza documenti spese
- Attore: Operatore
- Scopo: Consultazione documenti spese

**FP7.3 - Visualizzare documento**
- Descrizione: Visualizza documento (PDF/viewer)
- Attore: Operatore
- Scopo: Consultazione documento

**FP7.4 - Modificare documento**
- Descrizione: Modifica metadati documento
- Attore: Operatore
- Scopo: Aggiornamento documento

### 4.8 Gestione Contabilità

**FP8.1 - Visualizzare lista spese da contabilizzare**
- Descrizione: Visualizza spese non contabilizzate con documenti
- Attore: Operatore
- Scopo: Gestione contabilità spese

**FP8.2 - Visualizzare lista progetti da contabilizzare**
- Descrizione: Visualizza progetti non contabilizzati con fatture
- Attore: Operatore
- Scopo: Gestione contabilità progetti

**FP8.3 - Visualizzare documento contabilità**
- Descrizione: Visualizza documento in modalità contabilità
- Attore: Operatore
- Scopo: Consultazione documento

**FP8.4 - Aggiornare progetto contabilizzato**
- Descrizione: Marca progetto come contabilizzato
- Attore: Operatore
- Scopo: Registrazione contabilità

**FP8.5 - Aggiornare spesa contabilizzata**
- Descrizione: Marca spesa come contabilizzata
- Attore: Operatore
- Scopo: Registrazione contabilità

### 4.9 Gestione Estratti Conto Bancari

**FP9.1 - Visualizzare lista estratti conto**
- Descrizione: Visualizza estratti conto importati
- Attore: Operatore
- Scopo: Consultazione estratti conto

**FP9.2 - Visualizzare form import estratto conto**
- Descrizione: Visualizza form per import CSV
- Attore: Operatore
- Scopo: Preparazione import

**FP9.3 - Importare estratto conto**
- Descrizione: Importa estratto conto da CSV
- Attore: Operatore
- Scopo: Caricamento dati bancari

**FP9.4 - Eliminare estratto conto**
- Descrizione: Elimina estratto conto
- Attore: Operatore
- Scopo: Rimozione estratto conto

### 4.10 Gestione Abbinamenti Estratti Conto

**FP10.1 - Visualizzare lista abbinamenti**
- Descrizione: Visualizza abbinamenti estratti conto - spese
- Attore: Operatore
- Scopo: Consultazione abbinamenti

**FP10.2 - Creare abbinamento**
- Descrizione: Crea abbinamento estratto conto - spesa
- Attore: Operatore
- Scopo: Collegamento pagamento

**FP10.3 - Assegnare estratto conto a spesa**
- Descrizione: Assegna estratto conto esistente a spesa
- Attore: Operatore
- Scopo: Collegamento pagamento

**FP10.4 - Creare spesa da estratto conto**
- Descrizione: Crea nuova spesa partendo da estratto conto
- Attore: Operatore
- Scopo: Creazione spesa da pagamento

**FP10.5 - Eliminare abbinamento**
- Descrizione: Elimina abbinamento
- Attore: Operatore
- Scopo: Rimozione collegamento

### 4.11 Gestione Metodi di Pagamento

**FP11.1 - Visualizzare lista metodi pagamento**
- Descrizione: Visualizza metodi di pagamento
- Attore: Operatore
- Scopo: Consultazione metodi

**FP11.2 - Visualizzare dettaglio metodo pagamento**
- Descrizione: Visualizza dettaglio metodo
- Attore: Operatore
- Scopo: Consultazione dati metodo

**FP11.3 - Creare nuovo metodo pagamento**
- Descrizione: Crea nuovo metodo pagamento
- Attore: Operatore
- Scopo: Configurazione metodi

**FP11.4 - Modificare metodo pagamento**
- Descrizione: Modifica metodo pagamento
- Attore: Operatore
- Scopo: Aggiornamento metodo

**FP11.5 - Eliminare metodo pagamento**
- Descrizione: Elimina metodo pagamento
- Attore: Operatore
- Scopo: Rimozione metodo

### 4.12 Gestione Anni Fiscali

**FP12.1 - Modificare anno fiscale utente**
- Descrizione: Aggiorna anno fiscale corrente utente
- Attore: Operatore
- Scopo: Configurazione anno fiscale

### 4.13 Dashboard

**FP13.1 - Visualizzare dashboard**
- Descrizione: Visualizza dashboard con statistiche
- Attore: Operatore
- Scopo: Panoramica sistema

### 4.14 Gestione Utenti

**FP14.1 - Visualizzare lista utenti**
- Descrizione: Visualizza tabella utenti
- Attore: Amministratore
- Scopo: Consultazione utenti

**FP14.2 - Visualizzare dettaglio utente**
- Descrizione: Visualizza dettaglio utente
- Attore: Amministratore
- Scopo: Consultazione dati utente

**FP14.3 - Creare nuovo utente**
- Descrizione: Crea nuovo utente
- Attore: Amministratore
- Scopo: Inserimento utente

**FP14.4 - Modificare utente**
- Descrizione: Modifica utente
- Attore: Amministratore
- Scopo: Aggiornamento utente

**FP14.5 - Eliminare utente**
- Descrizione: Elimina utente
- Attore: Amministratore
- Scopo: Rimozione utente

### 4.15 Gestione Permessi e Ruoli

**FP15.1 - Visualizzare lista permessi**
- Descrizione: Visualizza permessi disponibili
- Attore: Amministratore
- Scopo: Consultazione permessi

**FP15.2 - Visualizzare lista ruoli**
- Descrizione: Visualizza ruoli disponibili
- Attore: Amministratore
- Scopo: Consultazione ruoli

**FP15.3 - Creare nuovo ruolo**
- Descrizione: Crea nuovo ruolo
- Attore: Amministratore
- Scopo: Configurazione ruoli

**FP15.4 - Modificare permessi ruolo**
- Descrizione: Aggiorna permessi associati a ruolo
- Attore: Amministratore
- Scopo: Configurazione permessi

**FP15.5 - Eliminare ruolo**
- Descrizione: Elimina ruolo
- Attore: Amministratore
- Scopo: Rimozione ruolo

### 4.16 Gestione Attività

**FP16.1 - Visualizzare lista attività**
- Descrizione: Visualizza attività di sistema
- Attore: Operatore
- Scopo: Consultazione attività

**FP16.2 - Visualizzare dettaglio attività**
- Descrizione: Visualizza dettaglio attività
- Attore: Operatore
- Scopo: Consultazione dati attività

**FP16.3 - Creare nuova attività**
- Descrizione: Crea nuova attività
- Attore: Operatore
- Scopo: Inserimento attività

**FP16.4 - Modificare attività**
- Descrizione: Modifica attività
- Attore: Operatore
- Scopo: Aggiornamento attività

**FP16.5 - Eliminare attività**
- Descrizione: Elimina attività
- Attore: Operatore
- Scopo: Rimozione attività

### 4.17 Autenticazione

**FP17.1 - Login utente**
- Descrizione: Autenticazione utente
- Attore: Utente
- Scopo: Accesso sistema

**FP17.2 - Logout utente**
- Descrizione: Disconnessione utente
- Attore: Utente
- Scopo: Uscita sistema

**FP17.3 - Richiesta reset password**
- Descrizione: Richiesta reset password
- Attore: Utente
- Scopo: Recupero credenziali

**FP17.4 - Reset password**
- Descrizione: Impostazione nuova password
- Attore: Utente
- Scopo: Recupero credenziali

**FP17.5 - Verifica email**
- Descrizione: Verifica indirizzo email
- Attore: Utente
- Scopo: Validazione account

---

## 5. CONTEggio MOVIMENTI DATI

### 5.1 Gestione Progetti

**FP1.1 - Visualizzare lista progetti**
- Entry: 0
- Exit: 1
- Read: 2 (progetti + categorie)
- Write: 0
- **CFP: 3**

**FP1.2 - Visualizzare dettaglio progetto**
- Entry: 1
- Exit: 1
- Read: 2 (progetto + relazioni)
- Write: 0
- **CFP: 4**

**FP1.3 - Creare nuovo progetto**
- Entry: 1
- Exit: 1
- Read: 4 (lookup: clients, employees, categories + validazioni)
- Write: 2 (progetto + documenti upload)
- **CFP: 8**

**FP1.4 - Modificare progetto**
- Entry: 1
- Exit: 1
- Read: 5 (dati esistenti + 4 lookup)
- Write: 2 (update progetto + documenti)
- **CFP: 9**

**FP1.5 - Eliminare progetto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP1.6 - Scaricare documento progetto**
- Entry: 1
- Exit: 1 (file download)
- Read: 1
- Write: 0
- **CFP: 3**

**FP1.7 - Eliminare documento progetto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 2 (delete DB + delete file)
- **CFP: 5**

**FP1.8 - Export progetto**
- Entry: 1
- Exit: 1 (file export)
- Read: 2 (progetto + dati correlati)
- Write: 0
- **CFP: 4**

**Totale Gestione Progetti: 40 CFP**

### 5.2 Gestione Categorie Progetti

**FP2.1 - Visualizzare lista categorie progetti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP2.2 - Visualizzare dettaglio categoria**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP2.3 - Creare nuova categoria progetto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP2.4 - Modificare categoria progetto**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP2.5 - Eliminare categoria progetto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Categorie Progetti: 18 CFP**

### 5.3 Gestione Spese

**FP3.1 - Visualizzare lista spese**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP3.2 - Visualizzare dettaglio spesa**
- Entry: 1
- Exit: 1
- Read: 2 (spesa + relazioni)
- Write: 0
- **CFP: 4**

**FP3.3 - Creare nuova spesa**
- Entry: 1
- Exit: 1
- Read: 4 (lookup: clients, suppliers, payment methods + validazioni)
- Write: 2 (spesa + documenti upload)
- **CFP: 8**

**FP3.4 - Modificare spesa**
- Entry: 1
- Exit: 1
- Read: 5 (dati esistenti + 4 lookup)
- Write: 2 (update spesa + documenti)
- **CFP: 9**

**FP3.5 - Eliminare spesa**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP3.6 - Duplicare spesa**
- Entry: 1
- Exit: 1
- Read: 2 (spesa originale + lookup)
- Write: 1
- **CFP: 5**

**FP3.7 - Scaricare documento spesa**
- Entry: 1
- Exit: 1 (file download)
- Read: 1
- Write: 0
- **CFP: 3**

**FP3.8 - Eliminare documento spesa**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 2 (delete DB + delete file)
- **CFP: 5**

**Totale Gestione Spese: 40 CFP**

### 5.4 Gestione Clienti

**FP4.1 - Visualizzare lista clienti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP4.2 - Visualizzare dettaglio cliente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP4.3 - Creare nuovo cliente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.4 - Modificare cliente**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP4.5 - Eliminare cliente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Clienti: 18 CFP**

### 5.5 Gestione Fornitori

**FP5.1 - Visualizzare lista fornitori**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP5.2 - Visualizzare dettaglio fornitore**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP5.3 - Creare nuovo fornitore**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP5.4 - Modificare fornitore**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP5.5 - Eliminare fornitore**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Fornitori: 18 CFP**

### 5.6 Gestione Dipendenti

**FP6.1 - Visualizzare lista dipendenti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP6.2 - Visualizzare dettaglio dipendente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP6.3 - Creare nuovo dipendente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP6.4 - Modificare dipendente**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP6.5 - Eliminare dipendente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Dipendenti: 18 CFP**

### 5.7 Gestione Documenti

**FP7.1 - Visualizzare lista fatture**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP7.2 - Visualizzare lista documenti spese**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP7.3 - Visualizzare documento**
- Entry: 1
- Exit: 1 (file viewer)
- Read: 1
- Write: 0
- **CFP: 3**

**FP7.4 - Modificare documento**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**Totale Gestione Documenti: 12 CFP**

### 5.8 Gestione Contabilità

**FP8.1 - Visualizzare lista spese da contabilizzare**
- Entry: 0
- Exit: 1
- Read: 2 (spese + documenti)
- Write: 0
- **CFP: 3**

**FP8.2 - Visualizzare lista progetti da contabilizzare**
- Entry: 0
- Exit: 1
- Read: 2 (progetti + documenti)
- Write: 0
- **CFP: 3**

**FP8.3 - Visualizzare documento contabilità**
- Entry: 1
- Exit: 1 (file viewer)
- Read: 1
- Write: 0
- **CFP: 3**

**FP8.4 - Aggiornare progetto contabilizzato**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP8.5 - Aggiornare spesa contabilizzata**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Contabilità: 17 CFP**

### 5.9 Gestione Estratti Conto Bancari

**FP9.1 - Visualizzare lista estratti conto**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP9.2 - Visualizzare form import estratto conto**
- Entry: 1 (selezione metodo pagamento)
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP9.3 - Importare estratto conto**
- Entry: 1 (file CSV upload)
- Exit: 1
- Read: 1 (validazioni)
- Write: 2 (import DB + file storage)
- **CFP: 5**

**FP9.4 - Eliminare estratto conto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Estratti Conto: 14 CFP**

### 5.10 Gestione Abbinamenti Estratti Conto

**FP10.1 - Visualizzare lista abbinamenti**
- Entry: 0
- Exit: 1
- Read: 2 (abbinamenti + relazioni)
- Write: 0
- **CFP: 3**

**FP10.2 - Creare abbinamento**
- Entry: 1
- Exit: 1
- Read: 2 (estratto conto + spesa)
- Write: 1
- **CFP: 5**

**FP10.3 - Assegnare estratto conto a spesa**
- Entry: 1
- Exit: 1
- Read: 2 (estratto conto + spesa)
- Write: 1
- **CFP: 5**

**FP10.4 - Creare spesa da estratto conto**
- Entry: 1
- Exit: 1
- Read: 2 (estratto conto + lookup)
- Write: 1
- **CFP: 5**

**FP10.5 - Eliminare abbinamento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Abbinamenti: 22 CFP**

### 5.11 Gestione Metodi di Pagamento

**FP11.1 - Visualizzare lista metodi pagamento**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP11.2 - Visualizzare dettaglio metodo pagamento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP11.3 - Creare nuovo metodo pagamento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP11.4 - Modificare metodo pagamento**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP11.5 - Eliminare metodo pagamento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Metodi di Pagamento: 18 CFP**

### 5.12 Gestione Anni Fiscali

**FP12.1 - Modificare anno fiscale utente**
- Entry: 1
- Exit: 1
- Read: 2 (utente + anni disponibili)
- Write: 1
- **CFP: 5**

**Totale Gestione Anni Fiscali: 5 CFP**

### 5.13 Dashboard

**FP13.1 - Visualizzare dashboard**
- Entry: 0
- Exit: 1
- Read: 1 (query aggregata)
- Write: 0
- **CFP: 2**

**Totale Dashboard: 2 CFP**

### 5.14 Gestione Utenti

**FP14.1 - Visualizzare lista utenti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP14.2 - Visualizzare dettaglio utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP14.3 - Creare nuovo utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP14.4 - Modificare utente**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP14.5 - Eliminare utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Utenti: 18 CFP**

### 5.15 Gestione Permessi e Ruoli

**FP15.1 - Visualizzare lista permessi**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP15.2 - Visualizzare lista ruoli**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP15.3 - Creare nuovo ruolo**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP15.4 - Modificare permessi ruolo**
- Entry: 1
- Exit: 1
- Read: 2 (ruolo + permessi disponibili)
- Write: 1
- **CFP: 5**

**FP15.5 - Eliminare ruolo**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Permessi e Ruoli: 17 CFP**

### 5.16 Gestione Attività

**FP16.1 - Visualizzare lista attività**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP16.2 - Visualizzare dettaglio attività**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP16.3 - Creare nuova attività**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP16.4 - Modificare attività**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP16.5 - Eliminare attività**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Attività: 18 CFP**

### 5.17 Autenticazione

**FP17.1 - Login utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP17.2 - Logout utente**
- Entry: 1
- Exit: 1
- Read: 0
- Write: 1
- **CFP: 3**

**FP17.3 - Richiesta reset password**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP17.4 - Reset password**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP17.5 - Verifica email**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Autenticazione: 20 CFP**

---

## 6. FUNCTIONAL SIZE (CFP)

### 6.1 Riepilogo per Categoria

| Categoria | Entry | Exit | Read | Write | Totale CFP |
|-----------|-------|------|------|-------|------------|
| Gestione Progetti | 8 | 8 | 18 | 6 | 40 |
| Gestione Categorie Progetti | 4 | 5 | 6 | 3 | 18 |
| Gestione Spese | 7 | 8 | 17 | 6 | 40 |
| Gestione Clienti | 4 | 5 | 6 | 3 | 18 |
| Gestione Fornitori | 4 | 5 | 6 | 3 | 18 |
| Gestione Dipendenti | 4 | 5 | 6 | 3 | 18 |
| Gestione Documenti | 2 | 4 | 5 | 1 | 12 |
| Gestione Contabilità | 3 | 5 | 7 | 2 | 17 |
| Gestione Estratti Conto | 3 | 4 | 4 | 3 | 14 |
| Gestione Abbinamenti | 4 | 5 | 9 | 4 | 22 |
| Gestione Metodi di Pagamento | 4 | 5 | 6 | 3 | 18 |
| Gestione Anni Fiscali | 1 | 1 | 2 | 1 | 5 |
| Dashboard | 0 | 1 | 1 | 0 | 2 |
| Gestione Utenti | 4 | 5 | 6 | 3 | 18 |
| Gestione Permessi e Ruoli | 3 | 5 | 6 | 3 | 17 |
| Gestione Attività | 4 | 5 | 6 | 3 | 18 |
| Autenticazione | 5 | 5 | 6 | 5 | 20 |
| **TOTALE** | **62** | **81** | **123** | **50** | **316** |

### 6.2 Functional Size Totale

**CFP Totali: 316**

**Breakdown:**
- Entry (E): 62 CFP
- Exit (X): 81 CFP
- Read (R): 123 CFP
- Write (W): 50 CFP

---

## 7. DATI EFFORT (OSSERVAZIONE)

**Ore Reali Sostenute:** 290 ore

**Periodo Sviluppo:** Non specificato nei dati disponibili

**Fonte Dati:** Analisi retrospettiva progetti completati (dato da `docs/CONTESTO_TARIFFE.md`)

**⚠️ NOTA:** Questo dato è documentato come **osservazione**. Non viene calcolato alcun coefficiente di produttività. Con N progetti < 3 non è possibile derivare relazioni statistiche valide tra SIZE e COST.

---

## 8. LIMITI E INCERTEZZE

### 8.1 Functional Processes Non Identificabili

**API Endpoints:**
- Esistono API endpoints (ActivityApiController, BankStatementsApiController, ExportProjectController, ExpenseSearchController) che potrebbero rappresentare Functional Processes aggiuntivi
- **Incertezza:** Non analizzati in dettaglio - potrebbero aggiungere 15-25 CFP

**Funzionalità Minori:**
- Alcune funzionalità minori potrebbero non essere state identificate
- **Incertezza:** Stimata ±5% sul totale

### 8.2 Dati Mancanti

- Dettagli implementativi di alcune funzionalità avanzate (ricerca spese, export progetti)
- Alcune operazioni batch o automatizzate potrebbero non essere state identificate

### 8.3 Assunzioni Fatte

- Filtri di ricerca integrati in liste non contati come Entry separati
- Validazioni base non contate come Read separati se integrate nel processo
- Operazioni di calcolo automatico non contate come Functional Process separati se non identificabili dall'utente

### 8.4 Incertezze nel Conteggio

**Range di Incertezza Stimato:**
- CFP minimo: 300 (escludendo API e funzionalità minori)
- CFP massimo: 350 (includendo stima API e funzionalità aggiuntive)
- CFP più probabile: 316

**Affidabilità:** Media (analisi codice sorgente completa, ma alcune API non analizzate in dettaglio)

---

**Firma Analisi:**  
Analisi COSMIC eseguita secondo standard ISO/IEC 19761  
Functional Size: 316 CFP (range incertezza: 300-350 CFP)  
Separazione rigorosa SIZE vs COST mantenuta

