# Executive Summary - Calibrazione Coefficiente Produttività

**Data:** Dicembre 2024  
**Analista:** Software Estimation Engineer Senior  
**Metodologia:** COSMIC Function Points / DEV UNIT  
**Stato:** ⚠️ CALIBRAZIONE PARZIALE

---

## RISULTATO PRINCIPALE

### Coefficiente Produttività Calibrato

**Valore Verificato:**
- **0,147 ore per DEV UNIT** (8,8 minuti)
- Basato su: MsCarichi (1968 DEV UNITS, 290 ore reali)

**Affidabilità:** ⚠️ **BASSA** - Campione singolo

**Range Stimato (Non Verificato):**
- Minimo: 0,091 ore/DEV UNIT
- Massimo: 0,263 ore/DEV UNIT
- Variabilità: 2,9x (molto alta)

---

## DATI DISPONIBILI

### Progetti Analizzati

| Progetto | DEV UNITS | Ore | Coefficiente | Utilizzabile |
|----------|-----------|-----|--------------|--------------|
| **MsCarichi** | 1968 ✅ | 290 | 0,147 | ✅ Sì |
| **Cactusboard** | ❓ | 290 | ❓ | ❌ No |
| **Klabhouse** | ❓ | 250 | ❓ | ❌ No |
| **CZServizi** | ❓ | 120 | ❓ | ❌ No |
| **Footility** | ❓ | 385 | ❓ | ❌ No |

**Tasso Utilizzabilità:** 20% (1 su 5 progetti)

---

## METODOLOGIA APPLICATA

### Approccio COSMIC/DEV UNIT

**DEV UNIT:** Astrazione data-centrica che include:
- Analisi requisiti
- Progettazione dati
- Implementazione (model, controller, view, migration, route, test)
- Validazione
- Documentazione implicita

**Mappatura COSMIC:**
- **Entry:** Input dati da utente esterno
- **Exit:** Output dati verso utente esterno
- **Read:** Lettura da storage persistente
- **Write:** Scrittura in storage persistente

**Separazione SIZE vs COST:**
- SIZE = DEV UNITS (dimensione funzionale)
- COST = Ore reali (effort sostenuto)
- Coefficiente = COST / SIZE

---

## RISULTATI ANALISI

### 1. Coefficiente Base

**0,147 ore/DEV UNIT** (verificato su MsCarichi)

**Validazione:**
- ✅ Basato su dato reale verificato
- ✅ Metodologia COSMIC standardizzata
- ❌ Campione singolo (non statisticamente valido)
- ❌ Range non calcolabile

### 2. Regole Empiriche Derivate

| Regola | Formula | Affidabilità |
|--------|---------|--------------|
| **Coefficiente Base** | Ore = DEV UNITS × 0,147 | ⚠️ Bassa |
| **Campo → DEV UNITS** | DEV UNITS = Campi × 22,9 | ⚠️ Media |
| **Fattore Granularità** | Reale = Stimato × 4,58 | ⚠️ Bassa |
| **Entità → DEV UNITS** | DEV UNITS = Entità × 55 | ⚠️ Bassa |

### 3. Pattern Identificati

**Sottostima Sistematica:**
- Stima manuale DEV UNITS sottostima del 357% (430 vs 1968)
- Sistema Footility conta DEV UNITS molto più granulari
- Fattore correzione: 4,58x

**Variabilità Alta:**
- Range stimato: 0,091 - 0,263 ore/DEV UNIT (2,9x)
- Indica necessità normalizzazione per complessità
- O stime DEV UNITS imprecise

---

## LIMITI CRITICI

### 1. Campione Insufficiente

- Solo 1 progetto con DEV UNITS verificato
- Impossibile calcolare deviazione standard
- Impossibile validare range

### 2. Dati Aggregati

- Effort non separato per funzionalità
- Impossibile analisi per classe complessità
- Impossibile identificare pattern sottostima/sovrastima specifici

### 3. Dati Mancanti

- 4 progetti su 5 senza DEV UNITS verificati
- CZServizi: dati funzionali completamente mancanti
- Impossibile cross-validation

### 4. Variabilità Non Spiegata

- Range stimato molto ampio (2,9x)
- Necessaria normalizzazione per complessità (non validata)
- O stime DEV UNITS imprecise

---

## RACCOMANDAZIONI

### Uso Coefficiente

**✅ USARE quando:**
- Progetto molto simile a MsCarichi
- Stima rapida con margine errore ±50% accettabile
- Validazione prevista durante sviluppo

**❌ NON USARE quando:**
- Progetto molto diverso da MsCarichi
- Stima precisa richiesta (<20% errore)
- Difesa formale senza validazione

**⚠️ USARE CON CAUTELA:**
- Applicare fattori correttivi non validati
- Estrapolare a progetti molto diversi
- Usare range stimato senza validazione

### Formula Raccomandata

**Per Progetti Simili a MsCarichi:**
```
DEV UNITS = (Entità × 55) + (Funzionalità Avanzate × 20) + (Infrastruttura × 30)
Ore = DEV UNITS × 0,147 ore/DEV UNIT
Margine Errore: ±50%
```

**Per Progetti Diversi:**
```
DEV UNITS = (Entità × 55) + (Funzionalità Avanzate × 20) + (Infrastruttura × 30)
Ore Base = DEV UNITS × 0,147 ore/DEV UNIT
Ore Corrette = Ore Base × Fattore Complessità (0,8x - 1,2x)
Margine Errore: ±70%
```

---

## PROSSIMI PASSI PRIORITARI

### URGENTE

1. **Estrarre DEV UNITS reali da altri progetti**
   - Cactusboard, Klabhouse, Footility (se disponibili in Footility)
   - Aumentare campione da 1 a 4+ progetti

2. **Separare effort per funzionalità**
   - Analisi commit per area funzionale
   - Tracciamento ore per feature
   - Documentazione preventivi ex-ante

### IMPORTANTE

3. **Standardizzare definizione DEV UNIT**
   - Documentare cosa include esattamente 1 DEV UNIT
   - Validare conteggio manuale vs automatico

4. **Validare su progetto nuovo**
   - Applicare a L'Altramusica
   - Tracciare DEV UNITS reali durante sviluppo
   - Confrontare stima vs reale
   - Aggiornare coefficiente iterativamente

---

## CONCLUSIONE

### Stato Calibrazione

**⚠️ PARZIALE - DATI INSUFFICIENTI**

**Coefficiente Disponibile:**
- **0,147 ore/DEV UNIT** (verificato su 1 progetto)

**Affidabilità:**
- ⚠️ BASSA (campione singolo)
- ⚠️ Range non calcolabile
- ⚠️ Impossibile validazione statistica

**Uso:**
- ✅ Accettabile per stime rapide con margine errore ±50%
- ❌ Non difendibile formalmente senza validazione aggiuntiva
- ⚠️ Richiede validazione su progetto nuovo

### Difendibilità

**Punti a Favore:**
- Metodologia COSMIC standardizzata
- Separazione SIZE/COST corretta
- Dato reale verificato
- Trasparenza metodologica

**Punti Critici:**
- Campione singolo
- Range non calcolabile
- Variabilità alta
- Impossibile validazione statistica

**Raccomandazione Difesa:**
- Presentare come "coefficiente provvisorio"
- Specificare margine errore ±50%
- Proporre validazione iterativa
- Offrire revisione post-validazione

---

**Documento generato:** Dicembre 2024  
**Completamento Calibrazione:** 20% (1 progetto verificato su 5)  
**Prossimo Aggiornamento:** Dopo estrazione DEV UNITS da altri progetti

