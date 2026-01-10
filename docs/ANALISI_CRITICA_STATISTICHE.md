# Analisi Critica Statistiche e Metodologia

**Data:** Dicembre 2024  
**Obiettivo:** Valutare validità statistiche e metodologia per stima L'Altramusica

---

## Problemi Identificati

### 1. Variabilità Tariffe Reali

**Dati:**
- mscarichi: 22,41€/h
- cactusdashboard: 18,97€/h
- klabhouse: 30,00€/h
- czservizi: 25,00€/h
- **Range:** 18,97€ - 30,00€ (variazione 58%)

**Problema:**
- Variabilità alta (58%) indica progetti diversi o condizioni diverse
- Media semplice (24,10€/h) potrebbe non essere rappresentativa

**Causa Possibile:**
- Progetti diversi (nuovo vs migrazione vs evoluzione)
- Periodi diversi (esperienza crescente?)
- Condizioni contrattuali diverse

---

### 2. Disallineamento Tariffe Dichiarate vs Reali

**Tariffe Dichiarate:**
- Spot: 50€/h
- Progetto: 200€/g (40€/h) o 150€/g (30€/h)

**Tariffe Reali:**
- Media: 24,10€/h
- Range: 18,97€ - 30,00€/h

**Problema:**
- Tariffa spot (50€/h) non corrisponde a nessun progetto reale
- Tariffa progetto 200€/g (40€/h) è più alta della media reale
- Tariffa progetto 150€/g (30€/h) è più vicina ma ancora sopra la media

**Implicazione:**
- Le tariffe dichiarate sono "di listino" ma non corrispondono ai costi reali
- Potrebbero essere tariffe "target" o "massime" non applicate

---

### 3. Metodo Stima Ore da Commit

**Assunzione:**
- 5 ore/giorno per giorni con commit
- Calcolo: giorni con commit × 5 ore

**Problemi:**
- Non considera intensità lavorativa reale
- Alcuni giorni potrebbero essere solo fix veloci (1-2 ore)
- Altri giorni potrebbero essere sessioni lunghe (6-8 ore)
- Gap temporali non considerati

**Validazione:**
- Commit/giorno varia: 2,7 (cactus) - 9,7 (klabhouse)
- Intensità diversa ma stesso calcolo ore

**Conclusione:**
- Metodo approssimativo ma accettabile per stime
- Potrebbe sottostimare o sovrastimare del 20-30%

---

### 4. Differenze Tra Progetti

**Tipi Progetti:**
- **mscarichi/cactusdashboard:** Progetti nuovi, sviluppo da zero
- **klabhouse:** Migrazione CodeIgniter → Laravel (refactoring)
- **czservizi:** Progetto nuovo, più recente

**Complessità:**
- LOC variano: 7.368 (cactus) - 30.000 (footility)
- Ratio LOC/Ora varia: 25,4 - 77,9 LOC/ora

**Problema:**
- Progetti non comparabili direttamente
- Migrazione (klabhouse) ha logica diversa da nuovo sviluppo
- Complessità funzionale non normalizzata

---

## Cosa Ha Senso

### ✅ Analisi LOC per Complessità

**Perché funziona:**
- LOC è indicatore di complessità
- Confronto progetti simili (Laravel) è valido
- Range 20.000-30.000 LOC per L'Altramusica è ragionevole

**Limiti:**
- LOC finali vs lavoro effettivo (già analizzato)
- Complessità funzionale non sempre proporzionale a LOC

**Conclusione:**
- **MANTENERE** - Utile per stimare complessità

---

### ✅ Tariffe Reali vs Dichiarate

**Perché funziona:**
- Dati reali sono più affidabili delle tariffe dichiarate
- Mostra gap tra "listino" e "realtà"
- Utile per capire quale tariffa applicare

**Limiti:**
- Variabilità alta (18-30€/h)
- Dipende da condizioni contrattuali

**Conclusione:**
- **MANTENERE** - Utile per capire tariffe effettive

---

### ✅ Confronto Progetti Simili

**Perché funziona:**
- Tutti progetti Laravel
- Pattern simili (CRUD, autenticazione, etc.)
- Stessa tecnologia stack

**Limiti:**
- Progetti diversi (nuovo vs migrazione)
- Periodi diversi (esperienza crescente?)

**Conclusione:**
- **MANTENERE** ma con cautela

---

## Cosa Non Ha Senso

### ❌ Usare Tariffa Spot (50€/h)

**Problema:**
- Non corrisponde a nessun progetto reale
- Sovrastima costi del 100%+
- Probabilmente tariffa "di listino" non applicata

**Conclusione:**
- **BUTTARE** per stime reali
- Mantenere solo come riferimento "massimo"

---

### ❌ Media Semplice Tariffe

**Problema:**
- Ignora differenze tra progetti
- Klabhouse (migrazione) ha tariffa diversa
- Non considera evoluzione nel tempo

**Conclusione:**
- **RIVEDERE** - Usare media ponderata o escludere outlier

---

### ❌ Estrapolazione Diretta

**Problema:**
- L'Altramusica è progetto nuovo (come mscarichi/cactus)
- Ma complessità potrebbe essere diversa
- Non considera fattori specifici (integrazioni, etc.)

**Conclusione:**
- **RIVEDERE** - Applicare fattori correttivi

---

## Cosa Rivedere

### 1. Metodo Stima Ore

**Problema Attuale:**
- Ore da commit (giorni × 5h) è approssimativo
- Non considera intensità reale

**Proposta:**
- Mantenere metodo ma con range (min-max)
- Considerare commit/giorno come indicatore intensità
- Aggiungere fattore correttivo ±20%

---

### 2. Applicazione Tariffe

**Problema Attuale:**
- Tariffe dichiarate non corrispondono a realtà
- Media semplice non rappresentativa

**Proposta:**
- Usare tariffa progetto 150€/g (30€/h) per progetti standard
- Usare tariffa reale media (24€/h) come "minimo realistico"
- Considerare range 24-30€/h invece di valore fisso

---

### 3. Normalizzazione Progetti

**Problema Attuale:**
- Progetti non comparabili (nuovo vs migrazione)
- Complessità non normalizzata

**Proposta:**
- Separare progetti nuovi da migrazioni
- Normalizzare per complessità funzionale
- Considerare solo progetti simili a L'Altramusica (nuovo sviluppo)

---

## Raccomandazioni per L'Altramusica

### Stima Ore

**Metodo LOC (validato):**
- LOC stimati: 28.000 LOC
- LOC/Ora media: 59,9 LOC/ora (4 progetti)
- **Ore base:** 28.000 ÷ 59,9 = **467 ore**

**Con fattori correttivi:**
- Complessità: +25% = +117 ore
- Integrazioni: +12% = +56 ore
- Esperienza: -12% = -56 ore
- **Totale:** 584 ore

**Range conservativo:**
- Minimo: 500 ore
- Massimo: 700 ore
- **Consigliato:** **600 ore**

---

### Applicazione Tariffe

**Opzioni:**
1. **Tariffa progetto 150€/g (30€/h):** 600h × 30€ = **18.000€**
2. **Tariffa reale media (24€/h):** 600h × 24€ = **14.400€**
3. **Range:** 14.400€ - 18.000€

**Raccomandazione:**
- Usare **tariffa progetto 150€/g** (più conservativa)
- **Costo stimato: 18.000€** (600h × 30€/h)

---

## Cosa Mantenere/Buttare/Rivedere

### ✅ MANTENERE

1. **Analisi LOC** - Utile per complessità
2. **Tariffe reali** - Dati reali affidabili
3. **Confronto progetti** - Pattern utili
4. **Metodo stima ore da LOC** - Validato

### ❌ BUTTARE

1. **Tariffa spot (50€/h)** - Non corrisponde a realtà
2. **Media semplice tariffe** - Non rappresentativa
3. **Estrapolazione diretta senza fattori** - Troppo semplificata

### 🔄 RIVEDERE

1. **Metodo stima ore** - Aggiungere range e fattori correttivi
2. **Applicazione tariffe** - Usare range invece di valore fisso
3. **Normalizzazione progetti** - Separare nuovi da migrazioni
4. **Fattori correttivi** - Rivedere pesi e percentuali

---

## Conclusione

**Le statistiche hanno senso MA:**

1. ✅ **Utili per capire complessità** (analisi LOC)
2. ✅ **Utili per capire tariffe reali** (dati progetti)
3. ⚠️ **Non usare direttamente** senza fattori correttivi
4. ⚠️ **Considerare differenze** tra progetti

**Per L'Altramusica:**
- **Ore:** 600 ore (range 500-700)
- **Tariffa:** 30€/h (progetto 150€/g)
- **Costo:** **18.000€** (range 14.400€ - 21.000€)

**Documentazione da mantenere:**
- Analisi LOC (complessa ma utile)
- Tariffe reali (dati reali)
- Metodologia (riveduta con fattori correttivi)

**Documentazione da rivedere:**
- Applicazione tariffe (usare range)
- Fattori correttivi (rivedere pesi)
- Normalizzazione progetti (separare tipi)

