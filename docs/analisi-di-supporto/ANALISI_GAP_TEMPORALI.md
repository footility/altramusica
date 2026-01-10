# Analisi Gap Temporali - Pause e Inattività

**Data Analisi:** Dicembre 2024  
**Obiettivo:** Identificare gap temporali significativi che influenzano stime ore

---

## Gap Identificati

### 1. MSCARICHI

**Periodo Totale:** 13/05/2024 - 03/12/2024 (569 giorni)  
**Giorni con Commit:** 58 giorni  
**Giorni senza Commit:** 511 giorni (90%)  
**Percentuale Giorni Lavorativi:** 10%

**Gap Significativi (>30 giorni):**

| Da | A | Gap | Note |
|----|---|-----|------|
| 2024-05-13 | 2024-06-13 | 31 giorni | Pausa iniziale |
| 2024-06-13 | 2024-07-24 | 41 giorni | Pausa estiva |
| 2024-09-09 | 2024-10-10 | 31 giorni | Pausa autunnale |
| 2024-11-13 | 2025-01-02 | 50 giorni | Pausa invernale (Natale) |
| 2025-02-06 | 2025-04-02 | 55 giorni | Pausa primaverile |

**Totale Gap >30 giorni:** 7 gap (208 giorni totali)

**Analisi:**
- **Gap >7 giorni:** 14 gap (24% dei giorni)
- **Gap >30 giorni:** 7 gap (12% dei giorni)
- Pattern: pause regolari, probabilmente per altri progetti/ferie

---

### 2. CACTUSDASHBOARD

**Periodo Totale:** 14/01/2024 - 21/05/2025 (493 giorni)  
**Giorni con Commit:** 58 giorni  
**Giorni senza Commit:** 435 giorni (88%)  
**Percentuale Giorni Lavorativi:** 11%

**Gap Significativi (>30 giorni):**

| Da | A | Gap | Note |
|----|---|-----|------|
| 2024-08-04 | 2024-10-07 | 64 giorni | Pausa estiva/autunnale |
| 2024-12-23 | 2025-02-26 | 65 giorni | Pausa invernale (Natale/Capodanno) |
| 2025-02-28 | 2025-04-29 | 60 giorni | Pausa primaverile |

**Totale Gap >30 giorni:** 3 gap (189 giorni totali)

**Analisi:**
- **Gap >7 giorni:** 16 gap (28% dei giorni)
- **Gap >30 giorni:** 3 gap (5% dei giorni)
- Pattern: pause lunghe, sviluppo concentrato in periodi specifici

---

### 3. KLABHOUSE

**Periodo Totale:** 20/03/2025 - 27/11/2025 (252 giorni)  
**Giorni con Commit:** 50 giorni  
**Giorni senza Commit:** 202 giorni (80%)  
**Percentuale Giorni Lavorativi:** 19%

**Gap Significativi (>30 giorni):**

| Da | A | Gap | Note |
|----|---|-----|------|
| 2025-04-16 | 2025-05-26 | 40 giorni | Pausa primaverile |
| 2025-05-26 | 2025-06-27 | 32 giorni | Pausa estiva |

**Totale Gap >30 giorni:** 2 gap (72 giorni totali)

**Analisi:**
- **Gap >7 giorni:** 9 gap (18% dei giorni)
- **Gap >30 giorni:** 2 gap (4% dei giorni)
- Pattern: sviluppo più concentrato, meno pause lunghe

---

## Impatto sulle Stime Ore

### Problema Identificato

**Calcolo Attuale:**
- Giorni lavorativi: 58 giorni (giorni con commit)
- Ore/giorno: 5 ore
- **Ore totali:** 58 × 5 = 290 ore

**Problema:**
- I 58 giorni sono **giorni con commit**, non giorni lavorativi consecutivi
- Ci sono **gap significativi** (30+ giorni) che indicano pause dal progetto
- Le pause potrebbero essere:
  - Lavoro su altri progetti
  - Attese feedback cliente
  - Ferie/vacanze
  - Altri impegni

### Correzione Necessaria

**Opzione A: Escludere Gap >30 giorni**
- Considerare solo periodi di sviluppo attivo
- Escludere pause lunghe dal calcolo

**Opzione B: Considerare Intensità Lavorativa**
- Giorni con commit = giorni lavorativi
- Ma considerare che non tutti i giorni sono pieni (5 ore)
- Alcuni giorni potrebbero essere solo fix veloci

**Opzione C: Calcolare Ore Reali da Commit**
- Analizzare numero commit/giorno
- Stimare ore in base a intensità commit

---

## Analisi Intensità Lavorativa

### Commit per Giorno

| Progetto | Giorni Totali | Commit Totali | Commit/Giorno | Pattern |
|----------|--------------|---------------|---------------|---------|
| **Mscarichi** | 58 | 312 | 5,4 | Intensivo |
| **Cactusdashboard** | 58 | 155 | 2,7 | Medio |
| **Klabhouse** | 50 | 484 | 9,7 | Molto intensivo |

**Osservazioni:**
- Klabhouse: 9,7 commit/giorno (molto intensivo, sessioni lunghe)
- Mscarichi: 5,4 commit/giorno (intensivo)
- Cactusdashboard: 2,7 commit/giorno (medio)

### Stima Ore Reali da Intensità

**Assunzioni:**
- 1-2 commit: 2-3 ore (fix veloci, piccole modifiche)
- 3-5 commit: 4-6 ore (giornata normale)
- 6+ commit: 6-8 ore (giornata intensiva)

**Calcolo per Progetto:**

**Mscarichi (5,4 commit/giorno):**
- Media: ~5-6 ore/giorno
- 58 giorni × 5,5 ore = **319 ore** (vs 290 ore calcolate)

**Cactusdashboard (2,7 commit/giorno):**
- Media: ~3-4 ore/giorno
- 58 giorni × 3,5 ore = **203 ore** (vs 290 ore calcolate)

**Klabhouse (9,7 commit/giorno):**
- Media: ~7-8 ore/giorno
- 50 giorni × 7,5 ore = **375 ore** (vs 250 ore calcolate)

---

## Gap Temporali e Ore Effettive

### Analisi Gap

**Gap >30 giorni indicano:**
- Pause significative dal progetto
- Lavoro su altri progetti
- Attese/ferie

**Impatto:**
- Le ore calcolate (giorni × ore/giorno) potrebbero essere **sovrastimate** se:
  - I gap sono pause reali (non lavoro)
  - I giorni con commit non sono tutti giorni pieni (5 ore)

**Correzione Necessaria:**

**Metodo Corretto:**
1. Identificare periodi di sviluppo attivo (escludendo gap >30 giorni)
2. Calcolare ore per periodo attivo
3. Sommare ore di tutti i periodi attivi

**Esempio Mscarichi:**
- Periodi attivi: 5 periodi (separati da gap >30 giorni)
- Ogni periodo: X giorni × Y ore/giorno
- **Totale:** Somma ore periodi attivi

---

## Ricalcolo Ore con Gap

### MSCARICHI

**Periodi Attivi (escludendo gap >30 giorni):**
1. 13/05 - 13/06: 31 giorni → 0 ore (gap)
2. 13/06 - 24/07: 41 giorni → 0 ore (gap)
3. 24/07 - 09/09: ~47 giorni attivi → 47 × 5 = 235 ore
4. 09/09 - 10/10: 31 giorni → 0 ore (gap)
5. 10/10 - 13/11: ~34 giorni attivi → 34 × 5 = 170 ore
6. 13/11 - 02/01: 50 giorni → 0 ore (gap)
7. 02/01 - 06/02: ~35 giorni attivi → 35 × 5 = 175 ore
8. 06/02 - 02/04: 55 giorni → 0 ore (gap)
9. 02/04 - 03/12: ~245 giorni attivi → 245 × 5 = 1.225 ore

**Problema:** Questo metodo sovrastima perché conta tutti i giorni del periodo, non solo quelli con commit.

**Metodo Corretto:**
- **Giorni con commit:** 58 giorni
- **Gap >30 giorni:** 7 gap (208 giorni esclusi)
- **Giorni effettivi:** 58 giorni (non cambia)
- **Ore effettive:** 58 × 5 = **290 ore** (corretto)

**Conclusione:** I gap non cambiano le ore se contiamo solo giorni con commit. Ma potrebbero indicare che alcuni giorni non sono pieni (5 ore).

---

## Correzione Ore/Giorno

### Analisi Intensità

**Mscarichi:**
- 5,4 commit/giorno → ~5-6 ore/giorno realistiche
- **Ore corrette:** 58 × 5,5 = **319 ore** (vs 290 ore)

**Cactusdashboard:**
- 2,7 commit/giorno → ~3-4 ore/giorno realistiche
- **Ore corrette:** 58 × 3,5 = **203 ore** (vs 290 ore)

**Klabhouse:**
- 9,7 commit/giorno → ~7-8 ore/giorno realistiche
- **Ore corrette:** 50 × 7,5 = **375 ore** (vs 250 ore)

### Media Ore Corrette

| Progetto | Ore Precedenti | Ore Corrette (da intensità) | Variazione |
|----------|----------------|----------------------------|------------|
| **Mscarichi** | 290 | 319 | +10% |
| **Cactusdashboard** | 290 | 203 | -30% |
| **Klabhouse** | 250 | 375 | +50% |
| **Media** | 277 | **299** | **+8%** |

**Ratio LOC/Ora Corretto:**
- LOC totali: 52.712
- Ore corrette: 897 ore (319 + 203 + 375)
- **LOC/Ora:** 52.712 ÷ 897 = **58,8 LOC/ora**

**Nota:** Questo ratio è molto alto perché:
- Klabhouse ha molti seeders (veloci da scrivere)
- Cactusdashboard ha meno ore (2,7 commit/giorno)

---

## Raccomandazione Finale

### Gap Temporali: Impatto Limitato

**Conclusione:**
1. I gap >30 giorni indicano pause, ma non cambiano giorni con commit
2. L'impatto principale è sull'**intensità lavorativa** (ore/giorno)
3. Alcuni progetti hanno giorni più intensivi (Klabhouse) vs meno intensivi (Cactusdashboard)

### Ore Corrette per Intensità

**Opzione A: Usare Ore da Intensità Commit**
- Mscarichi: 319 ore
- Cactusdashboard: 203 ore
- Klabhouse: 375 ore
- **Media:** 299 ore
- **LOC/Ora:** 58,8 LOC/ora (molto alto, influenzato da seeders)

**Opzione B: Mantenere Ore Originali (5 ore/giorno)**
- Più conservativo
- Considera che alcuni giorni sono meno intensivi
- **Media:** 277 ore
- **LOC/Ora:** 52.712 ÷ 831 = **63,4 LOC/ora** (ancora più alto)

**Opzione C: Media Ponderata**
- Considerare intensità ma essere conservativi
- **Ore corrette:** (319 + 203 + 375) ÷ 3 = **299 ore**
- Ma applicare fattore conservativo 0.8 = **239 ore**
- **LOC/Ora:** 52.712 ÷ 717 = **73,5 LOC/ora** (troppo alto)

---

## Conclusione Gap Temporali

**I gap temporali NON cambiano significativamente le stime perché:**

1. **Giorni con commit sono già identificati** (58, 58, 50 giorni)
2. **Gap indicano pause**, ma giorni lavorativi rimangono gli stessi
3. **Impatto principale:** Intensità lavorativa (commit/giorno) varia tra progetti

**Raccomandazione:**
- Mantenere calcolo originale (giorni × 5 ore/giorno)
- Considerare che alcuni progetti sono più/meno intensivi
- Media LOC/Ora (43,0) già considera questa variabilità

**Gap temporali sono documentati ma non richiedono correzione significativa alle stime.**

---

**Documentazione:** Questo gap analysis completa la validazione considerando pause e intensità lavorativa.

