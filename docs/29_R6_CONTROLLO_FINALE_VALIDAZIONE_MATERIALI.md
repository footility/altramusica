# 29 — R6 · Controllo finale: Validazione materiali/noleggi/libri/esami

> Attività Footility **#8545** — *R6 · Controllo finale — Validazione materiali/libri/esami*.
> Test E2E su ciò che esiste in codice (CRUD `Book`, `InstrumentRental`, `Exam`, `BookDistribution`),
> con gap documentati rispetto al design R6 ([`28_UX_MATERIALI_NOLEGGI_LIBRI_ESAMI.md`](28_UX_MATERIALI_NOLEGGI_LIBRI_ESAMI.md)).
> Trascrizioni parte 2 r.76-90 (wireframe noleggi+cauzione), r.142 (+Noleggio), r.178-180 (catalogo libri).
> Test automatico: [`tests/Feature/MaterialiNoleggiLibriEsamiE2EValidationTest.php`](../tests/Feature/MaterialiNoleggiLibriEsamiE2EValidationTest.php) — **13/13 PASS**.

## Esito sintetico

| Flusso | Stato | Note |
| --- | --- | --- |
| Catalogo libri editabile — rinomina libro mock | ✅ OK | `BookController@update`: "Libro 1" → "Piano Adventures · Level 1" con autore/editore/ISBN/prezzo. |
| Catalogo — titolo e prezzo obbligatori | ✅ OK | `store` valida `title`/`price` required: niente record placeholder (design §6). |
| Catalogo — eliminazione protetta se distribuito | ✅ OK | `destroy` blocca la cancellazione di un libro con `distributions` (no righe orfane, §6). |
| Consegna libro pesca dal catalogo (prezzo override) | ✅ OK | `BookDistribution` con `price_paid` che sovrascrive il prezzo di catalogo (§5b). |
| Registra noleggio con cauzione | ✅ OK | `InstrumentRentalController@store`: `deposit` valorizzato, strumento → `rented` (§5a). |
| Registra restituzione strumento | ✅ OK | `update` a `returned` con `return_date`/`return_condition`; strumento → `available` (§4/§5a, approssimazione). |
| Registra esame | ✅ OK | `ExamController@store` crea l'esame (con valori enum validi, vedi BUG). |
| Aggregazione noleggi/libri/esami per studente | ✅ OK | Relazioni `instrumentRentals`/`bookDistributions`/`exams` sullo `Student` (base della vista §2). |
| **Stato cauzione esplicito (aperta/restituita/trattenuta)** | 🟥 **BLOCCO** | Cuore di R6: nessun campo `deposit_status`/`deposit_returned_at`/`deposit_withheld_*`. |
| **Materiali / accessori** | 🟥 **BLOCCO** | Entità `StudentAccessory` assente: no model/tabella/relazione/CRUD (§5c, §8). |
| **Vista unificata per studente (tab "Materiali")** | 🟥 **BLOCCO** | Nessun controller/route di aggregazione: restano 4 CRUD globali separati (§2, §3). |
| **Esiti esame merito/distinzione** | 🟨 **GAP** | Lo schema DB li prevede, ma `ExamController` valida solo `passed/failed/pending` (§5d, §8). |
| **Disallineamento enum `Exam` controller ↔ DB** | 🟧 **BUG** | `exam_type`/`subject`: il controller accetta valori che il CHECK del DB rifiuta. |

## Cosa è stato testato (E2E)

1. **Catalogo libri (§6)** — rinomina HTTP `PUT books/{id}` di un libro "mock" → titolo/editore/prezzo puliti; `title`/`price` obbligatori in `store`; `destroy` di un libro distribuito **bloccato** (resta in tabella); consegna `book-distributions.store` con `price_paid` che sovrascrive il prezzo di catalogo e pesca l'etichetta dal `Book`.
2. **Noleggi + cauzione (§5a)** — HTTP `POST instrument-rentals` con `deposit` 150 € → noleggio `active`, strumento `rented`; restituzione via `PUT` → `returned` + `return_date`/`return_condition`, strumento `available`.
3. **Esami (§5d)** — HTTP `POST exams` (valori enum validi) → esame `pending` ("in calendario").
4. **Aggregazione (§2)** — un solo studente con noleggio+libro+esame, raccolti dalle 3 relazioni esistenti.
5. **Gap / bug** — verificato in codice che stato cauzione, accessori, vista unificata e esiti graduati **non** esistono come da design, e che gli enum `Exam` sono disallineati tra controller e DB.

## Blocchi / gap che impediscono il flusso R6 completo (coerenti con §1, §4, §8)

Il design R6 aveva già marcato questi punti come `❌ assente`/`⚠`; il controllo finale lo conferma:

- **Stato cauzione esplicito (§4 — cuore di R6):** `instrument_rentals` non ha `deposit_status`, `deposit_returned_at`, `deposit_withheld_amount`, `deposit_withheld_reason`. Oggi "noleggio restituito" non distingue se la **cauzione** è stata resa o trattenuta — il semaforo 🟠/✅/🔴 e il totale "da rendere" non sono rappresentabili nel dato.
- **Materiali / accessori (§5c, §8):** manca del tutto l'entità `StudentAccessory` (model, tabella `student_accessories`, relazione `Student::accessories()`, CRUD/route). La sezione "Materiali" del tab non ha sorgente dati.
- **Vista unificata per studente (§2, §3):** nessuna route "tab Materiali" sulla scheda studente né controller aggregatore; i quattro mondi sono ancora `admin/instrument-rentals`, `admin/book-distributions`, `admin/exams` (+ accessori mancanti) come liste globali separate.

## Bug / gap minori emersi (fix mirati lato applicativo)

- **🟧 BUG — enum `Exam` disallineato controller ↔ DB.** `ExamController` valida `exam_type` ∈ `{abrsm,lcm,internal,other}` (lowercase, + `internal`) e `subject` come stringa libera; la migrazione `create_exams_table` vincola `exam_type` a `{ABRSM,LCM,other}` (case-sensitive) e `subject` a `{instrument,theory,both}`. Il caso realistico del design §5d (*Ente ABRSM · Materia "Pianoforte"*) **supera la validazione del controller ma viola il CHECK del DB → INSERT in errore**. Fix: allineare le `rules` del controller all'enum (o viceversa) — decisione di prodotto su quali valori tenere.
- **🟨 GAP — esiti esame merito/distinzione (§5d, §8).** Lo schema `result` prevede già `merit`/`distinction`, ma `ExamController` accetta solo `passed/failed/pending`: gli esiti graduati del microcopy non sono registrabili dal form. Fix: estendere la regola di validazione `result`.

## Raccomandazione

I **tre mondi dati già esistenti sono validati end-to-end**: catalogo libri editabile con regole pulite
(no placeholder, eliminazione protetta), consegna libro dal catalogo con prezzo override, noleggio con cauzione,
restituzione strumento, registrazione esame e aggregazione per studente funzionano. Coerente con la conclusione
del design R6 (§1): *"3 mondi su 4 sono già pronti a livello dati"*.

Restano da implementare, come da R6 §4/§8 (lavoro di Fase 2, non bug sul codice esistente):
**stato cauzione esplicito** (4 campi additivi su `instrument_rentals`), **entità materiali/accessori**
(`StudentAccessory` + CRUD), e la **vista unificata** (tab "Materiali" che aggrega per studente+anno).
Due ritocchi applicativi rapidi e indipendenti: **allineare gli enum `Exam`** (BUG controller↔DB) e
**ammettere gli esiti merito/distinzione** nel controller. Nessun blocco sulle funzioni base attuali.
