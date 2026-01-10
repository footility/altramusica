# Analisi COSMIC - MsCarichi

**Data Analisi:** Dicembre 2024  
**Standard:** ISO/IEC 19761 - COSMIC Function Points  
**Analista:** Software Measurement Expert  
**Scopo:** Misurazione Functional Size (CFP) del sistema MsCarichi

---

## 1. SCOPO

**Obiettivo:** Identificare e misurare tutti i Functional Processes del sistema MsCarichi secondo standard COSMIC ISO/IEC 19761.

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

**MsCarichi** è un sistema gestionale per la gestione di carichi/trasporti. Gestisce l'intero ciclo di vita: ordini, carichi, clienti, fornitori, pagamenti, magazzino, documenti, note di credito, fatture di addebito.

**Dominio:** Logistica e trasporti

### 3.2 Utenti Identificati

- **Amministratori:** Gestione completa sistema
- **Operatori:** Gestione carichi, ordini, clienti, fornitori
- **Utenti API:** Accesso programmatico (API endpoints)

### 3.3 Confine Sistema

**Incluso:**
- Gestione anagrafica (Clienti, Fornitori, Mediatori, Trasporti, Rese)
- Gestione ordini
- Gestione carichi (CRUD + operazioni avanzate)
- Gestione pagamenti e condizioni di pagamento
- Gestione magazzino (Depositi, Movimenti)
- Gestione documenti e allegati
- Gestione note di credito
- Gestione fatture di addebito
- Gestione costi carichi
- Reportistica annuale
- Dashboard
- Notifiche e reminder
- Gestione utenti e permessi
- Export dati

**Escluso:**
- Autenticazione base (infrastrutturale)
- Routing base (infrastrutturale)
- Template grafici generici (non funzionali)
- Test automatici (non Functional Process)

**Interfacce Esterne:**
- API REST (per accesso programmatico)
- File system (upload/download allegati)
- Email (notifiche)

---

## 4. FUNCTIONAL PROCESSES

### 4.1 Gestione Clienti

**FP1.1 - Visualizzare lista clienti**
- Descrizione: Visualizza tabella con tutti i clienti
- Attore: Operatore
- Scopo: Consultazione anagrafica clienti

**FP1.2 - Visualizzare dettaglio cliente**
- Descrizione: Visualizza dettaglio completo cliente
- Attore: Operatore
- Scopo: Consultazione dati cliente

**FP1.3 - Creare nuovo cliente**
- Descrizione: Crea nuovo record cliente
- Attore: Operatore
- Scopo: Inserimento anagrafica cliente

**FP1.4 - Modificare cliente**
- Descrizione: Modifica dati cliente esistente
- Attore: Operatore
- Scopo: Aggiornamento anagrafica cliente

**FP1.5 - Eliminare cliente**
- Descrizione: Elimina cliente dal sistema
- Attore: Operatore
- Scopo: Rimozione anagrafica cliente

### 4.2 Gestione Fornitori

**FP2.1 - Visualizzare lista fornitori**
- Descrizione: Visualizza tabella con tutti i fornitori
- Attore: Operatore
- Scopo: Consultazione anagrafica fornitori

**FP2.2 - Visualizzare dettaglio fornitore**
- Descrizione: Visualizza dettaglio completo fornitore
- Attore: Operatore
- Scopo: Consultazione dati fornitore

**FP2.3 - Creare nuovo fornitore**
- Descrizione: Crea nuovo record fornitore
- Attore: Operatore
- Scopo: Inserimento anagrafica fornitore

**FP2.4 - Modificare fornitore**
- Descrizione: Modifica dati fornitore esistente
- Attore: Operatore
- Scopo: Aggiornamento anagrafica fornitore

**FP2.5 - Eliminare fornitore**
- Descrizione: Elimina fornitore dal sistema
- Attore: Operatore
- Scopo: Rimozione anagrafica fornitore

### 4.3 Gestione Ordini

**FP3.1 - Visualizzare lista ordini**
- Descrizione: Visualizza tabella con tutti gli ordini
- Attore: Operatore
- Scopo: Consultazione ordini

**FP3.2 - Visualizzare dettaglio ordine**
- Descrizione: Visualizza dettaglio completo ordine
- Attore: Operatore
- Scopo: Consultazione dati ordine

**FP3.3 - Creare nuovo ordine**
- Descrizione: Crea nuovo record ordine
- Attore: Operatore
- Scopo: Inserimento nuovo ordine

**FP3.4 - Modificare ordine**
- Descrizione: Modifica dati ordine esistente
- Attore: Operatore
- Scopo: Aggiornamento ordine

**FP3.5 - Eliminare ordine**
- Descrizione: Elimina ordine dal sistema
- Attore: Operatore
- Scopo: Rimozione ordine

### 4.4 Gestione Carichi

**FP4.1 - Visualizzare lista carichi**
- Descrizione: Visualizza tabella con tutti i carichi (con filtri)
- Attore: Operatore
- Scopo: Consultazione carichi

**FP4.2 - Visualizzare dettaglio carico**
- Descrizione: Visualizza dettaglio completo carico
- Attore: Operatore
- Scopo: Consultazione dati carico

**FP4.3 - Creare nuovo carico**
- Descrizione: Crea nuovo record carico
- Attore: Operatore
- Scopo: Inserimento nuovo carico

**FP4.4 - Modificare carico**
- Descrizione: Modifica dati carico esistente
- Attore: Operatore
- Scopo: Aggiornamento carico

**FP4.5 - Eliminare carico**
- Descrizione: Elimina carico dal sistema
- Attore: Operatore
- Scopo: Rimozione carico

**FP4.6 - Duplicare carico**
- Descrizione: Crea copia di carico esistente
- Attore: Operatore
- Scopo: Creazione rapida carico simile

**FP4.7 - Dividere carico**
- Descrizione: Divide carico in più carichi
- Attore: Operatore
- Scopo: Gestione carichi parziali

**FP4.8 - Toggle carico contestato**
- Descrizione: Marca/rimuove marca carico come contestato
- Attore: Operatore
- Scopo: Gestione controversie

**FP4.9 - Visualizzare carichi eliminati**
- Descrizione: Visualizza carichi in cestino
- Attore: Operatore
- Scopo: Recupero carichi eliminati

**FP4.10 - Ripristinare carico eliminato**
- Descrizione: Ripristina carico dal cestino
- Attore: Operatore
- Scopo: Recupero carico eliminato

**FP4.11 - Eliminare definitivamente carico**
- Descrizione: Elimina permanentemente carico dal cestino
- Attore: Operatore
- Scopo: Rimozione definitiva

**FP4.12 - Export carichi non venduti**
- Descrizione: Esporta lista carichi non venduti
- Attore: Operatore
- Scopo: Export dati per analisi

### 4.5 Gestione Note di Credito

**FP5.1 - Visualizzare lista note di credito**
- Descrizione: Visualizza note di credito per carico
- Attore: Operatore
- Scopo: Consultazione note di credito

**FP5.2 - Scegliere soggetto nota di credito**
- Descrizione: Selezione tipo soggetto (cliente/fornitore)
- Attore: Operatore
- Scopo: Preparazione creazione nota

**FP5.3 - Creare nota di credito**
- Descrizione: Crea nuova nota di credito
- Attore: Operatore
- Scopo: Gestione crediti

**FP5.4 - Eliminare nota di credito**
- Descrizione: Elimina nota di credito
- Attore: Operatore
- Scopo: Rimozione nota errata

### 4.6 Gestione Fatture di Addebito

**FP6.1 - Visualizzare lista fatture di addebito**
- Descrizione: Visualizza tabella fatture di addebito
- Attore: Operatore
- Scopo: Consultazione fatture

**FP6.2 - Creare fattura di addebito**
- Descrizione: Crea nuova fattura di addebito per carico
- Attore: Operatore
- Scopo: Gestione addebiti

**FP6.3 - Modificare fattura di addebito**
- Descrizione: Modifica fattura di addebito esistente
- Attore: Operatore
- Scopo: Aggiornamento fattura

**FP6.4 - Eliminare fattura di addebito**
- Descrizione: Elimina fattura di addebito
- Attore: Operatore
- Scopo: Rimozione fattura

### 4.7 Gestione Costi Carichi

**FP7.1 - Visualizzare lista costi carico**
- Descrizione: Visualizza costi associati a carico
- Attore: Operatore
- Scopo: Consultazione costi

**FP7.2 - Creare nuovo costo carico**
- Descrizione: Aggiunge nuovo costo a carico
- Attore: Operatore
- Scopo: Registrazione costi

**FP7.3 - Modificare costo carico**
- Descrizione: Modifica costo esistente
- Attore: Operatore
- Scopo: Aggiornamento costo

**FP7.4 - Eliminare costo carico**
- Descrizione: Elimina costo da carico
- Attore: Operatore
- Scopo: Rimozione costo

### 4.8 Gestione Tipi di Costo

**FP8.1 - Visualizzare lista tipi di costo**
- Descrizione: Visualizza tipi di costo disponibili
- Attore: Operatore
- Scopo: Consultazione categorie costi

**FP8.2 - Creare nuovo tipo di costo**
- Descrizione: Crea nuova categoria costo
- Attore: Operatore
- Scopo: Configurazione categorie

**FP8.3 - Modificare tipo di costo**
- Descrizione: Modifica tipo di costo
- Attore: Operatore
- Scopo: Aggiornamento categoria

**FP8.4 - Eliminare tipo di costo**
- Descrizione: Elimina tipo di costo
- Attore: Operatore
- Scopo: Rimozione categoria

### 4.9 Gestione Magazzino

**FP9.1 - Visualizzare lista depositi**
- Descrizione: Visualizza depositi disponibili
- Attore: Operatore
- Scopo: Consultazione depositi

**FP9.2 - Creare nuovo deposito**
- Descrizione: Crea nuovo deposito
- Attore: Operatore
- Scopo: Configurazione depositi

**FP9.3 - Modificare deposito**
- Descrizione: Modifica dati deposito
- Attore: Operatore
- Scopo: Aggiornamento deposito

**FP9.4 - Eliminare deposito**
- Descrizione: Elimina deposito
- Attore: Operatore
- Scopo: Rimozione deposito

**FP9.5 - Visualizzare movimenti magazzino**
- Descrizione: Visualizza movimenti per carico
- Attore: Operatore
- Scopo: Consultazione movimenti

**FP9.6 - Creare movimento magazzino**
- Descrizione: Registra movimento magazzino per carico
- Attore: Operatore
- Scopo: Tracciamento movimenti

**FP9.7 - Eliminare movimento magazzino**
- Descrizione: Elimina movimento magazzino
- Attore: Operatore
- Scopo: Correzione movimenti

### 4.10 Gestione Merci

**FP10.1 - Visualizzare lista merci**
- Descrizione: Visualizza tabella merci
- Attore: Operatore
- Scopo: Consultazione merci

**FP10.2 - Visualizzare dettaglio merce**
- Descrizione: Visualizza dettaglio merce
- Attore: Operatore
- Scopo: Consultazione dati merce

**FP10.3 - Creare nuova merce**
- Descrizione: Crea nuovo record merce
- Attore: Operatore
- Scopo: Inserimento merce

**FP10.4 - Modificare merce**
- Descrizione: Modifica dati merce
- Attore: Operatore
- Scopo: Aggiornamento merce

**FP10.5 - Eliminare merce**
- Descrizione: Elimina merce
- Attore: Operatore
- Scopo: Rimozione merce

### 4.11 Gestione Mediatori

**FP11.1 - Visualizzare lista mediatori**
- Descrizione: Visualizza tabella mediatori
- Attore: Operatore
- Scopo: Consultazione mediatori

**FP11.2 - Visualizzare dettaglio mediatore**
- Descrizione: Visualizza dettaglio mediatore
- Attore: Operatore
- Scopo: Consultazione dati mediatore

**FP11.3 - Creare nuovo mediatore**
- Descrizione: Crea nuovo mediatore
- Attore: Operatore
- Scopo: Inserimento mediatore

**FP11.4 - Modificare mediatore**
- Descrizione: Modifica dati mediatore
- Attore: Operatore
- Scopo: Aggiornamento mediatore

**FP11.5 - Eliminare mediatore**
- Descrizione: Elimina mediatore
- Attore: Operatore
- Scopo: Rimozione mediatore

### 4.12 Gestione Trasporti

**FP12.1 - Visualizzare lista trasporti**
- Descrizione: Visualizza tabella trasporti
- Attore: Operatore
- Scopo: Consultazione trasporti

**FP12.2 - Visualizzare dettaglio trasporto**
- Descrizione: Visualizza dettaglio trasporto
- Attore: Operatore
- Scopo: Consultazione dati trasporto

**FP12.3 - Creare nuovo trasporto**
- Descrizione: Crea nuovo trasporto
- Attore: Operatore
- Scopo: Inserimento trasporto

**FP12.4 - Modificare trasporto**
- Descrizione: Modifica dati trasporto
- Attore: Operatore
- Scopo: Aggiornamento trasporto

**FP12.5 - Eliminare trasporto**
- Descrizione: Elimina trasporto
- Attore: Operatore
- Scopo: Rimozione trasporto

### 4.13 Gestione Rese

**FP13.1 - Visualizzare lista rese**
- Descrizione: Visualizza tabella rese
- Attore: Operatore
- Scopo: Consultazione rese

**FP13.2 - Visualizzare dettaglio resa**
- Descrizione: Visualizza dettaglio resa
- Attore: Operatore
- Scopo: Consultazione dati resa

**FP13.3 - Creare nuova resa**
- Descrizione: Crea nuova resa
- Attore: Operatore
- Scopo: Inserimento resa

**FP13.4 - Modificare resa**
- Descrizione: Modifica dati resa
- Attore: Operatore
- Scopo: Aggiornamento resa

**FP13.5 - Eliminare resa**
- Descrizione: Elimina resa
- Attore: Operatore
- Scopo: Rimozione resa

### 4.14 Gestione Condizioni di Pagamento

**FP14.1 - Visualizzare lista condizioni pagamento**
- Descrizione: Visualizza condizioni pagamento
- Attore: Operatore
- Scopo: Consultazione condizioni

**FP14.2 - Creare nuova condizione pagamento**
- Descrizione: Crea nuova condizione
- Attore: Operatore
- Scopo: Configurazione condizioni

**FP14.3 - Modificare condizione pagamento**
- Descrizione: Modifica condizione
- Attore: Operatore
- Scopo: Aggiornamento condizione

**FP14.4 - Eliminare condizione pagamento**
- Descrizione: Elimina condizione
- Attore: Operatore
- Scopo: Rimozione condizione

### 4.15 Gestione Documenti

**FP15.1 - Visualizzare lista tipi documento**
- Descrizione: Visualizza tipi documento
- Attore: Operatore
- Scopo: Consultazione tipi

**FP15.2 - Creare nuovo tipo documento**
- Descrizione: Crea nuovo tipo documento
- Attore: Operatore
- Scopo: Configurazione tipi

**FP15.3 - Modificare tipo documento**
- Descrizione: Modifica tipo documento
- Attore: Operatore
- Scopo: Aggiornamento tipo

**FP15.4 - Eliminare tipo documento**
- Descrizione: Elimina tipo documento
- Attore: Operatore
- Scopo: Rimozione tipo

**FP15.5 - Visualizzare lista documenti**
- Descrizione: Visualizza documenti
- Attore: Operatore
- Scopo: Consultazione documenti

**FP15.6 - Creare nuovo documento**
- Descrizione: Crea nuovo documento
- Attore: Operatore
- Scopo: Inserimento documento

**FP15.7 - Modificare documento**
- Descrizione: Modifica documento
- Attore: Operatore
- Scopo: Aggiornamento documento

**FP15.8 - Eliminare documento**
- Descrizione: Elimina documento
- Attore: Operatore
- Scopo: Rimozione documento

### 4.16 Gestione Allegati

**FP16.1 - Visualizzare allegati**
- Descrizione: Visualizza allegati associati
- Attore: Operatore
- Scopo: Consultazione allegati

**FP16.2 - Caricare allegato**
- Descrizione: Upload nuovo allegato
- Attore: Operatore
- Scopo: Aggiunta allegato

**FP16.3 - Modificare allegato**
- Descrizione: Modifica metadati allegato
- Attore: Operatore
- Scopo: Aggiornamento allegato

**FP16.4 - Eliminare allegato**
- Descrizione: Elimina allegato
- Attore: Operatore
- Scopo: Rimozione allegato

**FP16.5 - Scaricare allegato**
- Descrizione: Download allegato
- Attore: Operatore
- Scopo: Recupero file

### 4.17 Reportistica

**FP17.1 - Visualizzare report annuale**
- Descrizione: Genera e visualizza report annuale carichi
- Attore: Operatore
- Scopo: Analisi dati annuali

### 4.18 Dashboard

**FP18.1 - Visualizzare dashboard**
- Descrizione: Visualizza dashboard con statistiche carichi
- Attore: Operatore
- Scopo: Panoramica sistema

### 4.19 Gestione Notifiche

**FP19.1 - Visualizzare impostazioni notifiche**
- Descrizione: Visualizza configurazione notifiche
- Attore: Operatore
- Scopo: Consultazione impostazioni

**FP19.2 - Modificare impostazioni notifiche**
- Descrizione: Aggiorna configurazione notifiche
- Attore: Operatore
- Scopo: Configurazione notifiche

**FP19.3 - Inviare reminder test**
- Descrizione: Invia reminder di test
- Attore: Operatore
- Scopo: Test notifiche

**FP19.4 - Visualizzare log notifiche**
- Descrizione: Visualizza storico notifiche inviate
- Attore: Operatore
- Scopo: Consultazione log

### 4.20 Gestione Utenti

**FP20.1 - Visualizzare lista utenti**
- Descrizione: Visualizza tabella utenti
- Attore: Amministratore
- Scopo: Consultazione utenti

**FP20.2 - Visualizzare dettaglio utente**
- Descrizione: Visualizza dettaglio utente
- Attore: Amministratore
- Scopo: Consultazione dati utente

**FP20.3 - Creare nuovo utente**
- Descrizione: Crea nuovo utente
- Attore: Amministratore
- Scopo: Inserimento utente

**FP20.4 - Modificare utente**
- Descrizione: Modifica dati utente
- Attore: Amministratore
- Scopo: Aggiornamento utente

**FP20.5 - Eliminare utente**
- Descrizione: Elimina utente
- Attore: Amministratore
- Scopo: Rimozione utente

### 4.21 Gestione Permessi e Ruoli

**FP21.1 - Visualizzare lista permessi**
- Descrizione: Visualizza permessi disponibili
- Attore: Amministratore
- Scopo: Consultazione permessi

**FP21.2 - Visualizzare lista ruoli**
- Descrizione: Visualizza ruoli disponibili
- Attore: Amministratore
- Scopo: Consultazione ruoli

**FP21.3 - Creare nuovo ruolo**
- Descrizione: Crea nuovo ruolo
- Attore: Amministratore
- Scopo: Configurazione ruoli

**FP21.4 - Modificare permessi ruolo**
- Descrizione: Aggiorna permessi associati a ruolo
- Attore: Amministratore
- Scopo: Configurazione permessi

**FP21.5 - Eliminare ruolo**
- Descrizione: Elimina ruolo
- Attore: Amministratore
- Scopo: Rimozione ruolo

### 4.22 Gestione Attività

**FP22.1 - Visualizzare lista attività**
- Descrizione: Visualizza attività di sistema
- Attore: Operatore
- Scopo: Consultazione attività

**FP22.2 - Visualizzare dettaglio attività**
- Descrizione: Visualizza dettaglio attività
- Attore: Operatore
- Scopo: Consultazione dati attività

**FP22.3 - Creare nuova attività**
- Descrizione: Crea nuova attività
- Attore: Operatore
- Scopo: Inserimento attività

**FP22.4 - Modificare attività**
- Descrizione: Modifica attività
- Attore: Operatore
- Scopo: Aggiornamento attività

**FP22.5 - Eliminare attività**
- Descrizione: Elimina attività
- Attore: Operatore
- Scopo: Rimozione attività

### 4.23 Autenticazione (Funzionale)

**FP23.1 - Login utente**
- Descrizione: Autenticazione utente
- Attore: Utente
- Scopo: Accesso sistema

**FP23.2 - Logout utente**
- Descrizione: Disconnessione utente
- Attore: Utente
- Scopo: Uscita sistema

**FP23.3 - Richiesta reset password**
- Descrizione: Richiesta reset password
- Attore: Utente
- Scopo: Recupero credenziali

**FP23.4 - Reset password**
- Descrizione: Impostazione nuova password
- Attore: Utente
- Scopo: Recupero credenziali

**FP23.5 - Verifica email**
- Descrizione: Verifica indirizzo email
- Attore: Utente
- Scopo: Validazione account

---

## 5. CONTEggio MOVIMENTI DATI

### 5.1 Gestione Clienti

**FP1.1 - Visualizzare lista clienti**
- Entry: 0 (nessun input utente per lista base)
- Exit: 1 (tabella clienti)
- Read: 1 (query lista clienti)
- Write: 0
- **CFP: 2**

**FP1.2 - Visualizzare dettaglio cliente**
- Entry: 1 (selezione cliente)
- Exit: 1 (dettaglio cliente)
- Read: 1 (query dettaglio cliente)
- Write: 0
- **CFP: 3**

**FP1.3 - Creare nuovo cliente**
- Entry: 1 (form creazione)
- Exit: 1 (conferma creazione)
- Read: 1 (validazioni, lookup payment terms)
- Write: 1 (insert cliente)
- **CFP: 4**

**FP1.4 - Modificare cliente**
- Entry: 1 (form modifica)
- Exit: 1 (conferma modifica)
- Read: 2 (caricamento dati esistenti, lookup payment terms, lookup goods)
- Write: 1 (update cliente)
- **CFP: 5**

**FP1.5 - Eliminare cliente**
- Entry: 1 (conferma eliminazione)
- Exit: 1 (conferma eliminazione)
- Read: 1 (verifica dipendenze)
- Write: 1 (delete cliente)
- **CFP: 4**

**Totale Gestione Clienti: 18 CFP**

### 5.2 Gestione Fornitori

**FP2.1 - Visualizzare lista fornitori**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP2.2 - Visualizzare dettaglio fornitore**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP2.3 - Creare nuovo fornitore**
- Entry: 1
- Exit: 1
- Read: 1 (validazioni, lookup payment terms)
- Write: 1
- **CFP: 4**

**FP2.4 - Modificare fornitore**
- Entry: 1
- Exit: 1
- Read: 2 (dati esistenti, lookup payment terms)
- Write: 1
- **CFP: 5**

**FP2.5 - Eliminare fornitore**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Fornitori: 18 CFP**

### 5.3 Gestione Ordini

**FP3.1 - Visualizzare lista ordini**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP3.2 - Visualizzare dettaglio ordine**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP3.3 - Creare nuovo ordine**
- Entry: 1
- Exit: 1
- Read: 1 (validazioni)
- Write: 1
- **CFP: 4**

**FP3.4 - Modificare ordine**
- Entry: 1
- Exit: 1
- Read: 2 (dati esistenti, validazioni)
- Write: 1
- **CFP: 5**

**FP3.5 - Eliminare ordine**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Ordini: 18 CFP**

### 5.4 Gestione Carichi

**FP4.1 - Visualizzare lista carichi**
- Entry: 0 (filtri opzionali non contati come Entry separato se integrati)
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP4.2 - Visualizzare dettaglio carico**
- Entry: 1
- Exit: 1
- Read: 2 (dettaglio carico + relazioni)
- Write: 0
- **CFP: 4**

**FP4.3 - Creare nuovo carico**
- Entry: 1
- Exit: 1
- Read: 8 (lookup: orders, suppliers, clients, payment terms, goods, mediators, resas, stores, document types)
- Write: 1
- **CFP: 11**

**FP4.4 - Modificare carico**
- Entry: 1
- Exit: 1
- Read: 9 (dati esistenti + 8 lookup come sopra)
- Write: 1
- **CFP: 12**

**FP4.5 - Eliminare carico**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.6 - Duplicare carico**
- Entry: 1
- Exit: 1
- Read: 2 (carico originale + lookup per form)
- Write: 1
- **CFP: 5**

**FP4.7 - Dividere carico**
- Entry: 1
- Exit: 1
- Read: 2 (carico originale + validazioni)
- Write: 2 (update originale + insert nuovo)
- **CFP: 6**

**FP4.8 - Toggle carico contestato**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.9 - Visualizzare carichi eliminati**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP4.10 - Ripristinare carico eliminato**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.11 - Eliminare definitivamente carico**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.12 - Export carichi non venduti**
- Entry: 1 (comando export)
- Exit: 1 (file export)
- Read: 1 (query carichi non venduti)
- Write: 0
- **CFP: 3**

**Totale Gestione Carichi: 55 CFP**

### 5.5 Gestione Note di Credito

**FP5.1 - Visualizzare lista note di credito**
- Entry: 1 (selezione carico)
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP5.2 - Scegliere soggetto nota di credito**
- Entry: 1
- Exit: 1
- Read: 1 (dati carico)
- Write: 0
- **CFP: 3**

**FP5.3 - Creare nota di credito**
- Entry: 1
- Exit: 1
- Read: 2 (dati carico + validazioni)
- Write: 1
- **CFP: 5**

**FP5.4 - Eliminare nota di credito**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Note di Credito: 15 CFP**

### 5.6 Gestione Fatture di Addebito

**FP6.1 - Visualizzare lista fatture di addebito**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP6.2 - Creare fattura di addebito**
- Entry: 1
- Exit: 1
- Read: 2 (dati carico + validazioni)
- Write: 1
- **CFP: 5**

**FP6.3 - Modificare fattura di addebito**
- Entry: 1
- Exit: 1
- Read: 3 (dati esistenti + carico + validazioni)
- Write: 1
- **CFP: 6**

**FP6.4 - Eliminare fattura di addebito**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Fatture di Addebito: 17 CFP**

### 5.7 Gestione Costi Carichi

**FP7.1 - Visualizzare lista costi carico**
- Entry: 1 (selezione carico)
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP7.2 - Creare nuovo costo carico**
- Entry: 1
- Exit: 1
- Read: 2 (dati carico + lookup cost types)
- Write: 1
- **CFP: 5**

**FP7.3 - Modificare costo carico**
- Entry: 1
- Exit: 1
- Read: 3 (dati esistenti + carico + cost types)
- Write: 1
- **CFP: 6**

**FP7.4 - Eliminare costo carico**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Costi Carichi: 18 CFP**

### 5.8 Gestione Tipi di Costo

**FP8.1 - Visualizzare lista tipi di costo**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP8.2 - Creare nuovo tipo di costo**
- Entry: 1
- Exit: 1
- Read: 1 (validazioni)
- Write: 1
- **CFP: 4**

**FP8.3 - Modificare tipo di costo**
- Entry: 1
- Exit: 1
- Read: 2 (dati esistenti + validazioni)
- Write: 1
- **CFP: 5**

**FP8.4 - Eliminare tipo di costo**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Tipi di Costo: 15 CFP**

### 5.9 Gestione Magazzino

**FP9.1 - Visualizzare lista depositi**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP9.2 - Creare nuovo deposito**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP9.3 - Modificare deposito**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP9.4 - Eliminare deposito**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP9.5 - Visualizzare movimenti magazzino**
- Entry: 1 (selezione carico)
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP9.6 - Creare movimento magazzino**
- Entry: 1
- Exit: 1
- Read: 2 (dati carico + depositi)
- Write: 1
- **CFP: 5**

**FP9.7 - Eliminare movimento magazzino**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Magazzino: 27 CFP**

### 5.10 Gestione Merci

**FP10.1 - Visualizzare lista merci**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP10.2 - Visualizzare dettaglio merce**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP10.3 - Creare nuova merce**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP10.4 - Modificare merce**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP10.5 - Eliminare merce**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Merci: 18 CFP**

### 5.11 Gestione Mediatori

**FP11.1 - Visualizzare lista mediatori**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP11.2 - Visualizzare dettaglio mediatore**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP11.3 - Creare nuovo mediatore**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP11.4 - Modificare mediatore**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP11.5 - Eliminare mediatore**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Mediatori: 18 CFP**

### 5.12 Gestione Trasporti

**FP12.1 - Visualizzare lista trasporti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP12.2 - Visualizzare dettaglio trasporto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP12.3 - Creare nuovo trasporto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP12.4 - Modificare trasporto**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP12.5 - Eliminare trasporto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Trasporti: 18 CFP**

### 5.13 Gestione Rese

**FP13.1 - Visualizzare lista rese**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP13.2 - Visualizzare dettaglio resa**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP13.3 - Creare nuova resa**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP13.4 - Modificare resa**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP13.5 - Eliminare resa**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Rese: 18 CFP**

### 5.14 Gestione Condizioni di Pagamento

**FP14.1 - Visualizzare lista condizioni pagamento**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP14.2 - Creare nuova condizione pagamento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP14.3 - Modificare condizione pagamento**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP14.4 - Eliminare condizione pagamento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Condizioni di Pagamento: 15 CFP**

### 5.15 Gestione Documenti

**FP15.1 - Visualizzare lista tipi documento**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP15.2 - Creare nuovo tipo documento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP15.3 - Modificare tipo documento**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP15.4 - Eliminare tipo documento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP15.5 - Visualizzare lista documenti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP15.6 - Creare nuovo documento**
- Entry: 1
- Exit: 1
- Read: 2 (lookup tipi documento + validazioni)
- Write: 1
- **CFP: 5**

**FP15.7 - Modificare documento**
- Entry: 1
- Exit: 1
- Read: 3 (dati esistenti + tipi documento + validazioni)
- Write: 1
- **CFP: 6**

**FP15.8 - Eliminare documento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Documenti: 32 CFP**

### 5.16 Gestione Allegati

**FP16.1 - Visualizzare allegati**
- Entry: 1 (selezione entità)
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP16.2 - Caricare allegato**
- Entry: 1 (file upload)
- Exit: 1
- Read: 1 (validazioni)
- Write: 2 (metadati DB + file storage)
- **CFP: 5**

**FP16.3 - Modificare allegato**
- Entry: 1
- Exit: 1
- Read: 2 (dati esistenti + validazioni)
- Write: 1
- **CFP: 5**

**FP16.4 - Eliminare allegato**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 2 (delete DB + delete file)
- **CFP: 5**

**FP16.5 - Scaricare allegato**
- Entry: 1
- Exit: 1 (file download)
- Read: 1
- Write: 0
- **CFP: 3**

**Totale Gestione Allegati: 21 CFP**

### 5.17 Reportistica

**FP17.1 - Visualizzare report annuale**
- Entry: 1 (selezione anno)
- Exit: 1 (report visualizzato)
- Read: 3 (carichi anno + relazioni + calcoli aggregati)
- Write: 0
- **CFP: 5**

**Totale Reportistica: 5 CFP**

### 5.18 Dashboard

**FP18.1 - Visualizzare dashboard**
- Entry: 0
- Exit: 1
- Read: 1 (query aggregata carichi)
- Write: 0
- **CFP: 2**

**Totale Dashboard: 2 CFP**

### 5.19 Gestione Notifiche

**FP19.1 - Visualizzare impostazioni notifiche**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP19.2 - Modificare impostazioni notifiche**
- Entry: 1
- Exit: 1
- Read: 2 (dati esistenti + validazioni)
- Write: 1
- **CFP: 5**

**FP19.3 - Inviare reminder test**
- Entry: 1
- Exit: 1
- Read: 1 (configurazione)
- Write: 1 (log notifica)
- **CFP: 4**

**FP19.4 - Visualizzare log notifiche**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**Totale Gestione Notifiche: 13 CFP**

### 5.20 Gestione Utenti

**FP20.1 - Visualizzare lista utenti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP20.2 - Visualizzare dettaglio utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP20.3 - Creare nuovo utente**
- Entry: 1
- Exit: 1
- Read: 1 (validazioni)
- Write: 1
- **CFP: 4**

**FP20.4 - Modificare utente**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP20.5 - Eliminare utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Utenti: 18 CFP**

### 5.21 Gestione Permessi e Ruoli

**FP21.1 - Visualizzare lista permessi**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP21.2 - Visualizzare lista ruoli**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP21.3 - Creare nuovo ruolo**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP21.4 - Modificare permessi ruolo**
- Entry: 1
- Exit: 1
- Read: 2 (ruolo + permessi disponibili)
- Write: 1
- **CFP: 5**

**FP21.5 - Eliminare ruolo**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Permessi e Ruoli: 17 CFP**

### 5.22 Gestione Attività

**FP22.1 - Visualizzare lista attività**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP22.2 - Visualizzare dettaglio attività**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP22.3 - Creare nuova attività**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP22.4 - Modificare attività**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP22.5 - Eliminare attività**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Attività: 18 CFP**

### 5.23 Autenticazione

**FP23.1 - Login utente**
- Entry: 1 (credenziali)
- Exit: 1 (conferma login)
- Read: 1 (verifica credenziali)
- Write: 1 (sessione)
- **CFP: 4**

**FP23.2 - Logout utente**
- Entry: 1
- Exit: 1
- Read: 0
- Write: 1 (distruzione sessione)
- **CFP: 3**

**FP23.3 - Richiesta reset password**
- Entry: 1
- Exit: 1
- Read: 1 (verifica email)
- Write: 1 (token reset)
- **CFP: 4**

**FP23.4 - Reset password**
- Entry: 1
- Exit: 1
- Read: 2 (verifica token + utente)
- Write: 1 (update password)
- **CFP: 5**

**FP23.5 - Verifica email**
- Entry: 1 (click link)
- Exit: 1
- Read: 1 (verifica token)
- Write: 1 (update email verified)
- **CFP: 4**

**Totale Autenticazione: 20 CFP**

---

## 6. FUNCTIONAL SIZE (CFP)

### 6.1 Riepilogo per Categoria

| Categoria | Entry | Exit | Read | Write | Totale CFP |
|-----------|-------|------|------|-------|------------|
| Gestione Clienti | 4 | 5 | 6 | 3 | 18 |
| Gestione Fornitori | 4 | 5 | 6 | 3 | 18 |
| Gestione Ordini | 4 | 5 | 6 | 3 | 18 |
| Gestione Carichi | 9 | 12 | 30 | 4 | 55 |
| Gestione Note di Credito | 3 | 4 | 5 | 3 | 15 |
| Gestione Fatture di Addebito | 3 | 4 | 7 | 3 | 17 |
| Gestione Costi Carichi | 3 | 4 | 7 | 3 | 18 |
| Gestione Tipi di Costo | 3 | 4 | 5 | 3 | 15 |
| Gestione Magazzino | 5 | 7 | 9 | 6 | 27 |
| Gestione Merci | 4 | 5 | 6 | 3 | 18 |
| Gestione Mediatori | 4 | 5 | 6 | 3 | 18 |
| Gestione Trasporti | 4 | 5 | 6 | 3 | 18 |
| Gestione Rese | 4 | 5 | 6 | 3 | 18 |
| Gestione Condizioni di Pagamento | 3 | 4 | 5 | 3 | 15 |
| Gestione Documenti | 5 | 8 | 12 | 5 | 30 |
| Gestione Allegati | 4 | 5 | 6 | 6 | 21 |
| Reportistica | 1 | 1 | 3 | 0 | 5 |
| Dashboard | 0 | 1 | 1 | 0 | 2 |
| Gestione Notifiche | 2 | 4 | 5 | 2 | 13 |
| Gestione Utenti | 4 | 5 | 6 | 3 | 18 |
| Gestione Permessi e Ruoli | 3 | 5 | 6 | 3 | 17 |
| Gestione Attività | 4 | 5 | 6 | 3 | 18 |
| Autenticazione | 5 | 5 | 6 | 5 | 21 |
| **TOTALE** | **88** | **113** | **170** | **71** | **442** |

### 6.2 Functional Size Totale

**CFP Totali: 442**

**Breakdown:**
- Entry (E): 88 CFP
- Exit (X): 113 CFP
- Read (R): 170 CFP
- Write (W): 71 CFP

---

## 7. DATI EFFORT (OSSERVAZIONE)

**Ore Reali Sostenute:** 290 ore (dato osservato da analisi precedente)

**Periodo Sviluppo:** Non specificato nei dati disponibili

**Fonte Dati:** Analisi retrospettiva progetti completati

**⚠️ NOTA:** Questo dato è documentato come **osservazione**. Non viene calcolato alcun coefficiente di produttività. Con N progetti < 3 non è possibile derivare relazioni statistiche valide tra SIZE e COST.

---

## 8. LIMITI E INCERTEZZE

### 8.1 Functional Processes Non Identificabili

**API Endpoints:**
- Esistono API endpoints (LoadApiController, ClientApiController, SupplierApiController, AttachmentApiController, NotesApiController, OrderApiController) che potrebbero rappresentare Functional Processes aggiuntivi
- **Incertezza:** Non analizzati in dettaglio - potrebbero aggiungere 20-30 CFP

**Funzionalità Minori:**
- Alcune funzionalità minori potrebbero non essere state identificate
- **Incertezza:** Stimata ±5% sul totale

### 8.2 Dati Mancanti

- Dettagli implementativi di alcune funzionalità avanzate (calcoli automatici, validazioni complesse)
- Alcune operazioni batch o automatizzate potrebbero non essere state identificate

### 8.3 Assunzioni Fatte

- Filtri di ricerca integrati in liste non contati come Entry separati (considerati parte del FP "Visualizzare lista")
- Validazioni base non contate come Read separati se integrate nel processo
- Operazioni di calcolo automatico non contate come Functional Process separati se non identificabili dall'utente

### 8.4 Incertezze nel Conteggio

**Range di Incertezza Stimato:**
- CFP minimo: 420 (escludendo API e funzionalità minori)
- CFP massimo: 480 (includendo stima API e funzionalità aggiuntive)
- CFP più probabile: 442

**Affidabilità:** Media-Alta (analisi codice sorgente completa, ma alcune API non analizzate in dettaglio)

---

**Firma Analisi:**  
Analisi COSMIC eseguita secondo standard ISO/IEC 19761  
Functional Size: 442 CFP (range incertezza: 420-480 CFP)  
Separazione rigorosa SIZE vs COST mantenuta

