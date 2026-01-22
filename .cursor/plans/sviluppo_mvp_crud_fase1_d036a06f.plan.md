---
name: Sviluppo MVP CRUD Fase1
overview: Realizzare in Laravel un backend Bootstrap 5 (light, componentizzato) che copra tutte le funzionalità AS‑IS identificate nei fogli ODS/Excel (12 sezioni), riusando dove possibile CRUD e servizi già presenti e completando ciò che manca (import, validazioni, viste).
todos:
  - id: ui-foundation
    content: Componentizzare layout admin e applicare tema Bootstrap 5 light (palette + sidebar + componenti riusabili) riusando i componenti esistenti in resources/views/components/admin; introdurre menu a gruppi/separatori per aree funzionali
    status: completed
  - id: crud-gap-analysis
    content: "Per ciascuna sezione 01..12: mappare funzionalità AS-IS su controller/model/view/migration esistenti e identificare gap (entità mancanti, campi mancanti, relazioni, filtri), classificando ogni dato come master vs annuale (AcademicYear-scoped) secondo la strategia"
    status: completed
    dependencies:
      - ui-foundation
  - id: crud-implement-missing
    content: Implementare CRUD mancanti per coprire AS-IS (es. noleggi strumenti, libri, pagamenti/piani, documenti) e completare i CRUD esistenti dove mancano campi/relazioni/filtri
    status: in_progress
    dependencies:
      - crud-gap-analysis
  - id: import-and-validation
    content: "Completare/importare flussi dati (seeders ODS, import XLSX modulo disponibilità) con deduplica e validazioni minime, evitando update “distruttivi” che spostano dati storici: i dati per anno vanno scritti su entità annuali"
    status: in_progress
    dependencies:
      - crud-implement-missing
  - id: asis-reports
    content: Aggiungere viste/report semplici per AS-IS (scadenzario, stato pagato/saldo, crediti/debiti, statistiche storiche) senza automatismi avanzati
    status: pending
    dependencies:
      - import-and-validation
---

# Piano di sviluppo: Fase 1 MVP CRUD (AS‑IS da ODS/Excel)

## Scope

- **In scope**: solo le **12 sezioni AS‑IS** documentate in `docs/01..12` + `docs/ANALISI_COLONNE_ODS_COMPLETA.md`.
- **Out of scope (per ora)**: moduli extra già presenti ma non AS‑IS (Primi Contatti, Proposte Orarie, Comunicazioni, Attività Extra, Registro docente, ecc.). Rimangono nel codice ma **non vengono estesi**; opzionalmente li nascondiamo dal menu admin in Fase 1.
- **Vincolo**: non implementare funzionalità che richiedono **servizi esterni** (email provider, SMS/WhatsApp, SDI, integrazioni fiscali, ecc.). Se un pezzo AS‑IS oggi è “manuale” (invio mail, invio PDF), in Fase 1 rimane manuale.
- **Vincolo architetturale**: applicare il pattern **Master vs Annuale (AcademicYear-scoped)** per evitare propagazione retroattiva di tariffe/regole/calendari.

## Strategia dati (vincolo architetturale Fase 1): Master vs Annuale (AcademicYear-scoped)

**Obiettivo**: evitare che modifiche future (tariffe, regole corsi, calendario, rate, ecc.) “contaminino” gli anni passati.

- **Principio**: separare entità **master/stabili** da entità **time-scoped** (legate a `AcademicYear`).
- **Regola**: ogni implementazione in Fase 1 deve rispettare questa strategia (anche se alcune entità restano minimali).

### Master (stabili, senza `academic_year_id`)

Anagrafiche e definizioni “di catalogo”, valide trasversalmente nel tempo:

- Studenti (persona)
- Genitori/Tutori
- Docenti/Lavoratori (anagrafica base)
- Corsi (catalogo: es. “Chitarra”)
- Aule
- Strumenti (cespiti)
- Libri (catalogo)

### Annuali/Operative (time-scoped, con `academic_year_id`)

Dati che rappresentano configurazioni e fatti **dell’anno**:

- Iscrizioni, contratti, fatture, piani di pagamento/pagamenti
- Calendario annuale e sospensioni
- Noleggi strumenti, distribuzioni libri, esami, accessori
- Stato studente nell’anno (interessato/iscritto/preiscritto/ritirato) e note operative per esercizio

### Pattern corsi (best practice)

- `Course` = definizione stabile (es. “Chitarra”)
- `CourseOffering` = istanza per anno (es. “Chitarra 2025-26”) con tariffe/regole variabili
- `Enrollment` = iscrizione operativa dello studente all’offerta (docente/aula/orari/date + eventuali override)

### Snapshot economico

- Importi e descrizioni **fatturati** devono essere salvati come snapshot su righe fattura (es. `InvoiceItem`)
- Non ricalcolare a posteriori leggendo i prezzi da `Course`/`CourseOffering`

## Stato attuale (riuso del codice esistente)

Già presenti nel backend admin (routes + controller + views):

- Anno scolastico: `routes/web.php`, `app/Http/Controllers/Admin/AcademicYearController.php`, `resources/views/admin/academic-years/*`
- Studenti/Genitori/Docenti: `StudentController`, `GuardianController`, `TeacherController` + CRUD views
- Corsi/Iscrizioni: `CourseController`, `EnrollmentController` + CRUD views
- Contratti: `ContractController` + CRUD views
- Fatture: `InvoiceController` + CRUD views + azioni per piano pagamenti e registrazione pagamento
- Calendari: `CalendarController`, `LessonCalendarController` (già esiste UI)
- Strumenti/Esami: `InstrumentController`, `ExamController` + CRUD views
- Componenti UI già avviati: `app/View/Components/Admin/*` e `resources/views/components/admin/*`
- Build frontend: Bootstrap 5 via `resources/sass/app.scss` + Vite (`vite.config.js`)

Gap principali rispetto all’AS‑IS:

- Gestione **Accessori/Libri/Noleggi/Cauzioni** (modelli esistono, CRUD/admin UI manca per varie entità)
- Contabilità “AS‑IS” è più ampia di “solo fatture”: servono viste/filtri/indicatori per pagato, scadenze, crediti/debiti, solleciti (versione semplice)
- Statistiche storiche (AS‑IS foglio `grafico`): manca una vista dedicata
- Documenti/Modelli: manca una gestione documentale minima (upload/archivio)
- UI admin è ancora molto “template inline” (`resources/views/layouts/admin.blade.php`) e sidebar dark (tu vuoi **light + componentizzato**)

## Integrazione con attività e sotto-attività (Footility) + tracciabilità tramite commit

Obiettivo: senza toccare le attività del preventivo, mantenere un collegamento chiaro tra:

- attività Footility (12 sezioni AS‑IS),
- documentazione `docs/01..12`,
- file di codice creati modificati o già presenti inerenti all'attività (da tanere a parte non nel'attività stessa)

### Regola commit (obbligatoria)

- **Commit per task** (o micro-gruppi di task omogenei), così possiamo ricostruire in futuro il mapping verso DEV UNIT.\n
- Formato consigliato commit message:\n
- `A01 - <task>` (A01..A12 corrispondono alle 12 attività/sezioni)\n
- esempi: `A01 - Stato operativo studente`, `A05 - Scadenzario rate (PaymentPlan)`\n
- Ogni commit deve includere solo file pertinenti al task (evitare “misc changes”).\n

### Mapping attività -> documenti

- **A01** Anagrafiche Studenti -> `docs/01_ANAGRAFICHE_STUDENTI.md`\n
- **A02** Genitori/Tutori -> `docs/02_GENITORI_TUTORI.md`\n
- **A03** Corsi e Iscrizioni -> `docs/03_CORSI_E_ISCRIZIONI.md`\n
- **A04** Contratti Studenti -> `docs/04_CONTRATTI_STUDENTI.md`\n
- **A05** Contabilità Corsi -> `docs/05_CONTABILITA_CORSI.md`\n
- **A06** Contabilità Accessori/Noleggi/Cauzioni -> `docs/06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md`\n
- **A07** Accessori/Noleggi/Libri/Esami -> `docs/07_ACCESSORI_NOLEGGI_LIBRI.md`\n
- **A08** Docenti/Lavoratori -> `docs/08_DOCENTI_LAVORATORI.md`\n
- **A09** Calendario Annuale -> `docs/09_CALENDARIO_ANNUALE.md`\n
- **A10** Modulo Anagrafica e Disponibilità -> `docs/10_MODULO_ANAGRAFICA_E_DISPONIBILITA.md`\n
- **A11** Statistiche Storiche -> `docs/11_STATISTICHE_STORICHE.md`\n
- **A12** Documenti e Modelli Contratti -> `docs/12_DOCUMENTI_E_MODELLI_CONTRATTI.md`\n

## Architettura UI (Bootstrap 5 light + componentizzata)

Obiettivo: mantenere Blade semplice ma riusabile.

- Estrarre layout admin in componenti:
- `x-admin.layout` (shell + sidebar + topbar)
- `x-admin.sidebar` (menu, gruppi, stati attivi)
- `x-admin.page-header` (titolo, breadcrumb, CTA)
- `x-admin.alerts` (success/error/validation)
- `x-admin.card` / `x-admin.stat-card`
- riuso di `x-admin.data-table`, `x-admin.filter-bar`, `x-admin.form-field`
- Tema light:
- definire palette e variabili in SCSS (es. `$primary`, `$secondary`, `$body-bg`, `$card-border-color`, ecc.) in `resources/sass/_variables.scss` + eventuale file `resources/sass/admin/_theme.scss`.
- Sidebar **light** (non scura), con accento colore.
- Tipografia/spacing moderni, senza aumentare complessità (no JS custom salvo Bootstrap).

### Menu admin: raggruppamento per aree (no loop di navigazione)

Obiettivo: mantenere divisione per aree, con menu leggibile e scalabile.\n

- Introdurre gruppi/separatori statici (non cliccabili) per categorie, ad es.:\n
- **Anagrafiche**: Studenti, Genitori/Tutori, Docenti\n
- **Didattica**: Corsi, Iscrizioni, Calendario\n
- **Amministrazione**: Contratti, Fatture/Pagamenti, Accessori/Noleggi\n
- **Report**: Statistiche\n
- **Documenti**: Modelli/Archivio\n
- Dashboard: evidenziare informazioni “operative” senza creare nuove sezioni (es. scadenze rate imminenti, contratti da firmare, fatture scadute).\n

## Piano funzionale per le 12 sezioni (deliverable)

Per ogni sezione, il deliverable è: **CRUD + filtri minimi + relazioni nei form + viste utili**.

1) **Anagrafiche Studenti** (`docs/01_ANAGRAFICHE_STUDENTI.md`)

- Verificare copertura campi ODS (stato, contatti, note, indirizzo, storico contatti, età/minore).
- Rafforzare form e validazioni.
- Liste con filtri: anno, stato, ricerca.

2) **Genitori/Tutori** (`docs/02_GENITORI_TUTORI.md`)

- CRUD + relazione con studente (pivot se già presente).
- UI per collegare/scollegare genitori dallo studente.

3) **Corsi e Iscrizioni** (`docs/03_CORSI_E_ISCRIZIONI.md`)

- Verifica: corso 1/2/3, lab teoria, tipologia, docenti, aule, giorni/orari.
- Migliorare form iscrizioni con select coerenti (studente, corso, docente).

4) **Contratti Studenti** (`docs/04_CONTRATTI_STUDENTI.md`)

- CRUD completo + stati (draft/sent/signed/expired/cancelled).
- Collegamenti rapidi: da studente → contratti; da contratto → crea fattura.

5) **Contabilità Corsi** (`docs/05_CONTABILITA_CORSI.md`)

- Basare su `Invoice`/`Payment`/`PaymentPlan` già esistenti.
- Aggiungere viste “semplici”:
- scadenzario (rate/piani),
- stato pagato/saldo,
- elenco crediti/debiti per studente.

6) **Contabilità Accessori/Noleggi/Cauzioni** (`docs/06_...`)

- Estendere fatture/pagamenti per gestire accessori/cauzioni (minimo: voci in fattura, stato cauzione).
- Vista riepilogo cauzioni e scadenze noleggi.

7) **Accessori/Noleggi/Libri/Esami** (`docs/07_...`)

- Implementare CRUD mancanti:
- `InstrumentRental` (noleggi)
- `Book` (+ eventuale distribuzione)
- Accessori (in base ai modelli presenti: `Instrument`, `Book`, eventuale entità accessori)
- Integrare con studenti (relazioni e navigazione).

8) **Docenti/Lavoratori** (`docs/08_...`)

- Verifica campi fiscali/documenti/IBAN, disponibilità.
- CRUD docenti già presente: integrare ciò che manca dai fogli.

9) **Calendario Annuale** (`docs/09_...`)

- Stabilizzare generazione e gestione sospensioni/lezioni libere.
- Se il layout ODS richiede: aggiungere una “vista configurazione cicli” semplice.

10) **Modulo Anagrafica e Disponibilità** (`docs/10_...`)

- Import e gestione risposte modulo:
- comando/azione admin per importare XLSX,
- deduplica per CF,
- generazione record/aggiornamenti su studenti + disponibilità.

11) **Statistiche Storiche** (`docs/11_...`)

- Creare pagina admin “Statistiche” con grafico/tabella (anche solo tabella in MVP) basata sui dati importati dal foglio `grafico`.

12) **Documenti e Modelli Contratti** (`docs/12_...`)

- Gestione minima documenti:
- upload template,
- archivio contratti generati/inviati (metadati: studente, anno, tipo, data).

## Associazione “doc/attività” -> codice

Durante lo sviluppo, manterremo un mapping stabile:

- Ogni sezione doc `docs/01..12` ha:
- controller/admin routes coinvolte,
- modelli e relazioni,
- viste,
- seed/import.

## Milestone suggerite

- **M1 UI foundation**: componentizzazione layout + theme light + refactor delle view esistenti per usare componenti.
- **M2 CRUD completion**: completare i CRUD mancanti (noleggi, libri, pagamenti/piani, documenti) e allineare i CRUD già presenti ai campi AS‑IS.
- **M3 Import & validation**: import XLSX modulo + hardening seeders/import ODS.
- **M4 Reportistiche AS‑IS**: scadenzario/pagato/crediti-debiti + statistiche storiche.

## Note operative

- Niente SPA/AJAX complesso.
- Bootstrap 5, tema light, componenti Blade.
- Test minimi: focus su import e relazioni critiche (studente↔genitore, fatture↔pagamenti, noleggi).
- Tutti i commit devono rispettare la strategia Master vs Annuale; eventuali eccezioni vanno documentate e motivate.
- Asset pipeline: usare **Vite** (non webpack) lavorando su `resources/sass/*` e `resources/js/*` con build via `@vite([...])` e toolchain Node/NVM già presente.
- Workflow DB (Fase 1): usiamo `php artisan migrate:refresh --seed` → possiamo ragionare **a T0**. Evitare “patchwork migrations” quando non necessarie: preferire modificare le migration di creazione tabelle già presenti, mantenendo coerenza del modello.

## Import ODS (deliverable Fase 1)

Obiettivo: rendere ripetibile e controllabile l’import del gestionale attuale (ODS/Excel) in DB.

- Implementare/rafforzare comandi e servizi di import per tutti i file presenti in `docs/materiale cliente/`:
- studenti + genitori/tutori + stato/anno
- corsi/iscrizioni/relazioni base
- contratti
- contabilità (fatture/pagamenti/piani) per quanto disponibile da ODS
- strumenti + noleggi/cauzioni dove presenti
- libri + distribuzioni dove presenti
- docenti/lavoratori + disponibilità dove presenti
- calendario annuale (cicli/sospensioni/lezioni libere) dove presenti
- Integrare l’import nel flusso `migrate:refresh --seed` (seeders richiamano i servizi/import) e documentare come avviarlo.