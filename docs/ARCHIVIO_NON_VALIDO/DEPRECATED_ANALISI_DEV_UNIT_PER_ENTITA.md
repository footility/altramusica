# Analisi DEV UNITS per Entità - Formula Probabilistica

**Data:** Dicembre 2024  
**Obiettivo:** Creare formula che parte da requisito (campo/entità) e calcola DEV UNITS totali

---

## Concetto: Campo → DEV UNITS → Ore → Costi

L'idea è partire da un **input minimo** (es. "10 campi per entità X") e calcolare tutto automaticamente:

**10 campi** → 
- Model (1 DEV UNIT base + N campi = X DEV UNITS)
- Migration (1 DEV UNIT)
- Controller CRUD (4-6 DEV UNITS: index, show, create, store, edit, update, destroy)
- Views (4-6 DEV UNITS: index, show, create, edit, form partials)
- Routes (1-2 DEV UNITS: resource routes)
- Request Validation (2-4 DEV UNITS: StoreRequest, UpdateRequest)
- **Totale: ~15-25 DEV UNITS per entità base**

**100 campi** →
- Stessa struttura ma con più complessità
- Form più complessi = più DEV UNITS views
- Validation più complessa = più DEV UNITS request
- **Totale: ~20-35 DEV UNITS per entità complessa**

---

## Dati da Analizzare

### Progetti Esistenti

**MsCarichi:**
- 22 Modelli
- 39 Controller (36 file Controller trovati)
- 148 Viste
- 1968 DEV UNITS totali
- **DEV UNITS per Modello:** 1968 / 22 = **89 DEV UNITS/modello**

**Cactusboard:**
- 13 Modelli
- 27 Controller (24 file Controller trovati)
- 111 Viste
- 1968 DEV UNITS totali
- **DEV UNITS per Modello:** 1968 / 13 = **151 DEV UNITS/modello**

**Klabhouse:**
- 32 Modelli (da report)
- 43 Controller (da report)
- 234 Viste
- DEV UNITS: (da calcolare se disponibile)

**Media (MsCarichi + Cactusboard):** (89 + 151) / 2 = **120 DEV UNITS/modello**

---

## Formula Probabilistica Proposta

### Input: Numero Campi per Entità

**Formula Base:**
```
DEV UNITS Entità = DEV UNITS Base + (Campi × Fattore Complessità)

Dove:
- DEV UNITS Base = 15-20 (Model + Migration + Routes base)
- Fattore Complessità = 2-3 DEV UNITS per campo (considerando Controller + View + Validation)
```

**Esempio:**
- Entità con 10 campi: 20 + (10 × 2.5) = **45 DEV UNITS**
- Entità con 50 campi: 20 + (50 × 2.5) = **145 DEV UNITS**

### Conversione Ore e Costi

```
Ore = DEV UNITS × 0,147 ore/DEV UNIT
Costo = Ore × € 30,00/ora
```

---

## Prossimi Passi

1. ✅ Identificato fattore DEV UNIT: 0,147 ore/DEV UNIT
2. ⏳ Analizzare progetti per validare formula campo → DEV UNITS
3. ⏳ Classificare complessità entità (base, media, complessa)
4. ⏳ Includere test e consulenza nel calcolo
5. ⏳ Creare formula finale unificata

