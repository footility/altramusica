# Calcolo Fattore DEV UNIT - Tempo Medio Reale

**Data:** Dicembre 2024  
**Obiettivo:** Calcolare il tempo medio reale per DEV UNIT dai progetti completati per creare formula: **Funzionalità → DEV UNITS → Tempo → Costi**

---

## Dati Progetti Completati

### 1. MSCARICHI
- **Ore Reali:** 290 ore (da analisi retrospettiva)
- **DEV UNITS:** 1968 (da commit f7ba8aa)
- **Tempo per DEV UNIT:** 290 ore / 1968 = **0,147 ore** = **8,8 minuti/DEV UNIT**

### 2. CACTUSDASHBOARD
- **Ore Reali:** 290 ore (da analisi retrospettiva)
- **DEV UNITS:** 1968 (commit c46783a: 3936 totali per entrambi, quindi ~1968 ciascuno)
- **Tempo per DEV UNIT:** 290 ore / 1968 = **0,147 ore** = **8,8 minuti/DEV UNIT**

### 3. KLABHOUSE
- **Ore Reali:** 250 ore (da analisi retrospettiva)
- **DEV UNITS:** (da verificare se analizzato)
- **Tempo per DEV UNIT:** (da calcolare)

---

## Calcolo Fattore Multiplicatore Reale

### Media Tempo per DEV UNIT (MsCarichi + Cactusboard)

**Media aritmetica:**
- (8,8 + 8,8) / 2 = **8,8 minuti/DEV UNIT**

**Conversione in ore:**
- 8,8 minuti = **0,147 ore/DEV UNIT**

### Formula Base

```
Ore Reali = DEV UNITS × 0,147 ore
Ore Reali = DEV UNITS × 8,8 minuti / 60
```

### Confronto con Valori Teorici

| Valore | Minuti/DEV UNIT | Ore/DEV UNIT |
|--------|----------------|--------------|
| **Teorico (vecchio)** | 5 min | 0,083 ore |
| **Teorico (commit c46783a)** | 15 min | 0,25 ore |
| **Reale (MsCarichi+Cactusboard)** | **8,8 min** | **0,147 ore** |

**Fattore correttivo vs teorico 5min:** 8,8 / 5 = **1,76x**  
**Fattore correttivo vs teorico 15min:** 8,8 / 15 = **0,59x**

---

## Formula Completa: Funzionalità → DEV UNITS → Tempo → Costi

### Passo 1: Stima DEV UNITS per Funzionalità

**Da definire:** Quante DEV UNITS tipicamente per tipo di funzionalità?

Esempi possibili:
- **CRUD base:** 50-100 DEV UNITS
- **CRUD complesso:** 100-200 DEV UNITS
- **Integrazione esterna:** 30-50 DEV UNITS
- **Reportistica:** 40-80 DEV UNITS

### Passo 2: Calcolo Tempo

```
Ore = DEV UNITS Totali × 0,147 ore/DEV UNIT
```

### Passo 3: Calcolo Costi

```
Costo = Ore × Tariffa Oraria (€ 30,00/ora)
```

---

## Prossimi Passi

1. ✅ Calcolato fattore reale: **0,147 ore/DEV UNIT** (8,8 minuti)
2. ⏳ Verificare DEV UNITS per Klabhouse (se analizzato)
3. ⏳ Verificare DEV UNITS per Footility (se analizzato)
4. ⏳ Creare tabella di conversione: Funzionalità → Range DEV UNITS
5. ⏳ Validare formula completa su progetti esistenti

