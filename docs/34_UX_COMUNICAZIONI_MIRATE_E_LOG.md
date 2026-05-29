# 34 — UX: Comunicazioni mirate + log invii (R9 · Design UX)

> Attività Footility **#8529** — *R9 · Design UX — Comunicazioni mirate + log* (progetto Gestionale Altramusica).
> Obiettivo: dare alla segreteria un **composer** per scrivere una comunicazione e **mirarla a un segmento**
> (per **corso**, **anno**, **stato**), inviarla via **email SMTP reale**, e per **SMS/WhatsApp** produrre una
> **bozza pronta con copia 1-click** da inviare a mano dal proprio telefono. Ogni invio finisce in un **log**
> per tracciabilità (chi, quando, a chi, su quale canale, esito).
> Deliverable: wireframe composer + selettore segmento con anteprima destinatari + flusso multi-canale
> (email reale / SMS-WhatsApp manuale) + vista log + casi reali + microcopy IT + impatti tecnici (1 tabella nuova).
> Base dati: contatti su **Guardian** (`email_1..3`, `cell_1..4`), segmenti da `CourseOffering`/`Enrollment`/`StudentYear`.
> Riusa il nucleo studente/famiglia di [`20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md`](20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md) (R1),
> le iscrizioni/corsi di [`21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md`](21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md) (R2),
> e rispetta i **consensi GDPR** introdotti con *Privacy #1* (`privacy_consent`).

---

## 0. Principi di design

1. **Prima il "a chi", poi il "cosa".** La comunicazione mirata nasce dal **segmento**: scegli corso/anno/stato e il sistema ti dice *subito quante famiglie* riceveranno e *su quali contatti*. Il messaggio si scrive dopo, sapendo già il pubblico. Niente invii al buio.
2. **Onestà sui canali.** L'**email parte davvero** (SMTP). **SMS e WhatsApp no**: la struttura non ha un gateway, quindi non fingiamo. Per questi canali produciamo una **bozza perfetta** + la **lista numeri** + un **[Copia]** per incollare nel telefono/WhatsApp Web. La UI lo dice chiaramente: *"questo testo lo invii tu, a mano"*.
3. **Il destinatario è la famiglia, non lo studente.** I contatti (mail, cellulari) stanno sui **genitori/tutori** (R1). Una comunicazione su Mario raggiunge i recapiti dei suoi guardian. Se uno studente ha due genitori con mail diverse, si decide *una volta* la regola (§5) e la si mostra.
4. **Il consenso comanda.** Chi non ha `privacy_consent` **non riceve** comunicazioni non strettamente operative. Il segmento mostra *quanti esclusi per mancato consenso*, in modo trasparente — non è un errore, è conformità (Privacy #1).
5. **Ogni invio lascia traccia.** Tutto ciò che esce (o viene marcato "inviato a mano") finisce nel **log**: data, operatore, segmento, canale, n. destinatari, esito. Serve per "*l'avevamo avvisata?*" e per non spammare due volte.
6. **Una tabella sola.** Il design aggiunge **un'unica tabella** (`communications` / log) + relazione ai destinatari. Tutto il resto (segmenti, contatti, consensi) **riusa** ciò che esiste. Niente CRM, niente sovra-ingegneria.

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| Contatti famiglia: `Guardian.email_1/2/3`, `cell_1..4`, `phone_home/work` | ✅ esiste |
| `Guardian.privacy_consent` (bool) + `StudentYear.privacy_consent`/`_at` | ✅ esiste (Privacy #1) |
| Segmenti: `CourseOffering` (per `course_id` + `academic_year_id`, `status`), `Enrollment.status` (`active/suspended/completed/cancelled`), `StudentYear.status` (`prospect/interested/enrolled/withdrawn`) | ✅ esiste |
| Relazione studente↔genitori (`student_guardian`) per risolvere i recapiti | ✅ esiste (R1) |
| Config mailer Laravel (`config/mail.php`, `.env MAIL_*`) | ⚠️ presente ma `MAIL_MAILER=log` (non invia davvero) |
| **Composer comunicazione** (oggetto + corpo, anteprima) | ❌ **assente** |
| **Selettore segmento** (corso/anno/stato) con **conteggio destinatari live** | ❌ assente |
| **Invio email SMTP reale** a un segmento | ❌ assente (nessuna classe `Mailable`/`Notification`, cartella `app/Mail` vuota) |
| **Bozza SMS/WhatsApp + copia testo + lista numeri** | ❌ assente |
| **Log invii** (chi/quando/segmento/canale/esito) | ❌ assente — nessuna tabella `communications` |
| **Rispetto consenso in fase di invio** (escludi chi non ha `privacy_consent`) | ❌ assente (il dato c'è, ma nessun flusso lo applica) |

> Conclusione: i **dati** ci sono tutti (contatti, segmenti, consensi, relazioni). Manca **tutto il flusso di comunicazione**: comporre, mirare, inviare (email) / preparare-bozza (SMS/WA), e **loggare**. È quindi una funzione nuova, ma poggiata su entità esistenti, con **una sola tabella aggiunta** (§10).

---

## 2. Le tre dimensioni del segmento (linguaggio cliente ↔ dati)

Il segmento è l'**AND** di filtri opzionali. Lasciandone uno vuoto = "tutti".

| Filtro (cliente) | Da dove | Valori |
| --- | --- | --- |
| **Corso** | `CourseOffering.course_id` (catalogo `Course`/`CourseType`) | es. Pianoforte, Chitarra, Canto… (multi-select) |
| **Anno** | `AcademicYear` (via `CourseOffering`/`StudentYear`/`Enrollment`) | es. 2025/26 (default = anno corrente) |
| **Stato** | `Enrollment.status` **oppure** `StudentYear.status` | iscrizione: *Attivo / Sospeso / Completato / Annullato* · anagrafica anno: *Prospect / Interessato / Iscritto / Ritirato* |

> "**Stato**" ha due nature nel dato: lo stato **dell'iscrizione a un corso** e lo stato **dell'anagrafica nell'anno**. La UI le tiene distinte con un toggle (*"Stato iscrizione"* / *"Stato anagrafica"*), perché "Ritirato" (anagrafica) ≠ "Annullato" (iscrizione). Default sensato: **Stato iscrizione = Attivo** (i destinatari più comuni: chi frequenta).

**Esempi di segmento reale:**
- *"Tutti i genitori degli iscritti attivi a **Pianoforte**, anno **2025/26**"* → reminder saggio.
- *"Anagrafiche **Interessate** quest'anno non ancora iscritte"* → campagna iscrizioni.
- *"Iscritti **Sospesi**"* → recupero/solleciti.

---

## 3. Vista composer — il deliverable centrale

Pagina `admin/communications/create`: a sinistra **chi** (segmento + anteprima), a destra **cosa** (canale + messaggio). Il conteggio destinatari si aggiorna **live** mentre stringi i filtri.

```
┌───────────────────────────────────────────────────────────────────────────────┐
│  ‹ Comunicazioni      Nuova comunicazione                                       │
├──────────────────────────────┬────────────────────────────────────────────────┤
│  1 · A CHI (segmento)         │  2 · COSA (messaggio)                           │
│                               │                                                 │
│  Anno      [ 2025/26  ▾]      │  Canale   (•) Email   ( ) SMS   ( ) WhatsApp    │
│  Corso     [ Pianoforte ✕]    │           └ Email: invio reale via SMTP         │
│            [ + aggiungi corso]│                                                 │
│  Stato     (•) Iscrizione     │  Oggetto  [ Saggio di fine anno — info ______ ] │
│            ( ) Anagrafica     │                                                 │
│            [✓ Attivo]         │  Corpo                                          │
│            [ ] Sospeso        │  ┌───────────────────────────────────────────┐ │
│            [ ] Completato     │  │ Gentile famiglia di {{studente}},         │ │
│            [ ] Annullato      │  │ vi invitiamo al saggio del {{data}}…      │ │
│                               │  │                                           │ │
│  ── Anteprima destinatari ──  │  │ Segreteria L'Altramusica                  │ │
│  📬  38 famiglie raggiungibili│  └───────────────────────────────────────────┘ │
│  👥  41 studenti nel segmento │  Campi: {{studente}} {{genitore}} {{corso}}     │
│  ✉️  38 email valide          │        {{anno}} {{data}}   [inserisci ▾]        │
│  ⚠️  3 senza consenso (esclusi)│                                                │
│  ⚠️  2 senza email            │  [ Anteprima ]            [ Invia email ▸ ]     │
│  [ vedi elenco destinatari ]  │                                                 │
└──────────────────────────────┴────────────────────────────────────────────────┘
```

**Regole UX:**
- **Conteggio live** sotto i filtri: *famiglie raggiungibili* (= con contatto valido **e** consenso), *studenti nel segmento*, *email/cellulari validi*, e le **esclusioni trasparenti** (senza consenso, senza recapito). Il numero che conta per il bottone è *raggiungibili*.
- **CTA dipende dal canale** (§4): Email → **[Invia email]**; SMS/WhatsApp → **[Prepara messaggi da inviare]**.
- **Placeholder/merge fields** semplici (`{{studente}}`, `{{genitore}}`, `{{corso}}`, `{{anno}}`, `{{data}}`) inseribili da menu; resi per-destinatario.
- **[vedi elenco destinatari]** apre un drawer con la lista risolta (nome studente → genitore → contatto usato), inclusi gli **esclusi col motivo**, prima di inviare (§5).

---

## 4. I tre canali — comportamento onesto

```
 EMAIL                         SMS                          WHATSAPP
 ───────────────────           ───────────────────          ───────────────────
 ✉️  Invio REALE via SMTP      📱 Bozza + lista numeri       💬 Bozza + lista numeri
 Parte dal gestionale.         NON parte dal gestionale.    NON parte dal gestionale.
 Log = esito per indirizzo.    Tu copi e invii a mano.      [Copia testo] + [Apri WA Web]
                               [Copia testo][Copia numeri]   Log = "marcato inviato a mano"
```

| Canale | Cosa fa il sistema | Cosa fa l'operatrice | Log |
| --- | --- | --- | --- |
| **Email** | Invia davvero (SMTP, §10), 1 mail per destinatario con merge fields risolti | Niente: clicca **[Invia email]**, attende l'esito | Esito **per indirizzo** (inviata/errore) |
| **SMS** | Genera il **testo finale** (un'unica versione, niente merge per-numero salvo nome) + **lista numeri** in formato copiabile | **[Copia testo]** → incolla nell'app SMS · **[Copia numeri]** → incolla nei destinatari | Comunicazione registrata; destinatari **"da inviare a mano"**, poi **[Segna come inviati]** |
| **WhatsApp** | Come SMS + scorciatoia **[Apri WhatsApp Web]** (`https://wa.me/<numero>?text=<testo>`) per il singolo, o copia massiva | Copia/incolla o usa il link per-contatto | Come SMS |

**Regole UX:**
- **Email = unico canale "automatico".** Il bottone primario per email è *"Invia email"*; per SMS/WhatsApp è *"Prepara messaggi"* (non promette un invio che non avviene).
- **Per SMS/WA il testo è uno solo** (niente `{{studente}}` per-numero, perché si copia in blocco): consentiti solo placeholder che restano generici, o l'operatrice manda 1-a-1 col link `wa.me` se vuole personalizzare.
- **Numeri in formato pulito** (E.164 quando possibile, es. `+39…`) per incolla diretto in WhatsApp; se un cellulare manca/è malformato → finisce negli **esclusi** con motivo.
- **Nessun finto "Inviato"**: per SMS/WA lo stato resta *"preparato — da inviare a mano"* finché l'operatrice non clicca **[Segna come inviati]** (onestà del log).

---

## 5. Risoluzione destinatari e consenso (la parte delicata)

Da *segmento* a *lista di contatti reali*: il passaggio dove si applicano regole e consenso. Mostrato nel **drawer destinatari** prima dell'invio.

```
┌──────── Destinatari · "Pianoforte · 2025/26 · Iscritti attivi" ───────────────┐
│  Includi:  ✉️ Email  →  38 raggiungibili / 43 nel segmento                    │
│                                                                               │
│  ✓ Mario Rossi      → Anna Rossi      anna.rossi@mail.it      (email_1)        │
│  ✓ Luca Bianchi     → Paolo Bianchi   p.bianchi@mail.it       (email_1)        │
│  ✓ Sara Verdi       → 2 genitori      2 indirizzi             (entrambi)       │
│  ⚠ Gaia Neri        → —               nessun consenso         [escluso]        │
│  ⚠ Tom Blu          → Lia Blu         (nessuna email)         [escluso]        │
│                                                                               │
│  Regola contatti:  (•) Tutti i genitori   ( ) Solo contatto primario          │
│  [ Esporta CSV ]                                          [ Chiudi ]          │
└───────────────────────────────────────────────────────────────────────────────┘
```

**Regole di risoluzione:**
- **Segmento → studenti → genitori → contatti.** Per ogni studente nel segmento si prendono i guardian collegati (`student_guardian`).
- **Quale recapito:** email → `email_1` (primario) e, se "Tutti i genitori", anche `email_2/3` e gli altri genitori; SMS/WA → `cell_1` primario. Toggle **"Tutti i genitori / Solo primario"** (default: **Tutti**, per le info famiglia).
- **Consenso:** se `Guardian.privacy_consent = false` → **escluso** con motivo *"nessun consenso"* (a meno che la comunicazione sia marcata **operativa/di servizio**, vedi sotto).
- **Dedup:** stesso indirizzo per più studenti (fratelli) → **una sola** email per indirizzo, con eventuale merge "famiglie multiple" (no doppioni).
- **Operativa vs promozionale:** flag *"Comunicazione di servizio"* (es. variazione orario, chiusura) → bypassa il filtro consenso marketing ma resta loggata; default = **promozionale** (consenso richiesto). Distinzione coerente con GDPR (Privacy #1).

---

## 6. Conferma invio email (reale) e barra di avanzamento

L'email parte davvero: la conferma è esplicita perché è irreversibile.

```
┌────────── Confermi l'invio? ───────────────────────────────────────────┐
│  Stai per inviare un'EMAIL REALE a 38 famiglie.                         │
│  Oggetto:  Saggio di fine anno — info                                   │
│  Esclusi:  3 senza consenso · 2 senza email  (non riceveranno)          │
│                                                                         │
│            [ Annulla ]                 [ Sì, invia ora a 38 ▸ ]         │
└─────────────────────────────────────────────────────────────────────────┘
        ↓ (in invio)
   Invio in corso…  ▓▓▓▓▓▓▓▓░░  31/38      (puoi lasciare la pagina)
        ↓ (fine)
   ✅ 36 inviate · ⚠️ 2 errori (indirizzo non valido) — vedi log #128
```

**Regole UX:**
- **Conteggio nel bottone** (*"Sì, invia ora a 38"*): chi clicca sa *quanti* partono.
- **Invio in coda** (queue) se il segmento è grande: la pagina non resta bloccata; l'esito si consulta nel log (§7). Per piccoli numeri, sincrono con barra.
- **Esito per indirizzo**: inviata / errore (bounce, indirizzo non valido) registrato sul singolo destinatario nel log.

---

## 7. Log invii — la tracciabilità

Pagina `admin/communications` (index): storico di tutte le comunicazioni, filtrabile. Ogni riga è **una comunicazione** (un invio a un segmento); il dettaglio mostra i **destinatari** e gli esiti.

```
┌───────────────────────────────────────────────────────────────────────────────┐
│  Comunicazioni                                   [ + Nuova comunicazione ]      │
│  Filtri:  Canale [tutti ▾]  Anno [2025/26 ▾]  Periodo [ultimi 30gg ▾]  [🔍]     │
├───────────────────────────────────────────────────────────────────────────────┤
│  Data        Canale  Oggetto / Segmento                  Dest.  Esito  Da       │
│  24/05 16:10 ✉️Email Saggio fine anno · Pianoforte 25/26   38   36✅2⚠  Anna S.  │
│  20/05 09:30 💬WA    Chiusura ponte · Tutti attivi 25/26   112  inviati⁎ Anna S.  │
│  18/05 14:02 📱SMS   Sollecito rata · Sospesi 25/26         9   da inviare Paola │
│  12/05 11:20 ✉️Email Open day · Interessati 25/26          54   54✅     Anna S.  │
│  ⁎ "inviato a mano" — marcato dall'operatore (SMS/WhatsApp)                      │
└───────────────────────────────────────────────────────────────────────────────┘
```

**Dettaglio comunicazione (drawer/show):**
```
┌────── Comunicazione #128 · ✉️ Email · 24/05/2026 16:10 ───────────────────────┐
│  Oggetto   Saggio di fine anno — info                                         │
│  Segmento  Corso=Pianoforte · Anno=2025/26 · Stato iscrizione=Attivo          │
│  Inviata da Anna Segreteria      Canale Email (SMTP)                           │
│  Esito     36 inviate · 2 errori · 3 esclusi (consenso) · 2 esclusi (no mail)  │
│  Corpo     [mostra testo inviato]                                             │
│  ── Destinatari ──────────────────────────────────────────────────────────   │
│  ✅ anna.rossi@mail.it      Mario Rossi        inviata 16:10                   │
│  ⚠️ vecchio@no.it           Tom Blu            errore: indirizzo non valido    │
│  …                                                              [ Esporta CSV ]│
└───────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Una riga = una comunicazione**; il **segmento usato è salvato** (snapshot leggibile), così a distanza di mesi si sa *a chi era diretta*.
- **Esito sintetico** in lista (inviate/errori/esclusi), **dettaglio per-destinatario** nel drawer.
- **SMS/WhatsApp**: stato *"da inviare"* → **[Segna come inviati]** (l'operatrice conferma di averli mandati) → diventa *"inviati a mano"*. Niente finto-automatismo.
- **Riusa testo**: dal dettaglio, **[Duplica]** ricrea una comunicazione col medesimo testo/segmento (es. reminder ricorrenti).

---

## 8. Microcopy (etichette IT)

- Sezioni composer: **"A chi (segmento)" · "Cosa (messaggio)"**.
- Filtri segmento: **"Anno" · "Corso" · "Stato iscrizione" / "Stato anagrafica"**.
- Anteprima: *"38 famiglie raggiungibili"*, *"3 senza consenso (esclusi)"*, *"2 senza email"*, **"vedi elenco destinatari"**.
- Canali: **"Email — invio reale via SMTP"**, **"SMS — bozza da inviare a mano"**, **"WhatsApp — bozza da inviare a mano"**.
- CTA: **"Invia email"** (email) · **"Prepara messaggi da inviare"** (SMS/WA) · **"Copia testo" · "Copia numeri" · "Apri WhatsApp Web"**.
- Conferma email: *"Stai per inviare un'EMAIL REALE a 38 famiglie."* · **"Sì, invia ora a 38"**.
- Esito: *"36 inviate · 2 errori (indirizzo non valido)"*.
- Log: **"Comunicazioni"**, colonne *"Dest." / "Esito" / "Da"*, stati **"da inviare" / "inviati a mano" / "inviata" / "errore"**, **"Segna come inviati" · "Duplica" · "Esporta CSV"**.
- Consenso: flag **"Comunicazione di servizio (operativa)"** con hint *"ignora il consenso marketing — usa solo per avvisi necessari"*.

---

## 9. Innesto futuro — gateway SMS/WhatsApp (NON parte di R9)

Il design lascia la porta aperta a un gateway reale (Twilio/Vonage per SMS, WhatsApp Business API) **senza ridisegnare nulla**: cambia solo *chi* manda i messaggi non-email.

```
   OGGI (manuale)                          DOMANI (gateway)
   ────────────────────                    ────────────────────────
   [Prepara messaggi] → bozza + numeri      [Invia SMS/WhatsApp] → API gateway
   Operatrice copia/incolla                 Sistema invia, riceve delivery receipt
   [Segna come inviati]                      stato per-destinatario automatico
            │                                        │
            ▼                                        ▼
   log: "inviati a mano"                    log: "inviata/consegnata/errore" (come email)
```

**Punti d'innesto già predisposti dal design:**
- **Modello log identico**: i destinatari hanno già `channel` + `status` + `error` → il gateway popola gli stessi campi che oggi popola l'email; gli SMS/WA passano da *"da inviare"* a *"inviata/consegnata"* automaticamente.
- **CTA condizionata al canale** (§4): basta sostituire *"Prepara messaggi"* con *"Invia"* quando il gateway è configurato.
- **Numeri già normalizzati** (E.164, §4) → pronti per l'API.
- **Webhook delivery**: endpoint futuro che aggiorna lo stato del destinatario (come i bounce email).

> R9 si ferma al **manuale** per SMS/WhatsApp. Il gateway è documentato come direzione, non implementato.

---

## 10. Impatti tecnici (per chi implementa — NON parte di questo R9)

Il design **riusa contatti, segmenti, consensi e relazioni esistenti**; aggiunge **una sola tabella** (+ tabella destinatari) e usa il mailer Laravel già configurato.

- **Nuova tabella `communications`** (il log): `id`, `subject`, `body`, `channel` (`email|sms|whatsapp`), `is_operational` (bool, bypass consenso), `segment` (JSON: corso/anno/stato — snapshot leggibile), `sent_by_user_id`, `sent_at`, contatori esito (`total/sent/failed/excluded`), `status` (`draft|queued|sent|manual_pending|manual_done`).
- **Nuova tabella `communication_recipients`**: `communication_id`, `student_id`, `guardian_id`, `contact` (email/numero usato), `channel`, `status` (`pending|sent|failed|manual`), `error` (nullable), `sent_at`. Dà l'esito **per-destinatario** (§7) e il pre-aggancio al futuro gateway (§9).
- **Email reale**: classe `Mailable` (es. `CommunicationMail`) con render dei merge field per-destinatario; invio in **queue** (`ShouldQueue`) per segmenti grandi (§6). Richiede **`MAIL_MAILER=smtp`** + credenziali in `.env`/`config/mail.php` (oggi `MAIL_MAILER=log`); il `from` può attingere a `Setting` per nome/indirizzo mittente.
- **Risoluzione segmento** (service, es. `CommunicationService::resolveRecipients($segment)`): query su `CourseOffering`→`Enrollment`/`StudentYear` per stato/anno/corso → `student_guardian` → `Guardian` contatti; applica regola "tutti i genitori / solo primario", **filtro consenso** (`privacy_consent`, salvo `is_operational`), **dedup** per indirizzo/numero, validazione recapito → ritorna inclusi + esclusi-con-motivo (alimenta l'anteprima §3 e il drawer §5).
- **Conteggio live** (§3): endpoint AJAX che chiama `resolveRecipients` e ritorna i contatori, senza inviare.
- **SMS/WhatsApp manuale**: nessun invio server; la view genera **testo finale** + **lista numeri normalizzati** (E.164) + link `https://wa.me/<num>?text=<urlencoded>`; `[Segna come inviati]` aggiorna `status` del log a `manual_done`.
- **Normalizzazione numeri**: helper per `cell_*` → E.164 (`+39…`); numeri non validi → esclusi.
- **Permessi**: azione riservata a ruoli `admin`/`segreteria` (ACL Spatie già presente); `sent_by_user_id` dal `auth()->user()`.
- **Route**: `GET admin/communications` (log/index), `GET admin/communications/create` (composer), `POST admin/communications/recipients-preview` (conteggio AJAX), `POST admin/communications` (invia email / prepara bozza), `POST admin/communications/{c}/mark-sent` (SMS/WA → inviati a mano), `GET admin/communications/{c}` (dettaglio).
- **Gateway (Fase 2, §9)**: driver SMS/WhatsApp che riempie gli stessi campi `communication_recipients` + webhook delivery; nessun cambio di schema.

---

## 11. Checklist di accettazione (Definition of Done del design)

- [x] **Composer** oggetto+corpo con **merge field** e anteprima. — §3
- [x] **Selettore segmento** per **corso / anno / stato** (iscrizione *o* anagrafica) con **conteggio destinatari live** ed **esclusioni trasparenti**. — §2–§3
- [x] **Email SMTP reale** con conferma esplicita, invio in coda, **esito per indirizzo**. — §4, §6, §10
- [x] **SMS/WhatsApp in bozza** + **[Copia testo]/[Copia numeri]/[Apri WhatsApp Web]** + **[Segna come inviati]** (nessun finto invio). — §4
- [x] **Risoluzione destinatari** famiglia (genitori), regola "tutti/primario", **dedup fratelli**, **filtro consenso GDPR** con flag *operativa*. — §5
- [x] **Log invii** completo (chi/quando/segmento snapshot/canale/esito) con dettaglio per-destinatario e **Duplica**. — §7
- [x] **Punto d'innesto gateway SMS/WhatsApp** documentato senza implementarlo (stessi campi log/recipient). — §9
- [x] Wireframe ASCII per composer, canali, drawer destinatari, conferma invio e log + microcopy IT. — §3–§8
- [x] Impatti tecnici minimi: riuso `Guardian`/`CourseOffering`/`Enrollment`/`StudentYear`/ACL + mailer Laravel; **una sola tabella nuova** (+ destinatari). — §10
