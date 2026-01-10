# Documentazione Gestionale L'Altramusica

Struttura organizzata per contesto e categoria.

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

---

## 📁 Struttura Cartelle

### `dev-unit/` - Analisi DEV UNIT
- **`reali/`** - DEV UNIT per trasformazione 1:1 ODS → Laravel
  - `DEV_UNIT_REALI_ODS_LARAVEL.md` - Analisi dettagliata migrazione dati
- **`ipotetiche/`** - DEV UNIT per funzionalità evolutive
  - `DEV_UNIT_IPOTETICHE_EVOLUZIONE.md` - Analisi dettagliata funzionalità nuove
- **`RIEPILOGO_DEV_UNIT.md`** - Riepilogo comparativo delle due analisi
- File legacy: `DEV_UNIT_MATRICE_IPOTETICA.md`, `DEV_UNIT_CALCOLO_DETTAGLIATO.md`

### `preventivi/` - Preventivi e Stime
- `PREVENTIVO_DETTAGLIATO.md` - Preventivo dettagliato per fasi
- `PREVENTIVO_TABELLA_FINALE.md` - Tabella preventivo finale
- `PREVENTIVO_FASI_FUNZIONALITA.txt` - Fasi funzionalità
- File appunti e originali

### `stime-cosmic/` - Stime COSMIC Function Points
- Analisi COSMIC per progetti completati
- Metodologia COSMIC
- Confronti e validazioni

### `analisi-funzionale/` - Analisi Funzionale
- `ANALISI_FUNZIONALE_DATA_CENTRICA.md` - Analisi data-centrica completa
- `FUNZIONALITA_RACCOLTE.md` - Funzionalità raccolte
- `PRIORITA_FUNZIONALITA_2026.md` - Priorità funzionalità

### `import-dati/` - Import Dati ODS
- `MAPPING_IMPORT_ODS.md` - Mapping colonne ODS → Database
- `ANALISI_COLONNE_ODS.md` - Analisi colonne ODS
- `ANALISI_SEEDER_IMPORT.md` - Analisi seeder import
- `STATO_IMPORT_SEEDER.md` - Stato import seeder
- `RIEPILOGO_SEEDER.md` - Riepilogo seeder

### `materiale-cliente/` - Materiale Cliente
- File ODS originali
- Documenti PDF
- Altri materiali forniti

### `analisi-di-supporto/` - Analisi di Supporto
- Analisi Footility
- Analisi gap temporali
- Analisi pattern commit
- Validazioni e metriche

---

## 📊 Analisi DEV UNIT

### Due Analisi Separate

1. **DEV UNIT REALI** (`dev-unit/reali/`)
   - Trasformazione 1:1 ODS → Laravel
   - Solo migrazione dati esistenti
   - **Totale: 730 DEV UNIT**

2. **DEV UNIT IPOTETICHE** (`dev-unit/ipotetiche/`)
   - Funzionalità evolutive richieste
   - Workflow avanzati, automazioni, PDF
   - **Totale: 710 DEV UNIT**

**Totale Progetto: 1440 DEV UNIT**

Vedi `dev-unit/RIEPILOGO_DEV_UNIT.md` per il confronto completo.

---

## 🔍 Come Navigare

### Per Analisi DEV UNIT
1. Leggi `dev-unit/RIEPILOGO_DEV_UNIT.md` per overview
2. Consulta `dev-unit/reali/` per migrazione dati
3. Consulta `dev-unit/ipotetiche/` per evoluzione

### Per Preventivi
1. Leggi `preventivi/PREVENTIVO_TABELLA_FINALE.md` per riepilogo
2. Consulta `preventivi/PREVENTIVO_DETTAGLIATO.md` per dettagli

### Per Import Dati
1. Leggi `import-dati/MAPPING_IMPORT_ODS.md` per mapping
2. Consulta `import-dati/ANALISI_COLONNE_ODS.md` per colonne

### Per Analisi Funzionale
1. Leggi `analisi-funzionale/ANALISI_FUNZIONALE_DATA_CENTRICA.md` per entità e funzionalità

---

## 📝 Note

- I file legacy sono stati spostati nelle cartelle appropriate
- La struttura è organizzata per contesto per facilitare la lettura per categorie
- Ogni cartella contiene documenti correlati allo stesso argomento


