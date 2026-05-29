# 24 — R10 · Controllo finale: Validazione documenti/modelli

> Attività Footility **#8542** — *R10 · Controllo finale — Validazione documenti/modelli*.
> Test E2E dell'archivio documenti eseguito su ciò che esiste in codice, con gap documentati
> rispetto al design R10 ([`23_UX_ARCHIVIO_DOCUMENTI_RICERCA_RAPIDA.md`](23_UX_ARCHIVIO_DOCUMENTI_RICERCA_RAPIDA.md)).
> Test automatico: [`tests/Feature/DocumentiE2EValidationTest.php`](../tests/Feature/DocumentiE2EValidationTest.php) — **7/7 PASS**.

## Esito sintetico

| Flusso | Stato | Note |
| --- | --- | --- |
| Upload documento (file singolo) | ✅ OK | `DocumentController@store` salva file su disk `public` e crea il `Document` con `uploaded_by`. |
| Validazione upload | ✅ OK | File obbligatorio; `type` vincolato all'enum {contract, privacy, photo_consent, other}. |
| Ricerca rapida (filtri) | ✅ OK | Filtri **tipo / studente / contratto** + ricerca testo (nome file e cognome studente) verificati. |
| CRUD (update / destroy) | ✅ OK | Cambio tipo e cancellazione record + file su disco verificati. |
| **Filtro per ANNO scolastico** | 🟥 **BLOCCO** | `documents` non ha `academic_year_id`; il parametro `year` è ignorato dal controller. |
| **Upload drag-drop / multi-file** | 🟥 **BLOCCO** | `store` accetta un solo file; nessun `storeMany`. |
| **Generazione documento da template** | 🟥 **BLOCCO** | Nessun `DocumentTemplateService`, nessuna route/azione `generate`, nessun flag origine. |

## Cosa è stato testato (E2E)

1. **Upload singolo** (HTTP `POST documents`) con `Storage::fake` → record creato, file presente su disco, `uploaded_by_user_id` valorizzato.
2. **Validazione** → file mancante e `type` fuori enum (`rental`) respinti, nessun record creato.
3. **Filtri archivio** → 3 documenti (contratto/privacy ×2) filtrati per tipo, studente, contratto e ricerca testo (nome file `A-0142`, cognome `Verdi`).
4. **Update + destroy** → cambio tipo `other → photo_consent`; cancellazione che rimuove sia il record sia il file (`assertMissing`).
5. **Gap anno / multi-file / template** → verificato che i tre punti del design R10 **non esistono** in codice (schema, route, metodi, fillable).

## Gap che bloccano il flusso completo (coerenti con R10 §1)

Il design R10 dichiarava già questi punti come `❌ assente`; il controllo finale lo conferma:

- **Filtro anno in 1 click** (R10 §1, §3): manca colonna `documents.academic_year_id` e il
  controller non riconosce il parametro `year`. L'anno è desumibile solo via `contract.academic_year_id`.
- **Upload drag-drop multi-file** (R10 §1, §4): `store` valida `file` come singolo; nessun
  endpoint `storeMany`. Oggi form classico con un solo `<input type=file>`.
- **Generazione da template** (R10 §1, §5): nessun `App\Services\DocumentTemplateService`, nessuna
  route/azione `documents.generate`, nessun flag `source` (uploaded/generated) né tipo `rental` (noleggio).
  I modelli ODT restano in `docs/materiale cliente/Contratti modelli/`, compilati a mano fuori dal gestionale.

## Raccomandazione

L'**archivio base è validato e solido**: modello + CRUD + ricerca + filtri tipo/studente/contratto +
upload/cancellazione file funzionano end-to-end. I tre punti dell'attività R10 (**filtro anno**,
**drag-drop multi-file**, **generazione da template**) restano da implementare come da R10 §8-§9 —
con il solo ritocco di schema `academic_year_id` (+ opzionali `source`/`title`). È il prossimo blocco
di lavoro, non un bug. Nessun blocco sul codice esistente.
