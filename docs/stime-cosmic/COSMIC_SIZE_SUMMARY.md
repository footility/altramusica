# Riepilogo Functional Size (CFP) - Progetti Completati

**Data:** Dicembre 2024  
**Standard:** ISO/IEC 19761 - COSMIC Function Points  
**Scopo:** Riepilogo Functional Size (CFP) per tutti i progetti analizzati

---

## 1. SCOPO

Questo documento contiene **SOLO** dati di SIZE (dimensione funzionale) misurata in CFP (COSMIC Function Points).

**NON contiene:**
- Coefficienti di produttività
- Formule di costo
- Normalizzazioni
- Confronti tra progetti
- Calcoli di produttività

**Contiene:**
- Functional Size (CFP) per ogni progetto
- Breakdown E/X/R/W per progetto
- Dati effort osservati (ore/giorni reali) - SOLO osservazione
- Spiegazione limiti statistici

---

## 2. FUNCTIONAL SIZE PER PROGETTO

### 2.1 MsCarichi

**Functional Size:** 442 CFP

**Breakdown:**
- Entry (E): 88 CFP
- Exit (X): 113 CFP
- Read (R): 170 CFP
- Write (W): 71 CFP

**Range Incertezza:** 420-480 CFP  
**Affidabilità:** Media-Alta

**Fonte:** `docs/COSMIC_ANALYSIS_MSCARICHI.md`

---

### 2.2 CactusBoard

**Functional Size:** 316 CFP

**Breakdown:**
- Entry (E): 62 CFP
- Exit (X): 81 CFP
- Read (R): 123 CFP
- Write (W): 50 CFP

**Range Incertezza:** 300-350 CFP  
**Affidabilità:** Media

**Fonte:** `docs/COSMIC_ANALYSIS_CACTUSBOARD.md`

---

### 2.3 Klabhouse

**Functional Size:** 514 CFP

**Breakdown:**
- Entry (E): 112 CFP
- Exit (X): 118 CFP
- Read (R): 210 CFP
- Write (W): 74 CFP

**Range Incertezza:** 480-580 CFP  
**Affidabilità:** Media-Alta

**Nota Speciale:** Progetto sviluppato interamente con assistenza AI (Cursor)

**Fonte:** `docs/COSMIC_ANALYSIS_KLABHOUSE.md`

---

### 2.4 CZServizi

**Functional Size:** 195 CFP

**Breakdown:**
- Entry (E): 36 CFP
- Exit (X): 53 CFP
- Read (R): 78 CFP
- Write (W): 24 CFP

**Range Incertezza:** 175-235 CFP  
**Affidabilità:** Bassa-Media

**⚠️ AVVISO:** Analisi basata su dati parziali - affidabilità inferiore

**Fonte:** `docs/COSMIC_ANALYSIS_CZSERVIZI.md`

---

## 3. TABELLA RIEPILOGATIVA FUNCTIONAL SIZE

| Progetto | Entry (E) | Exit (X) | Read (R) | Write (W) | Totale CFP | Range Incertezza | Affidabilità |
|----------|-----------|----------|----------|-----------|------------|------------------|--------------|
| MsCarichi | 88 | 113 | 170 | 71 | 442 | 420-480 | Media-Alta |
| CactusBoard | 62 | 81 | 123 | 50 | 316 | 300-350 | Media |
| Klabhouse | 112 | 118 | 210 | 74 | 514 | 480-580 | Media-Alta |
| CZServizi | 36 | 53 | 78 | 24 | 195 | 175-235 | Bassa-Media |
| **TOTALE** | **298** | **365** | **581** | **219** | **1.467** | - | - |

---

## 4. DATI EFFORT (OSSERVAZIONE)

### 4.1 MsCarichi

**Ore Reali Sostenute:** 290 ore

**Periodo Sviluppo:** Non specificato nei dati disponibili

**Fonte Dati:** Analisi retrospettiva progetti completati

**⚠️ NOTA:** Dato documentato come **osservazione**. Non viene calcolato alcun coefficiente di produttività.

---

### 4.2 CactusBoard

**Ore Reali Sostenute:** 290 ore

**Periodo Sviluppo:** Non specificato nei dati disponibili

**Fonte Dati:** Analisi retrospettiva progetti completati (dato da `docs/CONTESTO_TARIFFE.md`)

**⚠️ NOTA:** Dato documentato come **osservazione**. Non viene calcolato alcun coefficiente di produttività.

---

### 4.3 Klabhouse

**Ore Reali Sostenute:** 250 ore

**Periodo Sviluppo:** Febbraio 2024 - Dicembre 2024

**Fonte Dati:** Analisi retrospettiva progetti completati

**Nota Speciale:** Progetto sviluppato interamente con assistenza AI (Cursor)

**⚠️ NOTA:** Dato documentato come **osservazione**. Non viene calcolato alcun coefficiente di produttività.

---

### 4.4 CZServizi

**Ore Reali Sostenute:** 120 ore

**Periodo Sviluppo:** Non specificato nei dati disponibili

**Fonte Dati:** Analisi retrospettiva progetti completati (dati parziali)

**⚠️ NOTA:** Dato documentato come **osservazione**. Non viene calcolato alcun coefficiente di produttività.

---

## 5. TABELLA RIEPILOGATIVA EFFORT (OSSERVAZIONE)

| Progetto | Ore Reali | Periodo Sviluppo | Fonte Dati | Note |
|----------|-----------|------------------|------------|------|
| MsCarichi | 290 | Non specificato | Analisi retrospettiva | - |
| CactusBoard | 290 | Non specificato | Analisi retrospettiva | - |
| Klabhouse | 250 | Feb 2024 - Dic 2024 | Analisi retrospettiva | Sviluppato con AI |
| CZServizi | 120 | Non specificato | Analisi retrospettiva (parziale) | Dati parziali |

**⚠️ IMPORTANTE:** Questi dati sono documentati come **osservazioni**. Non vengono calcolati coefficienti di produttività.

---

## 6. LIMITI STATISTICI

### 6.1 Campione Disponibile

**Numero Progetti Analizzati:** 4

**Progetti con Dati Effort Disponibili:** 4 (MsCarichi, CactusBoard, Klabhouse, CZServizi)

**Progetti con Dati Effort Completi:** 3 (MsCarichi, CactusBoard, Klabhouse)

### 6.2 Impossibilità di Derivare Relazioni Statistiche Valide

**Con N progetti < 3 non è possibile derivare relazioni statistiche valide tra SIZE e COST.**

**Motivazione:**
- Campione statistico insufficiente (N=2-3 progetti con dati effort)
- Variabilità elevata tra progetti (diversi domini, complessità, tecnologie)
- Assenza di controllo su variabili confondenti (esperienza sviluppatore, complessità business, tooling, AI assistance)
- Impossibilità di validare ipotesi statistiche (test di significatività, intervalli di confidenza)

**Conseguenza:**
- **NON** vengono calcolati coefficienti di produttività
- **NON** vengono derivate formule SIZE → COST
- **NON** vengono fatte inferenze statistiche
- **NON** vengono confrontati progetti tra loro

### 6.3 Cosa È Possibile Fare

**Con i dati disponibili è possibile:**
- Documentare Functional Size (CFP) per ogni progetto
- Documentare effort osservato per ogni progetto
- Identificare pattern qualitativi (non quantitativi)
- Costruire base dati pulita per analisi future

**Con i dati disponibili NON è possibile:**
- Calcolare coefficiente produttività affidabile
- Predire effort per nuovi progetti
- Validare modelli predittivi
- Fare confronti statisticamente significativi

### 6.4 Requisiti per Analisi Futura

**Per derivare relazioni statistiche valide tra SIZE e COST servirebbero:**
- **Minimo 10-15 progetti** completati con dati effort completi
- Dati effort dettagliati (non solo totali, ma breakdown per fase/funzionalità)
- Controllo variabili confondenti (dominio, complessità, tooling, AI assistance)
- Validazione statistica (test di significatività, intervalli di confidenza, validazione incrociata)

---

## 7. SEPARAZIONE SIZE vs COST

### 7.1 SIZE (Dimensione Funzionale)

**Misurata in:** CFP (COSMIC Function Points)

**Caratteristiche:**
- Indipendente da tecnologia
- Indipendente da linguaggio
- Indipendente da complessità implementativa
- Basata esclusivamente su movimenti dati funzionali

**Documentazione:**
- Tabella Functional Size per progetto (sezione 3)
- Breakdown E/X/R/W per progetto (sezione 3)
- Range di incertezza per progetto (sezione 3)

### 7.2 COST (Effort)

**Misurata in:** Ore/giorni reali sostenuti

**Caratteristiche:**
- Documentata come **osservazione**
- **NON** calcolata da SIZE
- **NON** derivata da coefficienti

**Documentazione:**
- Ore reali per progetto (sezione 4)
- Periodo sviluppo (sezione 4)
- Fonte dati (sezione 4)

### 7.3 Separazione Rigorosa

**SIZE e COST sono documentati separatamente.**

**Nessuna formula di produttività in questo documento.**

**Nessun coefficiente calcolato.**

**Spiegazione esplicita:** Con N progetti < 3 non è possibile derivare relazioni statistiche valide.

---

## 8. RIFERIMENTI

**Documenti Analisi Dettagliate:**
- `docs/COSMIC_ANALYSIS_MSCARICHI.md`
- `docs/COSMIC_ANALYSIS_CACTUSBOARD.md`
- `docs/COSMIC_ANALYSIS_KLABHOUSE.md`
- `docs/COSMIC_ANALYSIS_CZSERVIZI.md`

**Documenti Metodologici:**
- `docs/COSMIC_METHODOLOGY_DECLARATION.md`
- `docs/COSMIC_METHODOLOGY.md`

**Standard Applicato:**
- ISO/IEC 19761 - COSMIC Function Points v4.0.1

---

## 9. CONCLUSIONI

### 9.1 Functional Size Totale

**Totale CFP Misurati:** 1.467 CFP

**Breakdown Totale:**
- Entry (E): 298 CFP
- Exit (X): 365 CFP
- Read (R): 581 CFP
- Write (W): 219 CFP

### 9.2 Base Dati Costruita

È stata costruita una **BASE COSMIC SOLIDA E PULITA** su cui, e **SOLO successivamente**, sarà possibile costruire un modello di produttività affidabile con dati statisticamente validi.

### 9.3 Prossimi Passi (Non in Questo Documento)

**Per costruire un modello di produttività affidabile servirebbero:**
1. Analisi di almeno 10-15 progetti completati
2. Dati effort dettagliati per ogni progetto
3. Controllo variabili confondenti
4. Validazione statistica

**Fino ad allora:**
- SIZE e COST rimangono separati
- Nessun coefficiente viene calcolato
- Nessuna formula viene derivata

---

**Firma Documento:**  
Riepilogo Functional Size eseguito secondo standard ISO/IEC 19761  
Separazione rigorosa SIZE vs COST mantenuta  
Nessun coefficiente di produttività calcolato  
Base dati pulita per analisi future

