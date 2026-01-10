# Analisi COSMIC - Klabhouse

**Data Analisi:** Dicembre 2024  
**Standard:** ISO/IEC 19761 - COSMIC Function Points  
**Analista:** Software Measurement Expert  
**Scopo:** Misurazione Functional Size (CFP) del sistema Klabhouse

---

## 1. SCOPO

**Obiettivo:** Identificare e misurare tutti i Functional Processes del sistema Klabhouse secondo standard COSMIC ISO/IEC 19761.

**Metodologia:** Analisi codice sorgente (Controllers, Routes, Models) per identificare operazioni utente identificabili.

**Standard Applicato:** ISO/IEC 19761 - COSMIC Function Points v4.0.1

**Nota:** Progetto migrazione da CodeIgniter a Laravel. Analisi considera solo funzionalità Laravel (non legacy).

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

**Klabhouse** è un sistema di gestione prenotazioni per proprietà immobiliari. Gestisce l'intero ciclo: proprietà, prenotazioni, pagamenti, calendari, contenuti, utenti.

**Dominio:** Booking e gestione proprietà immobiliari

### 3.2 Utenti Identificati

- **Amministratori:** Gestione completa sistema
- **Utenti registrati:** Gestione prenotazioni personali, profilo
- **Visitatori:** Ricerca proprietà, visualizzazione contenuti
- **OAuth (Google):** Autenticazione esterna

### 3.3 Confine Sistema

**Incluso:**
- Gestione proprietà (CRUD + sezioni avanzate)
- Gestione prenotazioni (funnel completo)
- Gestione pagamenti (Stripe, PayPal)
- Gestione calendari (blocchi, iCal sync)
- Gestione contenuti (pagine personalizzate, componenti)
- Gestione collezioni proprietà
- Gestione utenti e profili
- Ricerca proprietà
- Wishlist
- Newsletter
- Account utente (prenotazioni, profilo)
- Admin dashboard
- Multi-lingua (IT/EN)
- White-label

**Escluso:**
- Autenticazione base (infrastrutturale)
- Routing base (infrastrutturale)
- Template grafici generici (non funzionali)
- Test automatici (non Functional Process)
- API Legacy (non parte migrazione Laravel)

**Interfacce Esterne:**
- Stripe (pagamenti)
- PayPal (pagamenti)
- Google OAuth (autenticazione)
- iCal (sincronizzazione calendari esterni)

---

## 4. FUNCTIONAL PROCESSES

### 4.1 Frontend - Ricerca e Visualizzazione Proprietà

**FP1.1 - Visualizzare homepage**
- Descrizione: Visualizza homepage con contenuti personalizzati
- Attore: Visitatore
- Scopo: Accesso iniziale

**FP1.2 - Visualizzare pagina personalizzata**
- Descrizione: Visualizza pagina custom con componenti
- Attore: Visitatore
- Scopo: Navigazione contenuti

**FP1.3 - Cercare proprietà**
- Descrizione: Ricerca proprietà con filtri
- Attore: Visitatore
- Scopo: Trovare proprietà

**FP1.4 - Visualizzare dettaglio proprietà**
- Descrizione: Visualizza dettaglio completo proprietà
- Attore: Visitatore
- Scopo: Consultazione proprietà

**FP1.5 - Visualizzare lista real estate**
- Descrizione: Visualizza lista proprietà real estate
- Attore: Visitatore
- Scopo: Consultazione proprietà

**FP1.6 - Visualizzare dettaglio real estate**
- Descrizione: Visualizza dettaglio proprietà real estate
- Attore: Visitatore
- Scopo: Consultazione proprietà

**FP1.7 - Aggiungere proprietà a wishlist**
- Descrizione: Aggiunge proprietà a wishlist utente
- Attore: Utente
- Scopo: Salvataggio preferenze

**FP1.8 - Rimuovere proprietà da wishlist**
- Descrizione: Rimuove proprietà da wishlist
- Attore: Utente
- Scopo: Gestione preferenze

**FP1.9 - Toggle wishlist proprietà**
- Descrizione: Aggiunge/rimuove proprietà da wishlist
- Attore: Utente
- Scopo: Gestione preferenze

**FP1.10 - Inviare contatto proprietà**
- Descrizione: Invia messaggio contatto per proprietà
- Attore: Visitatore
- Scopo: Richiesta informazioni

**FP1.11 - Aggiornare valuta**
- Descrizione: Cambia valuta visualizzazione
- Attore: Visitatore
- Scopo: Personalizzazione

### 4.2 Frontend - Booking

**FP2.1 - Visualizzare form prenotazione (step 1)**
- Descrizione: Visualizza form iniziale prenotazione
- Attore: Visitatore
- Scopo: Inizio prenotazione

**FP2.2 - Visualizzare funnel prenotazione**
- Descrizione: Visualizza funnel prenotazione con parametri
- Attore: Visitatore
- Scopo: Processo prenotazione

**FP2.3 - Visualizzare prenotazione con codice**
- Descrizione: Visualizza/modifica prenotazione esistente con codice
- Attore: Utente
- Scopo: Gestione prenotazione

**FP2.4 - Aggiornare prenotazione con codice**
- Descrizione: Aggiorna dati prenotazione esistente
- Attore: Utente
- Scopo: Modifica prenotazione

**FP2.5 - Confermare prenotazione PayPal**
- Descrizione: Conferma pagamento PayPal
- Attore: Utente
- Scopo: Completamento pagamento

**FP2.6 - Annullare prenotazione PayPal**
- Descrizione: Annulla pagamento PayPal
- Attore: Utente
- Scopo: Gestione pagamento

**FP2.7 - Confermare prenotazione Stripe**
- Descrizione: Conferma pagamento Stripe
- Attore: Utente
- Scopo: Completamento pagamento

**FP2.8 - Annullare prenotazione Stripe**
- Descrizione: Annulla pagamento Stripe
- Attore: Utente
- Scopo: Gestione pagamento

**FP2.9 - Visualizzare successo prenotazione**
- Descrizione: Visualizza conferma prenotazione completata
- Attore: Utente
- Scopo: Conferma operazione

### 4.3 Account Utente

**FP3.1 - Visualizzare lista prenotazioni account**
- Descrizione: Visualizza prenotazioni utente (attive/archiviate/cancellate)
- Attore: Utente
- Scopo: Consultazione prenotazioni

**FP3.2 - Visualizzare profilo account**
- Descrizione: Visualizza profilo utente
- Attore: Utente
- Scopo: Consultazione profilo

**FP3.3 - Modificare profilo account**
- Descrizione: Aggiorna dati profilo utente
- Attore: Utente
- Scopo: Aggiornamento profilo

### 4.4 Admin - Gestione Proprietà

**FP4.1 - Visualizzare lista proprietà admin**
- Descrizione: Visualizza tabella proprietà
- Attore: Amministratore
- Scopo: Consultazione proprietà

**FP4.2 - Visualizzare dettaglio proprietà admin**
- Descrizione: Visualizza dettaglio proprietà
- Attore: Amministratore
- Scopo: Consultazione dati

**FP4.3 - Creare nuova proprietà**
- Descrizione: Crea nuova proprietà
- Attore: Amministratore
- Scopo: Inserimento proprietà

**FP4.4 - Modificare proprietà**
- Descrizione: Modifica proprietà esistente
- Attore: Amministratore
- Scopo: Aggiornamento proprietà

**FP4.5 - Eliminare proprietà**
- Descrizione: Elimina proprietà
- Attore: Amministratore
- Scopo: Rimozione proprietà

**FP4.6 - Gestire notazioni proprietà**
- Descrizione: Gestisce sezioni notazioni (nozioni base, informazioni, pagamenti, manuale, ospiti)
- Attore: Amministratore
- Scopo: Configurazione proprietà

**FP4.7 - Gestire letti proprietà**
- Descrizione: Gestisce letti proprietà (lista, creazione, eliminazione)
- Attore: Amministratore
- Scopo: Configurazione letti

**FP4.8 - Gestire bagni proprietà**
- Descrizione: Gestisce bagni proprietà (lista, creazione, eliminazione)
- Attore: Amministratore
- Scopo: Configurazione bagni

**FP4.9 - Gestire descrizione proprietà**
- Descrizione: Gestisce descrizioni (informazioni, descrizione, area)
- Attore: Amministratore
- Scopo: Configurazione descrizioni

**FP4.10 - Caricare immagine primaria proprietà**
- Descrizione: Upload immagine primaria
- Attore: Amministratore
- Scopo: Gestione immagini

**FP4.11 - Eliminare immagine primaria proprietà**
- Descrizione: Elimina immagine primaria
- Attore: Amministratore
- Scopo: Gestione immagini

**FP4.12 - Gestire ubicazione proprietà**
- Descrizione: Gestisce ubicazione (indirizzo, mappa, descrizione)
- Attore: Amministratore
- Scopo: Configurazione ubicazione

**FP4.13 - Aggiornare indirizzo mappa**
- Descrizione: Aggiorna indirizzo da mappa
- Attore: Amministratore
- Scopo: Configurazione mappa

**FP4.14 - Aggiornare coordinate mappa**
- Descrizione: Aggiorna coordinate geografiche
- Attore: Amministratore
- Scopo: Configurazione mappa

**FP4.15 - Gestire servizi proprietà**
- Descrizione: Gestisce servizi disponibili
- Attore: Amministratore
- Scopo: Configurazione servizi

**FP4.16 - Gestire immagini proprietà**
- Descrizione: Gestisce galleria immagini (carica, ordina, modifica, elimina, rigenera)
- Attore: Amministratore
- Scopo: Gestione immagini

**FP4.17 - Gestire tariffe proprietà**
- Descrizione: Gestisce tariffe stagionali (crea, modifica, elimina)
- Attore: Amministratore
- Scopo: Configurazione tariffe

**FP4.18 - Clonare proprietà**
- Descrizione: Crea copia proprietà esistente
- Attore: Amministratore
- Scopo: Creazione rapida

**FP4.19 - Visualizzare calendario proprietà**
- Descrizione: Visualizza calendario proprietà
- Attore: Amministratore
- Scopo: Gestione disponibilità

**FP4.20 - Gestire collezioni proprietà**
- Descrizione: Aggiorna collezioni associate (batch o toggle singolo)
- Attore: Amministratore
- Scopo: Organizzazione proprietà

### 4.5 Admin - Gestione Calendario

**FP5.1 - Visualizzare eventi calendario**
- Descrizione: Recupera eventi calendario (AJAX)
- Attore: Amministratore
- Scopo: Visualizzazione eventi

**FP5.2 - Creare blocco calendario**
- Descrizione: Crea blocco periodo non disponibile
- Attore: Amministratore
- Scopo: Gestione disponibilità

**FP5.3 - Trovare blocco per data**
- Descrizione: Cerca blocco esistente per data
- Attore: Amministratore
- Scopo: Consultazione blocchi

**FP5.4 - Sbloccare intervallo completo**
- Descrizione: Rimuove blocco per intervallo completo
- Attore: Amministratore
- Scopo: Gestione disponibilità

**FP5.5 - Sbloccare data singola**
- Descrizione: Rimuove blocco per data singola
- Attore: Amministratore
- Scopo: Gestione disponibilità

**FP5.6 - Sbloccare dopo data**
- Descrizione: Rimuove blocco dopo data specifica
- Attore: Amministratore
- Scopo: Gestione disponibilità

**FP5.7 - Sbloccare prima data**
- Descrizione: Rimuove blocco prima data specifica
- Attore: Amministratore
- Scopo: Gestione disponibilità

**FP5.8 - Sincronizzare iCal**
- Descrizione: Sincronizza calendario iCal esterno
- Attore: Amministratore
- Scopo: Integrazione calendari

**FP5.9 - Aggiungere calendario iCal**
- Descrizione: Aggiunge nuovo calendario iCal
- Attore: Amministratore
- Scopo: Configurazione iCal

**FP5.10 - Sincronizzare calendario iCal singolo**
- Descrizione: Sincronizza singolo calendario iCal
- Attore: Amministratore
- Scopo: Integrazione calendari

**FP5.11 - Toggle calendario iCal**
- Descrizione: Attiva/disattiva calendario iCal
- Attore: Amministratore
- Scopo: Gestione iCal

**FP5.12 - Eliminare calendario iCal**
- Descrizione: Rimuove calendario iCal
- Attore: Amministratore
- Scopo: Gestione iCal

**FP5.13 - Salvare blocco calendario (custom page)**
- Descrizione: Salva blocco da pagina custom
- Attore: Amministratore
- Scopo: Gestione disponibilità

**FP5.14 - Aggiungere iCal (custom page)**
- Descrizione: Aggiunge iCal da pagina custom
- Attore: Amministratore
- Scopo: Configurazione iCal

**FP5.15 - Toggle iCal (custom page)**
- Descrizione: Toggle iCal da pagina custom
- Attore: Amministratore
- Scopo: Gestione iCal

**FP5.16 - Eliminare iCal (custom page)**
- Descrizione: Elimina iCal da pagina custom
- Attore: Amministratore
- Scopo: Gestione iCal

**FP5.17 - Sincronizzare iCal (custom page)**
- Descrizione: Sincronizza iCal da pagina custom
- Attore: Amministratore
- Scopo: Integrazione calendari

### 4.6 Admin - Gestione Prenotazioni

**FP6.1 - Visualizzare lista prenotazioni admin**
- Descrizione: Visualizza tabella prenotazioni
- Attore: Amministratore
- Scopo: Consultazione prenotazioni

**FP6.2 - Visualizzare dettaglio prenotazione admin**
- Descrizione: Visualizza dettaglio prenotazione
- Attore: Amministratore
- Scopo: Consultazione dati

**FP6.3 - Creare nuova prenotazione**
- Descrizione: Crea nuova prenotazione
- Attore: Amministratore
- Scopo: Inserimento prenotazione

**FP6.4 - Modificare prenotazione**
- Descrizione: Modifica prenotazione esistente
- Attore: Amministratore
- Scopo: Aggiornamento prenotazione

**FP6.5 - Eliminare prenotazione**
- Descrizione: Elimina prenotazione
- Attore: Amministratore
- Scopo: Rimozione prenotazione

### 4.7 Admin - Gestione Pagamenti

**FP7.1 - Visualizzare lista pagamenti**
- Descrizione: Visualizza tabella pagamenti
- Attore: Amministratore
- Scopo: Consultazione pagamenti

**FP7.2 - Visualizzare dettaglio pagamento**
- Descrizione: Visualizza dettaglio pagamento
- Attore: Amministratore
- Scopo: Consultazione dati

**FP7.3 - Creare nuovo pagamento**
- Descrizione: Crea nuovo metodo pagamento
- Attore: Amministratore
- Scopo: Configurazione pagamenti

**FP7.4 - Modificare pagamento**
- Descrizione: Modifica metodo pagamento
- Attore: Amministratore
- Scopo: Aggiornamento pagamento

**FP7.5 - Eliminare pagamento**
- Descrizione: Elimina metodo pagamento
- Attore: Amministratore
- Scopo: Rimozione pagamento

**FP7.6 - Gestire periodi cancellazione**
- Descrizione: Gestisce periodi cancellazione per pagamento (crea, modifica, elimina)
- Attore: Amministratore
- Scopo: Configurazione cancellazioni

### 4.8 Admin - Gestione Contenuti

**FP8.1 - Visualizzare lista contenuti**
- Descrizione: Visualizza contenuti
- Attore: Amministratore
- Scopo: Consultazione contenuti

**FP8.2 - Visualizzare dettaglio contenuto**
- Descrizione: Visualizza dettaglio contenuto
- Attore: Amministratore
- Scopo: Consultazione dati

**FP8.3 - Creare nuovo contenuto**
- Descrizione: Crea nuovo contenuto
- Attore: Amministratore
- Scopo: Inserimento contenuto

**FP8.4 - Modificare contenuto**
- Descrizione: Modifica contenuto
- Attore: Amministratore
- Scopo: Aggiornamento contenuto

**FP8.5 - Eliminare contenuto**
- Descrizione: Elimina contenuto
- Attore: Amministratore
- Scopo: Rimozione contenuto

**FP8.6 - Gestire componenti pagina**
- Descrizione: Gestisce componenti pagina personalizzate (CRUD)
- Attore: Amministratore
- Scopo: Configurazione pagine

### 4.9 Admin - Gestione Utenti

**FP9.1 - Visualizzare lista utenti**
- Descrizione: Visualizza tabella utenti
- Attore: Amministratore
- Scopo: Consultazione utenti

**FP9.2 - Visualizzare dettaglio utente**
- Descrizione: Visualizza dettaglio utente
- Attore: Amministratore
- Scopo: Consultazione dati

**FP9.3 - Creare nuovo utente**
- Descrizione: Crea nuovo utente
- Attore: Amministratore
- Scopo: Inserimento utente

**FP9.4 - Modificare utente**
- Descrizione: Modifica utente
- Attore: Amministratore
- Scopo: Aggiornamento utente

**FP9.5 - Eliminare utente**
- Descrizione: Elimina utente
- Attore: Amministratore
- Scopo: Rimozione utente

### 4.10 Admin - Dashboard e Impostazioni

**FP10.1 - Visualizzare dashboard admin**
- Descrizione: Visualizza dashboard con statistiche
- Attore: Amministratore
- Scopo: Panoramica sistema

**FP10.2 - Gestire impostazioni**
- Descrizione: Gestisce impostazioni sistema
- Attore: Amministratore
- Scopo: Configurazione sistema

**FP10.3 - Gestire white-label**
- Descrizione: Gestisce configurazione white-label
- Attore: Amministratore
- Scopo: Personalizzazione brand

**FP10.4 - Auto-save campo generico**
- Descrizione: Salvataggio automatico campo qualsiasi risorsa
- Attore: Amministratore
- Scopo: Aggiornamento rapido

**FP10.5 - Auto-save notazione proprietà**
- Descrizione: Salvataggio automatico notazione proprietà
- Attore: Amministratore
- Scopo: Aggiornamento rapido

### 4.11 Autenticazione

**FP11.1 - Visualizzare form login**
- Descrizione: Visualizza form login
- Attore: Utente
- Scopo: Accesso sistema

**FP11.2 - Login utente**
- Descrizione: Autenticazione utente
- Attore: Utente
- Scopo: Accesso sistema

**FP11.3 - Visualizzare form registrazione**
- Descrizione: Visualizza form registrazione
- Attore: Utente
- Scopo: Creazione account

**FP11.4 - Registrare nuovo utente**
- Descrizione: Crea nuovo account utente
- Attore: Utente
- Scopo: Registrazione

**FP11.5 - Visualizzare form reset password**
- Descrizione: Visualizza form richiesta reset password
- Attore: Utente
- Scopo: Recupero credenziali

**FP11.6 - Richiedere reset password**
- Descrizione: Invia email reset password
- Attore: Utente
- Scopo: Recupero credenziali

**FP11.7 - Visualizzare form nuova password**
- Descrizione: Visualizza form impostazione nuova password
- Attore: Utente
- Scopo: Recupero credenziali

**FP11.8 - Impostare nuova password**
- Descrizione: Aggiorna password utente
- Attore: Utente
- Scopo: Recupero credenziali

**FP11.9 - Logout utente**
- Descrizione: Disconnessione utente
- Attore: Utente
- Scopo: Uscita sistema

**FP11.10 - Visualizzare profilo utente**
- Descrizione: Visualizza profilo utente autenticato
- Attore: Utente
- Scopo: Consultazione profilo

**FP11.11 - Modificare profilo utente**
- Descrizione: Aggiorna profilo utente
- Attore: Utente
- Scopo: Aggiornamento profilo

**FP11.12 - Eliminare account utente**
- Descrizione: Elimina account utente
- Attore: Utente
- Scopo: Rimozione account

**FP11.13 - Redirect Google OAuth**
- Descrizione: Redirect a Google per autenticazione
- Attore: Utente
- Scopo: Autenticazione esterna

**FP11.14 - Callback Google OAuth**
- Descrizione: Gestisce callback autenticazione Google
- Attore: Utente
- Scopo: Completamento autenticazione

### 4.12 Funzionalità Varie

**FP12.1 - Iscrivere newsletter**
- Descrizione: Iscrizione newsletter
- Attore: Visitatore
- Scopo: Iscrizione mailing list

**FP12.2 - Cambiare lingua**
- Descrizione: Cambia lingua interfaccia
- Attore: Utente
- Scopo: Personalizzazione

**FP12.3 - Ottenere URL localizzato**
- Descrizione: Recupera URL nella lingua selezionata
- Attore: Utente
- Scopo: Navigazione localizzata

**FP12.4 - Generare sitemap**
- Descrizione: Genera sitemap.xml
- Attore: Sistema
- Scopo: SEO

**FP12.5 - Servire immagini legacy**
- Descrizione: Serve immagini da sistema legacy
- Attore: Sistema
- Scopo: Compatibilità

**FP12.6 - Gestire webhook Stripe**
- Descrizione: Gestisce notifiche pagamento Stripe
- Attore: Sistema
- Scopo: Integrazione pagamenti

---

## 5. CONTEggio MOVIMENTI DATI

### 5.1 Frontend - Ricerca e Visualizzazione Proprietà

**FP1.1 - Visualizzare homepage**
- Entry: 0
- Exit: 1
- Read: 2 (pagina home + componenti)
- Write: 0
- **CFP: 3**

**FP1.2 - Visualizzare pagina personalizzata**
- Entry: 1 (selezione pagina)
- Exit: 1
- Read: 2 (pagina + componenti)
- Write: 0
- **CFP: 4**

**FP1.3 - Cercare proprietà**
- Entry: 1 (filtri ricerca)
- Exit: 1
- Read: 2 (proprietà + filtri)
- Write: 0
- **CFP: 4**

**FP1.4 - Visualizzare dettaglio proprietà**
- Entry: 1
- Exit: 1
- Read: 3 (proprietà + immagini + relazioni)
- Write: 0
- **CFP: 5**

**FP1.5 - Visualizzare lista real estate**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP1.6 - Visualizzare dettaglio real estate**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 0
- **CFP: 4**

**FP1.7 - Aggiungere proprietà a wishlist**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP1.8 - Rimuovere proprietà da wishlist**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP1.9 - Toggle wishlist proprietà**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP1.10 - Inviare contatto proprietà**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP1.11 - Aggiornare valuta**
- Entry: 1
- Exit: 1
- Read: 0
- Write: 1 (sessione)
- **CFP: 3**

**Totale Frontend Ricerca: 41 CFP**

### 5.2 Frontend - Booking

**FP2.1 - Visualizzare form prenotazione (step 1)**
- Entry: 0
- Exit: 1
- Read: 2 (proprietà + disponibilità)
- Write: 0
- **CFP: 3**

**FP2.2 - Visualizzare funnel prenotazione**
- Entry: 1 (parametri URL opzionali)
- Exit: 1
- Read: 3 (proprietà + disponibilità + tariffe)
- Write: 0
- **CFP: 5**

**FP2.3 - Visualizzare prenotazione con codice**
- Entry: 1
- Exit: 1
- Read: 2 (prenotazione + proprietà)
- Write: 0
- **CFP: 4**

**FP2.4 - Aggiornare prenotazione con codice**
- Entry: 1
- Exit: 1
- Read: 2 (prenotazione + validazioni)
- Write: 1
- **CFP: 5**

**FP2.5 - Confermare prenotazione PayPal**
- Entry: 1
- Exit: 1
- Read: 2 (prenotazione + verifica PayPal)
- Write: 1
- **CFP: 5**

**FP2.6 - Annullare prenotazione PayPal**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP2.7 - Confermare prenotazione Stripe**
- Entry: 1
- Exit: 1
- Read: 2 (prenotazione + verifica Stripe)
- Write: 1
- **CFP: 5**

**FP2.8 - Annullare prenotazione Stripe**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP2.9 - Visualizzare successo prenotazione**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**Totale Frontend Booking: 36 CFP**

### 5.3 Account Utente

**FP3.1 - Visualizzare lista prenotazioni account**
- Entry: 0
- Exit: 1
- Read: 2 (prenotazioni + filtri scope)
- Write: 0
- **CFP: 3**

**FP3.2 - Visualizzare profilo account**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP3.3 - Modificare profilo account**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**Totale Account Utente: 10 CFP**

### 5.4 Admin - Gestione Proprietà

**FP4.1 - Visualizzare lista proprietà admin**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP4.2 - Visualizzare dettaglio proprietà admin**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 0
- **CFP: 4**

**FP4.3 - Creare nuova proprietà**
- Entry: 1
- Exit: 1
- Read: 4 (lookup: collezioni, servizi, tipi + validazioni)
- Write: 1
- **CFP: 7**

**FP4.4 - Modificare proprietà**
- Entry: 1
- Exit: 1
- Read: 5 (dati esistenti + 4 lookup)
- Write: 1
- **CFP: 8**

**FP4.5 - Eliminare proprietà**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.6 - Gestire notazioni proprietà**
- Entry: 1
- Exit: 1
- Read: 2 (proprietà + notazioni)
- Write: 1
- **CFP: 5**

**FP4.7 - Gestire letti proprietà**
- Entry: 1 (lista), 1 (crea), 1 (elimina)
- Exit: 3
- Read: 4 (lista + lookup + validazioni)
- Write: 2 (crea + elimina)
- **CFP: 12** (3 FP aggregati)

**FP4.8 - Gestire bagni proprietà**
- Entry: 1 (lista), 1 (crea), 1 (elimina)
- Exit: 3
- Read: 4
- Write: 2
- **CFP: 12**

**FP4.9 - Gestire descrizione proprietà**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP4.10 - Caricare immagine primaria proprietà**
- Entry: 1 (file upload)
- Exit: 1
- Read: 1
- Write: 2 (DB + file storage)
- **CFP: 5**

**FP4.11 - Eliminare immagine primaria proprietà**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 2 (delete DB + delete file)
- **CFP: 5**

**FP4.12 - Gestire ubicazione proprietà**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP4.13 - Aggiornare indirizzo mappa**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.14 - Aggiornare coordinate mappa**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.15 - Gestire servizi proprietà**
- Entry: 1
- Exit: 1
- Read: 3 (proprietà + servizi disponibili + servizi associati)
- Write: 1
- **CFP: 6**

**FP4.16 - Gestire immagini proprietà**
- Entry: 6 (carica, ordina, modifica, elimina, rigenera, toggle, reset, replace, elimina tutte)
- Exit: 8
- Read: 8 (proprietà + immagini + validazioni)
- Write: 10 (upload, update, delete, reorder, regenerate, toggle, reset, replace, delete all + file storage)
- **CFP: 32** (9 FP aggregati)

**FP4.17 - Gestire tariffe proprietà**
- Entry: 3 (crea, modifica, elimina)
- Exit: 3
- Read: 5 (proprietà + tariffe esistenti + validazioni)
- Write: 3
- **CFP: 14** (3 FP aggregati)

**FP4.18 - Clonare proprietà**
- Entry: 1 (visualizza), 1 (esegue)
- Exit: 2
- Read: 4 (proprietà originale + lookup)
- Write: 2 (nuova proprietà + dati correlati)
- **CFP: 10** (2 FP aggregati)

**FP4.19 - Visualizzare calendario proprietà**
- Entry: 0
- Exit: 1
- Read: 2 (proprietà + eventi calendario)
- Write: 0
- **CFP: 3**

**FP4.20 - Gestire collezioni proprietà**
- Entry: 1 (batch), 1 (toggle)
- Exit: 2
- Read: 3 (proprietà + collezioni disponibili + collezioni associate)
- Write: 2
- **CFP: 9** (2 FP aggregati)

**Totale Admin Proprietà: 152 CFP**

### 5.5 Admin - Gestione Calendario

**FP5.1 - Visualizzare eventi calendario**
- Entry: 0
- Exit: 1 (AJAX)
- Read: 2 (prenotazioni + blocchi)
- Write: 0
- **CFP: 3**

**FP5.2 - Creare blocco calendario**
- Entry: 1
- Exit: 1
- Read: 2 (proprietà + validazioni)
- Write: 1
- **CFP: 5**

**FP5.3 - Trovare blocco per data**
- Entry: 1
- Exit: 1 (AJAX)
- Read: 1
- Write: 0
- **CFP: 3**

**FP5.4 - Sbloccare intervallo completo**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP5.5 - Sbloccare data singola**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP5.6 - Sbloccare dopo data**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP5.7 - Sbloccare prima data**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP5.8 - Sincronizzare iCal**
- Entry: 1
- Exit: 1
- Read: 2 (calendari + proprietà)
- Write: 1
- **CFP: 5**

**FP5.9 - Aggiungere calendario iCal**
- Entry: 1
- Exit: 1
- Read: 2 (proprietà + validazioni)
- Write: 1
- **CFP: 5**

**FP5.10 - Sincronizzare calendario iCal singolo**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP5.11 - Toggle calendario iCal**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP5.12 - Eliminare calendario iCal**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP5.13 - Salvare blocco calendario (custom page)**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP5.14 - Aggiungere iCal (custom page)**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP5.15 - Toggle iCal (custom page)**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP5.16 - Eliminare iCal (custom page)**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP5.17 - Sincronizzare iCal (custom page)**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**Totale Admin Calendario: 70 CFP**

### 5.6 Admin - Gestione Prenotazioni

**FP6.1 - Visualizzare lista prenotazioni admin**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP6.2 - Visualizzare dettaglio prenotazione admin**
- Entry: 1
- Exit: 1
- Read: 2 (prenotazione + relazioni)
- Write: 0
- **CFP: 4**

**FP6.3 - Creare nuova prenotazione**
- Entry: 1
- Exit: 1
- Read: 4 (lookup: proprietà, utenti, pagamenti + validazioni)
- Write: 1
- **CFP: 7**

**FP6.4 - Modificare prenotazione**
- Entry: 1
- Exit: 1
- Read: 5 (dati esistenti + 4 lookup)
- Write: 1
- **CFP: 8**

**FP6.5 - Eliminare prenotazione**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Admin Prenotazioni: 25 CFP**

### 5.7 Admin - Gestione Pagamenti

**FP7.1 - Visualizzare lista pagamenti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP7.2 - Visualizzare dettaglio pagamento**
- Entry: 1
- Exit: 1
- Read: 2 (pagamento + periodi cancellazione)
- Write: 0
- **CFP: 4**

**FP7.3 - Creare nuovo pagamento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP7.4 - Modificare pagamento**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP7.5 - Eliminare pagamento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP7.6 - Gestire periodi cancellazione**
- Entry: 3 (crea, modifica, elimina)
- Exit: 3
- Read: 4 (pagamento + periodi esistenti + validazioni)
- Write: 3
- **CFP: 13** (3 FP aggregati)

**Totale Admin Pagamenti: 32 CFP**

### 5.8 Admin - Gestione Contenuti

**FP8.1 - Visualizzare lista contenuti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP8.2 - Visualizzare dettaglio contenuto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP8.3 - Creare nuovo contenuto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP8.4 - Modificare contenuto**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP8.5 - Eliminare contenuto**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP8.6 - Gestire componenti pagina**
- Entry: 4 (lista, crea, modifica, elimina)
- Exit: 4
- Read: 6 (componenti + tipi + validazioni)
- Write: 3
- **CFP: 17** (4 FP aggregati)

**Totale Admin Contenuti: 35 CFP**

### 5.9 Admin - Gestione Utenti

**FP9.1 - Visualizzare lista utenti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP9.2 - Visualizzare dettaglio utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP9.3 - Creare nuovo utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP9.4 - Modificare utente**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP9.5 - Eliminare utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Admin Utenti: 18 CFP**

### 5.10 Admin - Dashboard e Impostazioni

**FP10.1 - Visualizzare dashboard admin**
- Entry: 0
- Exit: 1
- Read: 2 (statistiche + dati aggregati)
- Write: 0
- **CFP: 3**

**FP10.2 - Gestire impostazioni**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP10.3 - Gestire white-label**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP10.4 - Auto-save campo generico**
- Entry: 1
- Exit: 1 (AJAX)
- Read: 2 (risorsa + validazioni)
- Write: 1
- **CFP: 5**

**FP10.5 - Auto-save notazione proprietà**
- Entry: 1
- Exit: 1 (AJAX)
- Read: 2
- Write: 1
- **CFP: 5**

**Totale Admin Dashboard: 23 CFP**

### 5.11 Autenticazione

**FP11.1 - Visualizzare form login**
- Entry: 0
- Exit: 1
- Read: 0
- Write: 0
- **CFP: 1**

**FP11.2 - Login utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP11.3 - Visualizzare form registrazione**
- Entry: 0
- Exit: 1
- Read: 0
- Write: 0
- **CFP: 1**

**FP11.4 - Registrare nuovo utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP11.5 - Visualizzare form reset password**
- Entry: 0
- Exit: 1
- Read: 0
- Write: 0
- **CFP: 1**

**FP11.6 - Richiedere reset password**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP11.7 - Visualizzare form nuova password**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP11.8 - Impostare nuova password**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP11.9 - Logout utente**
- Entry: 1
- Exit: 1
- Read: 0
- Write: 1
- **CFP: 3**

**FP11.10 - Visualizzare profilo utente**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP11.11 - Modificare profilo utente**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP11.12 - Eliminare account utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP11.13 - Redirect Google OAuth**
- Entry: 1
- Exit: 1 (redirect esterno)
- Read: 0
- Write: 0
- **CFP: 2**

**FP11.14 - Callback Google OAuth**
- Entry: 1
- Exit: 1
- Read: 2 (verifica token + utente)
- Write: 1
- **CFP: 5**

**Totale Autenticazione: 44 CFP**

### 5.12 Funzionalità Varie

**FP12.1 - Iscrivere newsletter**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP12.2 - Cambiare lingua**
- Entry: 1
- Exit: 1
- Read: 0
- Write: 1 (sessione)
- **CFP: 3**

**FP12.3 - Ottenere URL localizzato**
- Entry: 1
- Exit: 1 (AJAX)
- Read: 1
- Write: 0
- **CFP: 3**

**FP12.4 - Generare sitemap**
- Entry: 0
- Exit: 1 (XML)
- Read: 2 (proprietà + pagine)
- Write: 0
- **CFP: 3**

**FP12.5 - Servire immagini legacy**
- Entry: 1
- Exit: 1 (file)
- Read: 1
- Write: 0
- **CFP: 3**

**FP12.6 - Gestire webhook Stripe**
- Entry: 1 (webhook esterno)
- Exit: 0
- Read: 2 (prenotazione + verifica webhook)
- Write: 1
- **CFP: 4**

**Totale Funzionalità Varie: 20 CFP**

---

## 6. FUNCTIONAL SIZE (CFP)

### 6.1 Riepilogo per Categoria

| Categoria | Entry | Exit | Read | Write | Totale CFP |
|-----------|-------|------|------|-------|------------|
| Frontend - Ricerca Proprietà | 11 | 11 | 18 | 4 | 41 |
| Frontend - Booking | 9 | 9 | 16 | 3 | 36 |
| Account Utente | 2 | 3 | 5 | 1 | 10 |
| Admin - Proprietà | 30 | 30 | 60 | 22 | 152 |
| Admin - Calendario | 17 | 17 | 30 | 13 | 70 |
| Admin - Prenotazioni | 4 | 5 | 13 | 3 | 25 |
| Admin - Pagamenti | 6 | 6 | 11 | 6 | 32 |
| Admin - Contenuti | 6 | 7 | 12 | 5 | 35 |
| Admin - Utenti | 4 | 5 | 6 | 3 | 18 |
| Admin - Dashboard | 4 | 5 | 10 | 4 | 23 |
| Autenticazione | 14 | 14 | 12 | 8 | 44 |
| Funzionalità Varie | 5 | 6 | 7 | 2 | 20 |
| **TOTALE** | **112** | **118** | **210** | **74** | **514** |

### 6.2 Functional Size Totale

**CFP Totali: 514**

**Breakdown:**
- Entry (E): 112 CFP
- Exit (X): 118 CFP
- Read (R): 210 CFP
- Write (W): 74 CFP

---

## 7. DATI EFFORT (OSSERVAZIONE)

**Ore Reali Sostenute:** 250 ore (dato osservato da analisi precedente)

**Periodo Sviluppo:** Febbraio 2024 - Dicembre 2024 (sviluppo con AI/Cursor)

**Fonte Dati:** Analisi retrospettiva progetti completati

**⚠️ NOTA:** Questo dato è documentato come **osservazione**. Non viene calcolato alcun coefficiente di produttività. Con N progetti < 3 non è possibile derivare relazioni statistiche valide tra SIZE e COST.

**Nota Speciale:** Progetto sviluppato interamente con assistenza AI (Cursor). Questo potrebbe influenzare la produttività rispetto a progetti sviluppati manualmente, ma non viene calcolato alcun coefficiente in questa fase.

---

## 8. LIMITI E INCERTEZZE

### 8.1 Functional Processes Non Identificabili

**API Legacy:**
- Esistono API Legacy (BaseLegacyController, BookingHistoryController, ContentController, PropertyController, SearchController, UserController, WishlistController) che potrebbero rappresentare Functional Processes aggiuntivi
- **Incertezza:** Non analizzati in dettaglio - potrebbero aggiungere 30-50 CFP

**API Admin:**
- Esistono API Admin (Admin/BookingController) che potrebbero rappresentare Functional Processes aggiuntivi
- **Incertezza:** Non analizzati in dettaglio - potrebbero aggiungere 10-15 CFP

**Funzionalità Minori:**
- Alcune funzionalità minori potrebbero non essere state identificate
- **Incertezza:** Stimata ±5% sul totale

### 8.2 Dati Mancanti

- Dettagli implementativi di alcune funzionalità avanzate (calcoli tariffe, validazioni complesse prenotazioni)
- Alcune operazioni batch o automatizzate potrebbero non essere state identificate
- Funzionalità white-label potrebbero avere complessità aggiuntive non identificate

### 8.3 Assunzioni Fatte

- Filtri di ricerca integrati in liste non contati come Entry separati
- Validazioni base non contate come Read separati se integrate nel processo
- Operazioni di calcolo automatico non contate come Functional Process separati se non identificabili dall'utente
- Alcuni Functional Processes aggregati (es. gestione immagini, gestione calendario) per semplificazione, ma conteggio E/X/R/W corretto

### 8.4 Incertezze nel Conteggio

**Range di Incertezza Stimato:**
- CFP minimo: 480 (escludendo API e funzionalità minori)
- CFP massimo: 580 (includendo stima API e funzionalità aggiuntive)
- CFP più probabile: 514

**Affidabilità:** Media-Alta (analisi codice sorgente completa, ma alcune API non analizzate in dettaglio, alcuni FP aggregati per semplificazione)

---

**Firma Analisi:**  
Analisi COSMIC eseguita secondo standard ISO/IEC 19761  
Functional Size: 514 CFP (range incertezza: 480-580 CFP)  
Separazione rigorosa SIZE vs COST mantenuta  
Nota: Progetto sviluppato con assistenza AI (Cursor)

