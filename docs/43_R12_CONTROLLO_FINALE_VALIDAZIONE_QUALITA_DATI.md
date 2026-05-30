# 43 — R12 · Controllo finale OMBRELLO — Validazione qualità dati

> Attività Footility **#8551** — *R12 · Controllo finale — Validazione qualità dati* (Gestionale Altramusica).
> Esito: **REPORT con BLOCCO parziale** (coerente con #8532/#8533/#8534). Questo è il **controllo finale ombrello** di R12: consolida i due sotto-controlli già eseguiti — **rilevamento anomalie** (#8533, doc 40) e **merge guidato reversibile** (#8534, doc 42) — e **aggiunge l'angolo non ancora coperto** richiesto dall'attività: le **VALIDAZIONI WARNING**, cioè il principio cardine §0.1 del design *"Segnalare, NON bloccare"*, verificato sui **flussi operativi reali** (POST ai controller `store`).
> Riferimenti: design [`39_…`](39_UX_PANNELLO_QUALITA_DATI_E_ANOMALIE.md) (#8532), [`41_…`](41_UX_MERGE_GUIDATO_E_LOG_REVERSIBILE.md). Sotto-controlli: [`40_…`](40_R12_CONTROLLO_FINALE_VALIDAZIONE_QUALITA_DATI_ANOMALIE.md), [`42_…`](42_R12_CONTROLLO_FINALE_VALIDAZIONE_MERGE_GUIDATO.md). Motore: [`app/Services/OdsImportService.php`](../app/Services/OdsImportService.php).
> Test: [`tests/Feature/ValidazioneQualitaDatiE2EValidationTest.php`](../tests/Feature/ValidazioneQualitaDatiE2EValidationTest.php) — **10/10 PASS**, suite **165/165**.
> Trascrizioni: parte 1 r.116-122 (dati sporchi / CF ripetuti), 454-474 (pulizia anagrafiche).

---

## 0. Sintesi

L'attività #8551 chiede l'E2E di tre cose: **report anomalie** (duplicati/omonimi/orfani), **merge guidato** studenti, **validazioni warning**. Le prime due hanno già un controllo finale dedicato e verde:

| Sotto-controllo | Attività | Doc | Test | Stato |
| --- | --- | --- | --- | --- |
| Rilevamento anomalie (duplicati CF/nome, omonimi, campi critici, orfani) | #8533 | [40](40_R12_CONTROLLO_FINALE_VALIDAZIONE_QUALITA_DATI_ANOMALIE.md) | `QualitaDatiAnomalieE2EValidationTest` (25) | ✅ validato |
| Merge guidato reversibile (FK/dedup/archivia/log/revert, +tutori/corsi) | #8534 | [42](42_R12_CONTROLLO_FINALE_VALIDAZIONE_MERGE_GUIDATO.md) | `MergeGuidatoReversibileE2EValidationTest` (15) | ✅ validato |

Questo controllo ombrello **non li riscrive**: li àncora (test `test_ombrello_sotto_validazioni_8533_e_8534_presenti`) e copre il **terzo pezzo**, finora implicito — le **validazioni warning** del principio §0.1.

**§0.1 "Segnalare, non bloccare"** (doc 39): *un dato sporco non deve impedire di iscrivere un allievo o registrare un pagamento oggi; salvataggi e flussi operativi restano permissivi (warning inline, mai errore bloccante).* È l'**opposto di una validazione rigida**. Questo controllo lo verifica **sul flusso reale** (HTTP POST ai `store`, non a livello di model), che è esattamente la dimensione "E2E warning" mancante ai due sotto-controlli (entrambi a livello di regola/semantica).

Il controllo ombrello fa quindi tre cose oneste:

1. **Consolida**: dichiara verdi e collegati i due sotto-controlli #8533/#8534.
2. **Valida le validazioni-warning** (§0.1): i salvataggi operativi **accettano dato anomalo senza errore bloccante**, e lo stesso dato resta **rilevabile** dopo (loop "segnala → rileva", il debito non è perso).
3. **Fotografa il blocco**: lo **strato di surfacing** del warning (avviso inline al salvataggio + badge "Qualità dati") è **assente** — oggi il save è permissivo ma *silenzioso*.

---

## 1. VALIDATO — §0.1 "Segnalare, non bloccare" sui flussi reali

Verificato via **POST ai controller `store`** (utente `admin`, `acl:sync`), non a livello di model: è il flusso che userebbe la segreteria.

| Caso (doc 39) | Cosa dimostra il test | Test |
| --- | --- | --- |
| **CF assente + data nascita mancante** (§0.1/§5) | `POST students` con `tax_code=null`, `birth_date=null` → **nessun 422**, redirect, record salvato | `test_store_studente_accetta_cf_assente_e_data_mancante` |
| **CF non valido** (§5) | un CF che l'import scarterebbe (`cleanTaxCode`+`TAX_CODE_REGEX`) entra lo stesso | `test_store_studente_accetta_cf_non_valido` |
| **CF duplicato** (§3) | due allievi con lo **stesso CF** salvati entrambi (nessun `unique` che blocchi) **e** il duplicato resta rilevabile | `test_store_studente_non_blocca_cf_duplicato` |
| **Minore senza tutore** (vincolo §0.1) | un minore si registra **senza alcun tutore**; l'anomalia lo ricorderà dopo | `test_store_minore_senza_tutore_non_e_bloccato` |
| **Tutore senza contatti e senza consenso** (§5, GDPR) | `POST guardians` con zero `cell_*`/`email_*` e `privacy_consent=false` → salvato | `test_store_tutore_accetta_nessun_contatto_e_senza_consenso` |
| **Fattura senza righe** (§6, orfano) | `POST invoices` senza `InvoiceItem` → fattura creata (orfano possibile, non impedito) | `test_store_fattura_non_richiede_righe` |
| **Loop "segnala → rileva"** (§0.1) | dato sporco salvato **via HTTP** → poi rilevato dalla spec anomalie (#8533): CF non valido + duplicato | `test_dato_sporco_salvato_via_http_resta_rilevabile_come_anomalia` |

> Conferma a codice: le regole `validate()` dei `store` sono **permissive by design** — `tax_code` è `nullable|string|max:16` (**niente regex, niente unique**), `birth_date`/`age` nullable, contatti tutore nullable, `privacy_consent` booleano (anche `false`), nessun vincolo "fattura deve avere righe". Il dato sporco è **accolto e raccolto come debito**, non respinto.

---

## 2. BLOCCO — manca lo STRATO DI SURFACING del warning

La permissività c'è già (è la metà "non bloccare"). Manca la metà **"segnalare"**: il design vuole un **warning inline al salvataggio** (§0.1) e un **badge "Qualità dati"** (§2). Oggi il save di dato sporco è permissivo ma **silenzioso**.

| Componente (doc 39) | Stato | Coperto da assert |
| --- | --- | --- |
| **Warning inline** al salvataggio di dato anomalo (flash `warning`/`data_quality_warnings`) | ❌ assente — oggi solo flash `success` | `test_blocco_warning_inline_al_salvataggio_assente` |
| **Badge/voce "Qualità dati ▸ Anomalie"** + rotta pannello (`admin.data-quality.*`) | ❌ assenti (esiste solo `admin.dashboard`) | `test_blocco_rotte_qualita_dati_assenti` |
| `App\Services\DataQualityScanner` (sola lettura sul dato vivo) | ❌ assente | `test_blocco_rotte_qualita_dati_assenti` |
| Ruleset condiviso / merge service / tabella eccezioni / `merge_logs` / `merged_into_id` | ❌ assenti | doc 40 §1 e doc 42 §1 (registro già aperto) |

> Tutto riconducibile al **doc 39 §10** ("Impatti tecnici — NON parte di R12"). Coerente con i blocchi già fotografati da #8533 (scanner/ruleset/controller) e #8534 (merge service/`merge_logs`/`merged_into_id`/rotte).

---

## 3. Scoperte rilevanti per l'implementazione

1. **Permissività ≠ silenzio.** La metà difficile del §0.1 ("non bloccare") **è già realtà** nelle regole `validate()`; manca la metà "segnalare". L'implementazione non deve *rendere permissivi* i flussi (lo sono già) ma **aggiungere un livello di avviso non bloccante** che giri lo stesso ruleset di rilevamento (#8533) al salvataggio e mostri un warning inline + alimenti il badge — senza toccare le regole esistenti.
2. **Bug latente nei `store`: chiave hidden mancante.** `StudentController@store` e `InvoiceController@store` accedono a `$validated['academic_year_id']` / `$validated['invoice_number']` come se ci fossero sempre, ma sono regole `nullable`: se il campo **non viene inviato** (non solo vuoto), Laravel non lo mette in `$validated` → `Undefined array key`. I form reali inviano sempre quegli hidden, quindi non emerge in produzione, ma è fragile (un POST API/headless senza quei campi va in 500). Fix sicuro: `$validated['academic_year_id'] ?? null`. Emerso scrivendo l'E2E.
3. **ACL per entità non uniforme.** `students.store` e `guardians.store` passano col solo utente autenticato; `invoices.store` richiede un ruolo (403 senza `admin`). Il pannello qualità dati, che agirà su tutte queste entità (merge/completa/archivia), dovrà passare per la **policy più restrittiva** (segreteria/admin) per coerenza.
4. **Il loop "segnala → rileva" è la prova che il debito non si perde.** Lo stesso CF sporco salvato via HTTP è ritrovato dallo scanner: è la garanzia operativa del §0.1 ("raccoglie il debito e lo rende lavorabile quando c'è tempo"). Il merge guidato (#8534) è poi l'azione che lo **chiude**.

---

## 4. Esito

**REPORT con BLOCCO parziale.** Il controllo finale ombrello di R12 è **completo e verde** (10 test nuovi, suite **165/165**): i due sotto-controlli #8533 (anomalie) e #8534 (merge) sono confermati e collegati, e la terza dimensione — le **validazioni warning** del §0.1 "Segnalare, non bloccare" — è ora **validata sui flussi operativi reali** (i `store` accettano dato anomalo senza errore bloccante e il debito resta rilevabile). L'unico **blocco** è lo **strato di surfacing** del warning (avviso inline + badge "Qualità dati") e tutta l'implementazione del pannello/merge/eccezioni — esplicitamente **fuori scope R12** per il doc 39 §10. Nessuna implementazione effettuata: il controllo produce il **verdetto consolidato** di R12 più un **bug latente** trovato nei `store` (chiave hidden mancante, §3.2) da sistemare quando l'implementazione verrà schedulata.
