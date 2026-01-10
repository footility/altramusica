# Metodologia COSMIC - Definizione Formale

**Standard:** ISO/IEC 19761 - COSMIC Function Points  
**Versione:** 4.0.1 (ultima versione standard)  
**Data:** Dicembre 2024  
**Scopo:** Definizione formale del metodo COSMIC applicato

---

## 1. CONCETTI COSMIC UFFICIALI

### 1.1 Functional Process

**Definizione:** Unità funzionale identificabile dall'utente come operazione completa e coerente che trasforma dati in input in dati in output.

**Criteri Identificazione:**
- Operazione identificabile dall'utente (non tecnica)
- Trasformazione dati completa (non parziale)
- Coerente e autonoma (non frammentata)
- Rilevante per il dominio applicativo

**Esempi:**
- ✅ "Creare nuovo cliente" (Functional Process)
- ✅ "Visualizzare lista ordini" (Functional Process)
- ❌ "Validare campo email" (non è FP, è parte di un FP)
- ❌ "Query database" (non è FP, è implementazione tecnica)

### 1.2 Movimenti Dati (Data Movements)

**Entry (E):** Movimento dati da utente esterno verso sistema
- Input utente (form, comando, file upload)
- Ogni Entry conta 1 CFP

**Exit (X):** Movimento dati da sistema verso utente esterno
- Output utente (schermata, report, export, messaggio)
- Ogni Exit conta 1 CFP

**Read (R):** Movimento dati da storage persistente verso sistema
- Lettura da database, file, storage esterno
- Ogni Read conta 1 CFP

**Write (W):** Movimento dati da sistema verso storage persistente
- Scrittura su database, file, storage esterno
- Ogni Write conta 1 CFP

### 1.3 Functional Size (CFP)

**Definizione:** Somma di tutti i movimenti dati identificati

**Formula:**
```
CFP = E + X + R + W
```

Dove:
- E = numero totale di Entry
- X = numero totale di Exit
- R = numero totale di Read
- W = numero totale di Write

---

## 2. REGOLE APPLICATE

### 2.1 Conteggio Movimenti Dati

**Regola Base:**
- Ogni movimento dati conta esattamente 1 CFP
- Nessuna ponderazione per complessità
- Nessuna normalizzazione
- Nessun fattore correttivo

**Criteri Conteggio:**
- Un Functional Process può avere multipli movimenti dati
- Movimenti dati multipli per stessa entità contano separatamente
- Movimenti dati per validazione contano se sono movimenti funzionali distinti

### 2.2 Identificazione Functional Process

**Criteri:**
1. Operazione identificabile dall'utente
2. Trasformazione dati completa
3. Coerente e autonoma
4. Rilevante per il dominio

**Esempi CRUD:**
- **Create:** Functional Process "Creare [Entità]"
  - Entry: Form input
  - Read: Validazioni, lookup
  - Write: Insert database
  - Exit: Conferma creazione

- **Read/List:** Functional Process "Visualizzare lista [Entità]"
  - Entry: Filtri ricerca (se presenti)
  - Read: Query lista
  - Exit: Tabella/lista visualizzata

- **Read/Detail:** Functional Process "Visualizzare dettaglio [Entità]"
  - Entry: Selezione elemento
  - Read: Query dettaglio
  - Exit: Dettaglio visualizzato

- **Update:** Functional Process "Modificare [Entità]"
  - Entry: Form modifica
  - Read: Caricamento dati esistenti, validazioni
  - Write: Update database
  - Exit: Conferma modifica

- **Delete:** Functional Process "Eliminare [Entità]"
  - Entry: Conferma eliminazione
  - Read: Verifica dipendenze
  - Write: Delete database
  - Exit: Conferma eliminazione

### 2.3 Movimenti Dati Non Contati

**Non contati come movimenti dati COSMIC:**
- Codice infrastrutturale (autenticazione base, routing base, middleware generico)
- Template grafici generici (layout, componenti UI riutilizzabili)
- Configurazioni (file config, environment)
- Test automatici (non sono Functional Process)
- Logging tecnico (non è output funzionale)
- Cache (non è storage persistente funzionale)

**Criterio:** Se non è identificabile dall'utente come operazione funzionale, non conta.

---

## 3. METODO DI APPLICAZIONE

### 3.1 Fase 1: Identificazione Confine Sistema

**Definire:**
- Cosa è incluso nel sistema
- Cosa è escluso
- Interfacce esterne
- Utenti identificati

### 3.2 Fase 2: Identificazione Functional Processes

**Procedura:**
1. Analizzare codice sorgente (Controllers, Routes)
2. Identificare operazioni utente
3. Raggruppare in Functional Processes coerenti
4. Documentare ogni FP: descrizione, attore, scopo

### 3.3 Fase 3: Conteggio Movimenti Dati

**Per ogni Functional Process:**
1. Identificare Entry (input utente)
2. Identificare Exit (output utente)
3. Identificare Read (letture storage)
4. Identificare Write (scritture storage)
5. Documentare conteggio E/X/R/W

### 3.4 Fase 4: Calcolo Functional Size

**Calcolare:**
- CFP per Functional Process: E + X + R + W
- CFP totale: somma di tutti i FP
- Breakdown per categoria (E/X/R/W)
- Breakdown per Functional Process

---

## 4. LIMITI E INCERTEZZE

### 4.1 Cosa NON è Misurabile COSMIC

**Non misurabile:**
- Codice infrastrutturale generico
- Configurazioni
- Test automatici
- Template grafici riutilizzabili
- Logging tecnico

**Dichiarazione esplicita:**
- Se un Functional Process non è identificabile, dichiararlo
- Se dati mancanti, dichiarare incertezza
- Se assunzioni fatte, documentarle

### 4.2 Incertezze nel Conteggio

**Possibili incertezze:**
- Functional Processes non completamente identificabili
- Movimenti dati ambigui
- Dati mancanti (codice non accessibile)
- Assunzioni su implementazione

**Trattamento:**
- Documentare esplicitamente incertezze
- Indicare range di incertezza (se applicabile)
- Separare conteggio certo da conteggio incerto

---

## 5. SEPARAZIONE SIZE vs COST

### 5.1 SIZE (Dimensione Funzionale)

**Misurata in:** CFP (COSMIC Function Points)

**Caratteristiche:**
- Indipendente da tecnologia
- Indipendente da linguaggio
- Indipendente da complessità implementativa
- Basata esclusivamente su movimenti dati funzionali

**Documentazione:**
- Tabella Functional Size per progetto
- Breakdown E/X/R/W
- Breakdown per Functional Process

### 5.2 COST (Effort)

**Misurata in:** Ore/giorni reali sostenuti

**Caratteristiche:**
- Documentata come **osservazione**
- **NON** calcolata da SIZE in questa fase
- **NON** derivata da coefficienti

**Documentazione:**
- Ore reali (se disponibili)
- Periodo sviluppo
- Fonte dati
- **Spiegazione:** Con N progetti < 3 non è possibile derivare relazioni statistiche valide

---

## 6. STANDARD DI DOCUMENTAZIONE

### 6.1 Struttura Documento Analisi

Ogni documento `COSMIC_ANALYSIS_[PROGETTO].md` deve contenere:

1. **SCOPO**
   - Obiettivo analisi
   - Data analisi
   - Standard: ISO/IEC 19761

2. **METODO COSMIC**
   - Breve descrizione metodo applicato
   - Criteri identificazione Functional Process
   - Criteri conteggio E/X/R/W

3. **CONTESTO E CONFINE SISTEMA**
   - Descrizione sistema
   - Utenti identificati
   - Confine sistema
   - Interfacce esterne

4. **FUNCTIONAL PROCESSES**
   - Lista completa FP identificati
   - Per ogni FP: descrizione, attore, scopo

5. **CONTEggio MOVIMENTI DATI**
   - Per ogni FP: E/X/R/W
   - Tabella riepilogativa

6. **FUNCTIONAL SIZE (CFP)**
   - Somma totale
   - Breakdown per categoria
   - Breakdown per FP

7. **DATI EFFORT (OSSERVAZIONE)**
   - Ore reali (se disponibili)
   - Periodo sviluppo
   - Fonte dati
   - ⚠️ NON calcolare coefficienti

8. **LIMITI E INCERTEZZE**
   - FP non identificabili
   - Dati mancanti
   - Assunzioni fatte
   - Incertezze nel conteggio

### 6.2 Stile Documentazione

**Caratteristiche:**
- Tecnico e asciutto
- Scientificamente difendibile
- Dichiarazioni esplicite su limiti
- Separazione rigorosa osservazione vs inferenza

**NON ammesso:**
- Linguaggio da preventivo
- Formule di costo
- Coefficienti produttività
- Normalizzazioni
- Fattori correttivi

---

## 7. RIFERIMENTI

**Standard Ufficiale:**
- ISO/IEC 19761:2017 - COSMIC Function Points

**Documentazione COSMIC:**
- COSMIC Measurement Manual v4.0.1
- COSMIC Implementation Guide

**Questo Documento:**
- Basato su standard ISO/IEC 19761
- Applicazione rigorosa senza contaminazioni
- Separazione SIZE vs COST

---

**Firma Metodologica:**  
Metodo COSMIC applicato secondo standard ISO/IEC 19761  
Nessuna contaminazione metodologica  
Separazione rigorosa SIZE vs COST

