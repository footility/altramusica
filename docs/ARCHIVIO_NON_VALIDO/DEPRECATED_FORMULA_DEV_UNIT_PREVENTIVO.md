# Formula DEV UNIT per Preventivo - L'Altramusica

**Data:** Dicembre 2024  
**Obiettivo:** Creare formula definitiva per preventivo basata su DEV UNITS

---

## Fattore Calcolato da Progetti Reali

**Tempo medio per DEV UNIT:** **0,147 ore** (8,8 minuti)  
**Fonte:** Media MsCarichi + Cactusboard (290 ore / 1968 DEV UNITS ciascuno)

---

## Formula Base

```
Ore = DEV UNITS × 0,147 ore/DEV UNIT
Costo = Ore × € 30,00/ora
```

**Esempio:**
- 1000 DEV UNITS × 0,147 = 147 ore
- 147 ore × 30€/ora = € 4.410,00

---

## Prossima Sfida: Stima DEV UNITS da Funzionalità

Per completare la formula **Funzionalità → DEV UNITS → Tempo → Costi**, dobbiamo:

1. **Analizzare progetti esistenti** per capire quante DEV UNITS per tipo di funzionalità
2. **Creare tabella di conversione** funzionalità → range DEV UNITS
3. **Validare** contro progetti completati

---

## Domande Aperte

1. Quante DEV UNITS per un CRUD base?
2. Quante DEV UNITS per un CRUD complesso con relazioni?
3. Quante DEV UNITS per integrazione esterna (API)?
4. Come varia in base alla complessità?

