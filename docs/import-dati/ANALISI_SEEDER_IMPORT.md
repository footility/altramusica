# Analisi Seeder e Importazione Dati

## Stato Implementazione Seeder

### ✅ Completati
1. **AdminSeeder** - Crea utente admin
2. **AcademicYearSeeder** - Crea anno accademico 2025-26
3. **TeacherSeeder** - Importa docenti da `dati lavoratori 25-26.ods`
4. **StudentSeeder** - Importa studenti da `db 2025-26 gestionale.ods`
5. **GuardianSeeder** - Verifica relazioni studenti-genitori

### ✅ Completati (con miglioramenti)
6. **ContractSeeder** - ✅ RISOLTO: Gestisce formule esterne che referenziano file gestionale
7. **InvoiceSeeder** - Struttura base, logica da completare
8. **InstrumentSeeder** - Struttura base, logica da completare
9. **ExamSeeder** - Struttura base, logica da completare
10. **CalendarSeeder** - Struttura base, logica da completare

## Problemi Identificati e Risolti

### 1. Contratti - Formule Esterne ✅ RISOLTO
**Problema**: Il file `Db Contratti 25-26.ods` contiene formule che referenziano celle del file `db 2025-26 gestionale.ods`.

**Soluzione Implementata**: 
- Il seeder ora carica anche il file gestionale quando rileva riferimenti esterni
- Estrae il riferimento cella dalla formula (es: `#$dati!H2`)
- Legge il valore direttamente dal file gestionale

**Esempio Formula**: `='file:///.../db%202025-26%20gestionale.ods'#$dati!H2`
**Soluzione**: Parsing regex per estrarre `H2` e leggere dal file gestionale.

### 2. Contratti - Gestione Corsi Multipli
**Problema**: Nel file ODS, un contratto può avere fino a 3 corsi (Corso 1, Corso 2, Corso 3) e orchestra.

**Analisi Modello Attuale**:
- `Contract` ha solo campo `terms` (testo) per descrizione
- Non c'è relazione diretta tra `Contract` e `Course` o `Enrollment`

**Soluzione Proposta**:
- Creare tabella pivot `contract_courses` per collegare contratti a corsi/enrollments
- Oppure: usare `Enrollment` come collegamento (un enrollment = un corso nel contratto)

**Raccomandazione**: Usare `Enrollment` come collegamento naturale. Un contratto può avere più enrollments.

### 3. Fatture - Rate Multiple
**Problema**: Nel file ODS, le fatture hanno rate multiple (1°, 2°, 3° rata) con scadenze e importi diversi.

**Analisi Modello Attuale**:
- `Invoice` esiste
- `PaymentPlan` esiste per rate
- `Payment` esiste per pagamenti

**Verifica Necessaria**:
- `PaymentPlan` supporta rate multiple con scadenze diverse?
- Come gestire note di credito (ncr) e importi non fatturati?

**Raccomandazione**: Verificare se `PaymentPlan` è sufficiente o serve estendere.

### 4. Strumenti - Noleggi
**Problema**: Nel file ODS, gli strumenti possono essere noleggiati con durata, costo mensile, scadenza.

**Analisi Modello Attuale**:
- `Instrument` esiste
- `InstrumentRental` esiste per noleggi

**Verifica Necessaria**:
- `InstrumentRental` ha tutti i campi necessari (durata, costo mensile, scadenza)?

**Raccomandazione**: Verificare e completare se necessario.

### 5. Esami - Esami Multipli per Studente
**Problema**: Nel file ODS, uno studente può avere fino a 5 esami diversi.

**Analisi Modello Attuale**:
- `Exam` esiste con relazione `belongsTo(Student)`

**Verifica Necessaria**:
- Il modello supporta esami multipli? (Sì, relazione 1:N)
- Come parsare i dettagli degli esami dal file ODS?

**Raccomandazione**: Il modello è adeguato, serve solo implementare la logica di parsing.

### 6. Calendario - Struttura Dati
**Problema**: Il file `Calendario 2025-26.ods` ha struttura complessa con formule Excel.

**Analisi Necessaria**:
- Identificare quali fogli contengono le date di lezione
- Identificare quali fogli contengono le sospensioni
- Parsare le date correttamente

**Raccomandazione**: Analisi approfondita del file necessario.

## Ordine di Esecuzione Seeder

L'ordine attuale è corretto:
1. AdminSeeder (base)
2. AcademicYearSeeder (base)
3. TeacherSeeder (necessario per corsi futuri)
4. StudentSeeder (base per relazioni)
5. GuardianSeeder (dipende da studenti)
6. ContractSeeder (dipende da studenti)
7. InvoiceSeeder (dipende da contratti/studenti)
8. InstrumentSeeder (indipendente)
9. ExamSeeder (dipende da studenti)
10. CalendarSeeder (dipende da anno accademico)

## Prossimi Passi

1. **Correggere ContractSeeder**: Risolvere problema `academic_year_id`
2. **Completare InvoiceSeeder**: Implementare logica rate multiple
3. **Completare InstrumentSeeder**: Implementare importazione noleggi
4. **Completare ExamSeeder**: Implementare parsing esami multipli
5. **Completare CalendarSeeder**: Analizzare struttura e implementare
6. **Valutare Miglioramenti Modello**: Decidere se aggiungere `academic_year_id` a contracts

## Note Tecniche

- I seeder possono essere eseguiti più volte (idempotenti con `firstOrCreate`)
- I file ODS vengono riletti per ogni seeder (può essere ottimizzato in futuro)
- Gli errori vengono loggati ma non bloccano l'esecuzione
- Le relazioni vengono create automaticamente dove possibile

