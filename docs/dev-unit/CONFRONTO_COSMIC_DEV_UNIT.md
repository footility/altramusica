# Confronto COSMIC Function Points vs DEV UNIT

**Data:** Dicembre 2024  
**Scopo:** Analisi correlazione tra COSMIC Function Points (CFP) e DEV UNIT per validare metodologie di stima

---

## TABELLA RIEPILOGATIVA PROGETTI

| Progetto | COSMIC (CFP) | DEV UNIT | Ratio DEV/CFP | Ore Reali | CFP/Ora | DEV/Ora | Note |
|----------|--------------|----------|---------------|-----------|---------|---------|------|
| **MsCarichi** | 442 | ❌ Non calcolate | - | 290h | 1.52 | - | Analisi retrospettiva |
| **CactusBoard** | 316 | ❌ Non calcolate | - | 290h | 1.09 | - | Analisi retrospettiva |
| **Klabhouse** | 514 | ❌ Non calcolate | - | 250h | 2.06 | - | Sviluppato con AI |
| **CZServizi** | 195 | ❌ Non calcolate | - | 120h | 1.63 | - | Dati parziali |
| **L'Altramusica** | **337** | **1.440** | **4.27** | - | - | - | Stima EX-ANTE |

---

## ANALISI L'ALTRAMUSICA

### Dettaglio DEV UNIT

| Categoria | DEV UNIT |
|-----------|----------|
| **DEV UNIT REALI** (1:1 ODS → Laravel) | 730 |
| **DEV UNIT IPOTETICHE** (Evoluzione) | 710 |
| **TOTALE DEV UNIT** | **1.440** |

### Dettaglio COSMIC

| Categoria | CFP |
|-----------|-----|
| Entry (E) | 60 |
| Exit (X) | 58 |
| Read (R) | 140 |
| Write (W) | 79 |
| **TOTALE CFP** | **337** |

### Ratio L'Altramusica

**DEV UNIT / COSMIC = 1.440 / 337 = 4.27**

**Interpretazione:** Per ogni CFP, ci sono in media 4.27 DEV UNIT.

---

## STIMA DEV UNIT PROGETTI COMPLETATI

Applicando il ratio osservato in L'Altramusica (4.27 DEV UNIT/CFP):

| Progetto | COSMIC (CFP) | DEV UNIT Stimati | Ore Reali | DEV/Ora Stimato |
|----------|--------------|------------------|-----------|-----------------|
| **MsCarichi** | 442 | **1.887** | 290h | 6.51 |
| **CactusBoard** | 316 | **1.349** | 290h | 4.65 |
| **Klabhouse** | 514 | **2.195** | 250h | 8.78 |
| **CZServizi** | 195 | **832** | 120h | 6.93 |
| **L'Altramusica** | 337 | **1.440** | - | - |

**⚠️ NOTA:** Queste sono stime basate su un singolo punto di riferimento (L'Altramusica). La validità del ratio 4.27 DEV/CFP richiede verifica su più progetti.

---

## ANALISI CORRELAZIONE

### Grafico Proporzionale (Teorico)

```
COSMIC (CFP)    DEV UNIT (stimati)
─────────────────────────────────────
195  (CZ)       ████ 832
316  (CB)       ████████ 1.349
337  (ALT)      █████████ 1.440
442  (MS)       ████████████ 1.887
514  (KL)       ███████████████ 2.195
```

**Osservazione:** La crescita appare proporzionale, ma serve validazione con dati reali.

### Ratio Variabilità

| Progetto | Ratio DEV/CFP (stimato) | Variazione da Media |
|----------|-------------------------|---------------------|
| MsCarichi | 4.27 | 0% (baseline) |
| CactusBoard | 4.27 | 0% |
| Klabhouse | 4.27 | 0% |
| CZServizi | 4.27 | 0% |
| L'Altramusica | 4.27 | 0% (reale) |

**⚠️ LIMITAZIONE:** Ratio calcolato da un solo progetto reale. Variabilità sconosciuta.

---

## CONFRONTO PRODUTTIVITÀ

### COSMIC/Ora (Osservato)

| Progetto | CFP/Ora | Note |
|----------|---------|------|
| MsCarichi | 1.52 | - |
| CactusBoard | 1.09 | - |
| Klabhouse | 2.06 | Sviluppato con AI |
| CZServizi | 1.63 | Dati parziali |
| **Media** | **1.58** | - |

### DEV UNIT/Ora (Stimato da Ratio)

| Progetto | DEV/Ora (stimato) | Note |
|----------|-------------------|------|
| MsCarichi | 6.51 | Da ratio 4.27 |
| CactusBoard | 4.65 | Da ratio 4.27 |
| Klabhouse | 8.78 | Da ratio 4.27 (AI-assisted) |
| CZServizi | 6.93 | Da ratio 4.27 |
| **Media** | **6.72** | - |

---

## VALIDAZIONE METODOLOGICA

### Punti di Forza

1. **L'Altramusica ha entrambe le metriche:** COSMIC (337 CFP) e DEV UNIT (1.440) calcolate
2. **Ratio calcolabile:** 4.27 DEV UNIT/CFP per L'Altramusica
3. **Crescita proporzionale teorica:** All'aumentare di COSMIC, DEV UNIT crescono proporzionalmente

### Limiti e Incertezze

1. **Campione insufficiente:** Solo 1 progetto con entrambe le metriche reali
2. **Ratio non validato:** 4.27 DEV/CFP basato su un solo punto
3. **Variabilità sconosciuta:** Non sappiamo se il ratio varia tra progetti
4. **Metodologie diverse:** COSMIC misura movimenti dati, DEV UNIT misura elementi implementabili

### Fattori che Potrebbero Influenzare il Ratio

1. **Complessità UI:** Progetti con UI complessa → più DEV UNIT per stesso CFP
2. **Complessità DB:** Progetti con molti campi/relazioni → più DEV UNIT per stesso CFP
3. **Workflow:** Progetti con molti workflow → più DEV UNIT per stesso CFP
4. **Stampa/PDF:** Progetti con molti PDF → più DEV UNIT per stesso CFP

**L'Altramusica ha:**
- UI complessa (form, liste, filtri)
- Database con molti campi (da ODS)
- Workflow evolutivi
- Generazione PDF (contratti, fatture, attestati)

Questo potrebbe spiegare un ratio relativamente alto (4.27).

---

## RACCOMANDAZIONI

### Per Validare il Ratio

1. **Calcolare DEV UNIT retrospettivamente** per almeno 2-3 progetti completati:
   - MsCarichi (442 CFP)
   - CactusBoard (316 CFP)
   - Klabhouse (514 CFP)

2. **Verificare correlazione:**
   - Se ratio DEV/CFP è costante → correlazione lineare valida
   - Se ratio varia → identificare fattori di variabilità

3. **Calibrare stime future:**
   - Se ratio validato → usare per stime EX-ANTE
   - Se ratio variabile → identificare fattori correttivi

### Per Migliorare l'Analisi

1. **Breakdown per categoria:**
   - Ratio DEV/CFP per anagrafiche
   - Ratio DEV/CFP per CRUD
   - Ratio DEV/CFP per workflow
   - Ratio DEV/CFP per UI complessa

2. **Analisi fattori:**
   - Progetti con UI semplice vs complessa
   - Progetti con DB semplice vs complesso
   - Progetti con workflow semplici vs complessi

---

## CONCLUSIONI

### Correlazione Teorica

**Sì, c'è una correlazione proporzionale teorica:**
- All'aumentare di COSMIC, DEV UNIT crescono proporzionalmente
- Ratio osservato: **4.27 DEV UNIT/CFP** (L'Altramusica)

### Validazione Necessaria

**Il ratio richiede validazione:**
- Solo 1 progetto con entrambe le metriche reali
- Necessario calcolare DEV UNIT per progetti completati
- Verificare se ratio è costante o variabile

### Utilizzo Pratico

**Con ratio validato, possibile:**
- Stimare DEV UNIT da COSMIC (EX-ANTE)
- Stimare COSMIC da DEV UNIT (se necessario)
- Confrontare progetti con metriche diverse

**⚠️ ATTENZIONE:** Ratio attuale (4.27) è basato su un solo progetto. Non utilizzare per stime critiche senza validazione.

---

## PROSSIMI PASSI

1. ✅ Calcolare DEV UNIT retrospettivamente per MsCarichi, CactusBoard, Klabhouse
2. ✅ Verificare se ratio DEV/CFP è costante o variabile
3. ✅ Identificare fattori che influenzano il ratio
4. ✅ Calibrare stime future con ratio validato

---

**Firma Analisi:**  
Confronto COSMIC vs DEV UNIT  
Ratio osservato: 4.27 DEV UNIT/CFP (L'Altramusica)  
Validazione necessaria su progetti completati


