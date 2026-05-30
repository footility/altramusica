# 39 — UX: Pannello qualità dati e anomalie (R12 · Design UX)

> Attività Footility **#8532** — *R12 · Design UX — Pannello qualità dati e anomalie* (progetto Gestionale Altramusica).
> Obiettivo: una **vista che mette in fila i problemi dei dati** — duplicati, omonimi, campi critici mancanti, record orfani — e per ognuno offre un'**azione guidata per pulire**, **senza mai bloccare** il lavoro della segreteria.
> Deliverable: wireframe (ASCII) del pannello + drill-down per tipo di anomalia + flussi di correzione (unisci duplicati, completa campo, collega/risolvi orfano, ignora falso positivo), casi reali, microcopy IT, impatti tecnici.
> **Principio cardine**: le anomalie si **segnalano**, non si **impongono**. Nessun blocco a salvataggio/iscrizione/pagamento: la qualità dati è un *cruscotto di lavoro arretrato*, non un cancello.
> Base AS-IS: il motore di rilevamento anomalie **esiste già** in [`OdsImportService`](../app/Services/OdsImportService.php) (`detectRowAnomalies`: CF mancante/invalido/duplicato, omonimi, email invalida, date non parsabili, note con dati strutturati) — ma vive **solo durante l'import ODS** (R-import). R12 lo trasforma in una **vista permanente sul dato vivo**, estesa a record orfani e campi critici. Riusa le anagrafiche di [`20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md`](20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md) (R1) e la riconciliazione di [`36_UX_RICONCILIAZIONE_MANUALE_E_IMPORT_CSV.md`](36_UX_RICONCILIAZIONE_MANUALE_E_IMPORT_CSV.md) (R5).

---

## 0. Principi di design

1. **Segnalare, non bloccare.** Un dato sporco non deve impedire di iscrivere un allievo o registrare un pagamento oggi. Il pannello raccoglie il **debito di qualità** e lo rende lavorabile *quando c'è tempo*. Salvataggi e flussi operativi restano permissivi (warning inline, mai errore bloccante). Questo è l'opposto di una validazione rigida: la realtà di una scuola di musica è disordinata e va accolta, poi pulita.
2. **Un problema, un'azione.** Ogni anomalia ha **un pulsante che la risolve** (unisci, completa, collega, ignora), non solo una descrizione. La segreteria non deve capire *cosa fare*: deve solo confermare. L'azione apre la scheda giusta col campo giusto già a fuoco.
3. **Falsi positivi previsti e gestiti.** Due "Mario Rossi" possono essere davvero due persone; un CF duplicato può essere un errore di battitura o un dato reale (il foglio ODS *ha* CF ripetuti — vedi `OdsImportService` PASS 1). Ogni anomalia si può **marcare come "non è un problema"** (ignora/risolvi-come-falso-positivo) e **non riappare**. Senza questo, il pannello diventa rumore e viene abbandonato.
4. **Gravità prima di tutto.** Non tutte le anomalie pesano uguale: un **minorenne senza tutore di fatturazione** è grave (blocca contratto/fattura), un **campo "città" vuoto** è cosmetico. Tre livelli — **🔴 critica**, **🟠 da sistemare**, **🟡 cosmetica** — ordinano la lista e colorano i contatori. Si lavora dall'alto.
5. **Il conteggio vive nell'header, non solo nel pannello.** Un badge **"⚠ 14 anomalie"** vicino al menu rende il debito *visibile passivamente* — non serve ricordarsi di aprire la pagina. Cliccando si entra nel pannello già filtrato.
6. **Idempotente e ripetibile.** Il rilevamento è una **lettura derivata** (query sul dato attuale), non uno stato salvato che si disallinea: ricalcola al volo. L'unico stato persistito sono le **eccezioni** (anomalie marcate "ignora") e i log di merge. Riusa lo stesso identico set di regole dell'import, così "pulito all'import" = "pulito nel pannello".
7. **Reversibile dov'è rischioso.** L'unica azione che *distrugge* informazione è **unisci duplicati**: deve mostrare un'anteprima di cosa si fonde, tenere un log, ed essere annullabile (o almeno tracciata) — coerente con lo stile "eventi reversibili" di R5. Le altre azioni (completa campo, collega orfano) sono normali edit.

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| **Motore anomalie** (CF mancante/invalido/duplicato, omonimi, email invalida, date non parsabili, note con dati strutturati) | ✅ esiste in `OdsImportService::detectRowAnomalies()` — ma **solo durante l'import ODS**, su righe del foglio, non sul dato vivo |
| Indici trasversali (duplicati CF, omonimi nome+cognome) | ✅ già calcolati nell'import (`OdsImportService` PASS 1: `$duplicateCfs`, `$homonymNames`) — logica riusabile |
| Anagrafica allievi (`Student`: `first_name`, `last_name`, `birth_date`, `tax_code`) | ✅ esiste — campi critici noti, ma **nessun controllo "completezza" continuo** |
| Tutori (`Guardian`: contatti `cell_1..4`, `email_1..3`, `privacy_consent`) + pivot `student_guardian` (`is_primary`, `is_billing_contact`) | ✅ esiste — base per "minorenne senza tutore/contatto fatturazione" e "consenso privacy mancante" |
| Relazioni (iscrizioni, anni, contratti, fatture, pagamenti, noleggi) | ✅ esistono — ma **nessuna scansione di integrità** (orfani: figlio senza padre referenziale) |
| **Pannello qualità dati permanente** (vista sul dato vivo) | ❌ **assente** — l'unico punto in cui l'utente vede anomalie è il report dry-run dell'import |
| **Record orfani** (es. `StudentYear`/`Enrollment`/`Contract` che puntano a entità mancanti, allievo senza alcun anno, tutore senza allievi) | ❌ **assente** — nessuna query di integrità referenziale esposta |
| **Campi critici mancanti** come lista lavorabile (CF, data nascita, contatto, consenso privacy, tutore fatturazione per minori) | ❌ assente come vista — i dati ci sono/mancano ma non c'è un cruscotto |
| **Azioni guidate** (unisci duplicati, completa, collega, ignora) | ❌ assenti — nessun flusso di merge, nessuna tabella eccezioni |
| **Badge di conteggio** nell'header | ❌ assente |

> Conclusione: la parte **difficile esiste già** (le regole di rilevamento, testate sull'import). Manca **(a)** farle girare sul **dato vivo** invece che sulle righe ODS, **(b)** estenderle a **orfani** e **completezza campi critici**, **(c)** una **vista** che le ordina per gravità, **(d)** le **azioni guidate** (soprattutto *unisci duplicati* con anteprima/log) e **(e)** la tabella **eccezioni** per i falsi positivi. Nessuna logica nuova di parsing: si **riusa e si estrae** `detectRowAnomalies` in un servizio condiviso.

---

## 2. Architettura della vista (in chiave UX)

```
        QUALITÀ DATI ▸ Anomalie                         [Tutte ▾]  [🔴 5] [🟠 7] [🟡 2]
  ┌──────────────────────────────────────────────────────────────────────────────┐
  │  Fascia di sintesi: quante anomalie, per gravità, ultimo controllo            │
  ├──────────────────────────────────────────────────────────────────────────────┤
  │  FILTRI:  [Duplicati] [Omonimi] [Campi mancanti] [Orfani]   [☐ mostra ignorate]│
  ├──────────────────────────────────────────────────────────────────────────────┤
  │  LISTA ANOMALIE (ordinata per gravità ↓), ogni riga = 1 problema + 1 azione    │
  │   🔴  Minorenne senza tutore di fatturazione · M. Bianchi   [ Aggiungi tutore ▸]│
  │   🔴  CF duplicato · RSSMRA…  → 2 allievi                    [ Confronta/Unisci ▸]│
  │   🟠  Omonimi · "Mario Rossi" ×2                             [ Verifica ▸]       │
  │   🟠  Email tutore non valida · "mario@"                     [ Correggi ▸]       │
  │   🟡  Città mancante · L. Verdi                              [ Completa ▸]       │
  │   …                                                          [ Ignora ]         │
  └──────────────────────────────────────────────────────────────────────────────┘
```

- **Sorgente dati (sola lettura + eccezioni):** scansione derivata sul dato vivo (`Student`, `Guardian`, `student_guardian`, `Enrollment`, `StudentYear`, `Contract`, `Invoice`, `Payment`); le sole scritture sono **(a)** le azioni di correzione (normali edit sulle schede) e **(b)** la riga in tabella `data_quality_dismissals` quando si ignora.
- **Filtri per categoria** (duplicati / omonimi / campi mancanti / orfani) **+** chip di gravità in alto a destra; di default si mostra **tutto tranne le ignorate**.
- **Ogni riga è auto-contenuta**: descrizione breve + entità coinvolta (linkata alla scheda) + **una azione primaria** + "Ignora". Niente sotto-menù complessi.
- **Accesso**: voce **Qualità dati ▸ Anomalie** per segreteria/admin (è lavoro operativo, non direzionale). Badge di conteggio sempre visibile nell'header.

---

## 3. Categoria A — Duplicati (stesso allievo inserito due volte)

Il rischio più costoso: due schede per la stessa persona → pagamenti spezzati, storico diviso, comunicazioni doppie.

**Wireframe — confronto e unione guidata:**

```
┌─ Possibile duplicato · CF RSSMRA10A01H501U ────────────────────────────────┐
│  Due allievi condividono lo stesso codice fiscale.                         │
│                                                                            │
│                     SCHEDA A (#142)            SCHEDA B (#388)             │
│  Nome               Mario Rossi                Mario Rossi                 │
│  Nato il            01/01/2010                 01/01/2010                  │
│  Iscrizioni         2024/25, 2025/26           2025/26                     │
│  Pagamenti          3 (1.240 €)                1 (180 €)                    │
│  Tutori             Anna Rossi (princ.)        — nessuno                   │
│  Contratti          1 firmato                  0                           │
│                                                                            │
│  ◉ Tieni A come principale, sposta tutto da B   ○ Tieni B   ○ Non è un dup.│
│                                                                            │
│  Verrà spostato da B → A:  1 iscrizione, 1 pagamento (180 €)               │
│  La scheda B verrà archiviata (annullabile, log conservato).              │
│                                                                            │
│                       [ Annulla ]      [ Anteprima ]      [ Unisci ▸ ]      │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Rilevamento** = stesso `tax_code` (logica `$duplicateCfs` dell'import) **oppure** stesso `first_name`+`last_name`+`birth_date`. Il CF da solo **non** è prova certa (il foglio storico ha CF ripetuti): per questo l'azione è *Confronta*, non *Unisci automaticamente*.
- **Confronto affiancato** dei due record con i dati che contano per decidere (anni, pagamenti, tutori, contratti). La direzione del merge è **scelta dall'utente** (quale tieni come principale).
- **Anteprima obbligatoria** prima di unire: elenca *cosa si sposta* (iscrizioni, pagamenti, contratti, noleggi, documenti) e *cosa si archivia*. Niente sorprese.
- **Reversibile/tracciato**: la scheda assorbita si **archivia** (non hard-delete), con log del merge (chi, quando, da→a). Coerente con lo stile "eventi reversibili" di R5.
- **"Non è un duplicato"** → marca la coppia come eccezione: non riappare (vedi §7).

---

## 4. Categoria B — Omonimi (stesso nome, persone diverse)

Diverso dal duplicato: qui **vogliamo** due schede, ma serve **distinguerle** per non sbagliare in iscrizioni/pagamenti/comunicazioni.

**Wireframe:**

```
┌─ Omonimi · "Mario Rossi" (2 schede) ───────────────────────────────────────┐
│  Stesso nome e cognome, dati diversi → probabilmente persone diverse.      │
│                                                                            │
│   #142  Mario Rossi · nato 01/01/2010 · CF RSS…501U · Pianoforte           │
│   #501  Mario Rossi · nato 14/06/2008 · CF RSS…733K · Chitarra             │
│                                                                            │
│   ⓘ Date di nascita e CF diversi: sono due persone. Marca come verificato. │
│                                                                            │
│              [ Apri #142 ]   [ Apri #501 ]   [ ✓ Verificato, sono diversi ]│
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Rilevamento** = stesso `first_name`+`last_name` ma **CF/data nascita differenti** (logica `$homonymNames` dell'import). Se invece *coincidono* CF o data nascita, l'anomalia sale a **§3 Duplicato** (gravità maggiore).
- **Azione tipica = conferma, non correzione**: "Verificato, sono diversi" → eccezione registrata, l'omonimia non risuona più. Resta utile per la segreteria sapere che *esistono* due Mario Rossi.
- **Suggerimento di disambiguazione**: proporre di aggiungere un dettaglio distintivo (es. anno di nascita) nelle viste di scelta allievo → riduce errori a valle. (Solo suggerimento, non imposto.)

---

## 5. Categoria C — Campi critici mancanti

Non "tutti i campi compilati", ma **i pochi che servono davvero** a far girare contratti, fatture, comunicazioni e a rispettare il GDPR.

| Campo critico | Perché conta | Gravità |
| --- | --- | --- |
| **Tutore di fatturazione** per allievo **minorenne** (`student_guardian.is_billing_contact` su un minore) | Senza, non si emette contratto/fattura corretti | 🔴 critica |
| **Almeno un contatto** (cell o email su tutore primario / allievo maggiorenne) | Senza, nessuna comunicazione possibile (R9) | 🔴 critica |
| **Consenso privacy** (`Guardian.privacy_consent`) per chi riceve comunicazioni | GDPR — base legale per email/SMS (R9) | 🔴 critica |
| **Codice fiscale** (`Student.tax_code`) mancante o **invalido** (formato) | Serve per contratto/fattura; logica `cleanTaxCode`/validazione import | 🟠 da sistemare |
| **Data di nascita** (`Student.birth_date`) | Determina minorenne/maggiorenne → guida le regole sopra | 🟠 da sistemare |
| **Email/telefono in formato non valido** ("mario@", numeri nelle note) | Comunicazione fallisce silenziosamente; logica `invalid_email` + `notes_with_data` dell'import | 🟠 da sistemare |
| Indirizzo / CAP / città | Utile per contratti cartacei, non bloccante | 🟡 cosmetica |

**Wireframe — completamento guidato (inline, non blocca):**

```
┌─ Campo critico mancante ───────────────────────────────────────────────────┐
│  🔴  Lucia Bianchi (minorenne, 12 anni) — nessun tutore di fatturazione    │
│      Senza un tutore di fatturazione non potrai emettere contratto/fattura.│
│      → [ Aggiungi tutore di fatturazione ▸ ]   [ Ignora per ora ]          │
│                                                                            │
│  🟠  Tutore "Anna Rossi" — codice fiscale assente                          │
│      → [ Inserisci CF ▸ ]   [ Ignora ]                                     │
│                                                                            │
│  🟠  Tutore "M. Verdi" — email "mario@" non valida                         │
│      → [ Correggi email ▸ ]   [ Ignora ]                                   │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **L'azione apre la scheda col campo a fuoco**, già scrollato e con focus: la segreteria scrive il valore e salva, l'anomalia sparisce al ricalcolo. Zero navigazione manuale.
- **Mai bloccante a monte**: si può iscrivere un minorenne *senza* tutore oggi; l'anomalia lo ricorda dopo. (Vincolo §0.1.) Il *contratto*/*fattura* può invece avvisare in linea — ma è scelta del rispettivo flusso (R3/R4), non di questo pannello.
- **Note con dati strutturati** (telefono/email/data finiti nel campo "note", anomalia `notes_with_data` dell'import): azione = "Sposta nel campo giusto", che pre-compila il campo destinazione con l'estratto rilevato.
- **Validazione CF/email = identica all'import** (`cleanTaxCode`, `FILTER_VALIDATE_EMAIL`): un dato "ok qui" è "ok all'import" e viceversa.

---

## 6. Categoria D — Record orfani (integrità referenziale)

Righe che puntano nel vuoto o entità senza il legame minimo che le rende sensate. Sintomo tipico di import parziali o cancellazioni a metà.

| Anomalia orfano | Definizione | Gravità |
| --- | --- | --- |
| **Allievo senza alcun anno** | `Student` senza nessuno `StudentYear` | 🟠 |
| **Iscrizione/anno orfano** | `Enrollment`/`StudentYear` il cui `student_id` non esiste più | 🔴 |
| **Contratto senza allievo** | `Contract.student_id` non risolve | 🔴 |
| **Pagamento senza piano/fattura** | `Payment` non collegato a `PaymentPlan`/`Invoice` (cassa "sospesa", link R5) | 🟠 |
| **Tutore senza allievi** | `Guardian` con zero righe in `student_guardian` (rimasto dopo cancellazioni) | 🟡 |
| **Iscrizione a offerta corso inesistente** | `Enrollment` → `CourseOffering` mancante | 🟠 |
| **Noleggio senza strumento/allievo** | `InstrumentRental` con riferimento mancante | 🟠 |

**Wireframe:**

```
┌─ Record orfano ────────────────────────────────────────────────────────────┐
│  🔴  Contratto #77 — l'allievo collegato (#999) non esiste più             │
│      → [ Ricollega a un allievo ▸ ]   [ Archivia contratto ]   [ Ignora ]  │
│                                                                            │
│  🟠  Pagamento #310 (180 €, 12/03) — non collegato a nessuna scadenza      │
│      → [ Abbina a una scadenza ▸ (riconciliazione R5) ]   [ Ignora ]       │
│                                                                            │
│  🟡  Tutore "G. Neri" — non collegato a nessun allievo                     │
│      → [ Collega a un allievo ▸ ]   [ Archivia tutore ]   [ Ignora ]       │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Tre vie per ogni orfano**: **ricollega** (alla entità giusta), **archivia** (se è davvero residuo), **ignora** (falso positivo). Mai cancellazione silenziosa.
- **Il pagamento orfano rimanda a R5**: l'azione "Abbina a una scadenza" apre la schermata di riconciliazione manuale già esistente nel design R5 — niente doppione di logica.
- **Archiviazione, non delete**: coerente con §3 e R5 (eventi reversibili). L'archiviato esce dal pannello ma resta recuperabile.

---

## 7. Ignora / falsi positivi (tabella eccezioni)

Il meccanismo che tiene il pannello **utile nel tempo** invece che rumoroso.

**Regole UX:**
- **"Ignora"** su qualsiasi anomalia → riga in `data_quality_dismissals` (tipo anomalia + chiave entità/coppia + chi + quando + nota opzionale). Al ricalcolo, le anomalie con eccezione attiva **non compaiono** (salvo flag "☐ mostra ignorate").
- **Chiave stabile**: l'eccezione è legata all'*identità del problema* (es. coppia di `student_id` per un duplicato, non a un id di riga volatile) così non riemerge al ricalcolo successivo.
- **Riapparizione intelligente**: se il dato sottostante **cambia in modo rilevante** (es. uno dei due omonimi cambia CF e ora *coincide*), l'eccezione decade e l'anomalia torna — perché ora è un problema diverso/più grave.
- **"Mostra ignorate"** + **"Annulla ignora"**: si possono rivedere e riattivare le eccezioni (nessun vicolo cieco).

---

## 8. Casi e stati (riepilogo)

| Caso | Comportamento UX |
| --- | --- |
| **CF duplicato ma persone diverse** (errore di battitura altrui) | §3 confronto → "Non è un duplicato" → eccezione; oppure correggi il CF sbagliato. |
| **Omonimi reali** | §4 → "Verificato, sono diversi" → eccezione, restano due schede. |
| **Minorenne appena inserito senza tutore** | Iscrizione **consentita** oggi; anomalia 🔴 nel pannello finché non si aggiunge il tutore. |
| **Unione duplicati per errore** | Log del merge + scheda archiviata → annullabile/ripristinabile. |
| **Pagamento orfano** | Rimanda alla riconciliazione R5; finché non abbinato resta "cassa sospesa". |
| **Anomalia risolta** | Sparisce al **ricalcolo** (lettura derivata), niente stato da aggiornare a mano. |
| **Falso positivo ricorrente** | Eccezione persistente; riappare solo se il dato cambia in modo rilevante (§7). |
| **Nessuna anomalia** | Stato vuoto positivo: "✓ Nessuna anomalia rilevata — dati puliti". |
| **Tante anomalie (centinaia post-import)** | Lista paginata, ordinata per gravità; azione "Ignora tutte le 🟡 di questo tipo" per sgrossare. |
| **Permessi** | Le azioni di merge/archiviazione richiedono ruolo segreteria/admin; sola lettura per ruoli inferiori. |

---

## 9. Microcopy (etichette IT)

- Sezioni: **"Qualità dati ▸ Anomalie"** · filtri **"Duplicati"**, **"Omonimi"**, **"Campi mancanti"**, **"Orfani"** · **"mostra ignorate"**.
- Gravità: **"🔴 Critica"**, **"🟠 Da sistemare"**, **"🟡 Cosmetica"**.
- Badge header: **"⚠ 14 anomalie"** (link al pannello già filtrato).
- Duplicati: **"Possibile duplicato"**, **"Confronta / Unisci"**, **"Tieni come principale"**, **"Verrà spostato…"**, **"La scheda verrà archiviata (annullabile)"**, **"Non è un duplicato"**.
- Omonimi: **"Stesso nome, dati diversi"**, **"✓ Verificato, sono diversi"**.
- Campi mancanti: **"Tutore di fatturazione mancante"**, **"Nessun contatto (email/telefono)"**, **"Consenso privacy mancante"**, **"Codice fiscale assente/non valido"**, **"Sposta nel campo giusto"**, **"Completa ▸"**, **"Ignora per ora"**.
- Orfani: **"Record orfano"**, **"Ricollega ▸"**, **"Archivia"**, **"Abbina a una scadenza (riconciliazione)"**.
- Stati: **"✓ Nessuna anomalia rilevata — dati puliti"** · **"Ignora"** · **"Annulla ignora"** · **"Ultimo controllo: oggi 14:03"**.

---

## 10. Impatti tecnici (per chi implementa — NON parte di questo R12)

Il cuore (regole di rilevamento) **esiste**: va **estratto** da `OdsImportService` in un servizio condiviso e fatto girare sul dato vivo.

- **Estrazione regole**: portare `detectRowAnomalies` + indici (`$duplicateCfs`, `$homonymNames`), `cleanTaxCode`, validazione email/date in un **`DataQualityRuleset`** condiviso, chiamato sia dall'import (su righe) sia dal pannello (su record). Una sola fonte di verità → "pulito qui = pulito all'import".
- **Servizio scansione** `DataQualityScanner` (sola lettura) con metodi per categoria:
  - `duplicates()` → `Student` raggruppati per `tax_code` e per (`first_name`,`last_name`,`birth_date`).
  - `homonyms()` → stesso nome+cognome con CF/`birth_date` diversi.
  - `missingCriticalFields()` → minori senza `student_guardian.is_billing_contact`; tutori/allievi senza contatto; `privacy_consent` nullo; `tax_code`/`birth_date` mancanti o invalidi; email/telefono malformati.
  - `orphans()` → controlli di integrità referenziale su `Enrollment`/`StudentYear`/`Contract`/`Payment`/`Guardian`/`InstrumentRental` (LEFT JOIN/`whereDoesntHave`).
- **Vista/Controller** `Admin\DataQualityController` (sola lettura per la lista; azioni dedicate per merge/archivia/ignora), rotta sotto policy segreteria/admin. Badge header da un conteggio cache-abile.
- **Merge allievi** `StudentMergeService::merge($keep, $absorb)`: ribalta FK (`student_years`, `enrollments`, `payments` via piani, `contracts`, `instrument_rentals`, `documents`, `student_guardian`) su `$keep`, archivia `$absorb` (soft-delete + `merged_into_id`), scrive **log merge**. Idealmente in transazione; ripristino dal log.
- **Eccezioni**: tabella **`data_quality_dismissals`** (`anomaly_type`, `entity_key` — stringa stabile, es. `student:142|388` —, `dismissed_by`, `dismissed_at`, `note`). Il filtro esclude le chiavi attive; ricalcolo invalida l'eccezione se la condizione che la generava è cambiata.
- **Performance**: scansione = poche query aggregate; cache del conteggio per badge (TTL breve o invalidazione su scrittura anagrafica). Nessuna tabella di "anomalie materializzate" (eviterebbe il disallineamento, ma il dato è già derivabile a basso costo).
- **Nessuna libreria nuova**: tutto su query Eloquent + regole già scritte. Nessuna PII esce dal sistema (vista interna, GDPR coerente con R9).

---

## 11. Checklist di accettazione (Definition of Done del design)

- [x] **Quattro categorie** coperte: duplicati, omonimi, campi critici mancanti, record orfani — con definizione data-driven di ciascuna. — §3–§6
- [x] **Azione guidata per ogni anomalia** (unisci/confronta, verifica, completa-col-campo-a-fuoco, ricollega/archivia/abbina) — un problema, un pulsante. — §3–§6
- [x] **Non bloccante**: la qualità dati è un cruscotto di arretrato, i flussi operativi restano permissivi. — §0.1, §5, §8
- [x] **Gravità a tre livelli** (🔴/🟠/🟡) che ordina la lista e alimenta il badge header. — §0.4, §2
- [x] **Falsi positivi gestiti** con tabella eccezioni (ignora/annulla-ignora, chiave stabile, riapparizione se il dato cambia). — §7
- [x] **Unione duplicati reversibile/tracciata** con anteprima di cosa si sposta e archiviazione (non delete). — §3, §8
- [x] **Riuso del motore anomalie esistente** (`OdsImportService`) come fonte unica di regole, esteso a orfani e completezza. — §0.6, §1, §10
- [x] Wireframe ASCII per pannello, confronto/unione duplicati, omonimi, completamento campi, orfani + microcopy IT. — §2–§9
- [x] Impatti tecnici senza implementare: ruleset condiviso, scanner sola lettura, merge service con log, tabella `data_quality_dismissals`, badge cache-abile, zero PII e zero package nuovi. — §10
