# Analisi Concetto DEV UNIT

**Data Analisi:** Dicembre 2024  
**Progetto:** Footility  
**Obiettivo:** Comprendere il concetto di DEV UNIT per applicarlo al preventivo L'Altramusica

---

## Cos'è una DEV UNIT?

Una **DEV UNIT** è un'**unità di codice** estratta automaticamente dal progetto attraverso un sistema di parsing.

### Caratteristiche

- **Estratta automaticamente** da file sorgente del progetto
- **Rappresenta elementi di codice** specifici (metodi, funzioni, classi, viste, ecc.)
- **Calcolo tempo:** 5 minuti per ogni DEV UNIT
- **Struttura dati:**
  - `name`: Nome dell'unità
  - `type`: Tipo (es. controller_action, model_method, view, ecc.)
  - `entity`: Entità associata
  - `source_file_path`: Percorso file sorgente
  - `tree`: Albero gerarchico (es. Entities/Dashboard/controller/index)
  - `code_signature`: Firma del codice

### Driver Supportati

1. **Laravel Driver:** Estrae elementi da progetti Laravel
2. **CodeIgniter Driver:** Estrae elementi da progetti CodeIgniter
   - Controller actions
   - Model methods
   - Library methods
   - Helper functions
   - CI Load statements

### Calcolo Durata e Costi

**Formula:**
```
Durata (ore) = (Numero DEV UNITS × 5 minuti) / 60
Costo = Durata (ore) × Tariffa Oraria
```

**Esempio:**
- 100 DEV UNITS = 100 × 5 min = 500 minuti = 8,33 ore
- A 30€/ora = 8,33 × 30 = € 250,00

---

## Commit Chiave Footility

1. **f7ba8aa** - CHECKPOINT: Sistema Attività-DEVUNIT-Calendario Completato
   - Sistema Matriosca: Progetto → Attività → DEV UNITS
   - 1968 DEV UNITS per MsCarichi
   - Raggruppamento per data+orario preciso

2. **c46783a** - FEAT: Sistema Matriosca Completato
   - 3936 DEV UNITS analizzate
   - 90 attività create automaticamente
   - Supporto CodeIgniter e Laravel

3. **fa6c04e** - Implementa filtro 'Tutti gli anni'
   - Calcolo totali basato su 5 minuti per DEV UNIT
   - Filtri per anno, settimana, categoria

---

## Applicazione al Preventivo L'Altramusica

### Domande da Porsi

1. **Quante DEV UNITS** ci sono in un progetto Laravel tipo L'Altramusica?
2. **Come calcolare** le DEV UNITS per funzionalità?
3. **Come validare** il calcolo ore basato su DEV UNITS vs analisi retrospettiva?

### Possibile Formula

```
Ore Stimato = (DEV UNITS Totali × 5 minuti) / 60
```

Ma potrebbe essere più complessa considerando:
- Complessità funzionale
- Integrazioni esterne
- Esperienza sviluppatore
- Fattori correttivi

---

## Prossimi Passi

1. ✅ Identificato concetto DEV UNIT
2. ⏳ Analizzare progetti simili per capire rapporto DEV UNITS / Ore reali
3. ⏳ Sviluppare formula preventivo basata su DEV UNITS
4. ⏳ Validare formula con progetti esistenti

