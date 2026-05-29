# 27 — R7 · Controllo finale: Validazione calendario/recuperi

> Attività Footility **#8544** — *R7 · Controllo finale — Validazione calendario/recuperi*.
> Test E2E del generatore calendario e del flusso recuperi eseguito su ciò che esiste in codice,
> con gap documentati rispetto al design R7 ([`26_UX_GENERATORE_CALENDARIO_E_RECUPERI.md`](26_UX_GENERATORE_CALENDARIO_E_RECUPERI.md)).
> Test automatico: [`tests/Feature/CalendarioRecuperiE2EValidationTest.php`](../tests/Feature/CalendarioRecuperiE2EValidationTest.php) — **9/9 PASS**.

## Esito sintetico

| Flusso | Stato | Note |
| --- | --- | --- |
| Generazione calendario base (lun-ven) | ✅ OK | `CalendarController@generate` → `CalendarService::generateLessonsForYear`: crea i `CalendarLesson` per i giorni scelti, nessun weekend, tutti attivi. |
| Aggiungi sospensione | ✅ OK | `storeSuspension` → `applySuspension`: crea `CalendarSuspension` e disattiva i giorni nel range (verificato Natale 23/12→06/01). |
| Settimane previste → effettive | ✅ OK | `countWeeksForDay` esclude i giorni sospesi: le settimane effettive calano dopo la sospensione (concetto R7 §5, a livello motore). |
| Calendario lezioni (`events`) | ✅ OK | Espone giorni-lezione, lezioni effettive (`Lesson`) e banda sospensione in un'unica risposta FullCalendar. |
| Rimozione sospensione | ✅ OK | `destroySuspension` riattiva i giorni nel range. |
| **Wizard a cicli (11 settimane)** | 🟥 **BLOCCO** | Nessun concetto di ciclo: no tabella `calendar_cycles`, `generateLessonsForYear` lavora solo per `daysOfWeek`. |
| **Anteprima prima del commit (dry-run)** | 🟥 **BLOCCO** | `generate()` scrive subito in DB; nessun endpoint `preview`. |
| **Scelta Sovrascrivi / Aggiungi mancanti** | 🟥 **BLOCCO** | `updateOrCreate` sovrascrive in silenzio; il controller non riconosce alcun parametro di modalità. |
| **Sposta / Recupera lezione (2 click)** | 🟥 **BLOCCO** | Nessuna route `reschedule`, nessuna colonna `original_date`/`reschedule_reason`, `Lesson.classroom_id` non fillable. |

## Cosa è stato testato (E2E)

1. **Generazione lun-ven** (HTTP `POST calendar/generate`) → oltre 100 giorni creati, 0 weekend, tutti `is_active`; redirect + flash `success`.
2. **Aggiungi sospensione** (HTTP `POST calendar/suspensions`) → record creato e tutti i giorni nel range Natale disattivati; un giorno fuori range resta attivo.
3. **Settimane effettive** → `countWeeksForDay` per il martedì diminuisce dopo `applySuspension` (Natale), validando "previste → effettive".
4. **events()** → la risposta JSON contiene i tre tipi `lesson` / `actual-lesson` / `suspension`; la lezione effettiva creata è presente.
5. **Rimozione sospensione** (HTTP `DELETE`) → record cancellato e giorni riattivati.
6. **Gap cicli / preview / overwrite / recupero** → verificato in codice che i quattro punti del design R7 **non esistono** (schema, route, firme metodi, fillable).

## Gap che bloccano il flusso completo (coerenti con R7 §1, §9)

Il design R7 dichiarava già questi punti come `❌ assente` / `⚠`; il controllo finale lo conferma:

- **Wizard a cicli da 11 settimane** (R7 §1, §3, §9): il motore genera per giorni della settimana, non per cicli. Nessuna tabella `calendar_cycles` (decisione di Fase 2 nel design). Resta da costruire il wizard sopra `CalendarService`.
- **Anteprima prima del commit** (R7 §2, §5, §9): `generate()` scrive direttamente con redirect "Generati N giorni". Manca l'endpoint dry-run che restituisce il conteggio settimane previste/effettive senza toccare il DB.
- **Sovrascrivi / Aggiungi mancanti** (R7 §5): `updateOrCreate` sovrascrive sempre; nessuna scelta esplicita all'operatrice. *(Nota tecnica emersa durante il test: la ri-generazione su un anno già generato è anche fragile rispetto al cast `date` — ulteriore motivo per introdurre la modalità esplicita.)*
- **Sposta / Recupera lezione** (R7 §6, §9): nessuna route `lessons/{id}/reschedule`, nessun campo traccia `original_date` / `reschedule_reason`, e `Lesson.classroom_id` non è fillable (lo spostamento aula non sarebbe persistibile). Oggi una lezione si può solo editare/cancellare, perdendo lo storico.

## Raccomandazione

Il **motore base del calendario è validato e solido**: generazione giorni-lezione, sospensioni (applica/rimuovi),
conteggio settimane effettive e visualizzazione FullCalendar (giorni + lezioni + bande) funzionano end-to-end.
I quattro punti dell'attività R7 (**wizard a cicli**, **anteprima**, **Sovrascrivi/Aggiungi mancanti**,
**Sposta/Recupera 2 click**) restano da implementare come da R7 §3-§6, §9 — con i ritocchi minimi di schema
(`lessons.original_date` + `lessons.reschedule_reason`, eventuale `calendar_cycles` in Fase 2). È il prossimo
blocco di lavoro, non un bug sul codice esistente. Nessun blocco sul motore attuale.
