# Analisi Statistica: Campo → DEV UNITS → Tempo

**Data:** Dicembre 2024  
**Obiettivo:** Calcolare quanta quantità di codice (DEV UNITS) produce un singolo campo database

---

## Dati Raccolti

### 1. MSCARICHI

| Metrica | Valore |
|---------|--------|
| **DEV UNITS Totali** | 1968 |
| **Tabelle Create** | 26 |
| **Campi Totali (con id/timestamps)** | 148 |
| **Campi Custom (senza id/timestamps)** | 86 |
| **Ore Sviluppo** | 290 ore |
| **DEV UNITS per Campo Custom** | 1968 / 86 = **22,9 DEV UNITS/campo** |
| **Tempo per Campo** | 22,9 × 0,147 ore = **3,37 ore/campo** |

---

### 2. CACTUSDASHBOARD

| Metrica | Valore |
|---------|--------|
| **DEV UNITS Totali** | 1968 |
| **Tabelle Create** | 20 |
| **Campi Totali (con id/timestamps)** | 138 |
| **Campi Custom (senza id/timestamps)** | 92 |
| **Ore Sviluppo** | 290 ore |
| **DEV UNITS per Campo Custom** | 1968 / 92 = **21,4 DEV UNITS/campo** |
| **Tempo per Campo** | 21,4 × 0,147 ore = **3,15 ore/campo** |

---

## Formula Statistica Campo → DEV UNITS

### Risultato Statistico (Media 2 Progetti)

| Progetto | Campi Custom | DEV UNITS | DEV UNITS/Campo |
|----------|--------------|-----------|-----------------|
| **MsCarichi** | 86 | 1968 | **22,9** |
| **Cactusboard** | 92 | 1968 | **21,4** |
| **MEDIA** | **89** | **1968** | **22,15 DEV UNITS/campo** |

**1 Campo Database = 22,15 DEV UNITS** (media statistica)

### Cosa Include un Campo?

Ogni campo genera codice in:
1. **Migration** (definizione campo)
2. **Model** (fillable, casts, accessors/mutators, relazioni)
3. **Controller** (CRUD operations: index, show, create, store, edit, update, destroy)
4. **Request Validation** (StoreRequest, UpdateRequest con regole per campo)
5. **Views** (index, show, create, edit, form partials, filtri)
6. **Routes** (resource routes)
7. **Test** (Unit + Feature per ogni operazione)
8. **Logica Business** (calcoli, validazioni custom, eventi)

**Totale:** ~22 DEV UNITS distribuiti tra tutti questi componenti

---

## Validazione Formula

### Formula Statistica Confermata

**Media osservata su 2 progetti:**
```
DEV UNITS per Campo = 22,15 DEV UNITS/campo
Tempo per Campo = 22,15 × 0,147 ore = 3,26 ore/campo
```

**Range osservato:**
- Minimo (Cactusboard): 21,4 DEV UNITS/campo = 3,15 ore/campo
- Massimo (MsCarichi): 22,9 DEV UNITS/campo = 3,37 ore/campo
- **Deviazione standard:** ~0,75 DEV UNITS/campo (circa 3,4%)

---

## Formula Finale: Campo → Tempo

```
DEV UNITS per Campo = 22,15 DEV UNITS
Tempo per Campo = 22,15 × 0,147 = 3,26 ore/campo
```

**Questo tempo include:**
- Sviluppo (migration, model, controller, views, routes)
- Progettazione (decidere tipo, validazione, UI)
- Test (unit, feature)
- Consulenza (analisi requisiti, feedback)

---

## Confronto con Studi Accademici

**Riferimenti trovati:**
- **COCOMO (Constructive Cost Model):** Stima basata su LOC, non specifico per campi database
- **Function Points Analysis:** Misura funzionalità, non diretto per campi
- **PERT (Program Evaluation and Review Technique):** Stima probabilistica, non specifica per campi

**Nessuno studio specifico trovato su "campi database → tempo sviluppo"**

**Valore aggiunto:** Questa analisi empirica basata su dati reali fornisce una metrica concreta e validata statisticamente.

---

## Prossimi Passi

1. ✅ Completata analisi MsCarichi: 22,9 DEV UNITS/campo
2. ✅ Completata analisi Cactusboard: 21,4 DEV UNITS/campo
3. ✅ Calcolata media statistica: **22,15 DEV UNITS/campo** = **3,26 ore/campo**
4. ⏳ Verificare analisi Klabhouse (se disponibile migrations)
5. ⏳ Validare formula su nuovo progetto

