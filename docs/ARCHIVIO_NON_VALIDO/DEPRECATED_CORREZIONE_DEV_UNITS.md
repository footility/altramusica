# Correzione Analisi DEV UNITS

**Data:** Dicembre 2024  
**Problema Identificato:** I dati DEV UNITS erano errati (stesso valore per progetti diversi)

---

## Problema Trovato

Nei documenti precedenti era indicato:
- **MsCarichi:** 1968 DEV UNITS
- **Cactusboard:** 1968 DEV UNITS

**Questo è impossibile!** Due progetti diversi non possono avere lo stesso identico numero di DEV UNITS.

### Origine Errore

Nel commit `c46783a` di Footility:
- "Totale: 3936 DEV UNITS analizzate e 90 attività create"
- Questo totale (3936) è stato erroneamente diviso a metà (1968 ciascuno)

Nel commit `f7ba8aa`:
- "106 attività MsCarichi create (1968 DEV UNITS)"
- Questo conferma che 1968 è solo per MsCarichi, non per entrambi

---

## Dati Confermati

### MsCarichi
- **DEV UNITS:** 1968 (confermato dal commit f7ba8aa: "106 attività MsCarichi create (1968 DEV UNITS)")
- **Ore:** 290 ore
- **Tempo per DEV UNIT:** 290 / 1968 = **0,147 ore** = **8,8 minuti/DEV UNIT**

### Cactusboard
- **DEV UNITS:** **SCONOSCIUTO** (non confermato)
- **Ore:** 290 ore
- **Possibile valore:** Se il totale 3936 è corretto, allora 3936 - 1968 = **1968 DEV UNITS**
- **Ma questo è sospetto!** Due progetti diversi non dovrebbero avere lo stesso valore

---

## Problema

Il commit `c46783a` dice "Totale: 3936 DEV UNITS analizzate" ma non specifica come sono divisi tra i due progetti.

**Ipotesi possibili:**
1. MsCarichi: 1968, Cactusboard: 1968 (coincidenza sospetta)
2. MsCarichi: 1968, Cactusboard: 1968 + altri progetti = 3936
3. I dati DEV UNITS nel commit sono errati o incompleti

---

## Soluzione Proposta

**Opzione 1:** Usare solo MsCarichi (dato confermato)
- 1968 DEV UNITS / 86 campi = **22,9 DEV UNITS/campo**
- 290 ore / 86 campi = **3,37 ore/campo**

**Opzione 2:** Ricalcolare DEV UNITS per Cactusboard usando metodo alternativo
- Contare componenti reali (models, controllers, views, ecc.)
- Stimare DEV UNITS basandosi su struttura progetto

**Opzione 3:** Usare LOC invece di DEV UNITS
- MsCarichi: 9.653 LOC / 86 campi = **112 LOC/campo**
- Cactusboard: 7.368 LOC / 92 campi = **80 LOC/campo**
- Media: **96 LOC/campo**

---

## Prossimi Passi

1. ✅ Confermato: MsCarichi ha 1968 DEV UNITS
2. ⏳ Verificare valore reale Cactusboard (se possibile)
3. ⏳ Decidere se usare solo MsCarichi o ricalcolare
4. ⏳ Aggiornare formula Campo → DEV UNITS con dati corretti

