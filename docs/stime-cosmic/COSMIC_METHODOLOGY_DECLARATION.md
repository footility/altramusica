# Dichiarazione Metodologica COSMIC - Reset Metodologico

**Data:** Dicembre 2024  
**Standard Applicato:** ISO/IEC 19761 - COSMIC Function Points  
**Scopo:** Dichiarazione formale di reset metodologico e standard applicato

---

## 1. DICHIARAZIONE DI RESET

Tutti i documenti precedenti relativi a "calibrazione di produttività" basati su metodologie non-standard (DEV UNIT, fattori correttivi inventati, normalizzazioni arbitrarie) sono stati **invalidati metodologicamente** e archiviati in `docs/ARCHIVIO_NON_VALIDO/`.

**Motivazione:**
- DEV UNIT non è uno standard COSMIC riconosciuto
- Fattori correttivi (es. 4,58x) non sono difendibili scientificamente
- Mescolanza impropria tra SIZE (dimensione funzionale) e COST (effort)
- Coefficienti di produttività derivati da campione insufficiente (N=1)
- Normalizzazioni e conversioni arbitrarie non standard

---

## 2. STANDARD COSMIC APPLICATO

**Standard Ufficiale:** ISO/IEC 19761 - COSMIC Function Points

**Metodologia:**
- **Functional Process:** Unità funzionale identificabile dall'utente come operazione completa e coerente
- **Entry (E):** Movimento dati da utente esterno verso sistema
- **Exit (X):** Movimento dati da sistema verso utente esterno
- **Read (R):** Movimento dati da storage persistente verso sistema
- **Write (W):** Movimento dati da sistema verso storage persistente
- **Functional Size (CFP):** Somma di tutti i movimenti dati (E + X + R + W)

**Regole Applicate:**
- Ogni movimento dati conta esattamente 1 CFP
- Nessuna normalizzazione
- Nessun fattore correttivo
- Nessuna conversione arbitraria
- Nessuna contaminazione con unità di misura non-standard

---

## 3. SEPARAZIONE RIGOROSA SIZE vs COST

**SIZE (Dimensione Funzionale):**
- Misurata in CFP (COSMIC Function Points)
- Indipendente da tecnologia, linguaggio, complessità implementativa
- Basata esclusivamente su movimenti dati funzionali
- Documentata in `COSMIC_ANALYSIS_[PROGETTO].md`

**COST (Effort):**
- Misurata in ore/giorni reali sostenuti
- Documentata come **osservazione**, non come formula
- **NON** calcolata da SIZE in questa fase
- **NON** derivata da coefficienti (campione insufficiente)

**Separazione:**
- SIZE e COST sono documentati separatamente
- Nessuna formula di produttività in questa fase
- Nessun coefficiente calcolato
- Spiegazione esplicita: con N progetti < 3 non è possibile derivare relazioni statistiche valide

---

## 4. DOCUMENTI INVALIDATI

I seguenti documenti sono stati archiviati come metodologicamente non validi:

- `DEPRECATED_CALCOLO_FATTORE_DEV_UNIT.md`
- `DEPRECATED_FORMULA_DEV_UNIT_PREVENTIVO.md`
- `DEPRECATED_FORMULA_PROBABILISTICA_DEV_UNIT.md`
- `DEPRECATED_FORMULA_CORRETTA_CAMPO_TEMPO.md`
- `DEPRECATED_FORMULA_FINALE_CAMPO_TEMPO.md`
- `DEPRECATED_ANALISI_DEV_UNIT.md`
- `DEPRECATED_ANALISI_DEV_UNIT_PER_ENTITA.md`
- `DEPRECATED_ANALISI_CAMPO_DEV_UNIT_STATISTICA.md`
- `DEPRECATED_CORREZIONE_DEV_UNITS.md`
- `DEPRECATED_CALIBRAZIONE_COEFFICIENTE_PRODUTTIVITA.md`
- `DEPRECATED_MAPPATURA_COSMIC_DETTAGLIATA.md`
- `DEPRECATED_TABELLA_CALIBRAZIONE_FINALE.md`
- `DEPRECATED_EXECUTIVE_SUMMARY_CALIBRAZIONE.md`
- `DEPRECATED_GUIDA_DOCUMENTI_CALIBRAZIONE.md`

**Motivo Invalidazione:** Contengono metodologie non-standard, fattori correttivi inventati, mescolanza SIZE/COST, o riferimenti a DEV UNIT.

---

## 5. DOCUMENTI VALIDI

**Documenti Metodologici:**
- `COSMIC_METHODOLOGY_DECLARATION.md` (questo documento)
- `COSMIC_METHODOLOGY.md` (definizione formale metodo)

**Documenti Analisi:**
- `COSMIC_ANALYSIS_MSCARICHI.md`
- `COSMIC_ANALYSIS_CACTUSBOARD.md`
- `COSMIC_ANALYSIS_KLABHOUSE.md`
- `COSMIC_ANALYSIS_CZSERVIZI.md`

**Documenti Riepilogativi:**
- `COSMIC_SIZE_SUMMARY.md` (solo SIZE, senza COST)

**Documenti di Riferimento (non misurativi):**
- `ANALISI_FUNZIONALE_DATA_CENTRICA.md` (descrittivo, utile come riferimento funzionale)
- `CONTESTO_TARIFFE.md` (dati costi reali, utile per osservazione effort, non per SIZE)

---

## 6. VINCOLI METODOLOGICI

**NON ammessi:**
- DEV UNIT o unità di misura non-standard
- Fattori correttivi o normalizzazioni
- Formule di costo o produttività
- Conversioni arbitrarie (campo→tempo, LOC→CFP, ecc.)
- Ipotesi non dimostrabili
- Linguaggio da preventivo ("ore stimate", "costo stimato")

**Ammessi:**
- Solo concetti COSMIC ISO/IEC 19761
- Conteggio rigoroso E/X/R/W
- Documentazione di limiti e incertezze
- Separazione rigorosa osservazione vs inferenza

---

## 7. OBIETTIVO

Costruire una **BASE COSMIC SOLIDA E PULITA** su cui, e **SOLO successivamente**, sarà possibile costruire un modello di produttività affidabile con dati statisticamente validi.

**Fase Attuale:**
- Misurazione SIZE (CFP) per progetti completati
- Documentazione effort osservato (senza calcolo coefficienti)
- Base dati pulita per analisi future

**Fase Futura (non in questo documento):**
- Calibrazione coefficiente produttività (richiede N ≥ 3 progetti)
- Modello predittivo SIZE → COST
- Validazione statistica

---

**Firma Metodologica:**  
Analisi eseguita secondo standard ISO/IEC 19761 - COSMIC Function Points  
Nessuna contaminazione metodologica  
Separazione rigorosa SIZE vs COST

