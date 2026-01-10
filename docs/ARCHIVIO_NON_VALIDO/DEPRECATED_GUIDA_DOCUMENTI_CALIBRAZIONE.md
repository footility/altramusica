# Guida Documenti - Calibrazione Coefficiente Produttività

**Data:** Dicembre 2024  
**Scopo:** Indica quali documenti leggere e in che ordine per la calibrazione

---

## DOCUMENTI DA LEGGERE (in ordine)

### 1. EXECUTIVE_SUMMARY_CALIBRAZIONE.md ⭐ INIZIA QUI

**Cosa contiene:**
- Risultato principale (coefficiente 0,147 ore/DEV UNIT)
- Sintesi dati disponibili
- Limiti critici
- Raccomandazioni d'uso
- Conclusioni

**Tempo lettura:** 5 minuti  
**Quando leggerlo:** Per avere overview completa

---

### 2. TABELLA_CALIBRAZIONE_FINALE.md 📊 TABELLE RIEPILOGATIVE

**Cosa contiene:**
- Tabella coefficiente produttività principale
- Tabella progetti analizzati
- Tabella regole empiriche
- Tabella limiti affidabilità
- Formula finale raccomandata

**Tempo lettura:** 3 minuti  
**Quando leggerlo:** Per vedere numeri e formule in formato tabellare

---

### 3. CALIBRAZIONE_COEFFICIENTE_PRODUTTIVITA.md 📖 ANALISI COMPLETA

**Cosa contiene:**
- Analisi dettagliata per ogni progetto (MsCarichi, Cactusboard, Klabhouse, CZServizi)
- Mappatura COSMIC/DEV UNIT
- Dati effort reali
- Coefficienti calcolati
- Pattern sottostima/sovrastima
- Regole empiriche
- Limiti affidabilità
- Conclusioni

**Tempo lettura:** 15-20 minuti  
**Quando leggerlo:** Per approfondimento completo e dettagli metodologici

---

### 4. MAPPATURA_COSMIC_DETTAGLIATA.md 🔍 DETTAGLIO TECNICO

**Cosa contiene:**
- Mappatura Entry/Exit/Read/Write per MsCarichi
- Esempi concreti di conteggio DEV UNITS
- Fattore correzione granularità (4,58x)
- Validazione fattore correzione

**Tempo lettura:** 10 minuti  
**Quando leggerlo:** Per capire come vengono contati i DEV UNITS e validare metodologia

---

## RIEPILOGO RAPIDO

### Se hai poco tempo (5 minuti):
Leggi solo: **EXECUTIVE_SUMMARY_CALIBRAZIONE.md**

### Se vuoi vedere numeri e tabelle (10 minuti):
1. **EXECUTIVE_SUMMARY_CALIBRAZIONE.md**
2. **TABELLA_CALIBRAZIONE_FINALE.md**

### Se vuoi analisi completa (30 minuti):
1. **EXECUTIVE_SUMMARY_CALIBRAZIONE.md**
2. **TABELLA_CALIBRAZIONE_FINALE.md**
3. **CALIBRAZIONE_COEFFICIENTE_PRODUTTIVITA.md**

### Se vuoi capire metodologia (40 minuti):
1. **EXECUTIVE_SUMMARY_CALIBRAZIONE.md**
2. **TABELLA_CALIBRAZIONE_FINALE.md**
3. **CALIBRAZIONE_COEFFICIENTE_PRODUTTIVITA.md**
4. **MAPPATURA_COSMIC_DETTAGLIATA.md**

---

## RISULTATO PRINCIPALE (TL;DR)

**Coefficiente Produttività:**
- **0,147 ore per DEV UNIT** (8,8 minuti)
- Basato su: MsCarichi (1968 DEV UNITS, 290 ore)
- Affidabilità: ⚠️ BASSA (campione singolo)

**Formula:**
```
Ore = DEV UNITS × 0,147 ore/DEV UNIT
Margine Errore: ±50%
```

**Regole Utili:**
- Campo database → 22,9 DEV UNITS
- Entità CRUD → 55 DEV UNITS (con fattore correzione)
- Stima manuale × 4,58 = DEV UNITS reali

---

## DOCUMENTI NON NECESSARI PER CALIBRAZIONE

Questi documenti sono relativi ad altre analisi (LOC, tariffe, preventivi) e NON servono per la calibrazione:

- ❌ CALCOLO_FATTORE_DEV_UNIT.md (dati parziali, superato)
- ❌ FORMULA_CORRETTA_CAMPO_TEMPO.md (analisi LOC, non COSMIC)
- ❌ CONTESTO_TARIFFE.md (analisi costi, non produttività)
- ❌ REPORT_ANALISI_RETROSPETTIVA.md (analisi LOC, non DEV UNITS)

---

**Inizia da:** `docs/EXECUTIVE_SUMMARY_CALIBRAZIONE.md`

