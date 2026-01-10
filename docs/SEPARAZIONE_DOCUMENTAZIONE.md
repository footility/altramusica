# Separazione Documentazione: Gestionale Altramusica vs Footility

**Data Analisi:** 2025-01-XX  
**Obiettivo:** Separare e riorganizzare documentazione per chiarire che Altramusica è progetto pilota, Footility è sistema di gestione progetti

---

## Principio di Separazione

**Footility:** Sistema di gestione progetti e analisi codice (dev units, preventivi, calibrazione)  
**Altramusica:** Progetto pilota che usa Footility per validazione e preventivazione

La documentazione deve riflettere questa separazione chiara.

---

## File Documentazione con Riferimenti a Footility

### File da Rivedere (Riferimenti Footility da chiarire/aggiornare)

#### 1. `ANALISI_ODS_DEV_UNIT_FOOTILITY.md`
- **Contenuto:** Analisi ODS e calcolo DEV UNIT per inserimento in Footility
- **Problema:** Titolo e contenuto suggeriscono che sia documentazione Footility
- **Decisione:** ⚠️ **RINOMINARE** in `ANALISI_ODS_DEV_UNIT.md` e chiarire che descrive analisi per progetto Altramusica (usando metodologia DEV UNIT di Footility)
- **Azione:** Aggiornare introduzione per chiarire che è analisi progetto Altramusica che usa metodologia Footility

#### 2. `RIEPILOGO_FOOTILITY_FASE1.md`
- **Contenuto:** Riepilogo attività Fase 1 per import in Footility
- **Problema:** Titolo suggerisce che sia documentazione Footility
- **Decisione:** ⚠️ **RINOMINARE** in `RIEPILOGO_FASE1_ATTIVITA.md` e chiarire contesto
- **Azione:** Aggiornare introduzione per chiarire che è riepilogo progetto Altramusica

#### 3. `ORGANIZZAZIONE_FASI_DEV_UNIT_FOOTILITY.md`
- **Contenuto:** Organizzazione fasi DEV UNIT per inserimento in Footility
- **Problema:** Titolo suggerisce che sia documentazione Footility
- **Decisione:** ⚠️ **RINOMINARE** in `ORGANIZZAZIONE_FASI_DEV_UNIT.md` e chiarire contesto
- **Azione:** Aggiornare introduzione per chiarire che è organizzazione progetto Altramusica

#### 4. `REVISIONE_PREVENTIVO_FASI_DEV_UNIT.md`
- **Contenuto:** Revisione preventivo con fasi e DEV UNIT (probabilmente per Footility)
- **Decisione:** ⚠️ **VERIFICARE** contenuto e rinominare se necessario
- **Azione:** Leggere contenuto e decidere se è documentazione progetto o sistema

#### 5. `ANALISI_SCRIPT_FOOTILITY.md`
- **Contenuto:** Analisi script cross-progetto (già creato)
- **Decisione:** ✅ **MANTENERE** (documentazione separazione progetti)
- **Azione:** Nessuna, già corretto

### File nella Cartella `dev-unit/`

#### 6. `dev-unit/MACRO_ATTIVITA_FOOTILITY.md`
- **Contenuto:** Probabilmente macro attività per import Footility
- **Decisione:** ⚠️ **RINOMINARE** in `MACRO_ATTIVITA.md` o spostare in preventivi/
- **Azione:** Verificare contenuto e decidere posizione

### File nella Cartella `analisi-di-supporto/`

#### 7. `analisi-di-supporto/ANALISI_FOOTILITY.md`
- **Contenuto:** Analisi sistema Footility (probabilmente per validazione)
- **Decisione:** ⚠️ **VALUTARE** se è analisi sistema Footility o progetto Altramusica
- **Azione:** Leggere contenuto per capire contesto

#### 8. `analisi-di-supporto/RIEPILOGO_AGGIORNAMENTO_FOOTILITY.md`
- **Contenuto:** Riepilogo aggiornamenti Footility (probabilmente per validazione)
- **Decisione:** ⚠️ **VALUTARE** se è documentazione sistema o progetto
- **Azione:** Leggere contenuto per capire contesto

#### 9. `analisi-di-supporto/REPORT_ANALISI_RETROSPETTIVA.md`
- **Contenuto:** Report analisi retrospettiva (probabilmente usa Footility)
- **Decisione:** ✅ **MANTENERE** se è analisi progetto Altramusica che usa Footility
- **Azione:** Verificare che introduzione chiarisca contesto

### File in Cartelle Specifiche

#### 10. File in `preventivi/`
- **File:** `preventivo aggiornato (appunti).txt`, `preventivo originale.txt`
- **Contenuto:** Appunti preventivo progetto
- **Decisione:** ✅ **MANTENERE** (sono preventivi progetto Altramusica)
- **Azione:** Verificare se contengono riferimenti Footility e chiarire se necessario

---

## File Documentazione Footility (Sistema)

La documentazione Footility è già ben organizzata in `/Users/mistre/develop/footility/footility/docs/`:

- ✅ `00-Configurazione.md` - Configurazione sistema Footility
- ✅ `01-Gestione-Progetti.md` - Gestione progetti Footility
- ✅ `02-DEV-UNITS-Analisi-Codice.md` - Sistema DEV UNITS Footility
- ✅ `03-Planning-Timeline.md` - Planning Footility
- ✅ `04-Preventivi-Quotations.md` - Sistema preventivi Footility
- ✅ `05-Google-Calendar-Sync.md` - Sincronizzazione Google Calendar
- ✅ `06-Araba-Fenice-Contabilita.md` - Sistema contabilità
- ✅ `07-Utenti-Permessi.md` - Gestione utenti
- ✅ `08-API.md` - API Footility
- ✅ `09-Comandi-Artisan.md` - Comandi CLI Footility

**Nessuna azione necessaria** - documentazione Footility è già separata correttamente.

---

## Strategia di Riorganizzazione

### Per Gestionale Altramusica

**Principio:** Documentazione progetto Altramusica che descrive:
- Analisi ODS e requisiti progetto
- Schema DEV UNIT per progetto
- Preventivi progetto-specifici
- Validazione sistema Footility su progetto pilota

**Struttura proposta:**

```
docs/
├── README.md (aggiornato con chiarimento separazione)
├── ANALISI_ODS_DEV_UNIT.md (rinominato, senza "FOOTILITY" nel nome)
├── ORGANIZZAZIONE_FASI_DEV_UNIT.md (rinominato)
├── RIEPILOGO_FASE1_ATTIVITA.md (rinominato)
├── SEPARAZIONE_DOCUMENTAZIONE.md (questo file)
├── ANALISI_SCRIPT_FOOTILITY.md (analisi separazione progetti)
├── dev-unit/ (analisi DEV UNIT progetto)
│   ├── reali/ (DEV UNIT Fase 1)
│   ├── ipotetiche/ (DEV UNIT evolutive)
│   └── MACRO_ATTIVITA.md (rinominato)
├── preventivi/ (preventivi progetto)
├── analisi-funzionale/ (requisiti progetto)
├── import-dati/ (import ODS progetto)
├── analisi-di-supporto/ (validazione Footility su progetto)
│   └── [file di analisi retrospettiva che usano Footility come strumento]
└── materiale-cliente/ (ODS, PDF, etc.)
```

### Per Footility

**Principio:** Documentazione sistema Footility (già corretta)

**Nessuna modifica necessaria** - struttura già corretta.

---

## File da Rinominare

1. ✅ `ANALISI_ODS_DEV_UNIT_FOOTILITY.md` → `ANALISI_ODS_DEV_UNIT.md`
2. ✅ `RIEPILOGO_FOOTILITY_FASE1.md` → `RIEPILOGO_FASE1_ATTIVITA.md`
3. ✅ `ORGANIZZAZIONE_FASI_DEV_UNIT_FOOTILITY.md` → `ORGANIZZAZIONE_FASI_DEV_UNIT.md`
4. ⚠️ `dev-unit/MACRO_ATTIVITA_FOOTILITY.md` → `dev-unit/MACRO_ATTIVITA.md` (verificare prima)

---

## File da Verificare Contenuto

1. `REVISIONE_PREVENTIVO_FASI_DEV_UNIT.md` - Verificare se è documentazione progetto o sistema
2. `analisi-di-supporto/ANALISI_FOOTILITY.md` - Verificare contesto
3. `analisi-di-supporto/RIEPILOGO_AGGIORNAMENTO_FOOTILITY.md` - Verificare contesto
4. `preventivi/preventivo aggiornato (appunti).txt` - Verificare riferimenti Footility

---

## README da Aggiornare

### Gestionale Altramusica - `docs/README.md`

**Aggiungere sezione iniziale:**

```markdown
## ⚠️ Separazione Progetti

**Questo progetto (Gestionale L'Altramusica)** è un progetto pilota che usa **Footility** come sistema di gestione progetti e preventivazione.

- **Footility** = Sistema di gestione progetti, analisi codice (DEV UNITS), preventivi, calibrazione
- **Gestionale L'Altramusica** = Progetto pilota che valida sistema Footility

La documentazione in questa cartella descrive:
- Analisi requisiti progetto Altramusica
- Schema DEV UNIT per progetto Altramusica
- Preventivi progetto-specifici
- Validazione Footility su progetto pilota

Per documentazione sistema Footility, vedere: `/Users/mistre/develop/footility/footility/docs/`

---
```

### Footility - `README.md` (già presente, verificare se necessario aggiungere)

**Verificare se aggiungere:**

```markdown
## Progetti Gestiti

Footility gestisce progetti software di vario tipo. Attualmente include:
- **Progetto pilota:** Gestionale L'Altramusica (validazione sistema)
- Altri progetti cliente...

La documentazione progetti pilota/clienti è mantenuta separatamente nei rispettivi repository.
```

---

## Azioni Consigliate

### Priorità Alta (Separazione Chiara)

1. ✅ Rinominare file con "FOOTILITY" nel nome (3-4 file)
2. ✅ Aggiornare `docs/README.md` Altramusica con sezione separazione
3. ✅ Verificare contenuto file `analisi-di-supporto/` per chiarire contesto

### Priorità Media (Chiarimento)

1. ⚠️ Verificare file `REVISIONE_PREVENTIVO_FASI_DEV_UNIT.md`
2. ⚠️ Verificare file `dev-unit/MACRO_ATTIVITA_FOOTILITY.md`
3. ⚠️ Aggiornare introduzioni file rinominati per chiarire contesto

### Priorità Bassa (Pulizia)

1. Documentare eventuali file obsoleti da archiviare
2. Verificare file in `preventivi/` per riferimenti Footility

---

## Note Finali

- **Footility** è sistema generico per gestione progetti
- **Altramusica** è progetto specifico che usa Footility
- Documentazione deve chiarire sempre il contesto (sistema vs progetto)
- File con "FOOTILITY" nel nome suggeriscono dipendenza: rivedere titoli per chiarezza
