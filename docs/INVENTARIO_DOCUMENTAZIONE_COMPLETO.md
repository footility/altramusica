# Inventario Completo Documentazione Gestionale Altramusica

**Data Inventario:** 2025-01-XX  
**Obiettivo:** Catalogare tutta la documentazione per separazione e riorganizzazione

---

## Struttura Documentazione

### 📁 Cartelle Principali

#### `dev-unit/` - Analisi DEV UNIT
- **`reali/DEV_UNIT_REALI_ODS_LARAVEL.md`** - Analisi dettagliata migrazione 1:1 ODS (730 DEV UNIT)
- **`ipotetiche/DEV_UNIT_IPOTETICHE_EVOLUZIONE.md`** - Analisi funzionalità evolutive (710 DEV UNIT)
- **`RIEPILOGO_DEV_UNIT.md`** - Riepilogo comparativo (1440 DEV UNIT totali)
- **`DEV_UNIT_MATRICE_IPOTETICA.md`** - File legacy (da verificare)
- **`DEV_UNIT_CALCOLO_DETTAGLIATO.md`** - File legacy (da verificare)
- **`CONFRONTO_COSMIC_DEV_UNIT.md`** - Confronto metodologie
- **`MACRO_ATTIVITA_FOOTILITY.md`** - ⚠️ DA RINOMINARE (rimuovere "FOOTILITY")
- **File JSON:** `FOOTILITY_DEV_UNITS.json`, `FOOTILITY_ER_MODELS.json`, `FOOTILITY_ATTIVITA_*.json` - ⚠️ DA VALUTARE (dati per import Footility, mantenere come riferimento?)

#### `preventivi/` - Preventivi Progetto
- `PREVENTIVO_DETTAGLIATO.md` - Preventivo dettagliato per fasi
- `PREVENTIVO_TABELLA_FINALE.md` - Tabella preventivo finale
- `PREVENTIVO_FASI_FUNZIONALITA.txt` - Fasi funzionalità
- `preventivo aggiornato (appunti).txt` - ⚠️ Appunti preventivo (verificare riferimenti Footility)
- `preventivo originale.txt` - Preventivo originale

#### `stime-cosmic/` - Stime COSMIC Function Points
- Analisi COSMIC per progetti completati
- Metodologia COSMIC
- Confronti e validazioni

#### `analisi-funzionale/` - Analisi Funzionale
- `ANALISI_FUNZIONALE_DATA_CENTRICA.md` - Analisi data-centrica completa
- `FUNZIONALITA_RACCOLTE.md` - Funzionalità raccolte
- `PRIORITA_FUNZIONALITA_2026.md` - Priorità funzionalità

#### `import-dati/` - Import Dati ODS
- `MAPPING_IMPORT_ODS.md` - Mapping colonne ODS → Database
- `ANALISI_COLONNE_ODS.md` - Analisi colonne ODS
- `ANALISI_SEEDER_IMPORT.md` - Analisi seeder import
- `STATO_IMPORT_SEEDER.md` - Stato import seeder
- `RIEPILOGO_SEEDER.md` - Riepilogo seeder

#### `materiale-cliente/` e `materiale cliente/` - Materiale Cliente
- File ODS originali:
  - `db 2025-26 gestionale.ods` (file principale, 485 righe, 1024 colonne)
  - `Db Contratti 25-26.ods`
  - `Db Contabile 2025-26.ods`
  - `Db Accessori 2025-26.ods`
  - `Calendario 2025-26.ods`
  - `dati lavoratori 25-26.ods`
- Documenti PDF
- Altri materiali forniti

#### `analisi-di-supporto/` - Analisi di Supporto
- `ANALISI_FOOTILITY.md` - ⚠️ Verificare contesto (analisi sistema o progetto?)
- `RIEPILOGO_AGGIORNAMENTO_FOOTILITY.md` - ⚠️ Verificare contesto
- `REPORT_ANALISI_RETROSPETTIVA.md` - Report analisi retrospettiva (usa Footility come strumento)
- `REPORT_VALIDAZIONE_FINALE.md` - Report validazione
- `ANALISI_LIMITE_METODOLOGICO_LOC.md` - Analisi metodologica

#### `ARCHIVIO_NON_VALIDO/` - File Deprecati
- File di calibrazione deprecati (riferimenti a metodologie obsolete)

---

## File Documentazione Principali (Root `docs/`)

### Analisi ODS e DEV UNIT
- ✅ `ANALISI_ODS_DEV_UNIT.md` (rinominato da `ANALISI_ODS_DEV_UNIT_FOOTILITY.md`)
- ✅ `ORGANIZZAZIONE_FASI_DEV_UNIT.md` (rinominato da `ORGANIZZAZIONE_FASI_DEV_UNIT_FOOTILITY.md`)
- ✅ `RIEPILOGO_FASE1_ATTIVITA.md` (rinominato da `RIEPILOGO_FOOTILITY_FASE1.md`)
- `REVISIONE_PREVENTIVO_FASI_DEV_UNIT.md` - Revisione preventivo (verificare se è documentazione progetto)
- `ANALISI_SCRIPT_FOOTILITY.md` - Analisi separazione script (documentazione separazione progetti)

### Analisi e Riepiloghi
- `ANALISI_DATI_ODS.md` - Analisi dati ODS
- `ANALISI_CRITICA_STATISTICHE.md` - Analisi critica statistiche
- `ANALISI_IA_POTENZIALE_RISPARMIO.md` - Analisi IA potenziale risparmio
- `ANALISI_PRAGMATICA_LOGO_RESIDUO.md` - Analisi logo residuo
- `RIEPILOGO_ANALISI_RETROSPETTIVA.md` - Riepilogo analisi retrospettiva
- `RIEPILOGO_FINALE.md` - Riepilogo finale
- `RIEPILOGO_LAVORO.md` - Riepilogo lavoro

### Stato e Implementazione
- `STATO_IMPLEMENTAZIONE.md` - Stato implementazione
- `IMPLEMENTAZIONE_COMPLETA.md` - Implementazione completa
- `CONTROVERIFICA_IMPLEMENTAZIONE.md` - Controverifica implementazione
- `VERIFICA_STATO_ANALISI_TEMPI.md` - Verifica stato analisi tempi
- `PIANO_IMPLEMENTAZIONE.md` - Piano implementazione
- `PROSSIMI_PASSI_ANALISI.md` - Prossimi passi analisi

### Contesto
- `CONTESTO.md` - Contesto progetto
- `CONTESTO_TARIFFE.md` - Contesto tariffe

### Trascrizioni
- `Trascrizione gestionale parte 1.txt` - Trascrizione conversazioni
- `Trascrizione gestionale parte 2.txt` - Trascrizione conversazioni

---

## Mappa Dipendenze Documenti

### Documenti Chiave (Punti di Ingresso)

1. **README.md** - Overview struttura documentazione
2. **dev-unit/RIEPILOGO_DEV_UNIT.md** - Overview DEV UNIT (730 reali + 710 evolutive)
3. **ANALISI_ODS_DEV_UNIT.md** - Analisi dettagliata ODS → DEV UNIT
4. **analisi-funzionale/ANALISI_FUNZIONALE_DATA_CENTRICA.md** - Analisi funzionale completa
5. **ORGANIZZAZIONE_FASI_DEV_UNIT.md** - Organizzazione fasi progetto

### Documenti che Dipendono da Altri

- `RIEPILOGO_FASE1_ATTIVITA.md` ← Dipende da `ANALISI_ODS_DEV_UNIT.md` e `dev-unit/reali/DEV_UNIT_REALI_ODS_LARAVEL.md`
- `REVISIONE_PREVENTIVO_FASI_DEV_UNIT.md` ← Dipende da `RIEPILOGO_DEV_UNIT.md` e preventivi
- `preventivi/PREVENTIVO_DETTAGLIATO.md` ← Dipende da `ORGANIZZAZIONE_FASI_DEV_UNIT.md`
- `import-dati/MAPPING_IMPORT_ODS.md` ← Dipende da `ANALISI_COLONNE_ODS.md`

---

## Identificazione Documenti Obsoleti/Duplicati

### Potenzialmente Obsoleti

1. **`ARCHIVIO_NON_VALIDO/`** - Tutti i file (già archiviati, deprecati)
2. **`dev-unit/DEV_UNIT_MATRICE_IPOTETICA.md`** - ⚠️ Verificare se sostituito da `ipotetiche/DEV_UNIT_IPOTETICHE_EVOLUZIONE.md`
3. **`dev-unit/DEV_UNIT_CALCOLO_DETTAGLIATO.md`** - ⚠️ Verificare se sostituito da analisi più recenti
4. **File JSON in `dev-unit/`** - ⚠️ Verificare se ancora usati o sono snapshot storici

### Potenzialmente Duplicati

1. **`analisi-di-supporto/ANALISI_FOOTILITY.md`** vs documentazione Footility - ⚠️ Verificare se è duplicato
2. **`RIEPILOGO_FINALE.md`** vs `RIEPILOGO_ANALISI_RETROSPETTIVA.md` - ⚠️ Verificare sovrapposizione

---

## File con Riferimenti Footility (da Chiarire)

### File che Menzionano Footility nel Contenuto

1. ✅ `ANALISI_SCRIPT_FOOTILITY.md` - OK (documentazione separazione)
2. ⚠️ `ANALISI_ODS_DEV_UNIT.md` - Menziona "per Footility" - aggiornare introduzione
3. ⚠️ `RIEPILOGO_FASE1_ATTIVITA.md` - Menziona "per Footility" - aggiornare introduzione
4. ⚠️ `ORGANIZZAZIONE_FASI_DEV_UNIT.md` - Menziona "per Footility" - aggiornare introduzione
5. ⚠️ `REVISIONE_PREVENTIVO_FASI_DEV_UNIT.md` - Usa dati Footility per validazione - OK se chiarito
6. ⚠️ `analisi-di-supporto/*.md` - Verificare se sono analisi sistema Footility o validazione progetto

### File JSON con Prefisso "FOOTILITY"

- `FOOTILITY_DEV_UNITS.json` - Dati DEV UNITS per import Footility
- `FOOTILITY_ER_MODELS.json` - Modelli E/R per import Footility
- `FOOTILITY_ATTIVITA_*.json` - Attività per import Footility

**Decisione:** ⚠️ Mantenere come riferimento storico o rinominare senza prefisso "FOOTILITY"?

---

## Prossimi Passi Inventario

1. ✅ Catalogazione completa (completata)
2. ⏭️ Verificare contenuto file potenzialmente obsoleti
3. ⏭️ Aggiornare introduzioni file con riferimenti Footility
4. ⏭️ Decidere su file JSON (mantenere/archiviare/rinominare)
5. ⏭️ Creare mappa dipendenze visuale (opzionale)
