# Analisi Script Cross-Progetto Footility

**Data Analisi:** 2025-01-XX  
**Obiettivo:** Catalogare e documentare script che fanno riferimento a Footility per separazione progetti

---

## Script Identificati (11 file totali)

### Script che chiamano direttamente Footility

#### 1. `analyze_ods_create_er_migration.php`
- **Path:** `scripts/analyze_ods_create_er_migration.php`
- **Dipendenze:** 
  - Cambia directory a `/Users/mistre/develop/footility/footility`
  - Include autoload di Footility
  - Usa database Footility (progetto ID 13, quotation ID 1)
- **Funzione:** Analizza ODS, crea E/R models, inserisce in Footility come entity
- **Da rimuovere/spostare:** ✅ SÌ (dipendenza diretta da Footility)

#### 2. `create_er_and_dev_units_footility.php`
- **Path:** `scripts/create_er_and_dev_units_footility.php`
- **Dipendenze:**
  - Cambia directory a `/Users/mistre/develop/footility/footility`
  - Include autoload di Footility
  - Usa database Footility (progetto ID 13)
  - Accede a tabelle: `quotation_activities`, `quotation_phases`, `dev_unit_definitions`, `devunits`, `activity_devunit`
- **Funzione:** Crea E/R models e DEV UNIT in Footility da analisi ODS
- **Da rimuovere/spostare:** ✅ SÌ (dipendenza diretta da Footility)

#### 3. `footility_update_activities_dev_units.php`
- **Path:** `scripts/footility_update_activities_dev_units.php`
- **Dipendenze:**
  - Cambia directory a `/Users/mistre/develop/footility/footility`
  - Include autoload di Footility
  - Usa database Footility (quotation ID 1)
  - Legge file JSON: `docs/dev-unit/FOOTILITY_DEV_UNITS.json`
- **Funzione:** Aggiorna attività preventivo con DEV UNIT e calcola ore/costi
- **Da rimuovere/spostare:** ✅ SÌ (dipendenza diretta da Footility)

#### 4. `footility_update_quotation_phases.php`
- **Path:** `scripts/footility_update_quotation_phases.php`
- **Dipendenze:**
  - Cambia directory a `/Users/mistre/develop/footility/footility`
  - Include autoload di Footility
- **Funzione:** Aggiorna fasi preventivo in Footility
- **Da rimuovere/spostare:** ✅ SÌ (dipendenza diretta da Footility)

#### 5. `footility_delete_and_import_activities.php`
- **Path:** `scripts/footility_delete_and_import_activities.php`
- **Dipendenze:**
  - Cambia directory a `/Users/mistre/develop/footility/footility`
  - Include autoload di Footility
  - Legge file JSON: `docs/dev-unit/FOOTILITY_ATTIVITA_IMPORT_CLEAN.json`
- **Funzione:** Elimina e re-importa attività in Footility
- **Da rimuovere/spostare:** ✅ SÌ (dipendenza diretta da Footility)

#### 6. `insert_footility_activities.php`
- **Path:** `scripts/insert_footility_activities.php`
- **Dipendenze:**
  - Chiama API Footility: `https://footility.test/api/v1`
  - Richiede token: `FOOTILITY_API_TOKEN` (env var)
  - Progetto ID: 13 (hardcoded)
- **Funzione:** Inserisce macro attività L'Altramusica in Footility tramite API
- **Da rimuovere/spostare:** ✅ SÌ (chiama API Footility)

#### 7. `insert_footility_activities_with_dev_units.php`
- **Path:** `scripts/insert_footility_activities_with_dev_units.php`
- **Dipendenze:**
  - Chiama API Footility: `https://footility.test/api/v1`
  - Richiede token: `FOOTILITY_API_TOKEN` (env var)
  - Progetto ID: 13 (hardcoded)
  - Include dati da: `footility_activities_data.php`
- **Funzione:** Inserisce attività con DEV UNITS in Footility tramite API
- **Da rimuovere/spostare:** ✅ SÌ (chiama API Footility)

#### 8. `analyze_ods_and_create_er_dev_units.php`
- **Path:** `scripts/analyze_ods_and_create_er_dev_units.php`
- **Dipendenze:**
  - Include autoload Footility: `require __DIR__ . '/../footility/footility/vendor/autoload.php'`
  - Bootstrap app Footility: `require_once __DIR__ . '/../footility/footility/bootstrap/app.php'`
  - Riferimenti a attività ID Footility (2576 + index)
- **Funzione:** Analizza ODS e crea E/R + DEV UNITS intelligenti (per Footility)
- **Da rimuovere/spostare:** ✅ SÌ (include autoload e bootstrap Footility)

### File dati per Footility (potenzialmente riutilizzabili)

#### 9. `footility_activities_data.php`
- **Path:** `scripts/footility_activities_data.php`
- **Contenuto:** Array PHP con struttura attività L'Altramusica (Fase 1, 2, 3)
- **Utilizzo:** Usato da altri script per importare in Footility
- **Da rimuovere/spostare:** ⚠️ DECISIONE: Può essere mantenuto come file dati, rinominato in `activities_data.php` (rimuovere prefisso "footility")

#### 10. `footility_activities_phase2_3_remaining.php`
- **Path:** `scripts/footility_activities_phase2_3_remaining.php`
- **Contenuto:** Dati attività Fase 2 e 3 (probabilmente parte di `footility_activities_data.php`)
- **Utilizzo:** Include nei dati attività
- **Da rimuovere/spostare:** ⚠️ DECISIONE: Stesso trattamento di `footility_activities_data.php`

---

## Riepilogo Decisioni

### Script da spostare in `scripts/_archived_footility/`:
1. ✅ `analyze_ods_create_er_migration.php`
2. ✅ `create_er_and_dev_units_footility.php`
3. ✅ `footility_update_activities_dev_units.php`
4. ✅ `footility_update_quotation_phases.php`
5. ✅ `footility_delete_and_import_activities.php`
6. ✅ `insert_footility_activities.php`
7. ✅ `insert_footility_activities_with_dev_units.php`
8. ✅ `analyze_ods_and_create_er_dev_units.php`

### File dati da rinominare (rimuovere prefisso "footility"):
- `footility_activities_data.php` → `activities_data.php` (o mantenere per riferimento storico)
- `footility_activities_phase2_3_remaining.php` → `activities_phase2_3_remaining.php` (o mantenere)

### File JSON da verificare:
- `docs/dev-unit/FOOTILITY_DEV_UNITS.json` - Verificare se usato solo per import Footility
- `docs/dev-unit/FOOTILITY_ATTIVITA_IMPORT_CLEAN.json` - Verificare se usato solo per import Footility

---

## Dipendenze Future Eventuali

Se in futuro si volesse integrare nuovamente con Footility, questi script potrebbero essere utili come riferimento:
- Pattern per analisi ODS → E/R models
- Pattern per creazione DEV UNITS da schema
- Pattern per associazione attività ↔ DEV UNITS
- Struttura dati attività per import

---

## Note

- Progetto ID hardcoded: **13** (L'Altramusica in Footility)
- Quotation ID hardcoded: **1** (preventivo L'Altramusica in Footility)
- URL API hardcoded: `https://footility.test/api/v1`
- Token API: richiede env var `FOOTILITY_API_TOKEN`

---

## Azioni Consigliate

1. **Creare cartella archivio:** `scripts/_archived_footility/`
2. **Spostare script con dipendenze Footility** (7-8 file)
3. **Rinominare file dati** (rimuovere prefisso "footility") se devono essere mantenuti
4. **Creare README in archivio** con descrizione script e uso storico
5. **Verificare file JSON** in `docs/dev-unit/` e decidere se archiviare
