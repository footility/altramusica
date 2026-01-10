# Tabella Calibrazione Finale - Coefficiente Produttività

**Data:** Dicembre 2024  
**Metodologia:** COSMIC Function Points / DEV UNIT  
**Stato:** ⚠️ PARZIALE - Dati insufficienti

---

## TABELLA PRINCIPALE: COEFFICIENTE PRODUTTIVITÀ

| Metrica | Valore | Affidabilità | Fonte | Note |
|---------|--------|--------------|-------|------|
| **Ore per DEV UNIT (verificato)** | **0,147 ore** | ⚠️ Bassa | MsCarichi (1968 DEV UNITS, 290 ore) | Unico dato verificato |
| **Minuti per DEV UNIT** | **8,8 minuti** | ⚠️ Bassa | Calcolato da ore/DEV UNIT | - |
| **Ore per CFP** | **0,147 ore** | ⚠️ Bassa | Assumendo 1 DEV UNIT = 1 CFP | Da validare |
| **Range Minimo (stimato)** | 0,091 ore | ❌ Molto Bassa | Klabhouse (stima non verificata) | Non affidabile |
| **Range Massimo (stimato)** | 0,263 ore | ❌ Molto Bassa | Cactusboard (stima non verificata) | Non affidabile |
| **Media Range (stimato)** | 0,182 ore | ❌ Molto Bassa | Media 3 progetti stimati | Non affidabile |
| **Deviazione Standard** | ❓ Non calcolabile | ❌ | Campione insufficiente | - |

---

## TABELLA PROGETTI ANALIZZATI

| Progetto | DEV UNITS Reali | DEV UNITS Stimati | Ore Reali | Ore/DEV UNIT | Affidabilità | Note |
|----------|----------------|-------------------|-----------|--------------|--------------|------|
| **MsCarichi** | **1968** ✅ | 430 | 290 | **0,147** | ✅ Alta | Dato verificato |
| **Cactusboard** | ❓ Sconosciuto | 241 | 290 | ❓ | ❌ Bassa | DEV UNITS non disponibili |
| **Klabhouse** | ❓ Sconosciuto | 602 | 250 | ❓ | ❌ Bassa | DEV UNITS non disponibili |
| **CZServizi** | ❓ Sconosciuto | ❓ | 120 | ❓ | ❌ Bassa | Dati funzionali mancanti |
| **Footility** | ❓ Sconosciuto | ❓ | 385 | ❓ | ❌ Bassa | Non analizzato |

**Progetti Utilizzabili per Calibrazione:** 1 su 5 (20%)

---

## TABELLA REGOLE EMPIRICHE

| Regola | Formula | Validità | Affidabilità | Uso Consigliato |
|--------|---------|----------|--------------|-----------------|
| **Coefficiente Base** | Ore = DEV UNITS × 0,147 | 1 progetto | ⚠️ Bassa | Progetti simili a MsCarichi |
| **Campo → DEV UNITS** | DEV UNITS = Campi × 22,9 | 86 campi, 1 progetto | ⚠️ Media | Stima rapida dimensione |
| **Fattore Granularità** | Reale = Stimato × 4,58 | 1 confronto | ⚠️ Bassa | Correggere stime manuali |
| **Entità → DEV UNITS** | DEV UNITS = Entità × 55 | Stima manuale | ⚠️ Bassa | Stima rapida dimensione |
| **Complessità → Coefficiente** | Vedi tabella sotto | Stime non verificate | ❌ Molto Bassa | Non usare |

---

## TABELLA COEFFICIENTI PER COMPLESSITÀ (NON VALIDATI)

| Classe Complessità | Fattore Correttivo | Ore/DEV UNIT Corretto | Affidabilità | Note |
|-------------------|-------------------|----------------------|--------------|------|
| **CRUD Semplice** | ❓ | ❓ | ❌ | Dati non disponibili |
| **CRUD Media** | 1,0x | 0,147 | ⚠️ Bassa | Baseline MsCarichi |
| **CRUD Complesso** | ❓ | ❓ | ❌ | Dati non disponibili |
| **Workflow** | ❓ | ❓ | ❌ | Dati non disponibili |
| **Integrazione Esterna** | ❓ | ❓ | ❌ | Dati non disponibili |
| **Sicurezza** | ❓ | ❓ | ❌ | Dati non disponibili |
| **UI Complessa** | ❓ | ❓ | ❌ | Dati non disponibili |

**Nota:** Coefficienti per classe non calcolabili per mancanza dati effort separati.

---

## TABELLA LIMITI AFFIDABILITÀ

| Limite | Descrizione | Impatto | Mitigazione |
|--------|-------------|---------|-------------|
| **Campione Insufficiente** | Solo 1 progetto verificato | ⚠️ Alto | Estrarre DEV UNITS da altri progetti |
| **Range Non Calcolabile** | Impossibile calcolare deviazione | ⚠️ Alto | Aumentare campione |
| **Dati Aggregati** | Effort non separato per funzionalità | ⚠️ Medio | Tracciare effort per feature |
| **Variabilità Alta** | Range stimato 0,091-0,263 (2,9x) | ⚠️ Alto | Normalizzare per complessità |
| **Definizione DEV UNIT** | Discrepanza stima vs reale (4,58x) | ⚠️ Medio | Standardizzare definizione |

---

## TABELLA APPLICAZIONE PRATICA

### Scenario: Stima Nuovo Progetto

| Passo | Metodo | Coefficiente | Affidabilità | Margine Errore |
|-------|--------|--------------|--------------|----------------|
| **1. Stima DEV UNITS** | Contare entità/campi/funzionalità | - | ⚠️ Media | ±30% |
| **2. Correzione Granularità** | Applicare fattore 4,58x | 4,58x | ⚠️ Bassa | ±20% |
| **3. Calcolo Ore** | DEV UNITS × 0,147 | 0,147 ore/DEV UNIT | ⚠️ Bassa | ±50% |
| **4. Fattore Complessità** | Applicare correttivo (se diverso) | 0,8x - 1,2x | ❌ Molto Bassa | ±30% |
| **5. Margine Finale** | Aggiungere buffer | - | - | **±70% totale** |

**Conclusione:** Margine errore totale molto alto (±70%) indica bassa affidabilità.

---

## RACCOMANDAZIONI USO

### ✅ USARE Coefficiente Quando:

1. Progetto molto simile a MsCarichi (Laravel, CRUD, complessità media)
2. Stima rapida con margine errore accettabile (±50%)
3. Validazione prevista durante sviluppo
4. Coefficiente come baseline, non valore assoluto

### ❌ NON USARE Coefficiente Quando:

1. Progetto molto diverso da MsCarichi (tecnologie diverse, complessità molto alta/bassa)
2. Stima precisa richiesta (margine errore <20%)
3. Difesa formale davanti a cliente/revisore senza validazione
4. Progetto critico senza margine errore

### ⚠️ USARE CON CAUTELA:

1. Applicare fattore correttivo complessità (non validato)
2. Estrapolare a progetti molto più grandi/piccoli
3. Usare range stimato (0,091-0,263) senza validazione

---

## FORMULA FINALE RACCOMANDATA

### Per Progetti Simili a MsCarichi

```
DEV UNITS = (Entità × 55) + (Funzionalità Avanzate × 20) + (Infrastruttura × 30)
Ore = DEV UNITS × 0,147 ore/DEV UNIT
Costo = Ore × Tariffa Oraria (24-30€/h)
```

**Margine Errore:** ±50% (conservativo)

### Per Progetti Diversi

```
DEV UNITS = (Entità × 55) + (Funzionalità Avanzate × 20) + (Infrastruttura × 30)
Ore Base = DEV UNITS × 0,147 ore/DEV UNIT
Ore Corrette = Ore Base × Fattore Complessità (0,8x - 1,2x)
Costo = Ore Corrette × Tariffa Oraria (24-30€/h)
```

**Margine Errore:** ±70% (molto conservativo)

---

## STATO CALIBRAZIONE: ⚠️ INCOMPLETA

**Completamento:** 20% (1 progetto verificato su 5)

**Prossimi Passi:**
1. Estrarre DEV UNITS reali da altri progetti (80% mancante)
2. Validare su progetto nuovo (L'Altramusica)
3. Aggiornare coefficiente iterativamente

---

**Documento generato:** Dicembre 2024  
**Metodologia:** COSMIC/DEV UNIT data-movement based  
**Limite Affidabilità:** ⚠️ BASSA - Calibrazione incompleta

