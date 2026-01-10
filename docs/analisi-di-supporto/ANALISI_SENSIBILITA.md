# Analisi Sensibilità - Variabilità Stime

**Data Analisi:** Dicembre 2024  
**Obiettivo:** Testare robustezza risultati variando assunzioni chiave

---

## Parametri Variabili

### 1. Ore/Giorno (4, 5, 6 ore)

**Base:** 5 ore/giorno (validata dall'analisi pattern commit)

| Ore/Giorno | Mscarichi (58 giorni) | Cactusdashboard (58 giorni) | Klabhouse (50 giorni) | Media Ore |
|------------|------------------------|----------------------------|------------------------|-----------|
| **4 ore** | 232 ore | 232 ore | 200 ore | 221 ore |
| **5 ore** | 290 ore | 290 ore | 250 ore | 277 ore |
| **6 ore** | 348 ore | 348 ore | 300 ore | 332 ore |

**Impatto su Ore L'Altramusica (28.000 LOC, 43 LOC/ora):**
- 4 ore/giorno: 28.000 ÷ 43 = **651 ore** (stesso, non dipende da ore/giorno)
- 5 ore/giorno: **651 ore**
- 6 ore/giorno: **651 ore**

**Nota:** Ore calcolate da LOC/Ora non dipendono da ore/giorno, ma da LOC totali.

---

### 2. LOC/Ora Base (25, 30, 35, 40, 43, 50)

**Base:** 43,0 LOC/ora (media corretta con migrations/seeders)

| LOC/Ora | LOC 25.000 | LOC 28.000 | LOC 30.000 |
|---------|------------|------------|------------|
| **25** | 1.000 ore | 1.120 ore | 1.200 ore |
| **30** | 833 ore | 933 ore | 1.000 ore |
| **35** | 714 ore | 800 ore | 857 ore |
| **40** | 625 ore | 700 ore | 750 ore |
| **43** | 581 ore | 651 ore | 698 ore |
| **50** | 500 ore | 560 ore | 600 ore |

**Range Ore Finali:**
- **Minimo (50 LOC/ora, 25.000 LOC):** 500 ore
- **Massimo (25 LOC/ora, 30.000 LOC):** 1.200 ore
- **Consigliato (43 LOC/ora, 28.000 LOC):** 651 ore

---

### 3. Pesi Media Ponderata (40/30/30, 50/25/25, 33/33/33)

**Base:** 40% Metodo 1, 30% Metodo 2, 30% Metodo 3

**Metodi:**
- Metodo 1 (LOC/Ora): 651 ore
- Metodo 2 (Complessità): 600 ore
- Metodo 3 (Funzionalità): 495 ore

| Pesi | Calcolo | Ore Finali |
|------|---------|------------|
| **40/30/30** | (651×0.4) + (600×0.3) + (495×0.3) | **591 ore** |
| **50/25/25** | (651×0.5) + (600×0.25) + (495×0.25) | **600 ore** |
| **33/33/33** | (651×0.33) + (600×0.33) + (495×0.33) | **582 ore** |
| **30/40/30** | (651×0.3) + (600×0.4) + (495×0.3) | **584 ore** |

**Range:** 582 - 600 ore (variazione ±3%)

**Conclusione:** I pesi hanno impatto limitato (±3%), la scelta dei pesi non è critica.

---

### 4. Stima LOC L'Altramusica (±20%)

**Base:** 28.000 LOC

| LOC | Ore (43 LOC/ora) | Variazione % |
|-----|------------------|--------------|
| **22.400** (-20%) | 521 ore | -20% |
| **25.200** (-10%) | 586 ore | -10% |
| **28.000** (base) | 651 ore | 0% |
| **30.800** (+10%) | 716 ore | +10% |
| **33.600** (+20%) | 781 ore | +20% |

**Range:** 521 - 781 ore (variazione ±20%)

**Conclusione:** La stima LOC ha impatto lineare sulle ore finali.

---

## Scenario Conservativo

**Assunzioni:**
- LOC: 30.000 (massimo range)
- LOC/Ora: 35 (minimo range conservativo)
- Ore/Giorno: 5 (validata)

**Calcolo:**
- Ore: 30.000 ÷ 35 = **857 ore**

**Costo:** 857 × €60 = **€ 51.420,00**

---

## Scenario Medio

**Assunzioni:**
- LOC: 28.000 (stima originale)
- LOC/Ora: 43 (media corretta)
- Ore/Giorno: 5 (validata)

**Calcolo:**
- Ore: 28.000 ÷ 43 = **651 ore**

**Costo:** 651 × €60 = **€ 39.060,00**

---

## Scenario Ottimistico

**Assunzioni:**
- LOC: 25.000 (minimo range)
- LOC/Ora: 50 (massimo range osservato, escludendo outlier)
- Ore/Giorno: 5 (validata)

**Calcolo:**
- Ore: 25.000 ÷ 50 = **500 ore**

**Costo:** 500 × €60 = **€ 30.000,00**

---

## Confronto Scenari

| Scenario | LOC | LOC/Ora | Ore | Costo | vs Preventivo |
|----------|-----|---------|-----|-------|---------------|
| **Preventivo Originale** | - | - | 980 | € 58.800 | - |
| **Conservativo** | 30.000 | 35 | 857 | € 51.420 | -12.6% |
| **Medio** | 28.000 | 43 | 651 | € 39.060 | -33.5% |
| **Ottimistico** | 25.000 | 50 | 500 | € 30.000 | -49.0% |
| **Analisi Retrospettiva Originale** | - | - | 725 | € 43.500 | -26.0% |

---

## Analisi Robustezza

### Sensibilità per Parametro

| Parametro | Variazione | Impatto Ore | Sensibilità |
|-----------|------------|-------------|-------------|
| **LOC** | ±20% | ±130 ore | **Alta** |
| **LOC/Ora** | ±20% | ±130 ore | **Alta** |
| **Ore/Giorno** | ±20% | 0 ore | **Nessuna** (non usato) |
| **Pesi Media** | ±10% | ±10 ore | **Bassa** |

**Parametri Critici:**
1. **LOC stimati** (impatto lineare)
2. **LOC/Ora** (impatto lineare)

**Parametri Non Critici:**
1. **Ore/Giorno** (non usato nel calcolo finale)
2. **Pesi Media** (impatto minimo ±3%)

---

## Conclusioni

### Range Ore Finali

| Scenario | Ore | Giustificazione |
|---------|-----|-----------------|
| **Minimo** | 500 ore | Scenario ottimistico (25.000 LOC, 50 LOC/ora) |
| **Consigliato** | 651 ore | Scenario medio (28.000 LOC, 43 LOC/ora) |
| **Massimo** | 857 ore | Scenario conservativo (30.000 LOC, 35 LOC/ora) |

### Validazione Analisi Retrospettiva

**Analisi Retrospettiva Originale:** 725 ore  
**Scenario Medio:** 651 ore  
**Differenza:** -74 ore (-10%)

**Conclusione:**
- L'analisi retrospettiva originale (725 ore) è **leggermente conservativa**
- Lo scenario medio (651 ore) è più preciso
- Range accettabile: **650-750 ore**

### Raccomandazione Finale

**Ore Consigliate:** **650-700 ore**
- **Minimo:** 650 ore (€ 39.000)
- **Massimo:** 700 ore (€ 42.000)
- **Consigliato:** 675 ore (€ 40.500)

**vs Preventivo Originale:**
- Riduzione: **-280 ore** (-28.6%)
- Risparmio: **€ 16.800 - € 18.300**

---

**Prossimi Step:**
- Normalizzare per complessità funzionale
- Calcolare metriche validazione incrociata

