# 31 — R3 · Controllo finale: validazione contratti + firma mock

> Attività Footility **#8546** — *R3 · Controllo finale — Validazione contratti + firma mock*.
> Obiettivo: validare con test E2E il ciclo di vita del contratto (crea da modello →
> proposta → inviato → firmato → upload firma) **esercitando solo il codice reale**, e
> riportare gap/bug rispetto al design R3 ([`30_UX_FLUSSO_CONTRATTO_E_FIRMA.md`](30_UX_FLUSSO_CONTRATTO_E_FIRMA.md)).
> Base AS-IS: [`04_CONTRATTI_STUDENTI.md`](04_CONTRATTI_STUDENTI.md), [`12_DOCUMENTI_E_MODELLI_CONTRATTI.md`](12_DOCUMENTI_E_MODELLI_CONTRATTI.md).
> Trascrizioni: parte 1 r.322-328 / 392-398 / 546-550, parte 2 r.142-156.
>
> **Esito: 12/12 PASS.** Test: `tests/Feature/ContrattiFirmaE2EValidationTest.php`.

---

## 1. Cosa è stato esercitato (E2E sul codice reale — PASS)

| Flusso | Copertura | Riferimento design |
| --- | --- | --- |
| Crea contratto "da modello" (`store`) | numero contratto `CONTR-YYYY-000N` + token link precompilato (64 char) generati, parte in `draft` | §2, §7 · trascr. p1 r.322-328 |
| Tre tipi reali + noleggio | `regular`/`short`/`summer`/`instrument_rental` accettati dalla validazione | parte 2 r.142-156 |
| Timeline proposta→inviato→firmato | route `contracts.send` poi `contracts.sign`: stato avanza, `sent_date`/`signed_date` popolate | §3, §5b |
| Proposta da iscrizione | `ContractService::createFromEnrollment()` → `draft` con periodo precompilato (riuso R2) | §7 |
| Numero contratto progressivo | sequenza incrementale nell'anno | §2 |

## 2. Fix applicati (mirati, sicuri, coerenti col design)

- **BUG — `ContractController@store` andava in errore** `Undefined array key "contract_number"`
  (idem `academic_year_id`) quando il numero NON era inviato — cioè il **caso normale**
  (numero auto-generato). `validate()` con regola `nullable` non restituisce le chiavi assenti
  dall'input, e `if (!$validated['contract_number'])` accedeva a una chiave inesistente.
  **Fix:** `empty($validated[...])` (sopprime il warning su chiave assente). Sblocca la
  creazione contratto dalla UI.
- **§5/§10 — `ContractService::signContract()` ora accetta una data firma opzionale**
  (`signContract($contract, $signedDate = null)`). Il design vuole registrare la data che
  compare sulla **copia cartacea** (può essere di ieri); senza data resta `now()`
  (retrocompatibile col chiamante attuale).

## 3. Gap rispetto al design R3 (documentati, NON implementati)

| # | Gap | Stato oggi | Design |
| --- | --- | --- | --- |
| G1 | **Firma mock = upload scansione** | `sign()` marca solo lo stato; **nessun `Document` allegato**, nessuna action/route `registerSignature` con upload | §5 (centrale) |
| G2 | **Tipo documento `signed_contract`** | l'enum `documents.type` è `{contract,privacy,photo_consent,other}` → non distingue la scansione firmata dalla bozza (enum da estendere) | §5/§10 |
| G3 | **Data firma dal form** | `ContractController@sign` chiama `signContract()` senza data → forza `now()` anche se nel POST arriva `signed_date` (il servizio ora la supporta, il controller no) | §5/§10 |
| G4 | **Passi indietro/correzioni** | nessuna action/route `unsend`/`unsign`/`cancel`/`reopen` | §6 |
| G5 | **Timeline visiva + CTA unica** | `show` mostra badge + date in `<dl>`; restano due bottoni secchi ("Invia"/"Segna come Firmato") | §3, §4 |
| G6 | **Webhook firma digitale** | nessun endpoint `contracts/webhook/sign` (**corretto: è Fase 2**) | §9 |

## 4. Bug residuo non risolto (blocco segnalato)

- **`ContractService::generatePrecompiledLinkToken()` è rotto:** referenzia la route
  nominata **`contracts.accept` che non esiste** → `RouteNotFoundException` se invocato, e
  genera un token **nuovo casuale** invece di usare `$contract->token` già salvato.
  Il metodo oggi non è chiamato dal controller (codice morto/rotto). **Non corretto qui**:
  la correzione richiede una **route pubblica** del link precompilato (area Fase 2, §9 design),
  fuori dallo scope del mock R3. Documentato dal test `test_bug_generatePrecompiledLinkToken_route_inesistente`.

## 5. Conclusione

Il back-end del flusso contratto (stati, date, `send`/`sign`, `createFromEnrollment`,
numerazione, token) **funziona** ed è ora coperto da E2E. Sbloccato un bug che impediva la
creazione contratto dalla UI. La **firma mock onesta** del design (upload scansione che
allega `Document` e marca `signed` in modo atomico, con data dalla copia cartacea) resta da
implementare: richiede action+route `registerSignature`, estensione enum `documents.type`
(`signed_contract`) e inoltro della data dal form — tutti gap circoscritti, nessuna nuova
tabella necessaria.
