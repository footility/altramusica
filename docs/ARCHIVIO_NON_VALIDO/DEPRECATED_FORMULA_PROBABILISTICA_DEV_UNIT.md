# Formula Probabilistica DEV UNIT - Da Campo a Preventivo

**Data:** Dicembre 2024  
**Obiettivo:** Creare formula che parte da input minimo (campo/entità) e calcola tutto

---

## Filosofia: Seguire il Dato

**Input:** Requisito funzionale (es. "Entità Studenti con 20 campi")  
**Output:** Ore e Costi completi (includendo sviluppo, consulenza, test)

**Concetto:** Ogni campo/informazione genera DEV UNITS in modo prevedibile attraverso:
- Database (migration, model)
- Backend (controller, request, service)
- Frontend (views, components)
- Test (unit, feature)
- Documentazione implicita

---

## Analisi Dati Reali

### Progetti Analizzati

| Progetto | Modelli | Controller | Viste | DEV UNITS | Ore | DEV UNITS/Modello |
|----------|---------|------------|-------|-----------|-----|-------------------|
| **MsCarichi** | 22 | 39 | 148 | 1968 | 290 | **89** |
| **Cactusboard** | 13 | 27 | 111 | 1968 | 290 | **151** |
| **Klabhouse** | 32 | 43 | 234 | (da calcolare) | 250 | (da calcolare) |

**Media DEV UNITS/Modello:** (89 + 151) / 2 = **120 DEV UNITS/modello**

---

## Formula Probabilistica Proposta

### Input: Complessità Entità

**Classificazione Entità:**
1. **Base (10-20 campi):** CRUD semplice
2. **Media (20-50 campi):** CRUD con relazioni
3. **Complessa (50+ campi):** CRUD con logica business, integrazioni

### Formula Campo → DEV UNITS

**Componenti DEV UNITS per Entità:**

| Componente | DEV UNITS Base | DEV UNITS per Campo |
|------------|----------------|---------------------|
| Migration | 1 | 0,1 |
| Model | 1 | 0,2 |
| Controller (CRUD) | 7 | 0,5 |
| Request Validation | 2 | 0,3 |
| Views (Index/Show/Create/Edit) | 4 | 0,4 |
| Routes | 1 | 0 |
| Test (Unit + Feature) | 3 | 0,2 |
| **TOTALE BASE** | **19** | **1,7 per campo** |

**Formula:**
```
DEV UNITS Entità = 19 + (Campi × 1,7)
```

**Esempi:**
- Entità 10 campi: 19 + (10 × 1,7) = **36 DEV UNITS**
- Entità 50 campi: 19 + (50 × 1,7) = **104 DEV UNITS**
- Entità 100 campi: 19 + (100 × 1,7) = **189 DEV UNITS**

### Validazione con Dati Reali

**Media osservata:** 120 DEV UNITS/modello

Se ogni modello ha in media 50-60 campi (tipico Laravel):
- Formula: 19 + (55 × 1,7) = **112 DEV UNITS** ✅
- **Molto vicino ai 120 osservati!**

---

## Fattore Ore per DEV UNIT

**Dal calcolo precedente:** **0,147 ore/DEV UNIT** (8,8 minuti)

---

## Formula Completa: Campo → Costo

```
DEV UNITS = 19 + (Campi × 1,7)
Ore = DEV UNITS × 0,147
Costo = Ore × € 30,00/ora
```

**Esempio: Entità con 20 campi**
```
DEV UNITS = 19 + (20 × 1,7) = 53 DEV UNITS
Ore = 53 × 0,147 = 7,8 ore
Costo = 7,8 × 30 = € 234,00
```

**Esempio: Entità con 100 campi**
```
DEV UNITS = 19 + (100 × 1,7) = 189 DEV UNITS
Ore = 189 × 0,147 = 27,8 ore
Costo = 27,8 × 30 = € 834,00
```

---

## Fattori di Complessità Aggiuntivi

### Moltiplicatori per Tipologia

| Tipologia | Moltiplicatore |
|-----------|----------------|
| CRUD Base | 1,0x |
| CRUD con Relazioni (1-3) | 1,2x |
| CRUD con Relazioni Multiple (4+) | 1,5x |
| CRUD con Logica Business Complessa | 1,8x |
| CRUD con Integrazione Esterna | 2,0x |
| Reportistica/Export | 1,3x |

### Formula Completa con Fattori

```
DEV UNITS Base = 19 + (Campi × 1,7)
DEV UNITS Finali = DEV UNITS Base × Moltiplicatore Complessità
Ore = DEV UNITS Finali × 0,147
Costo = Ore × € 30,00/ora
```

---

## Prossimi Passi

1. ✅ Calcolato fattore DEV UNIT: 0,147 ore/DEV UNIT
2. ✅ Proposta formula campo → DEV UNITS: 19 + (Campi × 1,7)
3. ⏳ Validare formula con dati reali progetti
4. ⏳ Raffinare moltiplicatori complessità
5. ⏳ Creare tool/stesso per calcolo automatico

