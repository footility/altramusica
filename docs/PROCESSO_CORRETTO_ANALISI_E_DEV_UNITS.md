# Processo Corretto Analisi ODS → E/R → DEV UNITS

**Data:** Gennaio 2026  
**Obiettivo:** Definire metodologia corretta per analisi ODS e generazione DEV UNITS realistiche basate su modello E/R ottimizzato

---

## PROBLEMA ATTUALE

Il processo precedente ha creato DEV UNITS con semantic_path generici (`field_1`, `field_2`, `ui_element_1`) invece di usare nomi reali colonne ODS. Questo non permette:
- Tracciabilità semantica corretta
- Confronto con database reale
- Validazione schema E/R
- Calibrazione accurata

---

## PROCESSO CORRETTO (Metodico e Strutturato)

### FASE 1: ANALISI SITUAZIONE ATTUALE - Tracciamento Colonne ODS

**Obiettivo:** Comprendere completamente la struttura dati attuale dagli ODS

**Input:**
- File ODS originali:
  - `db 2025-26 gestionale.ods` (file principale)
  - `Db Contratti 25-26.ods`
  - `Db Contabile 2025-26.ods`
  - `Db Accessori 2025-26.ods`
  - `Calendario 2025-26.ods`
  - `dati lavoratori 25-26.ods`

**Processo:**
1. **Estrae tutte le colonne ODS con:**
   - Nome colonna originale (es. "Cognome", "Cod. Fiscale Allievo")
   - Lettera colonna (es. G, H, V)
   - Tipo dato dedotto (string, date, number, boolean)
   - Valori esempio (per validazione)
   - Note/commenti presenti

2. **Raggruppa colonne per contesto semantico:**
   - Anagrafiche base (Cognome, Nome, Cod. Fiscale)
   - Disponibilità (Lu, Ma, Me, Gio, Ve, Sab, Lab)
   - Strumenti (Fornitore, Tipo, Marca, Modello, etc.)
   - Livelli (Livello, Livello str., Livello teoria)
   - Contratti/Pagamenti (Richiesta pagamento, Pagato, Contratto)
   - Corsi/Iscrizioni (Corso 1, Docente, Aula, Giorno, Ora)

3. **Identifica relazioni implicite:**
   - Collegamenti tra fogli (es. Contratto → Studente)
   - Riferimenti incrociati (es. "Corso 1" nel foglio studenti → foglio corsi)
   - Dipendenze funzionali (es. Rate dipendono da Contratto)

**Output:**
- Documento completo colonne ODS per ogni foglio
- Mappa colonne → contesto semantico
- Identificazione entità logiche (non ancora normalizzate)

---

### FASE 2: COMPRENSIONE SEMANTICA - Entità, Relazioni, Campi

**Obiettivo:** Trasformare colonne ODS in entità logiche normalizzate Laravel

**Processo:**

1. **Identifica Entità Logiche:**
   Per ogni gruppo semantico di colonne ODS:
   - Studente (da colonne: Cognome, Nome, Cod. Fiscale, Nato il, Età, etc.)
   - Genitore/Tutore (da colonne: Cognome Genitore 1, Nome Genitore 1, Cell 1, Mail 1, etc.)
   - Docente (da colonne: Cognome, Nome, Cod. fisc., P. IVA, IBAN, etc.)
   - Corso (da colonne: Corso 1, Docente, Aula, Giorno, Ora, N. settimane/anno, etc.)
   - Contratto (da colonne: Codice contratto, Stato, Data inizio, Tipologia, Rate, etc.)
   - Fattura (da colonne: Numero fattura, Data emissione, Scadenza, Subtotale, etc.)
   - etc.

2. **Normalizza Struttura:**
   Per ogni entità identificata:
   - Separa dati normalizzati (es. Disponibilità → tabella StudentAvailability)
   - Rileva relazioni molti-a-molti (es. Student ↔ Guardian)
   - Identifica chiavi primarie/esterne logiche
   - Determina campi obbligatori vs opzionali

3. **Mappa Colonne ODS → Campi Laravel:**
   Per ogni campo:
   - Nome colonna ODS originale (es. "Cognome")
   - Nome campo Laravel normalizzato (es. `last_name`)
   - Tipo campo (string, date, decimal, boolean, text, enum)
   - Vincoli (nullable, unique, default)
   - Relazioni (belongsTo, hasMany, belongsToMany)

**Output:**
- Schema E/R concettuale (non ancora ottimizzato)
- Mapping colonne ODS → campi Laravel
- Relazioni identificate (con tipo: one-to-many, many-to-many, etc.)

---

### FASE 3: GENERAZIONE MODELLO E/R OTTIMIZZATO

**Obiettivo:** Creare schema E/R moderno e normalizzato adeguato per migrazione 1:1 Laravel

**Processo:**

1. **Ottimizza Schema:**
   - Normalizza ulteriormente (3NF o superiore)
   - Raggruppa campi correlati (es. Disponibilità → StudentAvailability, TeacherAvailability)
   - Separa entità polivalenti (es. StudentLevel, InstrumentRental)
   - Gestisce relazioni molti-a-molti con pivot tables

2. **Definisci Entità Finali:**
   Per ogni entità:
   - Nome tabella (snake_case plural: `students`, `guardians`, `courses`)
   - Nome model (PascalCase singular: `Student`, `Guardian`, `Course`)
   - Campi esatti con tipi e vincoli
   - Indici necessari (per performance e relazioni)
   - Timestamps (created_at, updated_at)
   - Soft deletes (se necessario)

3. **Definisci Relazioni Laravel:**
   Per ogni entità:
   - Relazioni belongsTo (FK)
   - Relazioni hasMany (FK inversa)
   - Relazioni belongsToMany (pivot)
   - Relazioni polimorfiche (se necessario)

**Output:**
- Schema E/R ottimizzato completo
- Lista tabelle con campi esatti
- Lista relazioni con tipi Laravel
- Documentazione modello per ogni entità

---

### FASE 4: CREAZIONE DEV UNITS DATABASE

**Obiettivo:** Creare DEV UNITS database basate sullo schema E/R ottimizzato

**Processo:**

1. **Per ogni Tabella:**
   - **DEV UNIT database per ogni campo:**
     - semantic_path: `Entity/FieldName/table_name.field_name`
     - Esempio: `Student/LastName/students.last_name`
     - Campo: `database`
     - source_file_path: `database/migrations/create_students_table.php`

2. **Per ogni Relazione:**
   - **DEV UNIT database per FK (se esplicita):**
     - semantic_path: `Entity/RelatedEntityId/table_name.related_entity_id`
     - Esempio: `Student/AcademicYearId/students.academic_year_id`
     - Campo: `database`
   
   - **DEV UNIT data_relation per relazione Laravel:**
     - semantic_path: `Entity/_RelatedEntity/relation_name`
     - Esempio: `Student/_Guardian/guardians` (belongsToMany)
     - Campo: `data_relation`
     - source_file_path: `app/Models/Student.php`

**Output:**
- DEV UNITS database complete per tutte le tabelle
- DEV UNITS data_relation per tutte le relazioni
- Semantic_path corretti con nomi reali colonne/campi

---

### FASE 5: CREAZIONE DEV UNITS ALTRE CATEGORIE

**Obiettivo:** Basandosi sullo schema E/R e analisi funzionalità, creare DEV UNITS per be_logic, be_data, ui_layout, fe_logic

**Processo:**

1. **DEV UNITS be_data (Model Fillable):**
   Per ogni campo del model (dallo schema E/R):
   - semantic_path: `Entity/FieldName/field_name`
   - Esempio: `Student/LastName/last_name`
   - Campo: `be_data`
   - source_file_path: `app/Models/Student.php`
   
   **Nota:** Stesso numero di DEV UNITS database (1:1 mapping)

2. **DEV UNITS be_logic (Controller + Model Methods):**
   Per ogni entità, basandosi su funzionalità analizzate:
   
   **CRUD Base (7 DEV UNITS):**
   - `Entity/Index/index` → Controller@index
   - `Entity/Create/create` → Controller@create
   - `Entity/Store/store` → Controller@store
   - `Entity/Show/show` → Controller@show
   - `Entity/Edit/edit` → Controller@edit
   - `Entity/Update/update` → Controller@update
   - `Entity/Destroy/destroy` → Controller@destroy
   
   **Metodi Custom (basati su analisi funzionalità ODS):**
   - `Student/ConvertToClient/convertToClient` (funzionalità: conversione prospect → cliente)
   - `Student/CalculateAge/calculateAge` (calcolo età da birth_date)
   - `Contract/GenerateToken/generateToken` (generazione token contratto)
   - `Invoice/GenerateNumber/generateNumber` (generazione numero fattura)
   - etc.
   
   **Workflow/Import (se necessario):**
   - `Student/ImportFromOds/importFromOds` (import dati ODS)
   - `Contract/TrackStatus/trackStatus` (tracciamento stato)
   - etc.

3. **DEV UNITS ui_layout (Form + Liste + Filtri):**
   
   **Form (per ogni campo form):**
   - semantic_path: `Entity/FieldName/field_name_input`
   - Esempio: `Student/LastName/last_name_input`
   - Campo: `ui_layout`
   - source_file_path: `resources/views/students/form.blade.php`
   
   **Lista (per ogni colonna):**
   - semantic_path: `Entity/FieldName/field_name_column`
   - Esempio: `Student/LastName/last_name_column`
   - Campo: `ui_layout`
   - source_file_path: `resources/views/students/index.blade.php`
   
   **Filtri (per ogni filtro):**
   - semantic_path: `Entity/FilterName/filter_name`
   - Esempio: `Student/StatusFilter/status_filter`
   - Campo: `ui_layout`
   - source_file_path: `resources/views/students/index.blade.php`

4. **DEV UNITS fe_logic (JavaScript):**
   Solo se necessario (Laravel base minimo JS):
   - `Entity/ActionName/js_function_name`
   - Esempio: `Student/ToggleAvailability/toggleAvailability`
   - Campo: `fe_logic`
   - source_file_path: `resources/js/students.js`

**Output:**
- DEV UNITS complete per tutte le categorie
- Semantic_path corretti con nomi reali funzionalità
- Tracciabilità completa: ODS → E/R → DEV UNIT

---

## VANTAGGI PROCESSO CORRETTO

### vs Processo Precedente (Generico)

**PRIMA (Sbagliato):**
- ❌ Semantic_path generici (`field_1`, `field_2`)
- ❌ Nessuna analisi semantica colonne ODS
- ❌ Nessun modello E/R ottimizzato
- ❌ DEV UNITS create "a caso" senza tracciabilità

**ORA (Corretto):**
- ✅ Semantic_path semantici (`Student/LastName/students.last_name`)
- ✅ Analisi completa colonne ODS → campi Laravel
- ✅ Modello E/R ottimizzato e documentato
- ✅ Tracciabilità completa: ODS → E/R → DEV UNIT
- ✅ Validazione schema possibile
- ✅ Calibrazione accurata basata su struttura reale

---

## ESEMPIO CONCRETO: Student

### FASE 1: Analisi Colonne ODS
```
Colonna G: "Cognome" → Tipo: string → Esempi: "Rossi", "Bianchi"
Colonna H: "Nome" → Tipo: string → Esempi: "Mario", "Luigi"
Colonna V (cod. fiscale): "Cod. Fiscale Allievo" → Tipo: string → Esempi: "RSSMRA80A01H501Z"
...
```

### FASE 2: Comprensione Semantica
```
Entità: Student
Campi identificati:
- last_name (da "Cognome")
- first_name (da "Nome")
- tax_code (da "Cod. Fiscale Allievo")
- birth_date (da "Nato il")
- age (da "Età", calcolato o da ODS)
- is_minor (da "Minore")
...
Relazioni:
- Student → AcademicYear (belongsTo)
- Student → Guardian (belongsToMany, pivot: student_guardian)
- Student → Enrollment (hasMany)
...
```

### FASE 3: Schema E/R Ottimizzato
```php
// Migration: create_students_table.php
Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->foreignId('academic_year_id')->constrained();
    $table->string('code')->nullable();
    $table->string('first_name');
    $table->string('last_name');
    $table->string('tax_code')->nullable()->unique();
    $table->date('birth_date')->nullable();
    $table->integer('age')->nullable();
    $table->boolean('is_minor')->default(false);
    $table->date('first_contact_date')->nullable();
    $table->enum('status', ['interested', 'enrolled', 'former'])->default('interested');
    $table->string('how_know_us')->nullable();
    $table->text('notes')->nullable();
    $table->text('admin_notes')->nullable();
    $table->date('last_contact_date')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

// Model: Student.php
class Student extends Model {
    protected $fillable = [
        'academic_year_id', 'code', 'first_name', 'last_name', 
        'tax_code', 'birth_date', 'age', 'is_minor', 
        'first_contact_date', 'status', 'how_know_us', 
        'notes', 'admin_notes', 'last_contact_date',
    ];
    
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function guardians() { return $this->belongsToMany(Guardian::class); }
    public function enrollments() { return $this->hasMany(Enrollment::class); }
    // etc.
}
```

### FASE 4: DEV UNITS Database
```
Student/Id/students.id (category: database)
Student/AcademicYearId/students.academic_year_id (category: database)
Student/Code/students.code (category: database)
Student/FirstName/students.first_name (category: database)
Student/LastName/students.last_name (category: database)
Student/TaxCode/students.tax_code (category: database)
...
Student/_AcademicYear/academic_year (category: data_relation)
Student/_Guardian/guardians (category: data_relation)
Student/_Enrollment/enrollments (category: data_relation)
```

### FASE 5: DEV UNITS Altre Categorie
```
// be_data (stesso numero di database)
Student/Id/id (category: be_data)
Student/FirstName/first_name (category: be_data)
Student/LastName/last_name (category: be_data)
...

// be_logic
Student/Index/index (category: be_logic)
Student/Create/create (category: be_logic)
Student/Store/store (category: be_logic)
Student/Show/show (category: be_logic)
Student/Edit/edit (category: be_logic)
Student/Update/update (category: be_logic)
Student/Destroy/destroy (category: be_logic)
Student/ConvertToClient/convertToClient (category: be_logic)
Student/CalculateAge/calculateAge (category: be_logic)

// ui_layout
Student/FirstName/first_name_input (category: ui_layout)
Student/LastName/last_name_input (category: ui_layout)
Student/FirstName/first_name_column (category: ui_layout)
Student/LastName/last_name_column (category: ui_layout)
Student/StatusFilter/status_filter (category: ui_layout)
...

// fe_logic (minimo)
Student/ToggleAvailability/toggleAvailability (category: fe_logic)
```

---

## DIFFERENZE CHIAVE

### Semantic Path Generico (SBAGLIATO):
```
AcademicYear/Field1/field_1
AcademicYear/Field2/field_2
Student/Field1/field_1
Student/UiElement1/ui_element_1
```

### Semantic Path Semantico (CORRETTO):
```
AcademicYear/Name/name
AcademicYear/StartDate/start_date
Student/LastName/students.last_name
Student/LastName/last_name_input
Student/StatusFilter/status_filter
```

---

## PROSSIMI PASSI

1. ✅ Documentare processo corretto (questo documento)
2. ⏳ Eseguire FASE 1: Analisi completa colonne ODS con tracciamento
3. ⏳ Eseguire FASE 2: Comprensione semantica e mapping colonne → campi
4. ⏳ Eseguire FASE 3: Generare schema E/R ottimizzato completo
5. ⏳ Eseguire FASE 4: Creare DEV UNITS database con semantic_path corretti
6. ⏳ Eseguire FASE 5: Creare DEV UNITS altre categorie basate su schema E/R
7. ⏳ Validare DEV UNITS contro schema E/R
8. ⏳ Associare DEV UNITS alle attività macro

---

## CONCLUSIONE

Il processo corretto richiede:
1. **Analisi approfondita** prima di creare DEV UNITS
2. **Comprensione semantica** delle colonne ODS e loro significato
3. **Schema E/R ottimizzato** come base solida
4. **Tracciabilità completa** da ODS → E/R → DEV UNIT
5. **Semantic_path semantici** invece di generici

Questo garantisce:
- ✅ DEV UNITS realistiche e accurate
- ✅ Tracciabilità e validazione possibili
- ✅ Calibrazione basata su struttura reale
- ✅ Manutenibilità e comprensibilità
