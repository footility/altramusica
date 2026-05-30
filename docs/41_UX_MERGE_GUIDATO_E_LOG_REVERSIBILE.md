# 41 — UX: Merge guidato studenti/genitori/corsi con log reversibile (R12 · Design UX)

> Attività Footility **#8534** — *R12 · Merge guidato studenti/genitori/corsi con log* (Gestionale Altramusica).
> Obiettivo: un **tool di unione** delle schede duplicate (allievi, tutori, corsi) con **anteprima**, **conferma esplicita** e **log delle decisioni**, **reversibile per N giorni**.
> È l'approfondimento dell'azione più rischiosa del pannello qualità dati ([§3 del doc 39](39_UX_PANNELLO_QUALITA_DATI_E_ANOMALIE.md)): l'unica che *distrugge* informazione e quindi richiede anteprima/log/ripristino (principio §0.7 del design R12).
> **Principio cardine**: nessuna fusione automatica. Si **confronta**, si **vede cosa si sposta**, si **conferma**, e si **può tornare indietro** finché la finestra è aperta. Coerente con lo stile "eventi reversibili" di R5.
> Base AS-IS: lo schema espone già tutte le FK da ribaltare e i `SoftDeletes` per archiviare (vedi §3); manca solo il servizio + il log. Controllo finale: [`42_R12_CONTROLLO_FINALE_VALIDAZIONE_MERGE_GUIDATO.md`](42_R12_CONTROLLO_FINALE_VALIDAZIONE_MERGE_GUIDATO.md), test [`tests/Feature/MergeGuidatoReversibileE2EValidationTest.php`](../tests/Feature/MergeGuidatoReversibileE2EValidationTest.php) — **15/15 PASS**, suite **155/155**.

---

## 0. Principi

1. **Mai automatico, sempre confermato.** Il rilevamento dei duplicati (doc 39 §3) propone *candidati*; l'unione è un'azione **deliberata** della segreteria, con direzione scelta a mano (quale scheda tieni).
2. **Anteprima obbligatoria.** Prima di unire si vede **esattamente cosa si sposta** (iscrizioni, contratti, fatture, pagamenti, noleggi, documenti, tutori) e **cosa si archivia**. L'anteprima è **sola lettura**: non scrive nulla.
3. **Una sola fonte assorbe, una mantiene.** `merge($keep, $absorb)`: tutte le relazioni di `$absorb` passano a `$keep`; `$absorb` viene **archiviato** (soft-delete + `merged_into_id`), **mai cancellato fisicamente**.
4. **Log della decisione.** Ogni merge scrive una riga `merge_logs`: entità, *chi*, *quando*, `keep→absorb`, conteggi spostati e **snapshot** (gli id delle righe mosse) — abbastanza per ripristinare.
5. **Reversibile per N giorni.** Entro la finestra (default **30 gg**, configurabile via `Setting`), un pulsante **Annulla unione** ripunta le FK all'assorbito e lo ri-attiva. Oltre la finestra il merge è **definitivo** (lo snapshot può essere potato).
6. **Atomico.** Il merge gira in **transazione**: se qualcosa fallisce a metà, rollback totale — nessuna fusione parziale.
7. **Rispetta i vincoli.** Il pivot `student_guardian` ha `unique(student_id, guardian_id)`: i legami già presenti sul mantenuto si **scartano** (dedup), non si duplicano. Le offerte corso (`course_offerings.course_id`) sono `restrict`: si ripuntano **prima** di archiviare il corso.

---

## 1. Quando appare

- Dal **pannello qualità dati** (doc 39 §3), riga "Possibile duplicato" → pulsante **[ Confronta / Unisci ▸ ]**.
- Dalla scheda di un'entità (allievo/tutore/corso) → azione **"Unisci con un'altra scheda…"** (ricerca della scheda gemella).
- L'unione è riservata a **segreteria/admin** (sola lettura per ruoli inferiori) — coerente col doc 39 §8.

---

## 2. Schermata 1 — Confronto + anteprima (sola lettura)

```
┌─ Unisci schede · Allievo ──────────────────────────────────────────────────┐
│                          MANTIENI (#142)          ASSORBI (#388)            │
│  Nome                    Mario Rossi               Mario Rossi              │
│  Nato il                 01/01/2010                01/01/2010               │
│  CF                      RSSMRA10A01H501U          rssmra10a01h501u         │
│  Iscrizioni              2024/25, 2025/26          2025/26                  │
│  Contratti               1 firmato                 0                        │
│  Fatture / Pagamenti     3 (1.240 €)               1 (180 €)                │
│  Tutori                  Anna Rossi (princ.)       Anna Rossi, Luca Rossi   │
│                                                                             │
│  ◉ Tieni #142, sposta tutto da #388    ○ Tieni #388    ○ Non è un duplicato │
│                                                                             │
│  ANTEPRIMA — verrà spostato su #142:                                        │
│    • 1 iscrizione   • 1 fattura (180 €)   • 1 tutore nuovo (Luca Rossi)     │
│    • Anna Rossi è già su #142 → legame doppio scartato (non duplicato)      │
│  La scheda #388 verrà ARCHIVIATA (annullabile per 30 giorni, log conservato).│
│                                                                             │
│                         [ Annulla ]        [ Conferma unione ▸ ]            │
└─────────────────────────────────────────────────────────────────────────────┘
```

- **Confronto affiancato** dei dati che contano per decidere la direzione.
- **Anteprima** = `preview($keep, $absorb)`: conteggi di cosa si sposta + cosa si archivia, **senza scrivere**. Mostra anche i **doppioni di pivot** che saranno scartati (così "1 tutore" non sembra perso).
- **"Non è un duplicato"** → registra l'eccezione in `data_quality_dismissals` (doc 39 §7) e chiude: niente merge.

---

## 3. Cosa si sposta (mappa FK, per entità)

Lo schema attuale espone già tutte le relazioni: il merge è **meccanico** (ribalta FK), nessun parsing.

### Allievo (`Student`, ha `SoftDeletes`)
Relazioni 1:N ribaltate (FK `student_id`): `student_years`, `enrollments`, `contracts`, `invoices`, `instrument_rentals`, `exams`, `documents`, `student_levels`, `student_availability`, `book_distributions`. Pivot: `student_guardian` (con **dedup** sul vincolo unique). Archiviazione: soft-delete + `merged_into_id`.

### Tutore (`Guardian`) ⚠️
Solo pivot `student_guardian` (FK `guardian_id`), con **dedup** sul vincolo unique. **Caveat implementazione**: `Guardian` **non ha `SoftDeletes`** oggi → per archiviare il tutore assorbito serve aggiungere `deleted_at` (+ `merged_into_id`). Vedi controllo finale §BLOCCO.

### Corso catalogo (`Course`, ha `SoftDeletes`)
Si ripuntano le **offerte annuali** `course_offerings.course_id` (FK `restrict` → ripuntare **prima** di archiviare). Le iscrizioni seguono l'offerta (`hasManyThrough`), quindi non vanno toccate. Archiviazione: soft-delete + `merged_into_id`.

> Nota: il merge di **corsi catalogo** ha senso solo a parità di `course_type`/significato (es. "Pianoforte" inserito due volte con codici diversi). Le *offerte* dello stesso anno restano distinte: due slot orari di pianoforte non si fondono — è un'unione di anagrafica corso, non di calendario (R7).

---

## 4. Il log della decisione (`merge_logs`)

Una riga per merge, struttura proposta:

| campo | contenuto |
| --- | --- |
| `entity` | `student` / `guardian` / `course` |
| `keep_id` / `absorb_id` | direzione del merge |
| `performed_by` | utente che ha confermato |
| `performed_at` | timestamp |
| `revertible_until` | `performed_at + N giorni` (default 30, `Setting`) |
| `snapshot` (json) | per tabella, gli **id delle righe spostate** + i legami pivot mossi/scartati |
| `reverted_at` | valorizzato se annullato |

Lo `snapshot` è il **registro di ciò che è cambiato**: contiene tutto il necessario per ripuntare le stesse righe all'assorbito senza ambiguità (vedi §5).

---

## 5. Schermata 2 — Annulla unione (entro la finestra)

```
┌─ Unione del 30/05/2026 · Allievo #142 ← #388 ──────────────────────────────┐
│  Eseguita da segreteria@altramusica · annullabile fino al 29/06/2026       │
│  Spostati: 1 iscrizione, 1 fattura, 1 tutore. Scheda #388 archiviata.      │
│                                                                            │
│  Annullando: le righe tornano a #388, la scheda #388 viene ri-attivata.    │
│                              [ Chiudi ]      [ Annulla unione ↺ ]           │
└─────────────────────────────────────────────────────────────────────────────┘
```

- **Revert** = ripunta le FK dello snapshot all'`absorb_id`, ripristina i legami pivot mossi, `restore()` dell'assorbito. In transazione.
- **Oltre `revertible_until`** il pulsante sparisce: il merge è **definitivo** (lo snapshot diventa potabile da un job di pulizia retention).
- Il revert è **best-effort onesto**: se nel frattempo una riga spostata è stata cancellata/ri-assegnata a mano, il log lo segnala invece di fingere un ripristino perfetto.

---

## 6. Casi e stati

| Caso | Comportamento |
| --- | --- |
| Tutore condiviso tra le due schede | Legame doppio **scartato** (dedup), non duplicato → niente violazione `unique`. |
| "Non è un duplicato" | Nessun merge; eccezione in `data_quality_dismissals` (doc 39 §7). |
| Errore a metà merge | Rollback totale (transazione) — nessuna fusione parziale. |
| Annulla entro N giorni | Tutto torna all'assorbito, scheda ri-attivata. |
| Oltre N giorni | Merge definitivo, pulsante annulla assente, snapshot potabile. |
| Merge di un tutore | ⚠️ richiede `SoftDeletes` su `Guardian` (oggi assente) per archiviare. |
| Merge di un corso | Ripunta `course_offerings`, poi soft-delete; offerte/anno restano distinte. |
| Permessi | Solo segreteria/admin; sola lettura per ruoli inferiori. |

---

## 7. Microcopy (IT)

- **"Unisci schede"**, **"Mantieni"**, **"Assorbi"**, **"Tieni #… come principale"**.
- **"Verrà spostato su #…: …"**, **"legame doppio scartato (non duplicato)"**, **"La scheda verrà archiviata (annullabile per N giorni, log conservato)"**.
- **"Conferma unione"**, **"Non è un duplicato"**.
- **"Annulla unione"**, **"annullabile fino al …"**, **"Unione definitiva (finestra scaduta)"**.

---

## 8. Impatti tecnici (per chi implementa — NON parte di questo R12)

- **Servizi merge** (uno per entità o un `MergeService` generico parametrico):
  - `StudentMergeService::preview($keep,$absorb)` (sola lettura) + `merge($keep,$absorb,$by)` (transazione).
  - `GuardianMergeService` — **prerequisito**: migrazione che aggiunge `SoftDeletes` + `merged_into_id` a `guardians`.
  - `CourseMergeService` — ripunta `course_offerings` prima dell'archiviazione.
- **Tabella `merge_logs`** (struttura §4) + colonna `merged_into_id` su `students`/`guardians`/`courses`.
- **Setting** `merge.revertible_days` (default 30) per la finestra.
- **Controller/rotte** sotto policy segreteria/admin: `merge.preview` (GET, dry-run), `merge` (POST), `merge.revert` (POST). Innesto dal pannello qualità dati (doc 39 §3).
- **Job retention** che pota gli snapshot dei merge oltre `revertible_until` (coerente con la policy retention GDPR già introdotta in `add_privacy_consent_and_retention_fields`).
- **Riuso**: il rilevamento candidati è quello del doc 39/40 (`OdsImportService`/`DataQualityScanner`); questo tool è la **fase di azione** a valle. Nessun parsing nuovo, nessuna libreria nuova, nessuna PII fuori dal sistema.

---

## 9. Checklist di accettazione (Definition of Done del design)

- [x] **Anteprima sola lettura** con conteggio di cosa si sposta / cosa si archivia, dedup pivot visibile. — §2, §3
- [x] **Conferma esplicita** + direzione scelta a mano (keep/absorb), mai automatico. — §0.1, §2
- [x] **Log decisione** (chi/quando/da→a/conteggi/snapshot) su `merge_logs`. — §4
- [x] **Reversibile per N giorni** (default 30, configurabile), oltre la finestra definitivo. — §0.5, §5
- [x] **Atomico** (transazione, rollback su errore). — §0.6
- [x] **Vincoli rispettati**: dedup `unique(student_id,guardian_id)`, `course_offerings` ripuntate prima dell'archiviazione (`restrict`). — §0.7, §3
- [x] **Archivia, non cancella** (SoftDeletes + `merged_into_id`); caveat `Guardian` senza SoftDeletes documentato. — §3, §8
- [x] **Esteso a studenti / genitori / corsi** come da titolo attività. — §3, §6
- [x] Impatti tecnici senza implementare: servizi merge, `merge_logs`, `merged_into_id`, Setting finestra, rotte, job retention. — §8
