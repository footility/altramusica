# 30 — UX: Flusso contratto e firma (mock) (R3 · Design UX)

> Attività Footility **#8523** — *R3 · Design UX — Flusso contratto e firma mock* (progetto Gestionale Altramusica).
> Obiettivo: rendere il ciclo di vita del contratto **leggibile a colpo d'occhio** come **timeline visiva**
> *proposta → inviato → firmato*, e dare alla segreteria un modo semplice e onesto di **chiudere la firma**:
> upload della **scansione firmata** (mock manuale), senza promettere una firma digitale che oggi non c'è.
> Deliverable: timeline di stato (ASCII) + wireframe show/azioni + flusso firma mock + casi reali + microcopy IT
> + **punto d'innesto** per il futuro **webhook firma digitale** (senza implementarlo).
> Base AS-IS: [`04_CONTRATTI_STUDENTI.md`](04_CONTRATTI_STUDENTI.md) e [`12_DOCUMENTI_E_MODELLI_CONTRATTI.md`](12_DOCUMENTI_E_MODELLI_CONTRATTI.md).
> Riusa il nucleo studente/famiglia di [`20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md`](20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md) (R1),
> le iscrizioni di [`21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md`](21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md) (R2),
> l'archivio documenti di [`23_UX_ARCHIVIO_DOCUMENTI_RICERCA_RAPIDA.md`](23_UX_ARCHIVIO_DOCUMENTI_RICERCA_RAPIDA.md) (R10).

---

## 0. Principi di design

1. **Lo stato è una storia, non un'etichetta.** Oggi lo stato è un badge isolato (`draft`/`sent`/`signed`). Un contratto invece *avanza*: è stato proposto, poi inviato il giorno X, poi firmato il giorno Y. La vista deve raccontarlo come **timeline** con le date, non come una singola parola.
2. **Una sola CTA per volta, quella giusta per lo stato.** In bozza si *invia*; inviato si *registra la firma*; firmato si *consulta/stampa*. Niente muro di pulsanti: l'azione successiva è **una sola, evidente**, le altre sono secondarie.
3. **La firma mock è onesta.** Non simuliamo una firma digitale: registriamo che il contratto **è stato firmato su carta** allegando la **scansione**. La UI dice esattamente questo (*"Carica il contratto firmato"*), così l'operatrice sa cosa sta facendo e resta tracciabile (chi/quando).
4. **Avanti è facile, indietro è possibile ma esplicito.** Inviare e firmare sono 1-click. Tornare indietro (annullare un invio, sbloccare una firma errata) esiste ma è un'azione **secondaria e confermata**, non un incidente.
5. **Il futuro digitale ha già il suo posto.** La timeline e gli stati sono disegnati perché domani la firma possa arrivare da un **webhook** (provider esterno) senza ridisegnare nulla: cambia solo *chi* scrive `signed_date` e *come* (vedi §9). Oggi lo scrive l'operatrice via upload; domani lo scriverà il provider.
6. **Niente nuovi dati se non servono.** Il modello c'è già: `Contract.status` + `sent_date` + `signed_date` + `token`, e `Document` per la scansione. Questo design **non aggiunge tabelle**; usa i campi esistenti e li mette in scena (§10).

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| `Contract` (`status`, `sent_date`, `signed_date`, `start/end_date`, `type`, `token`, `terms`, `notes`) | ✅ esiste |
| Enum stato `draft,sent,signed,expired,cancelled` (validato in `store`/`update`) | ✅ esiste |
| `ContractService::sendContract()` → `status=sent` + `sent_date=now()` | ✅ esiste |
| `ContractService::signContract()` → `status=signed` + `signed_date=now()` | ✅ esiste |
| `ContractService::createFromEnrollment()` → contratto in `draft` da iscrizione | ✅ esiste |
| Route `contracts/{c}/send` e `contracts/{c}/sign` (POST) + link pubblico via `token` | ✅ esiste |
| `Document` collegabile al contratto (`contract_id`, `file_path`, `file_name`, `type`, `uploaded_by_user_id`) | ✅ esiste |
| **Timeline visiva proposta→inviato→firmato con le date** | ❌ **assente** — `show` mostra solo un badge colore + date in `<dl>` |
| **CTA contestuale unica per stato** | ⚠️ parziale: ci sono i bottoni "Invia"/"Segna come Firmato", ma "firmato" è un click secco **senza allegare nulla** |
| **Firma mock = upload scansione del contratto firmato** | ❌ assente: `sign()` mette `signed` ma **non chiede né collega la scansione** (il `Document` esiste ma non è usato qui) |
| **Annulla invio / sblocca firma (passi indietro)** | ❌ assente: nessun modo guidato per correggere uno stato errato |
| **Punto d'innesto webhook firma digitale** | ❌ assente (atteso in Fase 2) |
| **Stato "scaduto" visibile** (`scopeExpired` su `end_date < now`) | ⚠️ il dato/scope c'è ma non è mostrato in timeline |

> Conclusione: il **back-end del flusso esiste** (stati, date, servizi, route, entità Document). Mancano la **messa in scena** (timeline + CTA contestuale) e il **gesto di firma onesto** (upload della scansione che popola `signed_date` *e* allega il PDF firmato). Tutto realizzabile **senza nuove tabelle** (§10).

---

## 2. Mappa stati ↔ linguaggio cliente

Il cliente ragiona in italiano semplice (*"proposta → inviato → firmato"*); il codice usa l'enum inglese. Allineamento:

| Linguaggio cliente (timeline) | `Contract.status` | Campo data | Chi/cosa lo scrive |
| --- | --- | --- | --- |
| **Proposta** (bozza) | `draft` | `created_at` | creazione (manuale o `createFromEnrollment`) |
| **Inviato** | `sent` | `sent_date` | operatrice → `sendContract()` |
| **Firmato** | `signed` | `signed_date` | **oggi**: operatrice via upload scansione · **domani**: webhook provider (§9) |
| *Scaduto* (fuori timeline lineare) | `expired` | `end_date` | scope `expired()` / fine periodo |
| *Annullato* | `cancelled` | — | azione esplicita |

> La timeline principale è la **linea felice a 3 tappe**: *Proposta → Inviato → Firmato*. `expired`/`cancelled` sono **deviazioni** rappresentate come stato terminale colorato, non come tappa intermedia (§4 e §7).

---

## 3. Timeline visiva (il deliverable centrale)

Componente in testa alla scheda contratto: 3 tappe con pallino **fatto / corrente / da fare**, data sotto ogni tappa fatta, e la **CTA del passo successivo** ancorata alla tappa corrente.

**Stato bozza — "Proposta":**
```
   ●━━━━━━━━━━○ ─ ─ ─ ─ ─ ○
 PROPOSTA     Inviato      Firmato
 12/05/2026   —            —
   ▲
   sei qui                       [ Invia contratto ▸ ]
```

**Stato inviato — "Inviato":**
```
   ●━━━━━━━━━━●━━━━━━━━━━○
 Proposta    INVIATO      Firmato
 12/05/2026  18/05/2026   —
                ▲
                in attesa di firma     [ Registra firma ▸ ]
```

**Stato firmato — "Firmato" (completo):**
```
   ●━━━━━━━━━━●━━━━━━━━━━●
 Proposta    Inviato     FIRMATO ✔
 12/05/2026  18/05/2026  24/05/2026
                            ▲
                            completato   [ Scarica PDF firmato ⤓ ]
```

**Deviazioni (stato terminale, niente CTA "avanti"):**
```
 ⚪ ANNULLATO 20/05  — contratto annullato (motivo: …)        [ Riapri come bozza ]
 🔴 SCADUTO  31/08   — periodo terminato senza firma          [ Rinnova ▸ ]
```

**Regole UX:**
- **Pallini:** `●` tappa raggiunta · `○` tappa futura · linea piena fra tappe raggiunte, tratteggiata verso il futuro.
- **Data sotto ogni tappa raggiunta** (`created_at` / `sent_date` / `signed_date`). La tappa corrente ha l'etichetta in **MAIUSCOLO** + freccia "sei qui".
- **CTA del passo successivo** ancorata a destra della tappa corrente (una sola, primaria). Le azioni di servizio (indietro, annulla) stanno nel menu **⋯** (§6).
- **Colori semaforo:** proposta ⚪ grigio · inviato 🟠 (in attesa) · firmato 🟢 · scaduto 🔴 · annullato ⚪ barrato.

---

## 4. Vista contratto (show) — timeline + dettaglio + azione

Ridisegno di `admin/contracts/show`: la timeline va **in testa**, poi il dettaglio, poi il riquadro azione contestuale.

```
┌───────────────────────────────────────────────────────────────────────────┐
│  ‹ Contratti     Contratto CONTR-2026-0042 · Mario Rossi · 12 anni          │
├───────────────────────────────────────────────────────────────────────────┤
│   ●━━━━━━━━━━●━━━━━━━━━━○                                                    │
│  Proposta    INVIATO     Firmato            (in attesa di firma dal 18/05)   │
│  12/05/2026  18/05/2026  —                                                   │
├───────────────────────────────────────────────────────────────────────────┤
│  DETTAGLIO                                          Tipo: Regular  ⚙ ⋯       │
│  Studente   Mario Rossi · CF RSSMRA13…    Anno: 2025/26                      │
│  Periodo    01/10/2025 → 30/06/2026                                          │
│  Termini    Quota annuale Pianoforte, 3 rate… (estratto)        [vedi tutto] │
│  Note       —                                                                │
├───────────────────────────────────────────────────────────────────────────┤
│  AZIONE (stato = Inviato)                                                    │
│   Il contratto è stato inviato il 18/05. Quando ricevi la copia firmata:    │
│        [ Registra firma (carica scansione) ▸ ]                              │
│   Inviato a:  genitore1@mail.it · Link precompilato: [Copia] [Apri]         │
├───────────────────────────────────────────────────────────────────────────┤
│  DOCUMENTI COLLEGATI                                                         │
│   📄 Bozza contratto.pdf        generato 12/05      [Apri]                   │
│   (la scansione firmata comparirà qui dopo la registrazione firma)           │
└───────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Riquadro AZIONE = una sola CTA primaria**, decisa dallo stato (§3). Il testo spiega *cosa succede dopo*.
- **Documenti collegati** = `Document where contract_id` (riusa R10): bozza generata, scansione firmata, eventuali allegati. La scansione firmata, una volta caricata, è qui in evidenza.
- **Menu ⋯** (servizio): Modifica · Annulla contratto · *(se inviato)* Annulla invio · *(se firmato)* Sblocca firma · Stampa/PDF · Crea fattura. Riusa la route esistente `contracts/{c}/create-invoice`.
- **Link precompilato** (`token`): mostrato solo da `sent` in poi, con [Copia]/[Apri]; è già esposto nel codice (`contracts.public.show`).

---

## 5. Azione chiave — Registra firma (mock manuale via upload)

Dal contratto **Inviato**, **[Registra firma]** apre un modal che fa due cose insieme: **allega la scansione firmata** e **marca `signed`** con la data. È il gesto onesto: "ho la carta firmata, la archivio e chiudo".

**Percorso click (happy path):**
```
Contratto Inviato ▸ [Registra firma (carica scansione)]
   └─ MODAL "Registra firma — CONTR-2026-0042 · Mario Rossi"
        ├─ File firmato   [ Trascina qui il PDF/JPG  oppure  Sfoglia ]  (obbligatorio)
        ├─ Data firma     [ 24/05/2026 ]   (default oggi, modificabile = data sulla carta)
        ├─ Note (opz.)    [ es. "firmato dal genitore in segreteria" ]
        └─ [ Annulla ]   [ Conferma firma ]
              → Document(type=signed_contract) creato e collegato al contratto
              → status=signed, signed_date = data indicata
              → timeline avanza a "Firmato ✔", toast di conferma
```

**Wireframe — Modal "Registra firma":**
```
┌────────── Registra firma · CONTR-2026-0042 · Mario Rossi ─────────────────┐
│  Il contratto risulta INVIATO il 18/05/2026.                              │
│  Carica la copia firmata per chiudere il contratto.                       │
│                                                                           │
│  Contratto firmato *   ┌─────────────────────────────────────────────┐   │
│                        │   ⤓  Trascina qui il file (PDF/JPG/PNG)      │   │
│                        │        oppure  [ Sfoglia… ]                  │   │
│                        └─────────────────────────────────────────────┘   │
│                        max 10 MB · resterà nei documenti del contratto    │
│                                                                           │
│  Data firma            [ 24/05/2026 ]   (la data sulla copia firmata)     │
│  Note (opzionale)      [_____________________________________________]    │
│                                                                           │
│                                          [ Annulla ]  [ Conferma firma ]  │
└───────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **File obbligatorio** per confermare: senza scansione non c'è "firmato" (coerenza tra stato e prova). Eccezione esplicita: link "*Segna firmato senza allegato*" (caso raro, conferma extra) → marca `signed` ma evidenzia in timeline un ⚠ *"firma registrata senza scansione"*.
- **Data firma modificabile**: la firma su carta può essere di ieri; default = oggi. Scrive `signed_date` con questa data, non con `now()` a forza.
- **Atomicità**: upload `Document` + `signContract()` avvengono insieme; se l'upload fallisce, lo stato **non** cambia (niente "firmato senza documento" per errore).
- **Dopo conferma**: toast *"Contratto firmato e scansione archiviata."*, timeline → Firmato ✔, riquadro azione diventa *"[Scarica PDF firmato] · [Crea fattura]"*.

### 5b. Invia contratto (passo precedente)
Da **Proposta**, **[Invia contratto]** → conferma leggera con il recapito (mail genitore da R1) → `sendContract()` (`status=sent`, `sent_date=now()`). Toast *"Contratto inviato a genitore1@mail.it."*. La timeline avanza a *Inviato*. *(Oggi l'invio è il cambio di stato + link `token`; l'invio email reale è materia Fase 2, non R3.)*

---

## 6. Passi indietro e correzioni (azioni secondarie, confermate)

Errori capitano: invio sbagliato, firma registrata sul contratto sbagliato. Vanno gestiti **senza editare a mano il DB**, ma con attrito (conferma):

| Azione (menu ⋯) | Da stato | Effetto | Conferma |
| --- | --- | --- | --- |
| **Annulla invio** | `sent` | torna `draft`, azzera `sent_date` | "Il contratto tornerà in bozza. Confermi?" |
| **Sblocca firma** | `signed` | torna `sent`, azzera `signed_date`; la scansione resta nei documenti (storico) | "Annulli la firma registrata? La scansione resta archiviata." |
| **Annulla contratto** | qualsiasi (≠`signed` consigliato) | `cancelled` + motivo | "Motivo annullamento: [___]" |
| **Riapri come bozza** | `cancelled` | torna `draft` | conferma semplice |

> Queste sono **regole UX** sopra i servizi esistenti: oggi `ContractController` non le espone, ma sono il complemento naturale di `send`/`sign` (vedi §10). La scansione non si cancella sbloccando la firma: la prova storica resta.

---

## 7. Stati e casi reali

| Caso | Comportamento UX |
| --- | --- |
| **Nuova proposta da iscrizione** | `createFromEnrollment` → contratto in *Proposta* con periodo precompilato dall'iscrizione (R2). CTA = [Invia]. |
| **Inviato, in attesa** | Timeline a 🟠 *Inviato*; riquadro mostra data invio + link precompilato; CTA = [Registra firma]. |
| **Firmato con scansione** | Timeline 🟢 *Firmato ✔* con data; scansione in cima ai documenti; CTA = [Scarica PDF] / [Crea fattura]. |
| **Firmato senza scansione** (eccezione) | *Firmato* ma badge ⚠ *"senza scansione"*; suggerimento [Carica ora] per completare la prova. |
| **Invio per errore** | ⋯ → *Annulla invio* → torna *Proposta*; nessun dato perso oltre `sent_date`. |
| **Firma su contratto sbagliato** | ⋯ → *Sblocca firma* → torna *Inviato*; scansione resta nello storico documenti. |
| **Contratto scaduto senza firma** | `end_date < oggi` e non `signed` → timeline 🔴 *Scaduto*; CTA = [Rinnova ▸] (nuova proposta su nuovo anno, R2). |
| **Annullato** | ⋯ → *Annulla contratto* + motivo → ⚪ *Annullato* barrato; resta in archivio/storico. |
| **Noleggio strumento** (`type=instrument_rental`) | Stesso flusso (proposta→inviato→firmato); la timeline è identica, cambia solo il tipo nel dettaglio. Collega al noleggio di R6. |
| **Firma digitale (futuro)** | La tappa *Firmato* viene marcata dal **webhook** anziché dall'upload; timeline invariata (§9). |

---

## 8. Microcopy (etichette IT)

- Tappe timeline: **"Proposta" · "Inviato" · "Firmato"** (+ stati terminali **"Scaduto" · "Annullato"**).
- Sotto-etichette: *"sei qui"*, *"in attesa di firma dal 18/05"*, *"completato"*.
- CTA per stato: **"Invia contratto"** (proposta) · **"Registra firma (carica scansione)"** (inviato) · **"Scarica PDF firmato"** (firmato).
- Modal firma: titolo **"Registra firma"**, campo **"Contratto firmato *"**, hint *"Trascina qui il file (PDF/JPG/PNG) · max 10 MB"*, **"Data firma"**, **"Conferma firma"**.
- Azioni ⋯: **"Annulla invio" · "Sblocca firma" · "Annulla contratto" · "Riapri come bozza" · "Crea fattura" · "Stampa/PDF"**.
- Toast: *"Contratto inviato a genitore1@mail.it."* · *"Contratto firmato e scansione archiviata."* · *"Invio annullato — contratto tornato in bozza."* · *"Firma sbloccata — contratto di nuovo Inviato."*
- Badge eccezioni: **⚠ "Firma registrata senza scansione"** · 🔴 **"Scaduto il 31/08"**.

---

## 9. Innesto futuro — webhook firma digitale (NON parte di R3)

Il design lascia la porta aperta a un provider di firma digitale (es. Yousign/Namirial/DocuSign) **senza ridisegnare la timeline**: cambia solo *chi* marca la tappa *Firmato*.

```
   OGGI (mock manuale)                     DOMANI (firma digitale)
   ────────────────────                    ────────────────────────
   [Invia] → status=sent                   [Invia per firma digitale] → status=sent
            + link token                            + invio al provider (request_id)
                                                            │
   Operatrice riceve carta firmata          Genitore firma online sul provider
   [Registra firma] + upload scansione                     │
            │                                       webhook POST /contracts/webhook/sign
            ▼                                               ▼
   signContract() ← operatrice             signContract() ← webhook verificato (HMAC)
   status=signed, signed_date,             status=signed, signed_date=evento,
   Document(scansione)                     Document(PDF firmato scaricato dal provider)
```

**Punti d'innesto già predisposti dal design (da implementare in Fase 2):**
- **Stato e date invariati**: il webhook scrive gli stessi `status=signed` + `signed_date` che oggi scrive l'operatrice → la timeline (§3) non cambia.
- **`Contract.token`** già esiste: può diventare la correlazione con la pratica del provider (oppure aggiungere `external_signature_id` se serve un id dedicato — *unico campo potenzialmente nuovo*).
- **`Document`** già esiste: il PDF firmato restituito dal provider si archivia esattamente come la scansione mock (`type=signed_contract`).
- **Endpoint webhook** nuovo (es. `POST /contracts/webhook/sign`) con verifica firma HMAC del provider → chiama lo **stesso** `ContractService::signContract()`.
- **Indicatore canale** in timeline: piccola etichetta *"firmato a mano"* vs *"firma digitale"* sotto la tappa Firmato, per tracciabilità.

> R3 si ferma al **mock**: la UI parla di "carica scansione". Il webhook è documentato come direzione, non implementato.

---

## 10. Impatti tecnici (per chi implementa — NON parte di questo R3)

Il design **riusa entità, servizi e route esistenti**; nessuna nuova tabella richiesta per il mock:

- **Componente timeline** in `admin/contracts/show`: render di 3 tappe da `status` + `created_at`/`sent_date`/`signed_date` (più stati terminali `expired`/`cancelled`). Puramente di presentazione.
- **CTA contestuale**: sostituire i due bottoni attuali con **una** CTA decisa dallo stato (proposta→`send`, inviato→modal firma, firmato→download/fattura).
- **Modal "Registra firma"**: nuova action (es. `ContractController@registerSignature`) che in transazione: valida e salva l'upload come `Document(contract_id, type='signed_contract', file_path, file_name, mime_type, size, uploaded_by_user_id)` **e** chiama `ContractService::signContract()` con `signed_date` dal form. Riusa lo storage documenti di R10.
  - Estendere `signContract()` per accettare una data opzionale (oggi forza `now()`).
- **Passi indietro** (§6): action `unsend` (sent→draft, azzera `sent_date`), `unsign` (signed→sent, azzera `signed_date`, mantiene Document), `cancel` (+motivo in `notes`/campo dedicato), `reopen`. Tutte sopra i campi esistenti.
- **Stato scaduto** in vista: usare `Contract::scopeExpired()` già presente per dipingere la timeline 🔴 quando `end_date < now` e non firmato.
- **Documenti collegati**: `Document::where('contract_id', …)` (relazione `documents()` già definita sul model) — render lista + evidenza scansione firmata.
- **Webhook (Fase 2, §9)**: route `POST /contracts/webhook/sign` con verifica HMAC → `signContract()`; eventuale `external_signature_id` come **unico** campo nuovo se la correlazione via `token` non basta.

---

## 11. Checklist di accettazione (Definition of Done del design)

- [x] **Timeline visiva proposta→inviato→firmato** con date e tappa corrente. — §3
- [x] **Mappa stati cliente ↔ enum codice** (`draft/sent/signed` + scaduto/annullato). — §2
- [x] **CTA contestuale unica per stato** sulla scheda contratto. — §3–§4
- [x] **Firma mock onesta = upload scansione** che allega `Document` *e* marca `signed` (atomico, con data). — §5
- [x] **Passi indietro/correzioni** (annulla invio, sblocca firma, annulla, riapri) confermati. — §6
- [x] Coperti i **casi reali**: proposta da iscrizione, in attesa, firmato (con/senza scansione), errori, scaduto, annullato, noleggio. — §7
- [x] **Punto d'innesto webhook firma digitale** documentato senza implementarlo (stato/date/Document invariati). — §9
- [x] Wireframe ASCII per timeline, show e modal firma + microcopy IT. — §3–§8
- [x] Impatti tecnici minimi: riuso `Contract`/`Document`/`ContractService`/route esistenti, nessuna nuova tabella per il mock. — §10
