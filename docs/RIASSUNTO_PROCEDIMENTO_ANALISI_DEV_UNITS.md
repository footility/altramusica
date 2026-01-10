# Riassunto Procedimento: Analisi DEV UNITS Gestione Altramusica

**Data:** Gennaio 2026  
**Obiettivo:** Applicare processo metodico per estrarre DEV UNITS esistenti dal codice sorgente e affinare precisione creando codice mancante direttamente nel sorgente

---

## SITUAZIONE ATTUALE

### Progetto Gestionale Altramusica
- **Path:** `/Users/mistre/develop/gestionale-laltramusica`
- **Framework:** Laravel (con codice già presente)
- **URL locale:** https://gestionale.altramusica.test/login
- **Repository Git:** ✅ Inizializzato, remote origin aggiunto (https://github.com/footility/altramusica.git)
- **Git User:** footility / paolo.mistretta@footility.com
- **Progetto Footility:** ✅ Configurato (ID: 13, git_url: https://github.com/footility/altramusica.git, git_pat: SET)

### Codice Esistente
**Models presenti (36 modelli):**
- AcademicYear, Attendance, Book, BookDistribution
- CalendarLesson, CalendarSuspension, Classroom
- Communication, Contract, Course, CourseType, CreditNote
- Document, Enrollment, Exam
- ExtraActivity, ExtraActivityEnrollment, FirstContact
- Guardian, Instrument, InstrumentRental
- Invoice, InvoiceItem, Lesson
- Payment, PaymentPlan, ScheduleProposal
- Setting, Student, StudentAvailability, StudentLevel
- Teacher, TeacherAvailability, TeacherHour, User

**Migrations presenti:** ~30+ migrations già create (da `2025_12_11_093215_create_students_table.php`)

### Problema Attuale
- ❌ DEV UNITS errate create in Footility (semantic_path generici: `field_1`, `field_2`, 1.472 DEV UNITS)
- ⚠️ Codice non ancora committato e pushato su GitHub
- ❌ Analisi DEV UNITS non ancora eseguita sul codice esistente reale

---

## PROCEDIMENTO CORRETTO

### FASE 0: Preparazione Repository Git

**Obiettivo:** Collegare progetto locale a repository Git remoto

**Processo:**
1. Verificare se repository remoto esiste (https://github.com/footility/altramusica.git)
2. Se NON esiste: creare repository su GitHub
3. Inizializzare git nel progetto locale:
   ```bash
   cd /Users/mistre/develop/gestionale-laltramusica
   git init
   git remote add origin https://github.com/footility/altramusica.git
   ```
4. Configurare credenziali Git (usare PAT di altri progetti Footility)
5. Prima commit e push del codice esistente
6. Aggiornare progetto Footility se necessario

**Output:**
- Repository Git funzionante
- Codice esistente sincronizzato
- Progetto Footility può clonare/aggiornare repository

---

### FASE 1: Pulizia DEV UNITS Errate

**Obiettivo:** Rimuovere DEV UNITS create con semantic_path generici

**Processo:**
1. Eliminare tutte le DEV UNITS del progetto Altramusica (ID: 13) in Footility
2. Eliminare associazioni attività-DEV UNITS (tabella `activity_devunit`)
3. Verificare che attività macro rimangano (non eliminare attività, solo DEV UNITS)

**Comando:**
```bash
php artisan tinker
> $project = App\Models\Project::find(13);
> $project->devUnits()->delete();
> DB::table('activity_devunit')->whereIn('devunit_id', [])->delete(); // dopo eliminazione devunits
```

**Output:**
- Progetto Altramusica pulito da DEV UNITS errate
- Attività macro preservate

---

### FASE 2: Estrazione DEV UNITS Esistenti

**Obiettivo:** Analizzare codice sorgente Laravel esistente ed estrarre DEV UNITS reali

**Processo:**
1. Aggiornare repository in Footility (git clone/pull)
2. Eseguire analisi DEV UNITS con comando Footility:
   ```bash
   php artisan project:analyze-full 13 --skip-er
   ```
   oppure
   ```bash
   php artisan devunits:analyze 13
   ```
3. Analizzare risultati:
   - DEV UNITS estratte dal codice esistente
   - Categorie presenti (database, be_data, be_logic, ui_layout, fe_logic)
   - Semantic_path generati (verificare formato)

**Output:**
- DEV UNITS reali estratte dal codice sorgente
- Report analisi: cosa è già implementato
- Gap: cosa manca rispetto all'analisi ODS

---

### FASE 3: Analisi Gap e Codice Mancante

**Obiettivo:** Identificare cosa manca rispetto a schema E/R ottimizzato (basato su ODS)

**Processo:**

1. **Confronto Schema E/R Target vs Codice Esistente:**
   - Leggere migrations esistenti → Schema database attuale
   - Confrontare con schema E/R ottimizzato (da analisi ODS)
   - Identificare:
     - ✅ Tabelle/campi già presenti
     - ❌ Tabelle/campi mancanti
     - ⚠️ Tabelle/campi da modificare/normalizzare

2. **Confronto Funzionalità vs Codice:**
   - Leggere controllers esistenti → Funzionalità CRUD presenti
   - Leggere models esistenti → Relazioni presenti
   - Leggere views esistenti → UI presente
   - Confrontare con funzionalità identificate da ODS
   - Identificare:
     - ✅ Funzionalità già implementate
     - ❌ Funzionalità mancanti
     - ⚠️ Funzionalità parziali

**Output:**
- Report gap completo:
  - Tabelle/campi da creare
  - Funzionalità da implementare
  - Codice da aggiungere/modificare

---

### FASE 4: Creazione Codice Mancante (Iterativo)

**Obiettivo:** Creare codice mancante direttamente nel sorgente per affinare precisione DEV UNITS

**Processo Iterativo:**

Per ogni gap identificato:

1. **Database (Migrations):**
   - Creare migration per tabelle/campi mancanti
   - Usare nomi campi corretti da analisi ODS
   - Esempio: `php artisan make:migration create_students_table`
   - Scrivere migration completa con tutti i campi identificati

2. **Models:**
   - Creare/aggiornare model con:
     - Fillable corretti
     - Relazioni Laravel corrette
     - Metodi custom se necessari
   - Esempio: `app/Models/Student.php`

3. **Controllers:**
   - Creare/aggiornare controller con:
     - CRUD completo (7 actions)
     - Metodi custom per workflow
   - Esempio: `app/Http/Controllers/StudentController.php`

4. **Views:**
   - Creare/aggiornare views con:
     - Form completi (tutti i campi)
     - Liste con colonne e filtri
   - Esempio: `resources/views/students/`

5. **Commit e Ri-analisi:**
   - Commit codice creato
   - Push su repository
   - Ri-eseguire analisi DEV UNITS in Footility
   - Verificare che DEV UNITS estratte corrispondano a codice creato

**Output:**
- Codice mancante creato nel sorgente
- DEV UNITS aggiornate dopo ogni iterazione
- Precisione sempre maggiore man mano che si completa codice

---

### FASE 5: Validazione e Affinamento

**Obiettivo:** Verificare che DEV UNITS estratte corrispondano a schema E/R ottimizzato

**Processo:**
1. Confrontare DEV UNITS estratte con:
   - Schema E/R ottimizzato (da analisi ODS)
   - Attività macro definite
2. Verificare:
   - Semantic_path corretti (nomi reali campi/funzioni)
   - Categorie corrette (database, be_data, be_logic, ui_layout, fe_logic)
   - Copertura completa (tutte le entità, tutti i campi, tutte le funzionalità)
3. Se mancano DEV UNITS:
   - Identificare codice mancante
   - Tornare a FASE 4 (creazione codice)
   - Iterare fino a copertura completa

**Output:**
- DEV UNITS validate e corrette
- Codice sorgente completo per migrazione 1:1
- Documentazione finale schema E/R vs codice

---

## COMANDI FOOTILITY DA USARE

### 1. Analisi Completa Progetto (E/R + DEV UNITS)
```bash
php artisan project:analyze-full {project_id} [--force] [--skip-er]
```
- `--force`: Cancella dati esistenti prima di analizzare
- `--skip-er`: Salta analisi E/R (solo DEV UNITS)

### 2. Analisi Solo DEV UNITS
```bash
php artisan devunits:analyze {project_id}
```

### 3. Aggiornamento Repository
```bash
# GitManager aggiorna automaticamente durante analisi
# Oppure manualmente:
cd storage/app/projects/{project_id}
git pull
```

---

## VANTAGGI APPROCCIO

### vs Creazione DEV UNITS Manuale

**APPROCCIO CORRETTO (Estrarre da Codice):**
- ✅ DEV UNITS reali dal codice esistente
- ✅ Semantic_path corretti (estratti automaticamente)
- ✅ Tracciabilità: codice → DEV UNIT
- ✅ Validazione automatica: codice esistente = DEV UNIT estratta
- ✅ Iterativo: creo codice → ri-analizzo → DEV UNITS aggiornate
- ✅ Precisione: codice mancante viene creato → DEV UNITS corrispondenti

**APPROCCIO PRECEDENTE (Creazione Manuale):**
- ❌ DEV UNITS generiche senza corrispondenza codice
- ❌ Semantic_path inventati (non reali)
- ❌ Nessuna tracciabilità
- ❌ Nessuna validazione

---

## SEQUENZA OPERAZIONI

1. ✅ **FASE 0:** Inizializzare Git nel progetto gestionale-altramusica
2. ✅ **FASE 1:** Pulire DEV UNITS errate da Footility
3. ✅ **FASE 2:** Eseguire prima scansione DEV UNITS sul codice esistente
4. ✅ **FASE 3:** Analizzare gap: cosa c'è vs cosa serve
5. ⏳ **FASE 4:** Creare codice mancante (iterativo)
6. ⏳ **FASE 5:** Validare e affinare

---

## RISULTATO ATTESO

Al termine del procedimento:
- ✅ Repository Git funzionante e sincronizzato
- ✅ Codice sorgente completo per migrazione 1:1 ODS → Laravel
- ✅ DEV UNITS reali estratte dal codice con semantic_path corretti
- ✅ Validazione schema E/R ottimizzato vs codice implementato
- ✅ Possibilità di generare preventivo accurato basato su DEV UNITS reali
- ✅ Codice già fatto → possiamo farci pagare per qualcosa che esiste già

---

## NOTE IMPORTANTI

1. **Non ricreare DEV UNITS manualmente:** Estrarle sempre dal codice
2. **Creare codice mancante prima:** Poi ri-analizzare per ottenere DEV UNITS
3. **Iterativo:** Migliorare progressivamente precisione
4. **Validazione continua:** Verificare che DEV UNITS corrispondano a codice reale
5. **Tracciabilità:** Ogni DEV UNIT deve avere corrispondenza nel codice sorgente
