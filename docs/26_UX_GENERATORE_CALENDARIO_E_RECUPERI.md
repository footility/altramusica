# 26 — UX: Generatore calendario e recuperi (R7 · Design UX)

> Attività Footility **#8527** — *R7 · Design UX — Generatore calendario e recuperi* (progetto Gestionale Altramusica).
> Obiettivo: trasformare la creazione del calendario annuale da "genera tutto l'anno per giorni della settimana"
> in un **generatore guidato a cicli di 11 settimane + sospensioni con anteprima**, e rendere
> **recupero/spostamento di una lezione un'azione da 2 click** sulla lezione stessa — non un percorso CRUD.
> Deliverable: wireframe (ASCII) del wizard a cicli, della preview, e del flusso recupero/spostamento; casi reali; microcopy IT; default editabili.
> Base AS-IS: [`09_CALENDARIO_ANNUALE.md`](09_CALENDARIO_ANNUALE.md) (cicli `1°/2°/3° ciclo 11 sett` + `lezioni libere` dal `Calendario 2025-26.ods`).
> Riusa le offerte/iscrizioni di [`21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md`](21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md) (R2) e i docenti di [`08_DOCENTI_LAVORATORI.md`](08_DOCENTI_LAVORATORI.md).

---

## 0. Principi di design

1. **L'anno si pensa a cicli, non a settimane sciolte.** La scuola ragiona per *"1° ciclo, 2° ciclo, 3° ciclo, 11 settimane ciascuno"* (AS-IS ODS). Il generatore parte da **3 cicli da 11 settimane** come default editabile, non da una griglia anonima lunedì-venerdì.
2. **Niente generazione al buio: prima l'anteprima.** Oggi `[Genera]` scrive subito in DB. Il nuovo flusso è **wizard → preview → conferma**: chi genera *vede* quante lezioni escono, dove cadono i ponti, quante settimane reali restano per ciclo, **prima** di toccare il calendario.
3. **Le sospensioni sono parte della generazione, non un secondo passo.** Natale, Pasqua, ponti: si dichiarano **dentro il wizard** e la preview le sottrae già dal conteggio. Le sospensioni puntuali successive restano possibili (come oggi), ma il caso comune si chiude in un colpo.
4. **Recuperare/spostare una lezione è un gesto, non una pratica.** Da una lezione (sul calendario o in lista): **click lezione → [Sposta/Recupera] → scegli nuova data → fatto**. 2 click + scelta data. Niente "elimina e ricrea", niente perdita dello storico.
5. **Default sensati, tutti editabili.** Il wizard precompila: 3 cicli × 11 settimane, giorni di lezione = quelli dell'anno, sospensioni tipiche italiane (Natale/Pasqua). Ogni numero e ogni data è **modificabile** prima della preview. Mock con numeri reali, mai campi vuoti.
6. **Lo storico non si perde.** Una lezione spostata mantiene riferimento all'originale (data prevista → data effettiva), così "recupero" è leggibile e tracciabile, non una cancellazione silenziosa.

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| Calendario base giorni-lezione (`CalendarLesson`: `date`, `day_of_week`, `is_active`) | ✅ esiste |
| Generazione anno (`CalendarService::generateLessonsForYear`) | ✅ esiste, ma **genera tutto l'anno per giorni della settimana**, scrive subito in DB, **nessun concetto di ciclo** |
| Sospensioni (`CalendarSuspension`: `name`, `start_date`, `end_date`) + `applySuspension` | ✅ esiste, disattiva i giorni nel range; **passo separato dalla generazione** |
| Lezioni effettive (`Lesson`: `course_offering_id`, `teacher_id`, `substitute_teacher_id`, `date`, `time_start/end`, `completed`) | ✅ esiste |
| Calendario lezioni (`LessonCalendarController` + FullCalendar) con filtri docente/corso/aula | ✅ esiste (sola visualizzazione + colori stato) |
| **Cicli da 11 settimane** (1°/2°/3° ciclo) come struttura di generazione | ❌ **assente** nel codice (presente solo nell'ODS AS-IS) |
| **Wizard guidato con anteprima prima del commit** | ❌ oggi `CalendarController::generate` scrive direttamente, redirect con "Generati N giorni" |
| **Sospensioni dentro il flusso di generazione** | ⚠️ esistono ma vanno create a parte (`create-suspension`), poi ri-generare |
| **Spostamento/recupero di una `Lesson`** (cambio data conservando lo storico) | ❌ assente — non c'è azione né campo "data originale"/stato spostata; si potrebbe solo editare/cancellare |
| **"Lezioni libere"** (campo AS-IS) come slot recupero | ⚠️ concetto presente nell'ODS, non modellato |

> Conclusione: il **motore base c'è** (giorni-lezione, sospensioni, lezioni effettive, calendario FullCalendar). Mancano: (a) la **struttura a cicli** come lingua del generatore, (b) l'**anteprima prima del commit**, (c) il **gesto di recupero/spostamento** sulla lezione. Questo design non inventa entità pesanti: aggiunge un **wizard sopra il `CalendarService`** e un'**azione "sposta"** sulla `Lesson` (vedi §9).

---

## 2. Architettura della vista (in chiave UX)

```
                    CALENDARIO ANNUALE  [Anno: 2025/26 ▾]
  ┌──────────────────────────────────────────────────────────────────────────┐
  │  [⚙ Genera calendario]  ← apre il WIZARD a cicli (§3) → PREVIEW (§5)        │
  ├──────────────────────────────────────────────────────────────────────────┤
  │  GRIGLIA CICLI    1° ciclo · 2° ciclo · 3° ciclo (11 sett. cad.) + ponti   │
  │     ↳ sospensioni evidenziate, settimane reali per ciclo                   │
  ├──────────────────────────────────────────────────────────────────────────┤
  │  CALENDARIO LEZIONI (FullCalendar)   lezioni effettive per docente/corso   │
  │     ↳ click su lezione → [Sposta/Recupera]  azione 2 click (§6)            │
  └──────────────────────────────────────────────────────────────────────────┘
```

- **Due livelli**: il **generatore** lavora sui giorni-lezione (`CalendarLesson`, struttura dell'anno); il **recupero/spostamento** lavora sulle lezioni effettive (`Lesson`, didattica reale di un corso).
- **Sorgenti dati**: cicli = derivati da `AcademicYear.start_date` + N cicli × N settimane − sospensioni; preview = calcolo in memoria (nessuna scrittura finché non si conferma).

---

## 3. Generatore guidato — Wizard a cicli (il cuore di R7)

**Percorso click:**

```
Calendario annuale ▸ [⚙ Genera calendario]
   └─ WIZARD (3 step) → PREVIEW → [Conferma e genera]
```

**Step 1 — Struttura a cicli (default editabili):**

```
┌── Genera calendario · 2025/26 ─────────────────── Step 1/3 · Cicli ───────┐
│                                                                            │
│  Inizio anno   [ 15/09/2025 ]   (default = AcademicYear.start_date)        │
│                                                                            │
│  CICLI                                            [+ Aggiungi ciclo]       │
│   1° ciclo   settimane [ 11 ]   inizia [ 15/09/2025 ]   (auto)            │
│   2° ciclo   settimane [ 11 ]   inizia [ 08/12/2025 ]   (auto, dopo 1°)   │
│   3° ciclo   settimane [ 11 ]   inizia [ 02/03/2026 ]   (auto, dopo 2°)   │
│                                                                            │
│  Giorni di lezione                                                         │
│   ☑ Lun  ☑ Mar  ☑ Mer  ☑ Gio  ☑ Ven  ☐ Sab  ☐ Dom                        │
│                                                                            │
│  ℹ 3 cicli × 11 sett = 33 settimane di lezione (prima delle sospensioni)  │
│                                                                            │
│                                          [ Annulla ]   [ Avanti › ]        │
└────────────────────────────────────────────────────────────────────────────┘
```

**Step 2 — Sospensioni (dentro il flusso):**

```
┌── Genera calendario · 2025/26 ──────────────── Step 2/3 · Sospensioni ────┐
│                                                                            │
│  Sospensioni precaricate (modificabili)           [+ Aggiungi sospens.]   │
│   ☑ Vacanze di Natale    [ 23/12/2025 ] → [ 06/01/2026 ]   [✎] [✕]        │
│   ☑ Vacanze di Pasqua    [ 02/04/2026 ] → [ 07/04/2026 ]   [✎] [✕]        │
│   ☐ Ponte 1° novembre    [ 01/11/2025 ] → [ 01/11/2025 ]   [✎] [✕]        │
│                                                                            │
│  ℹ Le sospensioni spente (☐) non vengono applicate                        │
│                                                                            │
│                                  [ ‹ Indietro ]   [ Avanti › ]            │
└────────────────────────────────────────────────────────────────────────────┘
```

**Step 3 = la Preview** (§5).

**Regole UX:**
- **Default a 3 cicli × 11 settimane** (AS-IS); inizio del 1° ciclo = `start_date` dell'anno, gli altri cicli si **incatenano** automaticamente (inizio = fine del precedente), ma ogni data è editabile.
- **[+ Aggiungi ciclo]** per scuole con 4 cicli o periodi extra; rimuovibile. Le **"lezioni libere"** dell'ODS = ciclo/slot opzionale non vincolato alle 11 settimane (vedi §7).
- **Giorni di lezione** precompilati lun-ven; il sabato è frequente per la musica → un click per aggiungerlo.
- **Sospensioni precaricate** con i casi italiani tipici (Natale/Pasqua) + ponti come **toggle**: chi non li vuole li spegne, non li ridigita.

---

## 4. Sospensioni — coerenza con l'esistente

Le sospensioni del wizard creano/aggiornano `CalendarSuspension` e, in fase di generazione, **disattivano** i giorni-lezione nel range (riuso di `applySuspension`). Dopo la generazione restano gestibili dalla pagina calendario (aggiunta/rimozione puntuale), come oggi.

- **Una sospensione = un periodo nominato** (`name`, `start_date`, `end_date`): leggibile sul calendario (banda gialla) e nel conteggio.
- **Sospensione che taglia un ciclo**: la preview mostra le **settimane reali** del ciclo al netto della sospensione (es. *"2° ciclo: 11 previste → 9 effettive (2 sett. in vacanze di Natale)"*).
- **Modifica dopo generazione**: aggiungere una sospensione su un anno già generato spegne i giorni nel range e **segnala le lezioni effettive impattate** (suggerendo il recupero, §6) invece di cancellarle.

---

## 5. Preview — vedere prima di scrivere (Step 3)

Niente generazione finché non si vede il risultato.

```
┌── Genera calendario · 2025/26 ──────────────────── Step 3/3 · Anteprima ──┐
│                                                                            │
│  RIEPILOGO                                                                 │
│   Periodo        15/09/2025 → 31/05/2026                                  │
│   Giorni lezione Lun Mar Mer Gio Ven                                       │
│   Sospensioni    Natale (23/12→06/01) · Pasqua (02/04→07/04)              │
│                                                                            │
│  SETTIMANE REALI PER CICLO                                                 │
│   1° ciclo   15/09 → 30/11     11 sett → 11 effettive                     │
│   2° ciclo   01/12 → 22/02     11 sett → 9 effettive  (−2 Natale)         │
│   3° ciclo   23/02 → 17/05     11 sett → 10 effettive (−1 Pasqua)         │
│   ─────────────────────────────────────────────────────────────────     │
│   Totale                       33 previste → 30 settimane effettive       │
│                                                                            │
│  GIORNI DI LEZIONE GENERATI                                                │
│   148 giorni attivi · 11 giorni disattivati (sospensioni)                 │
│                                                                            │
│  ⚠ Attenzione: esiste già un calendario per 2025/26 (142 giorni).         │
│     ◉ Sovrascrivi   ○ Aggiungi solo i mancanti                            │
│                                                                            │
│                       [ ‹ Indietro ]   [ Conferma e genera ]              │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Tutto calcolato in memoria**: la preview non scrive nulla. `[Conferma e genera]` è l'unico punto che tocca il DB.
- **Settimane previste → effettive** per ciclo: il numero che conta per la scuola (e per il prezzo a lezione, R2) è quello *al netto delle sospensioni*.
- **Calendario già esistente**: scelta esplicita **Sovrascrivi / Aggiungi mancanti** (oggi `updateOrCreate` sovrascrive in silenzio). Mai distruggere dati senza dirlo.
- Dopo conferma: toast *"Calendario 2025/26 generato: 30 settimane effettive, 148 giorni di lezione."* e ritorno alla griglia cicli.

---

## 6. Recupero / spostamento lezione — azione da 2 click

Da una lezione effettiva (`Lesson`), sul calendario o in lista: **click → [Sposta/Recupera] → nuova data → fatto**.

**Percorso click (happy path):**

```
Calendario lezioni ▸ click su "Pianoforte · Rossi (A2)" del 18/12
   └─ POPOVER lezione → [Sposta / Recupera]
        └─ MODAL "Sposta lezione" → scegli nuova data → [Conferma]
             → lezione spostata, originale tracciata, calendario aggiornato
```

**Wireframe — Popover sulla lezione (click 1):**

```
┌─ Pianoforte · M. Rossi · Aula A2 ─────────────┐
│  Gio 18/12/2025 · 15:00–15:45                 │
│  Stato: ⏳ da svolgere                          │
│                                                │
│  [ Sposta / Recupera ]   [ Segna svolta ]      │
│  [ Apri corso › ]                              │
└────────────────────────────────────────────────┘
```

**Wireframe — Modal "Sposta / Recupera" (click 2 + scelta data):**

```
┌────────── Sposta lezione · Pianoforte M. Rossi ──────────────────────────┐
│  Originale   Gio 18/12/2025 · 15:00–15:45 · Aula A2                       │
│                                                                          │
│  Motivo      ◉ Recupero (festività/sospensione)  ○ Spostamento  ○ Assenza docente │
│                                                                          │
│  Nuova data  [ 09/01/2026 ]   ▸ slot liberi suggeriti:                   │
│                ◉ Ven 09/01 15:00  (aula A2 libera, docente libero)       │
│                ○ Lun 12/01 17:00  (aula A2 libera)                       │
│                ○ altra data… [ __/__/____ ]  ore [ 15:00 ]–[ 15:45 ]    │
│                                                                          │
│  Docente     M. Rossi ▾   (o sostituto: [—] )                            │
│  Aula        A2 ▾                                                         │
│  Note        [ Recupero ponte dell'Immacolata ]                          │
│                                                                          │
│  ⚠ Conflitto: l'aula A2 è occupata Ven 09/01 alle 15:00 → scegli altro slot │
│                                                                          │
│                                       [ Annulla ]   [ Conferma sposta ]  │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **2 click + data**: click sulla lezione → `[Sposta/Recupera]` → scelta data → `[Conferma]`. Niente eliminazione/ricreazione manuale.
- **Slot suggeriti**: la modal propone date/ore con **aula e docente liberi** (controllo conflitti su `Lesson` esistenti); l'operatrice può comunque scegliere una data libera.
- **Storico preservato**: la lezione cambia `date` (e all'occorrenza `time_*`, `classroom_id`, `substitute_teacher_id`) ma **conserva il riferimento alla data originale** + motivo, così "questa è un recupero del 18/12" resta leggibile (vedi §9, campi `original_date`/`reschedule_reason`).
- **Conflitti bloccanti soft**: se aula/docente sono occupati nello slot scelto, **avviso in modal** prima di confermare (non un errore dopo il salvataggio).
- **Sul calendario**: la lezione spostata appare alla nuova data con badge **↪ recupero**; un tooltip mostra *"recupero del 18/12/2025"*.

---

## 7. Stati e casi reali

| Caso | Comportamento UX |
| --- | --- |
| **Anno standard 3×11** | Wizard con default; preview 33→N settimane effettive; conferma. |
| **Scuola con sabato** | Aggiungi ☑ Sab nello step 1; preview ricalcola giorni. |
| **4° ciclo / lezioni libere** | [+ Aggiungi ciclo] o ciclo "lezioni libere" senza vincolo 11 settimane (slot per recuperi/extra). |
| **Sospensione che taglia un ciclo** | Preview mostra "11 previste → 9 effettive (−2 Natale)"; nessuna sorpresa a posteriori. |
| **Calendario già generato** | Preview chiede Sovrascrivi / Aggiungi mancanti; mai overwrite silenzioso. |
| **Recupero ponte** | Lezione in giorno sospeso → [Sposta/Recupera] motivo "Recupero" → slot libero suggerito → spostata con badge ↪. |
| **Assenza docente** | [Sposta/Recupera] motivo "Assenza docente" → o nuova data o sostituto (`substitute_teacher_id`) senza cambiare data. |
| **Conflitto aula/docente** | Avviso in modal sullo slot occupato; suggerimento di slot alternativi liberi. |
| **Lezione già svolta** | `completed = true` → [Sposta/Recupera] disabilitato (si recupera ciò che non è stato fatto), con nota. |
| **Spostamento a catena** | Una lezione spostata può essere rispostata; la traccia mostra la data originale, non l'ultima intermedia. |

---

## 8. Microcopy (etichette IT)

- Pulsanti: **"⚙ Genera calendario"**, **"Avanti ›"**, **"‹ Indietro"**, **"Conferma e genera"**, **"+ Aggiungi ciclo"**, **"+ Aggiungi sospensione"**, **"Sposta / Recupera"**, **"Conferma sposta"**, **"Segna svolta"**.
- Step wizard: **"Step 1/3 · Cicli" · "Step 2/3 · Sospensioni" · "Step 3/3 · Anteprima"**.
- Cicli: **"1° ciclo" · "2° ciclo" · "3° ciclo"**, **"11 settimane"**, **"lezioni libere"**.
- Preview: **"settimane previste → effettive"**, **"giorni attivi" · "giorni disattivati (sospensioni)"**, **"Sovrascrivi" · "Aggiungi solo i mancanti"**.
- Recupero: motivi **"Recupero (festività/sospensione)" · "Spostamento" · "Assenza docente"**; badge **"↪ recupero"**; tooltip *"recupero del 18/12/2025"*.
- Toast: *"Calendario 2025/26 generato: 30 settimane effettive, 148 giorni di lezione."* · *"Lezione spostata al 09/01/2026 (recupero del 18/12)."*
- Avvisi: *"⚠ Esiste già un calendario per 2025/26 (142 giorni)."* · *"⚠ Aula A2 occupata in questo slot — scegli un altro orario."*

---

## 9. Impatti tecnici (per chi implementa — NON parte di questo R7)

Il design **riusa il motore esistente** (`CalendarLesson`, `CalendarSuspension`, `Lesson`, `CalendarService`, `LessonCalendarController` + FullCalendar). Servono i pezzi mancanti:

- **Wizard a cicli (frontend + endpoint preview)**: nuovo step UI che raccoglie cicli (N × settimane, date) + giorni + sospensioni e chiama un endpoint **dry-run** che restituisce il conteggio (settimane previste/effettive per ciclo, giorni attivi/disattivati) **senza scrivere**. La generazione vera richiama `CalendarService::generateLessonsForYear` solo su [Conferma], aggiungendo l'opzione **Sovrascrivi / Aggiungi mancanti** (oggi `updateOrCreate` sovrascrive sempre).
- **Concetto di ciclo**: per la Fase 1 può restare **derivato/runtime** (cicli calcolati da `start_date` + N settimane, non persistiti) — il calendario base resta `CalendarLesson` per giorno. Se serve persistere i cicli (per report/prezzi), aggiungere una tabella leggera `calendar_cycles` (`academic_year_id`, `index`, `start_date`, `weeks`); decisione di Fase 2.
- **Sospensioni nel wizard**: riuso di `CalendarSuspension` + `applySuspension`; precaricare un set di default (Natale/Pasqua) come suggerimenti, non come record fissi.
- **Spostamento/recupero `Lesson`**: aggiungere all'azione campi per tracciare l'originale — proposta minima: `original_date` (date, nullable) + `reschedule_reason` (enum: recupero/spostamento/assenza). Endpoint `lessons/{id}/reschedule` che aggiorna `date`/`time_*`/`classroom_id` e valorizza i campi traccia; **controllo conflitti** (aula/docente già occupati nello slot) prima del commit; lezione `completed` non spostabile.
- **Slot suggeriti**: query sui `Lesson` esistenti per trovare date/ore con aula+docente liberi (riuso filtri già presenti in `LessonCalendarController::events`).
- **Badge calendario**: in `events()` marcare le lezioni con `original_date != null` (badge ↪, tooltip data originale).

---

## 10. Checklist di accettazione (Definition of Done del design)

- [x] Definito il **generatore guidato a cicli** (default 3 × 11 settimane, tutto editabile). — §3
- [x] **Sospensioni dentro il flusso** di generazione (default Natale/Pasqua come toggle). — §3–§4
- [x] **Anteprima prima del commit**: settimane previste→effettive per ciclo, giorni attivi/disattivati, scelta Sovrascrivi/Aggiungi. — §5
- [x] **Recupero/spostamento come azione 2 click** sulla lezione (click → Sposta/Recupera → nuova data). — §6
- [x] **Storico preservato** (data originale + motivo) e **controllo conflitti** aula/docente. — §6, §9
- [x] Coperti i **casi reali**: 3×11, sabato, 4° ciclo/lezioni libere, sospensione che taglia il ciclo, calendario già esistente, assenza docente, conflitti. — §7
- [x] **Mock con default editabili** e numeri reali, mai campi vuoti. — §3, §5
- [x] Wireframe ASCII per wizard, preview, popover e modal recupero + microcopy IT. — §3–§8
- [x] Indicati **impatti tecnici minimi** senza implementare; riuso entità/servizi esistenti, nessuna tabella obbligatoria in Fase 1. — §9
