# Verifica Stato Analisi Tempi

**Data:** Dicembre 2024  
**Scopo:** Verifica rigorosa di cosa è disponibile e cosa manca per i 4 punti richiesti

---

## 1. TEMPI IPOTIZZATI DA PREVENTIVO

### ✅ Documenti Disponibili

| Documento | Ore | Costo | Note |
|-----------|-----|--------|------|
| `preventivi/preventivo originale.txt` | ❌ Non specificate | € 5.000 | Preventivo iniziale (Aprile 2024) |
| `preventivi/preventivo aggiornato (appunti).txt` | ❌ Non specificate | € 5.000 | Appunti (Dicembre 2025) |
| `preventivi/PREVENTIVO_DETTAGLIATO.md` | **980 ore** | € 58.800 | Tariffa €60/ora |
| `preventivi/PREVENTIVO_TABELLA_FINALE.md` | **852 ore** | € 25.560 | Tariffa €30/ora |

### ⚠️ Problemi Rilevati

- **Due preventivi con ore diverse**: 980h vs 852h
- **Tariffe diverse**: €60/ora vs €30/ora
- **Preventivo originale**: Non specifica ore, solo costo fisso €5.000

### ❓ Cosa Serve

- **Conferma quale preventivo è quello ufficiale** da usare come riferimento
- **Chiarimento sulla tariffa**: €30/ora o €60/ora?
- **Eventuali altri preventivi** non ancora documentati

---

## 2. TEMPI DEDUCIBILI DAI GIT DI PROGETTO

### ✅ Documenti Disponibili

**Fonte principale:** `analisi-di-supporto/REPORT_ANALISI_RETROSPETTIVA.md`

| Progetto | Giorni Lavorativi | Metodo A (Giorni × 5h) | LOC | Metodo B (LOC/30) | Media Ponderata | **Ore Usate in COSMIC** |
|----------|-------------------|------------------------|-----|------------------|-----------------|------------------------|
| **MsCarichi** | 58 | 290h | 9.653 | 322h | 306h | **290h** |
| **CactusBoard** | 58 | 290h | 7.368 | 246h | 268h | **290h** |
| **Klabhouse** | 50 | 250h | 25.787 | 860h | 555h | **250h** |
| **Footility** | 77 | 385h | 30.000 | 1.000h | 631h | ❌ Non in COSMIC |
| **CZServizi** | ❓ **NON DOCUMENTATO** | ❓ | ❓ | ❓ | ❓ | **120h** (fonte sconosciuta) |

### ⚠️ Problemi Rilevati

1. **MsCarichi e CactusBoard hanno STESSI giorni (58)**: Sospetto, potrebbe essere errore
2. **CZServizi**: 120h documentate ma **giorni lavorativi NON documentati**
3. **Footility**: Analizzato ma non incluso nei progetti COSMIC
4. **Validazione 5 ore/giorno**: Documentata in `ANALISI_PATTERN_COMMIT.md` ma serve verifica per CZServizi

### ❓ Cosa Serve

- **Verifica giorni lavorativi CZServizi** da git log
- **Verifica se MsCarichi e CactusBoard hanno davvero 58 giorni** (potrebbe essere errore)
- **Conferma metodologia**: Usare Metodo A (giorni × 5h) o media ponderata?

---

## 3. TEMPI ANALIZZATI DA DEV UNIT

### ✅ Documenti Disponibili

**Fonte principale:** `dev-unit/`

| Progetto | DEV UNIT Reali | DEV UNIT Ipotetiche | Totale DEV UNIT | **Conversione a Ore** |
|----------|----------------|---------------------|-----------------|----------------------|
| **L'Altramusica** | 730 | 710 | **1.440** | ❌ **NON CALCOLATA** |
| **MsCarichi** | ❌ Non calcolate | ❌ | ❌ | ❌ |
| **CactusBoard** | ❌ Non calcolate | ❌ | ❌ | ❌ |
| **Klabhouse** | ❌ Non calcolate | ❌ | ❌ | ❌ |
| **CZServizi** | ❌ Non calcolate | ❌ | ❌ | ❌ |

### ⚠️ Problemi Rilevati

- **Nessuna conversione DEV UNIT → Ore** nei documenti
- **Solo L'Altramusica ha DEV UNIT calcolate** (EX-ANTE)
- **Progetti completati**: DEV UNIT non calcolate retrospettivamente

### ❓ Cosa Serve

- **Coefficiente di conversione DEV UNIT → Ore** (se esiste)
- **Calcolo DEV UNIT retrospettivo** per progetti completati (opzionale)
- **Metodologia di conversione** DEV UNIT → Ore (se disponibile)

---

## 4. TEMPI IPOTIZZATI CON COSMIC

### ✅ Documenti Disponibili

**Fonte principale:** `stime-cosmic/`

| Progetto | CFP | Ore Reali (Osservate) | **Conversione CFP → Ore** |
|----------|-----|------------------------|---------------------------|
| **MsCarichi** | 442 | 290h | ❌ **NON CALCOLATA** |
| **CactusBoard** | 316 | 290h | ❌ **NON CALCOLATA** |
| **Klabhouse** | 514 | 250h | ❌ **NON CALCOLATA** |
| **CZServizi** | 195 | 120h | ❌ **NON CALCOLATA** |
| **L'Altramusica** | 337 | - | ❌ **NON CALCOLATA** |

### ⚠️ Problemi Rilevati

- **Nessuna conversione CFP → Ore** nei documenti
- **Esplicitamente dichiarato**: Nei documenti COSMIC c'è scritto che **NON vengono calcolati coefficienti di produttività**
- **Motivazione**: Campione statistico insufficiente (N < 3 progetti con dati effort completi)

### ❓ Cosa Serve

- **Conferma se si vuole calcolare coefficiente CFP → Ore** (richiede almeno 3 progetti)
- **Metodologia COSMIC**: Se si vuole rispettare il principio di non calcolare produttività con N < 3
- **Eventuali coefficienti esterni** da applicare (se disponibili)

---

## RIEPILOGO STATO

### ✅ COMPLETO

1. **Preventivi documentati** (anche se con valori diversi)
2. **Tempi git per 3 progetti** (MsCarichi, CactusBoard, Klabhouse)
3. **DEV UNIT per L'Altramusica** (EX-ANTE)
4. **COSMIC per tutti i progetti** (SIZE, non produttività)

### ⚠️ INCOMPLETO / DA VERIFICARE

1. **Preventivo ufficiale**: Quale usare? (980h o 852h?)
2. **CZServizi giorni lavorativi**: Non documentati
3. **MsCarichi/CactusBoard**: Stesso numero giorni (58) - verificare se errore
4. **DEV UNIT → Ore**: Nessuna conversione disponibile
5. **COSMIC → Ore**: Nessuna conversione disponibile (volutamente)

### ❌ MANCANTE

1. **Coefficienti di conversione** DEV UNIT → Ore
2. **Coefficienti di conversione** COSMIC → Ore (se si vuole calcolare)
3. **DEV UNIT retrospettive** per progetti completati (opzionale)

---

## PROSSIMI PASSI

1. **Chiedere all'utente** quale preventivo è ufficiale (980h o 852h?)
2. **Verificare giorni lavorativi CZServizi** da git log
3. **Verificare se MsCarichi e CactusBoard hanno davvero 58 giorni** (potrebbe essere errore)
4. **Chiedere se si vuole calcolare conversioni** DEV UNIT → Ore e COSMIC → Ore
5. **Eliminare documenti ridondanti** dopo verifica

---

## DOCUMENTI DA MANTENERE

### Preventivi
- `preventivi/PREVENTIVO_DETTAGLIATO.md` (980h)
- `preventivi/PREVENTIVO_TABELLA_FINALE.md` (852h)
- `preventivi/preventivo originale.txt` (storico)
- `preventivi/preventivo aggiornato (appunti).txt` (storico)

### Git/Tempi Reali
- `analisi-di-supporto/REPORT_ANALISI_RETROSPETTIVA.md` (metodologia calcolo ore)
- `analisi-di-supporto/ANALISI_PATTERN_COMMIT.md` (validazione 5h/giorno)

### DEV UNIT
- `dev-unit/reali/DEV_UNIT_REALI_ODS_LARAVEL.md`
- `dev-unit/ipotetiche/DEV_UNIT_IPOTETICHE_EVOLUZIONE.md`
- `dev-unit/RIEPILOGO_DEV_UNIT.md`
- `dev-unit/CONFRONTO_COSMIC_DEV_UNIT.md`

### COSMIC
- `stime-cosmic/COSMIC_SIZE_SUMMARY.md`
- `stime-cosmic/COSMIC_ANALYSIS_ALTRAMUSCIA.md`
- `stime-cosmic/COSMIC_ANALYSIS_MSCARICHI.md`
- `stime-cosmic/COSMIC_ANALYSIS_CACTUSBOARD.md`
- `stime-cosmic/COSMIC_ANALYSIS_KLABHOUSE.md`
- `stime-cosmic/COSMIC_ANALYSIS_CZSERVIZI.md`
- `stime-cosmic/COSMIC_METHODOLOGY_DECLARATION.md`

---

## DOCUMENTI DA ELIMINARE (Ridondanti/Assunti)

- `analisi-di-supporto/ANALISI_LIMITE_METODOLOGICO_LOC.md` (assunzioni su LOC)
- `analisi-di-supporto/ANALISI_OUTLIER_KLABHOUSE.md` (analisi outlier)
- `analisi-di-supporto/ANALISI_SENSIBILITA.md` (analisi sensibilità)
- `analisi-di-supporto/REPORT_VALIDAZIONE_FINALE.md` (validazione con assunzioni)
- `analisi-di-supporto/METRICHE_VALIDAZIONE_INCROCIATA.md` (validazione con assunzioni)
- `RIEPILOGO_ANALISI_RETROSPETTIVA.md` (duplicato)
- `RIEPILOGO_FINALE.md` (duplicato)
- `RIEPILOGO_LAVORO.md` (duplicato)
- `ANALISI_CRITICA_STATISTICHE.md` (analisi con assunzioni)
- `ANALISI_IA_POTENZIALE_RISPARMIO.md` (analisi con assunzioni)
- `CONTROVERIFICA_IMPLEMENTAZIONE.md` (se non rilevante)
- `PROSSIMI_PASSI_ANALISI.md` (se non rilevante)

---

**Nota:** Questo documento verifica SOLO cosa c'è e cosa manca. **NON fa deduzioni o assunzioni**.


