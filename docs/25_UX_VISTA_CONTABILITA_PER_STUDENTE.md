# 25 — UX: Vista contabilità per studente (R4 · Design UX)

> Attività Footility **#8524** — *R4 · Design UX — Vista contabilità per studente* (progetto Gestionale Altramusica).
> Obiettivo: **una sola schermata per studente** che risponde a tre domande della segreteria in pochi secondi —
> *quanto deve? cosa scade ora? come registro il pagamento?* — senza saltare tra CRUD slegati di fatture, rate e note di credito.
> Deliverable: wireframe (ASCII) + casi reali (saldo OK, scaduto, parziale, credito) + microcopy IT.
> Mappa sulle attività Fase 2 **F2-05** (contabilità/crediti). Riusa il nucleo famiglia di [`20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md`](20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md) (R1)
> e le iscrizioni/sconti di [`21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md`](21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md) (R2).
> Base AS-IS: [`05_CONTABILITA_CORSI.md`](05_CONTABILITA_CORSI.md) e [`06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md`](06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md).

---

## 0. Principi di design

1. **La contabilità si guarda dallo studente, non dalla fattura.** L'operatrice ragiona *"quanto deve Mario e cosa scade?"*, non *"apri la fattura #2025-0142"*. La vista è centrata sullo **studente** (e, in lettura, sulla **famiglia**); fatture, rate, pagamenti e note di credito sono **dettagli accessibili da lì**, non sezioni separate da cercare a parte.
2. **Tre risposte in alto, sempre.** Sopra ogni cosa, una **striscia di sintesi**: *Dovuto · Pagato · Da saldare · Prossima scadenza*. Chi apre la scheda capisce la situazione senza scorrere.
3. **Registrare un pagamento è un'azione rapida, non un percorso.** Da qualunque riga scaduta/in scadenza: **[Registra pagamento]** → 1 modal con importo precompilato → fatto. Niente "apri fattura → apri tab pagamenti → nuovo → scegli fattura".
4. **Scadenze imminenti in evidenza, leggibili a colpo d'occhio.** Le rate (`PaymentPlan`) ordinate per data, con semaforo: 🔴 scaduta · 🟠 entro 7gg · 🟢 futura · ✅ pagata. Il "non pagato" non si deduce da una tabella di numeri: si **vede**.
5. **Niente CRUD slegati.** Pagamenti, rate (piano di dilazione) e note di credito si creano/leggono **dalla vista studente** in modal contestuali. Le pagine `invoices.*` restano per l'editing fine della fattura, ma il **flusso operativo quotidiano** vive qui.
6. **Una vista, due livelli di zoom.** *Sintesi studente* (default) → espandi una fattura per vedere righe, rate e pagamenti. La famiglia è un **roll-up** opzionale (utile per "quanto deve il nucleo Rossi").

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| Fatture (`Invoice`: `subtotal`, `discount_amount`, `total_amount`, `status`, `due_date`) + CRUD `admin/invoices` | ✅ esiste |
| Righe fattura (`InvoiceItem`: `item_type`, `description`, `quantity`, `unit_price`, `total_price`) | ✅ esiste |
| Pagamenti (`Payment`: `payment_date`, `amount`, `payment_method`) + `InvoiceService::recordPayment` | ✅ esiste, ma agganciato **alla fattura** (`recordPayment(Invoice $invoice)`) |
| Piano rate (`PaymentPlan`: `installment_number`, `due_date`, `amount`, `status`, scope `overdue`/`pending`) | ✅ esiste a livello dati + `PaymentPlanController` |
| Note di credito (`CreditNote`: `amount`, `reason`) | ✅ esiste |
| Anno scolastico (`AcademicYear`, fattura ha `academic_year_id`) | ✅ esiste |
| **Vista contabilità centrata sullo studente** (sintesi dovuto/pagato/scadenze in un colpo) | ❌ **assente** — oggi `students/show` ha solo un bottone "Nuova Fattura" |
| **Registra pagamento come azione rapida dalla scheda studente** | ❌ oggi si passa da `invoices/{id}` → azione `recordPayment` (CRUD per fattura) |
| **Lista fatture filtrabile per studente** | ⚠️ esiste `invoices.index?student_id=` ma è la lista globale filtrata, non una vista contabile per persona |
| **Scadenze imminenti aggregate** (rate in scadenza/scadute per studente) | ⚠️ il dato c'è (`PaymentPlan::overdue()`) ma **non aggregato** sullo studente |
| **Roll-up famiglia** (totale dovuto del nucleo) | ❌ assente |

> Conclusione: i **dati ci sono tutti** (fatture, rate con scadenze, pagamenti, note di credito). Manca la **vista che li mette insieme per studente** e la **scorciatoia "registra pagamento"** fuori dal CRUD fattura. Questo design non richiede nuove tabelle: aggrega ciò che esiste (vedi §8).

---

## 2. Architettura della vista (in chiave UX)

```
                       SCHEDA STUDENTE ▸ tab "Contabilità"  [Anno: 2025/26 ▾]
  ┌──────────────────────────────────────────────────────────────────────────┐
  │  STRISCIA SINTESI   Dovuto · Pagato · Da saldare · Prossima scadenza       │
  ├──────────────────────────────────────────────────────────────────────────┤
  │  SCADENZE IMMINENTI   (PaymentPlan ordinate per data, con semaforo)        │
  │     ↳ [Registra pagamento]  azione rapida per riga                         │
  ├──────────────────────────────────────────────────────────────────────────┤
  │  FATTURE DELL'ANNO    (Invoice, espandibili → righe/rate/pagamenti)        │
  │     ↳ [+ Nuova fattura] · [Piano rate] · [Nota di credito]  in modal       │
  └──────────────────────────────────────────────────────────────────────────┘
```

- **Sorgenti dati**: striscia = somma `Invoice.total_amount` vs `Payment.amount` dell'anno; scadenze = `PaymentPlan` (`pending`/`overdue`) dello studente; fatture = `Invoice` filtrate per `student_id` + `academic_year_id`.
- **"Da saldare"** = Σ dovuto − Σ pagato − Σ note di credito. Coerente con i fogli AS-IS (`tot anno dovuto`, `pagato tot`, `tot da saldare` del foglio *riepilogo sintetico*).

---

## 3. Vista principale — Contabilità studente (default)

**Percorso click:**

```
Sidebar ▸ Anagrafiche ▸ scheda studente ▸ tab "Contabilità"
   └─ [Anno: 2025/26 ▾]  (default = AcademicYear::getCurrent())
```

**Wireframe — Tab Contabilità (sintesi + scadenze + fatture):**

```
┌───────────────────────────────────────────────────────────────────────────┐
│  ‹ Studente   Mario Rossi · 12 anni            [Anno: 2025/26 ▾]            │
│  Anagrafica · Iscrizioni · 〔Contabilità〕· Documenti                       │
├───────────────────────────────────────────────────────────────────────────┤
│  DOVUTO 810,00 €   PAGATO 540,00 €   DA SALDARE 270,00 €   ⚠ scade 05/06    │
│  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░  pagato 67%                                          │
├───────────────────────────────────────────────────────────────────────────┤
│  SCADENZE IMMINENTI                                                         │
│  🔴 Rata 3/4   €135,00   scaduta 05/06   Fatt. 2025-0142   [Registra pag.]  │
│  🟠 Rata 4/4   €135,00   tra 6 gg 05/07  Fatt. 2025-0142   [Registra pag.]  │
│  🟢 (nessun'altra in scadenza)                                              │
├───────────────────────────────────────────────────────────────────────────┤
│  FATTURE 2025/26                                  [+ Nuova fattura]         │
│  ┌─────────────────────────────────────────────────────────────────────┐  │
│  │ ▸ 2025-0142 · 01/10  · Pianoforte (annuale)  540€   🟠 parziale 270€ │  │
│  │ ▸ 2025-0098 · 01/10  · Iscrizione + libri    120€   ✅ saldata        │  │
│  │ ▸ 2025-0210 · 12/03  · Noleggio violino       90€   🟢 da incassare   │  │
│  └─────────────────────────────────────────────────────────────────────┘  │
│  Credito disponibile: 45,00 € (nota di credito 2024/25)  [Usa]             │
└───────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Striscia sintesi sempre visibile** (sticky): 4 numeri + barra %. `DA SALDARE` in rosso se > 0 e c'è una rata scaduta.
- **Scadenze imminenti** = `PaymentPlan` con `status pending/overdue`, ordinate per `due_date`. Mostra solo le **rilevanti** (scadute + entro 30gg); le future lontane stanno dentro la fattura espansa.
- **Ogni riga scadenza ha [Registra pagamento]** (§5) con importo precompilato = importo rata.
- Le fatture si **espandono in linea** (▸) per mostrare righe/rate/pagamenti senza cambiare pagina (§4).
- **Credito disponibile**: se esistono `CreditNote` non interamente applicate, banner con [Usa] → detrazione su una rata/fattura aperta (§6).

---

## 4. Fattura espansa (zoom) — righe, rate, pagamenti in un posto

Espandere una fattura (▸) apre il dettaglio **nella stessa vista**: niente salto a `invoices/{id}`.

```
┌─ ▾ Fattura 2025-0142 · 01/10/2025 · scad. 31/05/2026 ─────────────────────┐
│  RIGHE                                                                     │
│   Pianoforte individuale · 9 mesi × 90,00            810,00 €              │
│   Sconto fratelli −10%                               −81,00 €              │
│   ───────────────────────────────────────────────────────────            │
│   Totale fattura                                      729,00 €             │
│                                                                            │
│  PIANO RATE (4)                          [✎ Modifica piano]                │
│   ✅ 1/4  €182,25  pagata 03/10  (contanti)                                │
│   ✅ 2/4  €182,25  pagata 04/01  (bonifico)                                │
│   🔴 3/4  €182,25  scaduta 05/06            [Registra pagamento]           │
│   🟠 4/4  €182,25  scade 05/07              [Registra pagamento]           │
│                                                                            │
│  PAGAMENTI                                                                 │
│   03/10  €182,25  contanti                                                 │
│   04/01  €182,25  bonifico · rif. BNF-7741                                 │
│                                                                            │
│  [Nota di credito]   [Apri fattura completa ›]   [Stampa/PDF]             │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Righe** da `InvoiceItem` (descrizione + `total_price`); lo sconto è una riga leggibile, non un campo nascosto.
- **Piano rate** da `PaymentPlan` con semaforo per stato. [✎ Modifica piano] apre il modal dilazioni (§5c).
- **Pagamenti** da `Payment`. Una rata pagata mostra metodo e (se presente) `reference_number`.
- **[Apri fattura completa ›]** resta come scappatoia verso il CRUD `invoices.show`/`edit` per l'editing fine — ma il 90% delle operazioni si fa da qui.

---

## 5. Azione rapida — Registra pagamento (il gesto chiave)

Da qualunque riga (scadenza in sintesi o rata in fattura espansa): **[Registra pagamento]** → modal con importo già compilato.

**Percorso click (happy path):**

```
Scadenza 🔴 Rata 3/4 ▸ [Registra pagamento]
   └─ MODAL "Registra pagamento — Mario Rossi · Fatt. 2025-0142 · Rata 3/4"
        ├─ Importo  135,00 €   (precompilato = importo rata, override possibile)
        ├─ Data     29/05/2026 (oggi)
        ├─ Metodo   ◉ Contanti ○ Bonifico ○ POS ○ Assegno
        ├─ Riferimento (opz.)  ___________
        └─ [Registra]  → Payment creato, rata → ✅ pagata, sintesi aggiornata
```

**Wireframe — Modal "Registra pagamento":**

```
┌────────── Registra pagamento · Mario Rossi ──────────────────────────────┐
│  Fattura 2025-0142  ·  Rata 3/4  ·  scaduta 05/06                         │
│                                                                          │
│  Importo        [ 135,00 ] €      ▸ [salda tutto il dovuto: 270,00 €]     │
│  Data           [ 29/05/2026 ]                                           │
│  Metodo         ◉ Contanti  ○ Bonifico  ○ POS  ○ Assegno                 │
│  Riferimento    [________________]  (n. bonifico / ricevuta)             │
│  Note           [________________________________]                       │
│                                                                          │
│                                       [ Annulla ]   [ Registra ]         │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Importo precompilato** = importo della rata selezionata; scorciatoia **"salda tutto il dovuto"** per chi paga l'intero residuo.
- **Aggancio rata**: se il pagamento parte da una rata, `Payment` viene collegato e la rata passa a `paid` (`PaymentPlan.payment_id` + `paid_date`). Se l'importo copre più rate consecutive, le marca a cascata (vedi §7 "pagamento cumulativo").
- **Dopo [Registra]**: `Payment` creato via `InvoiceService::recordPayment`, semaforo rata → ✅, striscia sintesi e barra % ricalcolate **senza ricaricare la pagina** (AJAX), toast *"Pagamento di 135,00 € registrato (Rata 3/4)."*

### 5b. + Nuova fattura (dal contesto studente)
`[+ Nuova fattura]` apre il modal con **studente già impostato** e anno = quello in testata (riusa `invoices.create?student_id=`, ma in modal): righe da iscrizioni/accessori dello studente proposte, non da ridigitare.

### 5c. Piano rate / dilazioni
`[Piano rate]` / `[✎ Modifica piano]`: modal che divide il `total_amount` in N rate con date (riusa `PaymentPlanController`). Default coerente con AS-IS (`n. dilazioni`, `Scadenza 1`, `Scadenza 2`).

---

## 6. Note di credito e crediti residui

Coerente con R2 §6: la **nota di credito** (`CreditNote`) è un importo a favore. In questa vista:

```
[Nota di credito]  (dentro fattura espansa)
   └─ MODAL: importo, motivo (storno/rimborso/abbuono), note  → CreditNote creata
              → "Credito disponibile" sale nella striscia studente
```

- Il **credito disponibile** = Σ `CreditNote` non ancora applicate. Banner in cima con **[Usa]** → detrazione su una rata/fattura aperta (riduce il "da saldare").
- Microcopy: *"Credito disponibile 45,00 € · [Usa su una scadenza]"*.

> Nota implementativa (NON parte di R4): un vero "borsellino crediti" riutilizzabile cross-fattura è materia di Fase 2 (vedi R2 §9). Qui il design **mostra e applica** il credito rilevabile dalle note esistenti.

---

## 7. Stati e casi reali

| Caso | Comportamento UX |
| --- | --- |
| **Tutto saldato** | Striscia verde, "Da saldare 0,00 €", nessuna scadenza in evidenza. Fatture tutte ✅. |
| **Rata scaduta** | 🔴 in testa alle scadenze, "Da saldare" rosso, badge ⚠ con data nella striscia. CTA [Registra pagamento] in primo piano. |
| **Pagamento parziale** | Fattura 🟠 "parziale", barra % a metà; il residuo resta come rata/e aperte. |
| **Pagamento cumulativo** (paga più rate insieme) | "salda tutto il dovuto" o importo > rata → marca le rate consecutive fino a capienza; eventuale resto come acconto sulla rata successiva. |
| **Acconto senza piano rate** | Fattura senza `PaymentPlan`: [Registra pagamento] sulla fattura intera; residuo = `total_amount` − Σ pagamenti. |
| **Nota di credito > residuo** | Si applica fino a capienza; il resto resta "Credito disponibile" (segnalato). |
| **Studente con fratelli** | Toggle "Vista famiglia" (§ sotto) per roll-up nucleo; lo sconto fratelli è già nelle righe fattura (R2). |
| **Anno diverso da quello attivo** | Selettore anno in testata; storico anni precedenti in sola lettura con stessa struttura. |
| **Fattura annullata/stornata** | `Invoice.status` annullata → esclusa dal "dovuto", note di credito collegate visibili nello storico. |

**Roll-up famiglia (lettura):** dalla scheda studente, toggle **"Vista famiglia"** somma dovuto/pagato/da-saldare dei fratelli (nucleo R1) — utile per *"quanto deve in tutto la famiglia Rossi?"*. È una **vista aggregata**, le azioni restano per-studente.

```
┌── Famiglia Rossi · 2025/26 ──────────────────────────────────────────────┐
│  Mario Rossi    dovuto 810  pagato 540  da saldare 270   ⚠ rata scaduta   │
│  Giulia Rossi   dovuto 648  pagato 648  da saldare   0   ✅               │
│  ─────────────────────────────────────────────────────────────────────  │
│  TOTALE FAMIGLIA   dovuto 1.458   pagato 1.188   da saldare 270           │
└────────────────────────────────────────────────────────────────────────────┘
```

---

## 8. Microcopy (etichette IT)

- Tab: **"Contabilità"** (non "Fatture"). Striscia: **"Dovuto" · "Pagato" · "Da saldare" · "Prossima scadenza"**.
- Semafori scadenza: **🔴 scaduta** · **🟠 in scadenza** · **🟢 futura/da incassare** · **✅ pagata**.
- Stati fattura: **✅ saldata** · **🟠 parziale** · **🟢 da incassare** · **⚪ annullata**.
- Pulsanti: **"+ Nuova fattura"**, **"Registra pagamento"**, **"salda tutto il dovuto"**, **"Piano rate"**, **"Nota di credito"**, **"Apri fattura completa ›"**, **"Vista famiglia"**.
- Toast: *"Pagamento di 135,00 € registrato (Rata 3/4)."* · *"Fattura 2025-0142 saldata."* · *"Nota di credito di 45,00 € creata."*
- Banner: *"Credito disponibile 45,00 € · [Usa su una scadenza]"* · *"⚠ Rata scaduta dal 05/06 — 135,00 €"*.

---

## 9. Impatti tecnici (per chi implementa — NON parte di questo R4)

Il design **riusa le entità e i servizi esistenti** (`Invoice`, `InvoiceItem`, `Payment`, `PaymentPlan`, `CreditNote`, `InvoiceService::recordPayment`, `PaymentPlanController`). Servono i **flussi/aggregazioni mancanti**, senza nuove tabelle:

- **Tab "Contabilità" sulla scheda studente**: nuova vista che aggrega per `student_id` + `academic_year_id` — Σ fatture, Σ pagamenti, Σ note di credito → striscia sintesi; `PaymentPlan` dello studente ordinate per `due_date` → scadenze imminenti.
- **"Registra pagamento" contestuale**: richiamare `InvoiceService::recordPayment` da modal sulla scadenza/rata (oggi parte da `InvoiceController@recordPayment(Invoice)`); precompilare importo dalla rata e, su pagamento, aggiornare `PaymentPlan.status`/`payment_id`/`paid_date`. Gestire pagamento cumulativo (marca rate consecutive).
- **Scadenze aggregate**: query su `PaymentPlan::pending()/overdue()` filtrata per studente (join `invoice.student_id`); il dato e gli scope esistono già.
- **Fattura espandibile in-page**: rendering del dettaglio (`items`, `paymentPlans`, `payments`) già caricabili (`Invoice::load([...])` lo fa in `show`) — qui in pannello espandibile via AJAX.
- **Credito disponibile**: leggere `CreditNote` non applicate; azione "Usa" = detrazione su rata/fattura aperta.
- **Roll-up famiglia**: aggregazione sugli studenti del nucleo (grafo studente↔genitore di R1); sola lettura.
- **Nuova fattura da contesto**: riuso `invoices.create?student_id=` in modal con righe proposte da iscrizioni/accessori dell'anno.

---

## 10. Checklist di accettazione (Definition of Done del design)

- [x] Definita la **vista unica per studente** centrata su persona, non su fattura. — §2–§3
- [x] **Striscia sintesi** (dovuto/pagato/da saldare/prossima scadenza) sempre visibile e leggibile. — §3
- [x] **Scadenze imminenti** in evidenza con semaforo e pagato/non-pagato a colpo d'occhio. — §3
- [x] **Registra pagamento come azione rapida** (1 modal, importo precompilato), fuori dal CRUD fattura. — §5
- [x] **Pagamenti, rate e note di credito accessibili dalla vista studente** (no CRUD slegati). — §4–§6
- [x] Coperti i **casi reali**: saldato, scaduto, parziale, cumulativo, acconto, credito > residuo. — §7
- [x] **Roll-up famiglia** in lettura (collega nucleo R1). — §7
- [x] Wireframe ASCII per ogni schermata/modal chiave + microcopy IT. — §3–§8
- [x] Indicati impatti tecnici minimi senza implementare; riuso entità/servizi esistenti, nessuna nuova tabella. — §9
