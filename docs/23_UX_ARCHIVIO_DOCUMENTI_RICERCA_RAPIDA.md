# 23 — UX: Archivio documenti con ricerca rapida (R10 · Design UX)

> Attività Footility **#8530** — *R10 · Design UX — Archivio documenti con ricerca rapida* (progetto Gestionale Altramusica).
> Obiettivo: trovare **qualsiasi documento in pochi secondi** (filtri anno/tipo/studente in **1 click**),
> caricare in fretta (**drag-drop**, anche multiplo) e **generare** i documenti ricorrenti **dai dati** già nel gestionale.
> Deliverable: wireframe (ASCII) + percorsi click + casi reali.
> Continua [`20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md`](20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md) (R1) e [`21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md`](21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md) (R2).
> Mappa sull'area documentale AS-IS descritta in [`12_DOCUMENTI_E_MODELLI_CONTRATTI.md`](12_DOCUMENTI_E_MODELLI_CONTRATTI.md).

---

## 0. Principi di design

1. **Cercare prima di sfogliare.** La segreteria pensa "il consenso privacy di Mario di quest'anno", non "tabella documents, riga 412". L'archivio si apre **già filtrabile**: una barra di filtri rapidi sopra, la lista sotto.
2. **1 click = 1 filtro.** Anno, tipo e studente sono i tre assi reali. Devono essere **chip/scorciatoie cliccabili**, non solo menù a tendina da aprire-scegliere-invia. Click su un chip → lista ricaricata, niente form da sottomettere.
3. **Caricare non deve interrompere.** Trascino i file nella pagina (o sulla scheda studente) e sono dentro: **drag-drop**, anche **più file insieme**, con tipo/anno pre-compilati dal contesto.
4. **Quello che si può generare non si carica a mano.** Contratto, ricevuta privacy, consenso foto, contratto noleggio: sono **documenti che nascono dai dati** (studente, famiglia, iscrizione, anno). Un click "Genera" → bozza compilata → PDF archiviato e collegato.
5. **Il documento vive attaccato a qualcosa.** Ogni file è collegato a uno **studente** e/o **contratto** e a un **anno**; da lì lo si ritrova. L'archivio è una **vista** sullo stesso dato, vista "per documento" invece che "per persona".

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| Modello `Document` (`type`, `file_path`, `file_name`, `mime_type`, `size`, `student_id`, `contract_id`, `uploaded_by_user_id`) | ✅ esiste (`app/Models/Document.php` + migration `2025_12_11_093315`) |
| CRUD documenti (`DocumentController` index/create/store/show/edit/update/destroy) + `Route::resource('documents')` | ✅ esiste |
| Ricerca testo (nome file / tipo / nome-cognome studente) | ✅ esiste (`index`, `where + orWhereHas('student')`) |
| Filtri **tipo**, **studente**, **contratto** (select + submit) | ✅ esistono ma come **tendine da inviare**, non chip 1-click |
| Tipi documento (`contract`, `privacy`, `photo_consent`, `other`) | ✅ enum in migration + controller |
| **Filtro per ANNO scolastico** | ❌ **assente** — `documents` non ha `academic_year_id`; l'anno è desumibile solo via `contract.academic_year_id` |
| **Upload drag-drop** e **multi-file** | ❌ assente — form classico con `<input type=file>` singolo (`create.blade.php`) |
| **Generazione documento dai dati** (template → PDF compilato) | ❌ **assente** — oggi i modelli ODT stanno solo in `docs/materiale cliente/Contratti modelli/`, compilati a mano fuori dal gestionale |
| Anteprima/preview inline, conteggio per filtro, "documenti mancanti" per studente | ❌ assente |

> Conclusione: l'**archivio base esiste** (modello + CRUD + ricerca + 3 filtri), ma mancano i **tre punti dell'attività**: filtro **anno** in 1 click, **upload drag-drop**, e **generazione da template**. Questo design li copre, con un solo ritocco di schema (colonna `academic_year_id` + qualche campo, §9).

---

## 2. Mappa entità (in chiave UX)

```
        ┌────────────┐        ┌──────────────┐        ┌───────────────┐
        │  STUDENTE  │◄──────►│   DOCUMENT   │◄──────►│   CONTRATTO   │
        │            │ 1   N  │ type, file,  │ N   1  │ (academic_year)│
        └────────────┘        │ uploaded_by  │        └───────────────┘
                              │ + anno (new) │
                              └──────┬───────┘
                                     │ type ∈ {contract, privacy,
                                     │         photo_consent, other}
                                     ▼
                          ┌─────────────────────────┐
                          │  TEMPLATE (per type)     │  modelli ODT/HTML
                          │  → genera PDF dai dati    │  con segnaposto
                          └─────────────────────────┘
```

- **Archivio** = la collezione di `Document`, vista "per documento" e filtrabile.
- L'**anno** del documento: oggi implicito (via contratto o data caricamento); va reso **esplicito e filtrabile** (vedi §9).
- Il **template** non è una nuova entità complessa: è un modello (file o blade) **per tipo**, con segnaposto compilati dai dati di studente/contratto/anno.

---

## 3. Flusso 1 — Trovare un documento (ricerca rapida, filtri 1-click)

**Percorso click (happy path):**

```
Sidebar ▸ Documenti
   └─ ARCHIVIO DOCUMENTI  (si apre già con barra filtri rapidi)
        ├─ click chip "2025/26"   → lista filtrata per anno      (1 click)
        ├─ click chip "Privacy"   → + filtro tipo                (1 click)
        └─ digito "Rossi" nella ricerca → restringe a quello studente
             └─ click riga ▸ anteprima / 👁 Visualizza / ⬇ Scarica
```

**Wireframe — Archivio documenti:**

```
┌───────────────────────────────────────────────────────────────────────────┐
│  Documenti                                   [⤓ Trascina qui i file] [+ Carica]│
├───────────────────────────────────────────────────────────────────────────┤
│  🔍 [ cerca nome file o studente…            ]                              │
│  Anno:   [ 2025/26 ]  2024/25   2023/24   Tutti                             │
│  Tipo:   [ Tutti ] Contratto  Privacy  Consenso foto  Noleggio  Altro       │
│  Studente: [ tutti ▾ ]            ▸ filtri attivi:  2025/26 ✕   Privacy ✕    │
├───────────────────────────────────────────────────────────────────────────┤
│  📄 Privacy_Rossi_Mario.pdf    Privacy   Mario Rossi    25/26  12/09  👁 ⬇ ✕ │
│  📄 Contratto_A-0142.pdf       Contratto Mario Rossi    25/26  03/09  👁 ⬇ ✕ │
│  📄 ConsensoFoto_Rossi.pdf     Cons.foto Mario Rossi    25/26  03/09  👁 ⬇ ✕ │
│  …                                                                          │
│  47 documenti · 2025/26 · Privacy            ◂ 1 2 3 ▸                       │
└───────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- I valori **anno / tipo** sono **chip cliccabili** (toggle), non select-da-inviare: un click applica il filtro e ricarica la lista (querystring `?year=…&type=…`). Il chip attivo resta evidenziato; "filtri attivi" mostra le `✕` per rimuoverli singolarmente.
- **Studente** resta una tendina con ricerca (sono tanti) — ma compilabile anche **da contesto**: aprendo l'archivio dalla scheda studente, il filtro studente è **già impostato**.
- La **ricerca testo** rimane (nome file + nome/cognome studente, come già fa `DocumentController::index`), combinabile con i chip.
- Il **contatore** in fondo riflette i filtri attivi ("47 documenti · 2025/26 · Privacy") — feedback immediato che il filtro ha "morso".
- Default all'apertura: **anno corrente** (`AcademicYear::getCurrent()`), tipo "Tutti" — così la segreteria parte sempre dall'anno giusto.

---

## 4. Flusso 2 — Caricare in fretta (drag-drop, multi-file)

Due punti d'ingresso: dall'**archivio** e dalla **scheda studente** (più frequente: "carico la privacy firmata di Mario").

**Percorso click (dall'archivio):**

```
Archivio Documenti ▸ trascino 1+ file nella dropzone
   └─ MODAL "Carica documenti"  (compare con i file già in coda)
        ├─ per ogni file: Tipo [▾]   (default dedotto dal nome: "privacy"→Privacy)
        ├─ Studente [▾]  Anno [▾ 2025/26]   (precompilati se aperto da scheda studente)
        └─ [Carica tutti]  → barra di avanzamento → toast "3 documenti caricati"
```

**Wireframe — Modal upload drag-drop:**

```
┌──────────────────────── Carica documenti ────────────────────────────────┐
│                                                                           │
│   ┌─────────────────────────────────────────────────────────────────┐   │
│   │            ⤓  Trascina qui i file  (PDF, JPG, ODT…)               │   │
│   │                  oppure  [ sfoglia… ]                             │   │
│   └─────────────────────────────────────────────────────────────────┘   │
│                                                                           │
│   In coda:                                                                │
│   • Privacy_Rossi.pdf    Tipo [ Privacy ▾]   Studente [ Mario Rossi ▾] ✕ │
│   • contratto_42.pdf     Tipo [ Contratto▾]  Studente [ Mario Rossi ▾] ✕ │
│   Anno per tutti: [ 2025/26 ▾ ]                                           │
│                                                                           │
│                                     [ Annulla ]   [ Carica tutti (2) ]    │
└───────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- La **dropzone** accetta più file; ognuno diventa una **riga in coda** con tipo + studente editabili. Validazione lato server invariata (`file|max:20480`, tipi enum) — il drag-drop è solo un involucro UX sopra lo `store`.
- **Tipo dedotto** dal nome file quando possibile ("privacy"→Privacy, "contratto"→Contratto, "noleggio"→Noleggio), sempre correggibile.
- Aperto **dalla scheda studente**: studente e anno **pre-impostati e bloccati** sul contesto → upload in 2 gesti (trascina, conferma).
- Errori per-riga non bloccano gli altri file: il file ko resta in coda con messaggio, gli altri passano. Toast riassuntivo: "2 caricati, 1 da correggere".
- Nessun JS pesante richiesto: dropzone HTML5 nativa (`dragover`/`drop` + `DataTransfer`) che riempie un `<input type=file multiple>`; chi non trascina usa **[sfoglia]**.

---

## 5. Flusso 3 — Generare un documento dai dati (template)

Il punto che oggi manca del tutto: contratti, privacy, consenso foto e contratti di noleggio **nascono dai dati** già presenti, non si ricaricano a mano. Modelli reali di riferimento: `docs/materiale cliente/Contratti modelli/Modello contratto 25-26.odt` e `Contratto Noleggio Modello.odt`.

**Percorso click (dalla scheda studente / contratto):**

```
Scheda studente ▸ box Documenti ▸ [+ Genera documento]
   └─ MODAL "Genera da modello"
        ├─ Scelgo Tipo:  ( ) Contratto  ( ) Privacy  ( ) Consenso foto  ( ) Noleggio
        ├─ Anno [▾ 2025/26]  (e, per il contratto, l'iscrizione di riferimento)
        └─ [Anteprima]  → bozza compilata coi dati  → [Genera PDF e archivia]
             └─ documento creato (type, student, anno) e già nell'archivio
```

**Wireframe — Modal generazione:**

```
┌──────────────── Genera documento per Mario Rossi ────────────────────────┐
│                                                                           │
│  Modello:  ( ) Contratto corso   ( ) Privacy   ( ) Consenso foto          │
│            ( ) Contratto noleggio                                         │
│  Anno:     [ 2025/26 ▾ ]                                                  │
│  Riferimento (contratto): iscrizione Pianoforte 25/26  [▾]                │
│                                                                           │
│  ── Anteprima (dati precompilati) ───────────────────────────────────    │
│  Il sottoscritto  Anna Rossi  (genitore di  Mario Rossi , nato il …)      │
│  C.F. RSSMRA… — residente in …                                            │
│  per il corso  Pianoforte , a.s.  2025/26 , importo  € 720 …              │
│  …                                                                        │
│                                                                           │
│                          [ Annulla ]   [ Genera PDF e archivia ]          │
└───────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- I **segnaposto** del modello (nome, CF, genitore primario/fatturazione, corso, anno, importo) sono compilati riusando i dati già modellati: `Student`, famiglia (pivot `student_guardian`, §R1), `Enrollment`/`CourseOffering` (§R2), `AcademicYear`.
- Output: **PDF** salvato come `Document` con `type` coerente, collegato a studente/contratto/anno e marcato `generated` (vs `uploaded`) per distinguerlo nell'archivio.
- L'anteprima è **modificabile** prima di generare (campi liberi tipo note), ma i dati anagrafici vengono dal sistema (no re-digitazione).
- Per il **contratto** la generazione può agganciarsi al `Contract` esistente (ha già `terms`, `token`, `contract_number`): il documento generato diventa l'allegato firmabile.

---

## 6. Stati e casi limite

| Caso | Comportamento UX |
| --- | --- |
| **Documento senza studente** (es. modulistica generica) | Consentito (`student_id` nullable); compare con studente vuoto, filtrabile per tipo/anno. |
| **Anno non determinabile** | Se il file non ha contratto né anno esplicito, si usa l'anno di caricamento come default proposto, modificabile. |
| **File troppo grande / tipo non ammesso** | Bloccato lato server (`max:20480`) con messaggio per-riga nel modal upload; gli altri file proseguono. |
| **Generazione con dati mancanti** (es. CF assente) | Anteprima mostra i segnaposto vuoti evidenziati ("⚠ manca CF") con link alla scheda da completare; generazione consentita ma segnalata. |
| **Documento già presente per quel tipo/anno/studente** | Avviso non bloccante "Esiste già una Privacy 25/26 per Mario" con `[Sostituisci]` / `[Aggiungi comunque]`. |
| **Eliminazione** | `✕` elimina il `Document` e il file su disco (come già fa `destroy`); conferma esplicita. Per i generati: si rigenerano. |
| **Studente con privacy/consenso mancante** | (collegamento Dashboard) l'alert "anagrafiche incomplete" può linkare "documenti mancanti" → genera al volo. |

---

## 7. Microcopy (etichette IT)

- Voce sidebar / titolo: **"Documenti"** (archivio).
- Filtri chip: **Anno**, **Tipo** (Contratto · Privacy · Consenso foto · Noleggio · Altro), **Studente**.
- Pulsanti: **"+ Carica"**, **"Trascina qui i file"**, **"Carica tutti (N)"**, **"+ Genera documento"**, **"Genera PDF e archivia"**, **"Anteprima"**.
- Toast: *"3 documenti caricati."* · *"2 caricati, 1 da correggere."* · *"Documento generato e archiviato."*
- Conferme: *"Eliminare questo documento? Il file verrà rimosso."* · *"Esiste già una {tipo} {anno} per {studente}."*
- Badge origine: **Caricato** / **Generato**.

---

## 8. Impatti tecnici (per chi implementa — NON parte di questo R10)

Il design richiede **un solo ritocco di schema** e per il resto riusa quanto c'è:

- **Schema**: aggiungere a `documents` la colonna `academic_year_id` (nullable, FK) per il filtro anno in 1 click; opzionali `title`/`description` e un flag `source` (`uploaded`/`generated`). Estendere l'enum `type` con `rental` (noleggio) se si vuole separarlo da `other`.
- **Filtri 1-click**: in `DocumentController::index` aggiungere il filtro `year` (`where academic_year_id` o via `contract.academic_year_id`) e passare i conteggi; nella vista sostituire i `select` di anno/tipo con **chip** che impostano la querystring. La ricerca testo e i filtri studente/contratto restano.
- **Upload drag-drop**: dropzone HTML5 (no dipendenze pesanti) che alimenta un `<input type=file multiple>`; lato server un `storeMany` che cicla i file riusando la logica di `store`. Apertura contestuale dalla scheda studente con `student_id`/`year` pre-compilati.
- **Generazione da template**: un `DocumentTemplateService` che, dato `type` + entità (studente/iscrizione/anno), compila un modello (blade→PDF via dompdf, o conversione ODT) e salva un `Document` `generated`. Sorgenti modelli: `docs/materiale cliente/Contratti modelli/`. Aggancio al `Contract` esistente (`terms`, `token`, `contract_number`).
- Dati già disponibili: famiglia/genitore primario (pivot `student_guardian`, §R1), iscrizione/offerta/importo (§R2), `AcademicYear::getCurrent()`.

---

## 9. Checklist di accettazione (Definition of Done del design)

- [x] Definita la **ricerca rapida** con filtri **anno/tipo/studente in 1 click** (chip + ricerca testo + contatore). — §3
- [x] Definito l'**upload drag-drop**, anche **multi-file**, con tipo/anno dedotti e apertura contestuale dalla scheda studente. — §4
- [x] Definita la **generazione di documenti dai dati** (template per tipo → anteprima → PDF archiviato). — §5
- [x] Coperti **stati e casi limite** (senza studente, anno ignoto, file ko, dati mancanti, duplicati). — §6
- [x] Wireframe ASCII per ogni schermata/modal chiave + microcopy IT. — §3–§7
- [x] Indicati **impatti tecnici minimi** (1 colonna `academic_year_id` + riuso CRUD/`store` esistenti). — §8
- [x] Allineato allo stato reale del codice (`Document`, `DocumentController`, `documents.index`) e all'AS-IS documentale (`12_…`). — §1
