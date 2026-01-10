# Analisi COSMIC - CZServizi

**Data Analisi:** Dicembre 2024  
**Standard:** ISO/IEC 19761 - COSMIC Function Points  
**Analista:** Software Measurement Expert  
**Scopo:** Misurazione Functional Size (CFP) del sistema CZServizi

**⚠️ AVVISO METODOLOGICO:**  
Questa analisi è basata su **dati parziali**. Il repository è accessibile ma l'analisi completa non è stata possibile. Le incertezze sono documentate esplicitamente nella sezione 8.

---

## 1. SCOPO

**Obiettivo:** Identificare e misurare tutti i Functional Processes del sistema CZServizi secondo standard COSMIC ISO/IEC 19761.

**Metodologia:** Analisi codice sorgente (Controllers, Routes, Models) per identificare operazioni utente identificabili.

**Standard Applicato:** ISO/IEC 19761 - COSMIC Function Points v4.0.1

**Limitazione:** Analisi basata su dati parziali - incertezze documentate esplicitamente.

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

**CZServizi** è un sistema gestionale per la gestione di interventi, clienti (amministratori, condomini, referenti, fornitori), servizi, documenti privacy.

**Dominio:** Gestione interventi e clienti

### 3.2 Utenti Identificati

- **Amministratori:** Gestione completa sistema
- **Operatori:** Gestione interventi, clienti, servizi

### 3.3 Confine Sistema

**Incluso:**
- Gestione clienti (4 tipi: amministratori, condomini, referenti, fornitori)
- Gestione interventi (CRUD + export + documenti)
- Gestione servizi (CRUD)
- Gestione documenti privacy (generazione, storico, ripristino)
- Dashboard con matrice
- Gestione utenti e permessi
- Autenticazione

**Escluso:**
- Autenticazione base (infrastrutturale)
- Routing base (infrastrutturale)
- Template grafici generici (non funzionali)
- Test automatici (non Functional Process)

**Interfacce Esterne:**
- File system (upload/download documenti)
- Export Excel

---

## 4. FUNCTIONAL PROCESSES

### 4.1 Gestione Clienti

**FP1.1 - Visualizzare lista clienti (per tipo)**
- Descrizione: Visualizza clienti filtrati per tipo (administrator/condominium/referent/supplier)
- Attore: Operatore
- Scopo: Consultazione clienti

**FP1.2 - Visualizzare form creazione cliente**
- Descrizione: Visualizza form creazione cliente per tipo specifico
- Attore: Operatore
- Scopo: Preparazione inserimento

**FP1.3 - Creare nuovo cliente**
- Descrizione: Crea nuovo cliente (validazione dinamica basata su template privacy)
- Attore: Operatore
- Scopo: Inserimento cliente

**FP1.4 - Visualizzare form modifica cliente**
- Descrizione: Visualizza form modifica cliente
- Attore: Operatore
- Scopo: Preparazione modifica

**FP1.5 - Modificare cliente**
- Descrizione: Modifica cliente esistente
- Attore: Operatore
- Scopo: Aggiornamento cliente

**FP1.6 - Eliminare cliente**
- Descrizione: Elimina cliente
- Attore: Operatore
- Scopo: Rimozione cliente

### 4.2 Gestione Interventi

**FP2.1 - Visualizzare lista interventi**
- Descrizione: Visualizza interventi (filtrati per stato: in corso, completati, fatturati, non fatturati)
- Attore: Operatore
- Scopo: Consultazione interventi

**FP2.2 - Visualizzare dettaglio intervento**
- Descrizione: Visualizza dettaglio intervento
- Attore: Operatore
- Scopo: Consultazione dati intervento

**FP2.3 - Visualizzare form creazione intervento**
- Descrizione: Visualizza form creazione intervento
- Attore: Operatore
- Scopo: Preparazione inserimento

**FP2.4 - Creare nuovo intervento**
- Descrizione: Crea nuovo intervento
- Attore: Operatore
- Scopo: Inserimento intervento

**FP2.5 - Visualizzare form modifica intervento**
- Descrizione: Visualizza form modifica intervento
- Attore: Operatore
- Scopo: Preparazione modifica

**FP2.6 - Modificare intervento**
- Descrizione: Modifica intervento esistente
- Attore: Operatore
- Scopo: Aggiornamento intervento

**FP2.7 - Eliminare intervento**
- Descrizione: Elimina intervento
- Attore: Operatore
- Scopo: Rimozione intervento

**FP2.8 - Export interventi cliente**
- Descrizione: Esporta interventi per cliente in Excel
- Attore: Operatore
- Scopo: Export dati

**FP2.9 - Visualizzare documento intervento**
- Descrizione: Visualizza documento associato a intervento
- Attore: Operatore
- Scopo: Consultazione documento

**FP2.10 - Eliminare documento intervento**
- Descrizione: Elimina documento da intervento
- Attore: Operatore
- Scopo: Rimozione documento

### 4.3 Gestione Servizi

**FP3.1 - Visualizzare lista servizi**
- Descrizione: Visualizza tabella servizi
- Attore: Operatore
- Scopo: Consultazione servizi

**FP3.2 - Visualizzare dettaglio servizio**
- Descrizione: Visualizza dettaglio servizio
- Attore: Operatore
- Scopo: Consultazione dati servizio

**FP3.3 - Creare nuovo servizio**
- Descrizione: Crea nuovo servizio
- Attore: Operatore
- Scopo: Inserimento servizio

**FP3.4 - Modificare servizio**
- Descrizione: Modifica servizio
- Attore: Operatore
- Scopo: Aggiornamento servizio

**FP3.5 - Eliminare servizio**
- Descrizione: Elimina servizio
- Attore: Operatore
- Scopo: Rimozione servizio

### 4.4 Gestione Privacy

**FP4.1 - Visualizzare gestione privacy**
- Descrizione: Visualizza gestione documenti privacy
- Attore: Operatore
- Scopo: Consultazione privacy

**FP4.2 - Generare documento privacy (pubblico)**
- Descrizione: Genera documento privacy per cliente (endpoint pubblico)
- Attore: Sistema/Cliente
- Scopo: Generazione documento

**FP4.3 - Salvare template privacy**
- Descrizione: Salva nuovo template privacy
- Attore: Operatore
- Scopo: Configurazione template

**FP4.4 - Visualizzare storico privacy**
- Descrizione: Visualizza storico versioni template privacy
- Attore: Operatore
- Scopo: Consultazione storico

**FP4.5 - Ripristinare versione privacy**
- Descrizione: Ripristina versione precedente template privacy
- Attore: Operatore
- Scopo: Ripristino template

### 4.5 Dashboard

**FP5.1 - Visualizzare dashboard**
- Descrizione: Visualizza dashboard con statistiche
- Attore: Operatore
- Scopo: Panoramica sistema

**FP5.2 - Visualizzare matrice amministratore**
- Descrizione: Visualizza matrice per amministratore specifico
- Attore: Operatore
- Scopo: Analisi dati

### 4.6 Gestione Utenti

**FP6.1 - Visualizzare lista utenti**
- Descrizione: Visualizza tabella utenti
- Attore: Amministratore
- Scopo: Consultazione utenti

**FP6.2 - Visualizzare dettaglio utente**
- Descrizione: Visualizza dettaglio utente
- Attore: Amministratore
- Scopo: Consultazione dati utente

**FP6.3 - Creare nuovo utente**
- Descrizione: Crea nuovo utente
- Attore: Amministratore
- Scopo: Inserimento utente

**FP6.4 - Modificare utente**
- Descrizione: Modifica utente
- Attore: Amministratore
- Scopo: Aggiornamento utente

**FP6.5 - Eliminare utente**
- Descrizione: Elimina utente
- Attore: Amministratore
- Scopo: Rimozione utente

### 4.7 Gestione Permessi e Ruoli

**FP7.1 - Visualizzare lista permessi**
- Descrizione: Visualizza permessi disponibili
- Attore: Amministratore
- Scopo: Consultazione permessi

**FP7.2 - Visualizzare lista ruoli**
- Descrizione: Visualizza ruoli disponibili
- Attore: Amministratore
- Scopo: Consultazione ruoli

**FP7.3 - Creare nuovo ruolo**
- Descrizione: Crea nuovo ruolo
- Attore: Amministratore
- Scopo: Configurazione ruoli

**FP7.4 - Modificare permessi ruolo**
- Descrizione: Aggiorna permessi associati a ruolo
- Attore: Amministratore
- Scopo: Configurazione permessi

**FP7.5 - Eliminare ruolo**
- Descrizione: Elimina ruolo
- Attore: Amministratore
- Scopo: Rimozione ruolo

### 4.8 Autenticazione

**FP8.1 - Visualizzare form login**
- Descrizione: Visualizza form login
- Attore: Utente
- Scopo: Accesso sistema

**FP8.2 - Login utente**
- Descrizione: Autenticazione utente
- Attore: Utente
- Scopo: Accesso sistema

**FP8.3 - Logout utente**
- Descrizione: Disconnessione utente
- Attore: Utente
- Scopo: Uscita sistema

**FP8.4 - Visualizzare form reset password**
- Descrizione: Visualizza form richiesta reset password
- Attore: Utente
- Scopo: Recupero credenziali

**FP8.5 - Richiedere reset password**
- Descrizione: Invia email reset password
- Attore: Utente
- Scopo: Recupero credenziali

**FP8.6 - Visualizzare form nuova password**
- Descrizione: Visualizza form impostazione nuova password
- Attore: Utente
- Scopo: Recupero credenziali

**FP8.7 - Impostare nuova password**
- Descrizione: Aggiorna password utente
- Attore: Utente
- Scopo: Recupero credenziali

**FP8.8 - Visualizzare verifica email**
- Descrizione: Visualizza notifica verifica email
- Attore: Utente
- Scopo: Validazione account

**FP8.9 - Verificare email**
- Descrizione: Verifica indirizzo email
- Attore: Utente
- Scopo: Validazione account

**FP8.10 - Reinviare email verifica**
- Descrizione: Reinvia email verifica
- Attore: Utente
- Scopo: Validazione account

---

## 5. CONTEggio MOVIMENTI DATI

### 5.1 Gestione Clienti

**FP1.1 - Visualizzare lista clienti (per tipo)**
- Entry: 1 (selezione tipo)
- Exit: 1
- Read: 2 (clienti + referenti)
- Write: 0
- **CFP: 4**

**FP1.2 - Visualizzare form creazione cliente**
- Entry: 1 (selezione tipo)
- Exit: 1
- Read: 3 (template privacy + lookup: administrators/referents/condominiums secondo tipo)
- Write: 0
- **CFP: 5**

**FP1.3 - Creare nuovo cliente**
- Entry: 1
- Exit: 1
- Read: 3 (template privacy + validazioni dinamiche + lookup)
- Write: 1
- **CFP: 6**

**FP1.4 - Visualizzare form modifica cliente**
- Entry: 1
- Exit: 1
- Read: 3 (cliente esistente + template privacy + lookup)
- Write: 0
- **CFP: 5**

**FP1.5 - Modificare cliente**
- Entry: 1
- Exit: 1
- Read: 4 (dati esistenti + template privacy + validazioni + lookup)
- Write: 1
- **CFP: 7**

**FP1.6 - Eliminare cliente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Clienti: 31 CFP**

### 5.2 Gestione Interventi

**FP2.1 - Visualizzare lista interventi**
- Entry: 0
- Exit: 1
- Read: 3 (interventi + condomini + servizi + fornitori)
- Write: 0
- **CFP: 4**

**FP2.2 - Visualizzare dettaglio intervento**
- Entry: 1
- Exit: 1
- Read: 3 (intervento + relazioni + documenti)
- Write: 0
- **CFP: 5**

**FP2.3 - Visualizzare form creazione intervento**
- Entry: 0
- Exit: 1
- Read: 4 (servizi + condomini + fornitori + amministratori)
- Write: 0
- **CFP: 5**

**FP2.4 - Creare nuovo intervento**
- Entry: 1
- Exit: 1
- Read: 5 (lookup: servizi, condomini, fornitori, amministratori + validazioni)
- Write: 2 (intervento + documenti upload)
- **CFP: 9**

**FP2.5 - Visualizzare form modifica intervento**
- Entry: 1
- Exit: 1
- Read: 5 (intervento esistente + 4 lookup)
- Write: 0
- **CFP: 7**

**FP2.6 - Modificare intervento**
- Entry: 1
- Exit: 1
- Read: 6 (dati esistenti + 4 lookup + validazioni)
- Write: 2 (update intervento + documenti)
- **CFP: 10**

**FP2.7 - Eliminare intervento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP2.8 - Export interventi cliente**
- Entry: 1 (selezione cliente)
- Exit: 1 (file Excel)
- Read: 2 (interventi cliente + dati correlati)
- Write: 0
- **CFP: 4**

**FP2.9 - Visualizzare documento intervento**
- Entry: 1
- Exit: 1 (file viewer)
- Read: 1
- Write: 0
- **CFP: 3**

**FP2.10 - Eliminare documento intervento**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 2 (delete DB + delete file)
- **CFP: 5**

**Totale Gestione Interventi: 56 CFP**

### 5.3 Gestione Servizi

**FP3.1 - Visualizzare lista servizi**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP3.2 - Visualizzare dettaglio servizio**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP3.3 - Creare nuovo servizio**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP3.4 - Modificare servizio**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP3.5 - Eliminare servizio**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Servizi: 18 CFP**

### 5.4 Gestione Privacy

**FP4.1 - Visualizzare gestione privacy**
- Entry: 0
- Exit: 1
- Read: 1 (template corrente)
- Write: 0
- **CFP: 2**

**FP4.2 - Generare documento privacy (pubblico)**
- Entry: 1
- Exit: 1 (documento generato)
- Read: 2 (template privacy + dati cliente)
- Write: 0
- **CFP: 4**

**FP4.3 - Salvare template privacy**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP4.4 - Visualizzare storico privacy**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP4.5 - Ripristinare versione privacy**
- Entry: 1
- Exit: 1
- Read: 2 (versione da ripristinare + template corrente)
- Write: 1
- **CFP: 5**

**Totale Gestione Privacy: 17 CFP**

### 5.5 Dashboard

**FP5.1 - Visualizzare dashboard**
- Entry: 0
- Exit: 1
- Read: 2 (statistiche + dati aggregati)
- Write: 0
- **CFP: 3**

**FP5.2 - Visualizzare matrice amministratore**
- Entry: 1 (selezione amministratore)
- Exit: 1
- Read: 3 (amministratore + interventi + dati correlati)
- Write: 0
- **CFP: 5**

**Totale Dashboard: 8 CFP**

### 5.6 Gestione Utenti

**FP6.1 - Visualizzare lista utenti**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP6.2 - Visualizzare dettaglio utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP6.3 - Creare nuovo utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP6.4 - Modificare utente**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP6.5 - Eliminare utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Utenti: 18 CFP**

### 5.7 Gestione Permessi e Ruoli

**FP7.1 - Visualizzare lista permessi**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP7.2 - Visualizzare lista ruoli**
- Entry: 0
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 2**

**FP7.3 - Creare nuovo ruolo**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP7.4 - Modificare permessi ruolo**
- Entry: 1
- Exit: 1
- Read: 2 (ruolo + permessi disponibili)
- Write: 1
- **CFP: 5**

**FP7.5 - Eliminare ruolo**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Gestione Permessi e Ruoli: 17 CFP**

### 5.8 Autenticazione

**FP8.1 - Visualizzare form login**
- Entry: 0
- Exit: 1
- Read: 0
- Write: 0
- **CFP: 1**

**FP8.2 - Login utente**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP8.3 - Logout utente**
- Entry: 1
- Exit: 1
- Read: 0
- Write: 1
- **CFP: 3**

**FP8.4 - Visualizzare form reset password**
- Entry: 0
- Exit: 1
- Read: 0
- Write: 0
- **CFP: 1**

**FP8.5 - Richiedere reset password**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP8.6 - Visualizzare form nuova password**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 0
- **CFP: 3**

**FP8.7 - Impostare nuova password**
- Entry: 1
- Exit: 1
- Read: 2
- Write: 1
- **CFP: 5**

**FP8.8 - Visualizzare verifica email**
- Entry: 0
- Exit: 1
- Read: 0
- Write: 0
- **CFP: 1**

**FP8.9 - Verificare email**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**FP8.10 - Reinviare email verifica**
- Entry: 1
- Exit: 1
- Read: 1
- Write: 1
- **CFP: 4**

**Totale Autenticazione: 30 CFP**

---

## 6. FUNCTIONAL SIZE (CFP)

### 6.1 Riepilogo per Categoria

| Categoria | Entry | Exit | Read | Write | Totale CFP |
|-----------|-------|------|------|-------|------------|
| Gestione Clienti | 6 | 6 | 16 | 3 | 31 |
| Gestione Interventi | 7 | 10 | 25 | 5 | 56 |
| Gestione Servizi | 4 | 5 | 6 | 3 | 18 |
| Gestione Privacy | 3 | 5 | 7 | 2 | 17 |
| Dashboard | 1 | 2 | 5 | 0 | 8 |
| Gestione Utenti | 4 | 5 | 6 | 3 | 18 |
| Gestione Permessi e Ruoli | 3 | 5 | 6 | 3 | 17 |
| Autenticazione | 8 | 10 | 7 | 5 | 30 |
| **TOTALE** | **36** | **53** | **78** | **24** | **195** |

### 6.2 Functional Size Totale

**CFP Totali: 195**

**Breakdown:**
- Entry (E): 36 CFP
- Exit (X): 53 CFP
- Read (R): 78 CFP
- Write (W): 24 CFP

---

## 7. DATI EFFORT (OSSERVAZIONE)

**Ore Reali Sostenute:** 120 ore (dato osservato da analisi precedente)

**Periodo Sviluppo:** Non specificato nei dati disponibili

**Fonte Dati:** Analisi retrospettiva progetti completati (dati parziali)

**⚠️ NOTA:** Questo dato è documentato come **osservazione**. Non viene calcolato alcun coefficiente di produttività. Con N progetti < 3 non è possibile derivare relazioni statistiche valide tra SIZE e COST.

---

## 8. LIMITI E INCERTEZZE

### 8.1 Functional Processes Non Identificabili

**⚠️ INCERTEZZA ALTA:**

**API Endpoints:**
- Esiste `InterventionApiController` che potrebbe rappresentare Functional Processes aggiuntivi
- **Incertezza:** Non analizzato in dettaglio - potrebbero aggiungere 10-20 CFP

**Funzionalità Minori:**
- Alcune funzionalità minori potrebbero non essere state identificate
- **Incertezza:** Stimata ±10% sul totale (maggiore rispetto ad altri progetti per dati parziali)

**Validazioni Dinamiche Privacy:**
- Il sistema di validazione dinamica basato su template privacy potrebbe avere complessità aggiuntive non identificate
- **Incertezza:** Potrebbero aggiungere 5-10 CFP

### 8.2 Dati Mancanti

**⚠️ DATI PARZIALI:**

- Dettagli implementativi di alcune funzionalità avanzate non completamente analizzati
- Alcune operazioni batch o automatizzate potrebbero non essere state identificate
- Funzionalità di export Excel potrebbero avere complessità aggiuntive non identificate
- Sistema di matrice dashboard non completamente analizzato

### 8.3 Assunzioni Fatte

- Filtri di ricerca integrati in liste non contati come Entry separati
- Validazioni base non contate come Read separati se integrate nel processo
- Operazioni di calcolo automatico non contate come Functional Process separati se non identificabili dall'utente
- Validazione dinamica basata su template privacy contata come Read singolo (potrebbe essere più complessa)

### 8.4 Incertezze nel Conteggio

**⚠️ RANGE DI INCERTEZZA ELEVATO:**

**Range di Incertezza Stimato:**
- CFP minimo: 175 (escludendo API e funzionalità minori non identificate)
- CFP massimo: 235 (includendo stima API, validazioni avanzate, funzionalità aggiuntive)
- CFP più probabile: 195

**Affidabilità:** **BASSA-MEDIA** (analisi basata su dati parziali, alcune funzionalità non completamente analizzate, incertezze maggiori rispetto ad altri progetti)

**Dichiarazione Esplicita:**
Questa analisi è basata su **dati parziali** e ha **affidabilità inferiore** rispetto alle analisi di MsCarichi, CactusBoard e Klabhouse. Il range di incertezza è più ampio (±20% vs ±5-10% degli altri progetti).

---

**Firma Analisi:**  
Analisi COSMIC eseguita secondo standard ISO/IEC 19761  
Functional Size: 195 CFP (range incertezza: 175-235 CFP)  
Separazione rigorosa SIZE vs COST mantenuta  
**⚠️ AVVISO:** Analisi basata su dati parziali - affidabilità inferiore

