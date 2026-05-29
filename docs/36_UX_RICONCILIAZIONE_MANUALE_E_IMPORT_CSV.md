# 36 — UX: Riconciliazione manuale + import CSV estratto conto (R5 · Design UX)

> Attività Footility **#8525** — *R5 · Design UX — Riconciliazione manuale + import CSV* (progetto Gestionale Altramusica).
> Obiettivo: una **schermata di match** che mette **estratto conto importato (CSV) a sinistra** e **scadenze aperte a destra**, e lascia alla segreteria
> **trascinare o cliccare per riconciliare** — con casi **parziali** e **storni** gestiti come **eventi** tracciati e reversibili.
> Deliverable: wireframe (ASCII) di import CSV + schermata di match + modal, casi reali (1:1, parziale, cumulativo, split, storno, non riconciliabile) + microcopy IT.
> Mappa sulle attività Fase 2 **F2-05** (contabilità/incassi). Riusa la vista contabilità di [`25_UX_VISTA_CONTABILITA_PER_STUDENTE.md`](25_UX_VISTA_CONTABILITA_PER_STUDENTE.md) (R4)
> e il pattern import **dry-run + report anomalie** di `OdsImportService` (commit *Import ODS #1*).
> Base AS-IS: [`05_CONTABILITA_CORSI.md`](05_CONTABILITA_CORSI.md) e [`06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md`](06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md).

---

## 0. Principi di design

1. **L'estratto conto è la verità delle entrate; le scadenze sono le attese.** Riconciliare = collegare ciò che è **realmente arrivato in banca** (riga CSV) a ciò che il gestionale **si aspettava** (`PaymentPlan`/`Invoice` aperte). La schermata mette le due colonne **una di fronte all'altra** e rende il collegamento un gesto, non una digitazione.
2. **Match a colpo d'occhio, conferma consapevole.** Il sistema **propone** l'abbinamento (per importo/data/causale/IBAN/nome), ma **non chiude nulla da solo**: l'operatrice vede il suggerimento, trascina o clicca, e **conferma**. Nessuna riconciliazione automatica silenziosa.
3. **Drag *o* click — mai obbligare il drag.** Trascinare una riga sulla scadenza è il gesto naturale a mouse; ma da tastiera/mobile ogni riga ha **[Abbina ▸]** che apre la stessa scelta. Il design non dipende dal drag-and-drop.
4. **Ogni riconciliazione è un evento, non una mutazione cieca.** Abbinare, stornare, annullare un abbinamento sono **eventi tracciati** (chi, quando, riga CSV ↔ scadenza, importo). Questo rende il **parziale** e lo **storno** rappresentabili e soprattutto **reversibili** ([Disfa abbinamento]), con audit per la segreteria.
5. **L'import non scrive prima di mostrare.** Come per le anagrafiche ODS: il CSV passa per un **dry-run con report anomalie** (righe illeggibili, importi non parse-abili, duplicati già importati, date fuori range) **prima** di entrare nello staging. L'import non crea `Payment`: crea **righe-movimento da riconciliare**.
6. **Riusa R4, non duplica.** Le scadenze, gli importi, il "da saldare" e l'azione che crea il `Payment` sono quelli di R4. Qui aggiungiamo solo il **ponte estratto-conto → scadenza** e la **memoria degli abbinamenti**.

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| Pagamenti (`Payment`: `invoice_id`, `payment_date`, `amount`, `payment_method`, `reference_number`, `notes`) | ✅ esiste |
| Registrazione pagamento (`InvoiceService::recordPayment(Invoice, amount, method, date)`) | ✅ esiste (parte **dalla fattura**) |
| Piano rate (`PaymentPlan`: `due_date`, `amount`, `status`, `paid_date`, `payment_id`; scope `pending`/`overdue`) | ✅ esiste — è la **colonna destra** della riconciliazione |
| Note di credito (`CreditNote`: `amount`, `reason`, `notes`) | ✅ esiste — base per gli **storni** |
| Pattern import **dry-run + report anomalie** (`OdsImportService`) | ✅ esiste (per studenti) — **riusabile** come modello per il CSV banca |
| **Import CSV estratto conto bancario** | ❌ **assente** — nessuna entità movimento bancario, nessun parser CSV banca |
| **Schermata di match estratto conto ↔ scadenze** | ❌ **assente** |
| **Memoria degli abbinamenti** (riga CSV ↔ scadenza/pagamento, stato, reversibilità) | ❌ assente — `Payment` non sa da quale riga di estratto conto nasce |
| **Suggerimento automatico di abbinamento** (per importo/data/causale) | ❌ assente |
| **Storno come evento** (movimento negativo / reso → `CreditNote` o rettifica pagamento) | ⚠️ `CreditNote` esiste ma non è collegabile a una riga di estratto conto |

> Conclusione: la **destra esiste già** (scadenze/rate da R4). Mancano: (a) l'**ingresso dei movimenti** (import CSV → staging), (b) la **schermata di match**, (c) una **memoria dell'abbinamento** che renda parziali e storni eventi reversibili. Servono **2 tabelle nuove** (movimento importato + evento di riconciliazione); il resto è riuso (vedi §9).

---

## 2. Architettura della vista (in chiave UX)

```
            CONTABILITÀ ▸ Riconciliazione        [Anno: 2025/26 ▾]
  ┌──────────────────────────────────────────────────────────────────────────┐
  │  ① IMPORT CSV       carica estratto conto → dry-run → report → conferma    │
  ├──────────────────────────────────────────────────────────────────────────┤
  │  ② SCHERMATA DI MATCH (split)                                              │
  │   ┌───────────────────────────┐        ┌───────────────────────────────┐  │
  │   │  ESTRATTO CONTO (sinistra) │  ⇄    │  SCADENZE APERTE (destra)      │  │
  │   │  righe importate, da abbin.│ drag  │  PaymentPlan pending/overdue   │  │
  │   │  + suggerimento match      │ click │  + fatture senza piano         │  │
  │   └───────────────────────────┘        └───────────────────────────────┘  │
  ├──────────────────────────────────────────────────────────────────────────┤
  │  ③ EVENTI        log abbinamenti/storni/annullamenti (reversibili)         │
  └──────────────────────────────────────────────────────────────────────────┘
```

- **Sorgenti dati**: sinistra = righe-movimento importate dal CSV (staging, stato `da_abbinare`/`abbinato`/`ignorato`); destra = `PaymentPlan` (`pending`/`overdue`) + `Invoice` aperte senza piano, per l'anno; conferma di un abbinamento → `Payment` via `InvoiceService::recordPayment` + aggiornamento `PaymentPlan`.
- **Match suggerito** = punteggio su **importo** (uguale/contenuto), **data** (≈ data scadenza/finestra), **causale/IBAN/nome ordinante** vs studente/famiglia (grafo R1).

---

## 3. ① Import CSV estratto conto (ingresso, non scrittura diretta)

**Percorso click:**

```
Sidebar ▸ Contabilità ▸ Riconciliazione ▸ [Importa estratto conto (CSV)]
   └─ carica file → mappa colonne (se serve) → DRY-RUN → report → [Conferma import]
```

**Wireframe — Import CSV (dry-run + report):**

```
┌──────────── Importa estratto conto · CSV ─────────────────────────────────┐
│  File   [ estratto_conto_maggio.csv ]   Banca [ Generico / Intesa / … ▾]  │
│  Separatore  ◉ ; (auto)   Data  gg/mm/aaaa   Decimale  , (auto)           │
│                                                                          │
│  ANTEPRIMA (dry-run, nessuna scrittura)                                  │
│   Data        Importo     Causale / Ordinante              Esito         │
│   05/05/2026  +135,00 €    BONIF. ROSSI MARIO PIANOFORTE    ✓ leggibile   │
│   06/05/2026  +120,00 €    POS RATA GIULIA ROSSI            ✓ leggibile   │
│   07/05/2026   −45,00 €    STORNO/RESO CAUZIONE             ⚠ negativo→storno│
│   08/05/2026   +90,00 €    BONIF. (causale vuota)           ⚠ senza riferim.│
│   09/05/2026   +200,00 €   (già importato il 09/05)          ⊘ duplicato   │
│                                                                          │
│  REPORT   24 righe · 22 importabili · 1 duplicato (saltato) · 1 storno   │
│           1 senza riferimento (importata, da abbinare a mano)            │
│                                                                          │
│                                    [ Annulla ]   [ Conferma import ]     │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX (coerenti con `OdsImportService`):**
- **Dry-run obbligatorio prima della scrittura**: l'anteprima mostra cosa accadrebbe, con un **report di anomalie** per categoria (illeggibile, importo non parse-abile, **duplicato** già importato — chiave su data+importo+causale+hash riga, **negativo → storno**, senza riferimento).
- **Mappatura banca**: profili comuni (Intesa, Unicredit, generico) per separatore/formato data/decimale; se l'header non è riconosciuto, **mappa-colonne** manuale una tantum (riusa l'idea di `buildColumnMap`).
- **[Conferma import]** crea **righe-movimento in staging** (stato `da_abbinare`), **non** `Payment`. I duplicati sono saltati e contati; gli storni entrano marcati per il trattamento di §7.
- Idempotenza: re-importare lo stesso CSV non duplica (chiave riga); il report dice quante righe già presenti.

---

## 4. ② Schermata di match — estratto conto (sx) ↔ scadenze aperte (dx)

**Wireframe — Riconciliazione (split, drag/click):**

```
┌─ Riconciliazione · 2025/26 ──────────────────────────  [Solo da abbinare ▾] ┐
│                                                                              │
│  ESTRATTO CONTO  (da abbinare: 7)        │  SCADENZE APERTE  (da incassare)  │
│ ┌────────────────────────────────────┐  │ ┌──────────────────────────────┐ │
│ │ 05/05  +135,00  ROSSI MARIO PIANOF. │──┼▶│ Mario Rossi · Rata 3/4  135,00│ │ ← suggerito ✦
│ │   ✦ match suggerito (importo+nome)  │  │ │   Fatt.2025-0142 · scad.05/06 │ │
│ ├────────────────────────────────────┤  │ ├──────────────────────────────┤ │
│ │ 06/05  +120,00  POS GIULIA ROSSI    │  │ │ Giulia Rossi · Rata 2/4  120,00│ │
│ ├────────────────────────────────────┤  │ │   Fatt.2025-0151 · scad.06/05 │ │
│ │ 08/05   +90,00  (causale vuota)     │  │ ├──────────────────────────────┤ │
│ │   ? nessun match certo → scegli     │  │ │ Bianchi L. · Noleggio    90,00│ │
│ ├────────────────────────────────────┤  │ │   Fatt.2025-0210 (no piano)   │ │
│ │ 07/05   −45,00  STORNO/RESO    [⤺]  │  │ │ … altre 14 scadenze           │ │
│ └────────────────────────────────────┘  │ └──────────────────────────────┘ │
│                                                                              │
│  ✦ 3 abbinamenti suggeriti   [Abbina tutti i suggeriti ✓]  [Rivedi a mano]  │
└──────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Due colonne affiancate.** Sx = movimenti importati (filtro default *Solo da abbinare*); dx = scadenze aperte (`PaymentPlan` pending/overdue + fatture senza piano). Le righe già abbinate spariscono dalla lista "da abbinare" (filtro per rivederle).
- **Gesto di match**: trascina una riga di sx sulla scadenza di dx **oppure** clicca **[Abbina ▸]** sulla riga → si evidenzia il candidato suggerito a destra → **conferma**. Il drag è un *di più*, non un requisito.
- **Suggerimento ✦**: il sistema pre-allinea per **importo esatto + nome/causale**; se il punteggio è alto mostra ✦ e abilita **[Abbina tutti i suggeriti]** (azione di massa, ma sempre con conferma e annullabile). Punteggio basso → nessun ✦, l'operatrice sceglie.
- **Importi che non combaciano** non bloccano: aprono il **modal di abbinamento** (§5) dove si decide parziale/cumulativo/split.
- **Riga negativa** (storno/reso) ha l'icona [⤺] e segue il flusso storno (§7), non l'abbinamento normale.
- Contatori sempre visibili: *da abbinare / suggeriti / abbinati oggi*.

---

## 5. Modal di abbinamento (il gesto che crea il pagamento)

Quando l'importo non è 1:1 o si abbina a mano, il drop/clic apre un modal che esplicita **cosa succederà** prima di scrivere.

**Wireframe — Modal "Abbina movimento":**

```
┌──────── Abbina movimento · 05/05 +135,00 € ──────────────────────────────┐
│  Movimento banca   135,00 €   ·   BONIF. ROSSI MARIO PIANOFORTE          │
│  Scadenza scelta   Mario Rossi · Rata 3/4 · Fatt.2025-0142 · 135,00 €    │
│                                                                          │
│  Esito             ◉ Salda la rata (135,00 = 135,00)                     │
│                    ○ Pagamento parziale (resta aperto il residuo)        │
│                    ○ Copre più scadenze (cumulativo) ▸ scegli rate       │
│                                                                          │
│  Metodo            Bonifico (da causale)        Data  05/05/2026         │
│  Riferimento       da estratto conto (causale)                          │
│                                                                          │
│              [ Annulla ]            [ Abbina e registra pagamento ]      │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **[Abbina e registra pagamento]** crea il `Payment` (via `InvoiceService::recordPayment`) con `amount`/`payment_date`/`payment_method`/`reference_number` **precompilati dal movimento**, collega la `PaymentPlan` (→ `paid`, `payment_id`, `paid_date`) e **registra l'evento** di riconciliazione (riga CSV ↔ pagamento ↔ scadenza).
- **Esito = parziale**: crea un `Payment` minore della rata; la rata resta `pending` col residuo (coerente con R4 §7 "pagamento parziale"). La riga banca è interamente consumata.
- **Esito = cumulativo**: un movimento copre **più rate consecutive** → marca a cascata fino a capienza; eventuale resto come acconto (R4 §7 "cumulativo").
- **Split inverso** (più movimenti → una scadenza): si abbinano N righe alla stessa rata; la rata si chiude quando la somma copre l'importo (ogni riga è un `Payment` collegato, ogni abbinamento un evento).
- Toast: *"Abbinato: 135,00 € → Rata 3/4 (Mario Rossi). Pagamento registrato."*

---

## 6. ③ Eventi di riconciliazione (memoria + reversibilità)

Ogni operazione sulla riconciliazione è un **evento** persistente — è ciò che rende parziali e storni rappresentabili e annullabili.

```
┌─ Eventi di riconciliazione · 2025/26 ────────────────────────────────────┐
│  05/05 14:02  ABBINATO   +135,00 → Rata 3/4 Mario Rossi      Anna  [Disfa]│
│  05/05 14:03  CUMULATIVO +270,00 → Rate 1/4+2/4 Bianchi      Anna  [Disfa]│
│  05/05 14:05  STORNO      −45,00 → Nota credito Fatt.2025-0210 Anna [Disfa]│
│  05/05 14:06  IGNORATO    +12,50  commissioni banca (non studente)  Anna  │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- Tipi evento: **ABBINATO** (1:1), **PARZIALE**, **CUMULATIVO**, **SPLIT**, **STORNO**, **IGNORATO**, **ANNULLATO** (disfa).
- **[Disfa]** è la vera potenza del modello a eventi: annulla l'abbinamento → elimina/azzera il `Payment` collegato, riporta la `PaymentPlan` a `pending`, e la riga banca torna **da abbinare**. L'evento resta in log come ANNULLATO (audit, non sparisce).
- **IGNORATO**: movimenti non riferiti a studenti (commissioni, giroconti) si marcano *ignorato* con motivo, così non restano a sporcare la colonna sinistra. Reversibile.

---

## 7. Storni e casi negativi (gestiti come eventi)

L'estratto conto contiene anche **uscite/storni** (reso cauzione, rimborso, addebito tornato indietro). Il design li tratta esplicitamente:

| Caso movimento | Comportamento UX |
| --- | --- |
| **Importo negativo = reso/rimborso** | Riga marcata [⤺] storno → modal storno: collega a fattura/cauzione → crea **`CreditNote`** (R4 §6) e registra evento **STORNO**. Non genera un `Payment` positivo. |
| **Pagamento tornato indietro** (insoluto) | Storno che **disfa** un abbinamento precedente: riporta la rata a `pending` e segnala "insoluto" (evento ANNULLATO + STORNO collegato). |
| **Reso cauzione** | Storno collegato alla cauzione (R6 — materiali/noleggi); `CreditNote` con motivo "reso cauzione". |
| **Storno > residuo** | Si applica fino a capienza; il resto resta credito disponibile (coerente con R4). |

> Il principio: **niente cancellazioni distruttive**. Uno storno è un evento che *neutralizza* un incasso o crea un credito — sempre tracciato, sempre reversibile.

---

## 8. Stati e casi reali (riepilogo match)

| Caso | Comportamento UX |
| --- | --- |
| **Match perfetto 1:1** | ✦ suggerito; [Abbina] o drag → salda la rata, 1 evento ABBINATO. |
| **Parziale** (movimento < scadenza) | Modal → "Pagamento parziale"; rata resta aperta col residuo; evento PARZIALE. |
| **Sovra-pagamento** (movimento > scadenza) | Salda la rata + resto come acconto/credito; evento con nota. |
| **Cumulativo** (1 movimento → N rate) | Marca rate consecutive fino a capienza; evento CUMULATIVO. |
| **Split** (N movimenti → 1 rata) | N abbinamenti sulla stessa rata; rata chiusa quando la somma copre; N eventi. |
| **Nessun candidato certo** | Nessun ✦; ricerca manuale per nome/studente; abbinamento a mano. |
| **Movimento non riferito a studente** | [Ignora] con motivo (commissioni/giroconto); evento IGNORATO. |
| **Storno / negativo** | Flusso §7 (CreditNote o annullamento abbinamento). |
| **Duplicato in import** | Saltato in dry-run; contato nel report; mai doppio movimento. |
| **Disfa abbinamento** | Payment annullato, rata→pending, riga→da abbinare; evento ANNULLATO in log. |

---

## 9. Microcopy (etichette IT)

- Sezioni: **"Importa estratto conto (CSV)"** · **"Riconciliazione"** · **"Estratto conto"** (sx) · **"Scadenze aperte"** (dx) · **"Eventi"**.
- Stati riga banca: **da abbinare · abbinato · parziale · ignorato · storno**.
- Pulsanti: **"Abbina ▸"**, **"Abbina e registra pagamento"**, **"Abbina tutti i suggeriti ✓"**, **"Ignora"**, **"Disfa abbinamento"**, **"Conferma import"**.
- Badge: **✦ match suggerito** · **? nessun match certo** · **⤺ storno** · **⊘ duplicato**.
- Toast: *"Abbinato: 135,00 € → Rata 3/4 (Mario Rossi)."* · *"Import: 22 movimenti, 1 duplicato saltato, 1 storno."* · *"Abbinamento annullato — la rata torna aperta."* · *"Storno registrato come nota di credito (45,00 €)."*
- Report import: *"24 righe · 22 importabili · 1 duplicato · 1 storno · 1 senza riferimento (da abbinare a mano)."*

---

## 10. Impatti tecnici (per chi implementa — NON parte di questo R5)

Il design **riusa** R4 (scadenze, `recordPayment`, `CreditNote`) e il **pattern dry-run+anomalie** di `OdsImportService`. Servono **2 tabelle nuove** e i flussi della schermata di match:

- **`bank_statement_lines` (staging movimenti)**: `import_batch`, `value_date`, `amount` (signed), `description`/`counterparty`/`iban`, `row_hash` (idempotenza), `status` (`da_abbinare`/`abbinato`/`parziale`/`ignorato`/`storno`). Popolata da un **`BankStatementImportService`** che ricalca `OdsImportService` (PASS-1 lettura+anomalie trasversali tipo duplicati su `row_hash`, PASS-2 normalizzazione+scrittura; flag `$dryRun`).
- **`reconciliation_events` (memoria abbinamenti)**: `bank_statement_line_id`, `payment_id?`, `payment_plan_id?`, `credit_note_id?`, `type` (`ABBINATO`/`PARZIALE`/`CUMULATIVO`/`SPLIT`/`STORNO`/`IGNORATO`/`ANNULLATO`), `amount`, `user_id`, `created_at`. È il log §6 e abilita **[Disfa]**.
- **Suggeritore di match**: query candidati su `PaymentPlan::pending()/overdue()` con punteggio per `amount` (uguale/contenuto), prossimità `due_date`↔`value_date`, fuzzy su nome/causale vs studente+famiglia (grafo R1). Nessuna scrittura: solo ranking.
- **Conferma abbinamento**: chiama `InvoiceService::recordPayment(invoice, amount, method, date)` con dati dal movimento, aggiorna `PaymentPlan` (`status`/`payment_id`/`paid_date`), e crea il `reconciliation_event`. Cumulativo/split = più chiamate, un evento ciascuna.
- **Storno**: movimento negativo → `InvoiceService::createCreditNote(invoice, amount, reason)` + evento STORNO; oppure ANNULLATO se neutralizza un abbinamento esistente (rollback del `Payment`, rata→`pending`).
- **Schermata split + drag/click**: vista a due colonne con AJAX (abbina/disfa senza ricaricare); drag-and-drop progressivo su `[Abbina ▸]` come fallback tastiera/mobile.

---

## 11. Checklist di accettazione (Definition of Done del design)

- [x] **Import CSV estratto conto** con dry-run + report anomalie (duplicati, illeggibili, storni, senza riferimento), nessuna scrittura prima della conferma. — §3
- [x] **Schermata di match split**: estratto conto a sinistra, scadenze aperte a destra. — §2, §4
- [x] **Drag *o* click** per riconciliare, con **suggerimento** automatico e azione di massa "abbina suggeriti", sempre confermabile. — §4–§5
- [x] **Casi parziali** (parziale, cumulativo, split, sovra-pagamento) coperti nel modal di abbinamento. — §5, §8
- [x] **Storni gestiti come eventi** (CreditNote o annullamento), reversibili. — §6–§7
- [x] **Memoria degli abbinamenti come eventi** con **[Disfa]** e audit. — §6
- [x] Wireframe ASCII per import, schermata di match, modal e log eventi + microcopy IT. — §3–§9
- [x] Impatti tecnici indicati senza implementare: 2 tabelle nuove (movimenti + eventi), riuso `recordPayment`/`CreditNote` e pattern import dry-run. — §10
