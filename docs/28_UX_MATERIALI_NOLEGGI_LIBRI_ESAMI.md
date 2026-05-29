# 28 — UX: Materiali, noleggi, libri ed esami (R6 · Design UX)

> Attività Footility **#8526** — *R6 · Design UX — Materiali, noleggi, libri ed esami* (progetto Gestionale Altramusica).
> Obiettivo: **una sola schermata per studente** che raccoglie *cosa ha noleggiato, quali libri ha ricevuto, quali accessori/materiali ha comprato e quali esami ha sostenuto* — per anno — senza saltare tra quattro CRUD slegati.
> Deliverable: wireframe (ASCII) + casi reali + microcopy IT.
> Tre fuochi chiesti dall'attività: **(a)** vista unificata accessori/libri/noleggi/esami per studente e per anno; **(b)** **catalogo libri editabile** con etichette pulite (non placeholder); **(c)** **cauzioni come stato chiaro** (aperto/restituito).
> Mappa sulle attività Fase 2 (materiali/noleggi/cauzioni). Riusa il nucleo studente di [`20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md`](20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md) (R1),
> le iscrizioni di [`21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md`](21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md) (R2)
> e la contabilità di [`25_UX_VISTA_CONTABILITA_PER_STUDENTE.md`](25_UX_VISTA_CONTABILITA_PER_STUDENTE.md) (R4).
> Base AS-IS: [`07_ACCESSORI_NOLEGGI_LIBRI.md`](07_ACCESSORI_NOLEGGI_LIBRI.md) e [`06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md`](06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md).

---

## 0. Principi di design

1. **Materiali, noleggi, libri ed esami si guardano dallo studente, non dal CRUD.** L'operatrice ragiona *"cosa ha in mano Mario quest'anno e cosa deve restituire?"*, non *"apri la lista noleggi e filtra"*. La vista è centrata sullo **studente** + **anno**; i quattro mondi sono **schede dello stesso pannello**, non pagine da cercare a parte.
2. **Una scheda, quattro sezioni, un colpo d'occhio.** *Noleggi · Libri · Materiali/Accessori · Esami* in un unico tab "Materiali" della scheda studente. In testa una **striscia di sintesi** (*Noleggi attivi · Cauzioni aperte · Libri ricevuti · Esami in calendario*).
3. **La cauzione è uno stato, non un numero perso in fondo.** Ogni noleggio mostra la cauzione con semaforo: 🟠 **aperta** (strumento ancora fuori) · ✅ **restituita** · 🔴 **trattenuta** (danno/mancato reso). Chi guarda la riga sa subito *se quei soldi vanno ancora resi*. Restituzione in **2 click**.
4. **Il catalogo libri è editabile e parla italiano.** Nessun placeholder ("Libro 1", "—", "N/D"): titolo, autore, editore, prezzo, ISBN. Il catalogo è la **fonte unica**; la distribuzione allo studente pesca da lì, non si ridigita il titolo a mano.
5. **Assegnare è un'azione rapida, non un percorso.** Da ogni sezione: **[+ Noleggio]**, **[+ Consegna libro]**, **[+ Materiale]**, **[+ Esame]** → 1 modal con studente e anno già impostati → fatto. Niente "vai al CRUD → nuovo → cerca lo studente".
6. **Tutto fatturabile, niente fatturato due volte.** Ogni riga sa se è già finita in fattura (badge "in fatt. 2025-0142") o è ancora "da fatturare". Aggancio coerente con `InvoiceItem.item_type` (`instrument_rental`, `book`, `exam`, `other` per accessori).

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| **Noleggi** (`InstrumentRental`: `instrument_id`, `start_date`, `end_date`, `monthly_fee`, `deposit`, `status` active/returned/cancelled, `return_date`, `return_condition`) + CRUD `admin/instrument-rentals` | ✅ esiste |
| **Libri — catalogo** (`Book`: `title`, `author`, `isbn`, `publisher`, `price`, `stock_quantity`) + CRUD `admin/books` | ✅ esiste |
| **Libri — consegna allo studente** (`BookDistribution`: `book_id`, `student_id`, `academic_year_id`, `course_offering_id`, `distribution_date`, `quantity`, `price_paid`) + CRUD | ✅ esiste |
| **Esami** (`Exam`: `exam_type` ABRSM/LCM/other, `level`, `subject`, `exam_date`, `registration_fee`, `result`, `grade`, `certificate_number`) + CRUD `admin/exams` | ✅ esiste |
| Relazioni sullo studente: `instrumentRentals()`, `bookDistributions()`, `exams()` | ✅ esistono |
| **Accessori / materiali** (bacchette, ance, leggii, custodie, corde…) come entità | ❌ **assente** — nessun model/tabella (vedi AS-IS `07`, colonne `Accessori 1..7`, `acquisto strumento`) |
| **Vista unificata per studente** (noleggi+libri+materiali+esami in un colpo, per anno) | ❌ **assente** — oggi sono 4 liste globali separate, filtrabili per studente ma non aggregate |
| **Cauzione come stato chiaro aperto/restituito** | ⚠️ il dato c'è (`deposit` + `status`/`return_date`) ma **non c'è uno stato dedicato della cauzione**: "noleggio restituito" ≠ "cauzione resa" (strumento può rientrare e i soldi non ancora) |
| **Catalogo libri con etichette pulite** | ⚠️ il model esiste, ma manca la regola UX "niente placeholder": campi obbligatori e label leggibili |
| **Aggancio a fattura visibile sulla riga** | ⚠️ `InvoiceItem.item_type` supporta `instrument_rental`/`book`/`exam` ma non c'è il badge "già fatturato" sulle righe |

> Conclusione: **3 mondi su 4 sono già pronti a livello dati** (noleggi, libri, esami). Mancano: la **vista che li unisce per studente/anno**, lo **stato cauzione** esplicito, gli **accessori/materiali** (entità nuova, §8), e regole UX sul catalogo. Questo design è in gran parte **aggregazione + 2 aggiunte mirate**, non riscrittura.

---

## 2. Architettura della vista (in chiave UX)

```
                    SCHEDA STUDENTE ▸ tab "Materiali"   [Anno: 2025/26 ▾]
  ┌──────────────────────────────────────────────────────────────────────────┐
  │  STRISCIA SINTESI  Noleggi attivi · Cauzioni aperte · Libri · Esami        │
  ├──────────────────────────────────────────────────────────────────────────┤
  │  〔Noleggi〕 〔Libri〕 〔Materiali〕 〔Esami〕   ← sub-tab, default Noleggi  │
  ├──────────────────────────────────────────────────────────────────────────┤
  │  CONTENUTO SEZIONE   (righe con stato + azione rapida riga)                │
  │     ↳ [+ Noleggio] / [+ Consegna libro] / [+ Materiale] / [+ Esame]        │
  └──────────────────────────────────────────────────────────────────────────┘
```

- **Sorgenti dati**: noleggi = `InstrumentRental` per `student_id`+`academic_year_id`; libri = `BookDistribution` (join `Book`); materiali = nuova `StudentAccessory` (§8); esami = `Exam` per studente.
- **Striscia** = conteggi rapidi: *N noleggi attivi · N cauzioni aperte (Σ € da rendere) · N libri ricevuti · N esami (prossimo: data)*.

---

## 3. Vista principale — Tab "Materiali" (default: Noleggi)

**Percorso click:**

```
Sidebar ▸ Anagrafiche ▸ scheda studente ▸ tab "Materiali"
   └─ [Anno: 2025/26 ▾]  (default = AcademicYear::getCurrent())
   └─ sub-tab: Noleggi · Libri · Materiali · Esami
```

**Wireframe — Tab Materiali ▸ sezione Noleggi (con stato cauzione):**

```
┌───────────────────────────────────────────────────────────────────────────┐
│  ‹ Studente   Mario Rossi · 12 anni            [Anno: 2025/26 ▾]            │
│  Anagrafica · Iscrizioni · Contabilità · 〔Materiali〕· Documenti           │
├───────────────────────────────────────────────────────────────────────────┤
│  NOLEGGI 1 attivo   CAUZIONI 🟠 1 aperta (150,00 €)   LIBRI 3   ESAMI 1     │
├───────────────────────────────────────────────────────────────────────────┤
│  〔Noleggi〕 Libri  Materiali  Esami                      [+ Noleggio]      │
│  ┌─────────────────────────────────────────────────────────────────────┐  │
│  │ 🎻 Violino 1/4  ·  dal 01/10/2025  ·  25,00 €/mese                   │  │
│  │    Cauzione 150,00 €   🟠 APERTA           [Registra restituzione]   │  │
│  │    Stato noleggio: attivo · in fatt. 2025-0210                       │  │
│  ├─────────────────────────────────────────────────────────────────────┤  │
│  │ 🎺 Tromba Sib  ·  01/10/2024 → 31/05/2025  ·  20,00 €/mese           │  │
│  │    Cauzione 100,00 €   ✅ RESTITUITA 04/06/2025 · stato: buono       │  │
│  │    Stato noleggio: restituito                                        │  │
│  └─────────────────────────────────────────────────────────────────────┘  │
└───────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Striscia sintesi sticky**: 4 contatori; *Cauzioni* in arancio se ≥1 aperta, con totale € da rendere — è il numero che la segreteria controlla a fine anno.
- **Ogni noleggio mostra la cauzione come riga con semaforo** e azione **[Registra restituzione]** (§5a). Lo **stato noleggio** (attivo/restituito/annullato) è separato dallo **stato cauzione**: vedi §4.
- **Badge fattura** sulla riga (*in fatt. 2025-0210* / *da fatturare*): da `InvoiceItem` con `item_type=instrument_rental`.

---

## 4. La cauzione come stato chiaro (cuore di R6)

Oggi il dato c'è (`deposit`, `return_date`, `return_condition`), ma "noleggio restituito" non equivale a "cauzione resa": lo strumento può rientrare **e** i soldi essere resi più tardi (o trattenuti per un danno). Il design introduce **uno stato cauzione esplicito**, indipendente dallo stato noleggio:

| Stato cauzione | Quando | Semaforo | Significato per la segreteria |
| --- | --- | --- | --- |
| **Aperta** | noleggio attivo, cauzione incassata | 🟠 | *Soldi del cliente che dovremo rendere.* In sintesi nel totale "da rendere". |
| **Restituita** | strumento reso + cauzione resa al cliente | ✅ | Chiuso. Data e condizione strumento registrate. |
| **Trattenuta (totale/parziale)** | danno / mancato reso → tratteniamo tutto o parte | 🔴 | Importo trattenuto + motivo; eventuale residuo reso. |
| **Non prevista** | noleggio senza cauzione (`deposit = 0`) | — | Riga senza badge cauzione. |

> Stato **derivabile** dai campi esistenti + 2 campi nuovi sul noleggio (`deposit_status`, `deposit_returned_at`, `deposit_withheld_amount`) — vedi §8. Senza i campi nuovi lo stato si approssima (noleggio `returned` ⇒ cauzione "da chiudere"), ma il design vuole **dire esplicitamente** se i soldi sono stati resi.

**Wireframe — Modal "Registra restituzione strumento + cauzione" (§5a):**

```
┌──── Restituzione · Violino 1/4 · Mario Rossi ────────────────────────────┐
│  Data restituzione   [ 04/06/2026 ]                                      │
│  Condizione strumento  ◉ Ottimo ○ Buono ○ Discreto ○ Da sistemare       │
│                                                                          │
│  CAUZIONE  150,00 €                                                      │
│   ◉ Restituita per intero                                                │
│   ○ Trattenuta:  [ 0,00 ] €   motivo [____________________]             │
│        → reso al cliente: 150,00 €                                       │
│                                                                          │
│                                       [ Annulla ]   [ Conferma reso ]    │
└────────────────────────────────────────────────────────────────────────────┘
```

- Default **"Restituita per intero"** (caso comune). Se trattieni, l'importo trattenuto e il residuo reso sono **calcolati e mostrati**.
- Su [Conferma reso]: noleggio → `returned`, `return_date`/`return_condition` valorizzati, cauzione → ✅/🔴 con `deposit_returned_at`. Toast *"Strumento reso · cauzione 150,00 € restituita."* La sintesi cala di una "cauzione aperta".

---

## 5. Azioni rapide per sezione

Tutte le aggiunte partono dal contesto **studente + anno già impostati**, in modal, riusando i CRUD esistenti.

### 5a. Noleggi — [+ Noleggio] / [Registra restituzione]
```
[+ Noleggio] → MODAL: strumento (catalogo Instrument ▾), inizio, €/mese, cauzione → InstrumentRental creato (status active, cauzione 🟠)
[Registra restituzione] → modal §4 (reso strumento + chiusura cauzione)
```
- **Cauzione precompilata** dal default dello strumento (se previsto); override possibile.

### 5b. Libri — [+ Consegna libro] (pesca dal catalogo)
```
[+ Consegna libro] → MODAL:
   Libro      [ cerca nel catalogo ▾ ]   → "Hal Leonard Piano Adventures 1 — Faber — 18,90 €"
   Quantità   [ 1 ]      Prezzo  [ 18,90 ] (precompilato dal catalogo, override)
   Data       [ 29/05/2026 ]   Corso (opz.) [ Pianoforte base ▾ ]
   → BookDistribution creata
```
- La tendina mostra **etichette pulite** dal catalogo (titolo · autore/editore · prezzo), **mai** "Libro 1". Niente ridigitazione del titolo.
- Se il libro non è in catalogo: link **[+ Aggiungi al catalogo]** che apre il modal catalogo (§6) senza perdere il contesto.

### 5c. Materiali / Accessori — [+ Materiale]
```
[+ Materiale] → MODAL:
   Descrizione  [ es. "Ance clarinetto 2½ (x10)" ]   (testo libero o voce ricorrente ▾)
   Tipo         ◉ Acquisto  ○ Accessorio incluso
   Quantità [ 1 ]   Prezzo [ 12,00 ]   Data [ 29/05/2026 ]
   → StudentAccessory creato (§8)
```
- Voci ricorrenti suggerite (ance, bacchette, leggio, custodia, corde) per non ridigitare; resta il testo libero (AS-IS `Accessori 1..7`).

### 5d. Esami — [+ Esame]
```
[+ Esame] → MODAL:
   Ente   ◉ ABRSM ○ LCM ○ Altro     Materia ◉ Strumento ○ Teoria ○ Entrambi
   Livello [ 3 ]   Data esame [ __/__/____ ]   Quota iscrizione [ 95,00 ]
   → Exam creato (result = "pending")
```
- A esame svolto: **[Registra esito]** → result (passed/merit/distinction/failed), voto, n. certificato.

---

## 6. Catalogo libri editabile — etichette pulite (no placeholder)

Pagina **Anagrafiche ▸ Catalogo libri** (CRUD `admin/books` già esistente), con regole UX che eliminano i placeholder:

```
┌── Catalogo libri ───────────────────────────────────  [+ Nuovo libro]  [🔍 cerca] ─┐
│  Titolo                          Autore        Editore       ISBN          Prezzo  │
│  Piano Adventures · Level 1       N. & R. Faber Hal Leonard   9781616770?    18,90 €│
│  Teoria musicale di base          E. Pozzoli   Ricordi       9790041...      9,50 €│
│  Il mio primo solfeggio           —            Curci         —              7,00 €│
│  ────────────────────────────────────────────────────────────────────────────── │
│  ▸ 32 titoli · usati in 118 consegne quest'anno                                   │
└────────────────────────────────────────────────────────────────────────────────────┘
```

**Wireframe — Modal "Nuovo / Modifica libro":**

```
┌──── Libro ───────────────────────────────────────────────────────────────┐
│  Titolo *      [ Piano Adventures · Level 1                            ]   │
│  Autore        [ Nancy & Randall Faber                                ]   │
│  Editore       [ Hal Leonard                                          ]   │
│  ISBN          [ 9781616770?                                          ]   │
│  Prezzo *      [ 18,90 ] €      Giacenza  [ 12 ]                          │
│                                                                          │
│                                       [ Annulla ]   [ Salva ]            │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX (le "etichette pulite"):**
- **Titolo e prezzo obbligatori.** Niente record con titolo vuoto o "Libro 1/2/3".
- **Label in italiano e leggibili** ovunque il libro compaia (catalogo, tendina consegna, riga distribuzione, riga fattura): *"Piano Adventures · Level 1 — Hal Leonard — 18,90 €"*.
- Campi facoltativi (autore/editore/ISBN) mostrano **"—"** solo in lettura, mai come valore salvato fittizio.
- **Eliminazione protetta**: un libro già distribuito non si cancella (si può disattivare); evita righe orfane nelle consegne.
- **Prezzo del catalogo = default** alla consegna, sempre sovrascrivibile per-studente (`price_paid`).

---

## 7. Stati e casi reali

| Caso | Comportamento UX |
| --- | --- |
| **Noleggio attivo** | 🎻 riga con €/mese, cauzione 🟠 aperta, [Registra restituzione] in primo piano. |
| **Strumento reso, cauzione resa** | Cauzione ✅ con data e condizione; noleggio "restituito"; esce dal totale "da rendere". |
| **Strumento reso ma cauzione non ancora resa** | Cauzione resta 🟠 "da chiudere": il design distingue i due eventi (cuore R6). |
| **Cauzione trattenuta per danno** | 🔴 importo trattenuto + motivo; residuo eventuale reso. Visibile nello storico. |
| **Noleggio senza cauzione** | Nessun badge cauzione; riga pulita. |
| **Libro consegnato senza addebito** (incluso) | `price_paid = 0`; riga "incluso", non finisce in fattura. |
| **Libro non in catalogo** | [+ Aggiungi al catalogo] inline; nessun titolo digitato a mano nella consegna. |
| **Materiale acquistato** | Riga in sezione Materiali, fatturabile come `item_type=other`. |
| **Esame in calendario / svolto** | "in calendario" (result pending, data futura) → poi [Registra esito] con voto/certificato. |
| **Anno diverso da quello attivo** | Selettore anno in testata; anni passati in sola lettura, stessa struttura. |
| **Già fatturato** | Badge "in fatt. NNNN" sulla riga; "da fatturare" se ancora no (aggancio R4). |

---

## 8. Microcopy (etichette IT)

- Tab: **"Materiali"**. Sub-tab: **"Noleggi" · "Libri" · "Materiali" · "Esami"**.
- Striscia: **"Noleggi attivi" · "Cauzioni aperte" · "Libri" · "Esami"**.
- Stato cauzione: **🟠 Aperta · ✅ Restituita · 🔴 Trattenuta · — Non prevista**.
- Condizione strumento: **Ottimo · Buono · Discreto · Da sistemare**.
- Pulsanti: **"+ Noleggio" · "Registra restituzione" · "+ Consegna libro" · "+ Aggiungi al catalogo" · "+ Materiale" · "+ Esame" · "Registra esito" · "+ Nuovo libro"**.
- Esiti esame: **In calendario · Superato · Merito · Distinzione · Non superato**.
- Toast: *"Strumento reso · cauzione 150,00 € restituita."* · *"Cauzione 50,00 € trattenuta (danno custodia)."* · *"Libro «Piano Adventures · Level 1» consegnato a Mario Rossi."* · *"Esame ABRSM Liv. 3 registrato."*
- Banner sintesi: *"🟠 1 cauzione aperta da rendere · 150,00 €"*.

---

## 9. Impatti tecnici (per chi implementa — NON parte di questo R6)

Il design **riusa le entità e i CRUD esistenti** (`InstrumentRental`, `Book`, `BookDistribution`, `Exam`) e aggiunge il minimo:

- **Tab "Materiali" sulla scheda studente**: vista che aggrega per `student_id` + `academic_year_id` le 4 relazioni già presenti (`instrumentRentals`, `bookDistributions`, `exams` + nuova `studentAccessories`), con sub-tab e striscia di conteggi.
- **Stato cauzione esplicito** su `instrument_rentals`: aggiungere `deposit_status` (enum `open`/`returned`/`withheld`/`none`), `deposit_returned_at` (date, nullable), `deposit_withheld_amount` (decimal, nullable), `deposit_withheld_reason` (string, nullable). Migrazione additiva, default `open` se `deposit > 0` altrimenti `none`. Il "Registra restituzione" valorizza questi campi insieme a `return_date`/`return_condition`.
- **Accessori/materiali**: nuova entità `StudentAccessory` (`student_id`, `academic_year_id`, `description`, `kind` acquisto/incluso, `quantity`, `price`, `purchase_date`, `notes`) + relazione `Student::accessories()` + CRUD admin. Copre AS-IS `07` (`Accessori 1..7`, `acquisto strumento`).
- **Catalogo libri — regole**: `title`/`price` required a validazione; vincolo soft-delete/disattivazione su libri già distribuiti; helper label `"{title} — {publisher} — {price}€"` riusato in tendine, righe e fatture.
- **Badge fatturazione**: derivare da `InvoiceItem` (`item_type` ∈ `instrument_rental`/`book`/`exam`/`other`) collegato alla riga; per accessori usare `other` con riferimento alla `StudentAccessory`.
- **Azioni rapide in modal**: riuso `instrument-rentals.create?student_id=`, `book-distributions.create?student_id=`, `exams.create?student_id=` (+ nuovo `student-accessories.create`) presentati in modal con studente/anno precompilati.

---

## 10. Checklist di accettazione (Definition of Done del design)

- [x] Definita la **vista unificata accessori/libri/noleggi/esami per studente e per anno** (un tab, 4 sub-sezioni). — §2–§3
- [x] **Striscia sintesi** con conteggi e totale cauzioni da rendere, sempre visibile. — §3
- [x] **Cauzione come stato chiaro** (aperta/restituita/trattenuta/non prevista), distinta dallo stato noleggio. — §4
- [x] **Restituzione cauzione in 2 click** con default "restituita per intero" e gestione trattenuta. — §4–§5a
- [x] **Catalogo libri editabile con etichette pulite** (no placeholder; titolo/prezzo obbligatori; label IT leggibili). — §6
- [x] **Consegna libro pesca dal catalogo** (niente titoli digitati a mano), con [+ Aggiungi al catalogo] inline. — §5b–§6
- [x] **Azioni rapide** per noleggi/libri/materiali/esami con studente+anno precompilati. — §5
- [x] **Materiali/accessori** coperti come sezione (entità nuova minima). — §5c, §8
- [x] Coperti i **casi reali**: reso, cauzione non ancora resa, trattenuta, libro incluso, esame in calendario, già fatturato. — §7
- [x] Wireframe ASCII per ogni schermata/modal chiave + microcopy IT. — §3–§8
- [x] Indicati **impatti tecnici minimi** senza implementare; riuso entità/CRUD esistenti + 1 entità e pochi campi additivi. — §9
