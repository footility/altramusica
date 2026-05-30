# 40 — R12 · Controllo finale — Report anomalie su tutte le entità

> Attività Footility **#8533** — *R12 · Report anomalie su tutte le entità* (Gestionale Altramusica).
> Esito: **REPORT con BLOCCO parziale**. Validate le **regole di rilevamento** del design sul **dato vivo** (record, non righe ODS) per le quattro categorie richieste — duplicati (CF, nome+cognome), omonimi, campi critici mancanti, record orfani (contratto senza iscrizione, fattura senza righe, …) — e dimostrato il **riuso del motore** di `OdsImportService`. L'E2E su controller/UI del pannello **non è eseguibile** perché ruleset condiviso, scanner, controller, merge service e tabella eccezioni sono assenti (vedi §1), come dichiarato dal §10 del design (fuori scope R12).
> Riferimenti: design [`39_UX_PANNELLO_QUALITA_DATI_E_ANOMALIE.md`](39_UX_PANNELLO_QUALITA_DATI_E_ANOMALIE.md) (#8532). Riusa anagrafiche di R1 [`20_…`](20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md), riconciliazione di R5 [`36_…`](36_UX_RICONCILIAZIONE_MANUALE_E_IMPORT_CSV.md), motore anomalie di [`app/Services/OdsImportService.php`](../app/Services/OdsImportService.php).
> Test: [`tests/Feature/QualitaDatiAnomalieE2EValidationTest.php`](../tests/Feature/QualitaDatiAnomalieE2EValidationTest.php) — **25/25 PASS**, suite **140/140**.
> Trascrizioni: parte 1 r.116-122 (dati sporchi / CF ripetuti), 454-474 (pulizia anagrafiche).

---

## 0. Sintesi

R12 (#8532, doc 39) è **solo design**: il §10 "Impatti tecnici" dichiara esplicitamente che l'implementazione **non è parte di R12**. La parte difficile — il **motore di rilevamento anomalie** — **esiste già** in `OdsImportService` (`detectRowAnomalies`, indici `$duplicateCfs`/`$homonymNames`, `cleanTaxCode`, `TAX_CODE_REGEX`), ma vive **solo durante l'import ODS** sulle righe del foglio. Mancano: il **ruleset condiviso** estratto da quel servizio, lo **scanner** che lo fa girare sul dato vivo, l'estensione a **orfani**/**completezza campi**, la **vista**, le **azioni guidate** (merge con anteprima/log) e la tabella **eccezioni**.

Per questo un E2E su controller/UI come nelle R3/R6/R7 **non è possibile** (= il *"blocco"*). Il controllo finale fa quindi due cose oneste:

1. **Valida le regole di rilevamento** sul **dato vivo** (record `Student`/`Guardian`/`Contract`/`Invoice`/…), sulle quattro categorie del design, e **dimostra il riuso** del motore esistente (la validazione CF del pannello è *identica* a quella dell'import via reflection su `cleanTaxCode` + `TAX_CODE_REGEX`).
2. **Fotografa il blocco**: assert che ruleset/scanner/controller/merge service/tabella eccezioni/rotte del §10 sono assenti → registro vivo del gap da colmare.

---

## 1. BLOCCO — cosa manca per un E2E reale (fase implementazione, non R12)

| Componente | Stato | Coperto da assert |
| --- | --- | --- |
| `App\Services\DataQualityRuleset` (regole estratte da `OdsImportService`, fonte unica) | ❌ assente (il motore-sorgente `OdsImportService` invece **c'è**) | `test_blocco_data_quality_ruleset_assente` |
| `App\Services\DataQualityScanner` (`duplicates`/`homonyms`/`missingCriticalFields`/`orphans`, sola lettura) | ❌ assente (qui solo come **spec eseguibile** nel test) | `test_blocco_data_quality_scanner_assente` |
| `App\Services\StudentMergeService::merge($keep,$absorb)` (ribalta FK, archivia, **log reversibile**) | ❌ assente | `test_blocco_student_merge_service_assente` |
| `Admin\DataQualityController` (lista + azioni merge/archivia/ignora) | ❌ assente | `test_blocco_data_quality_controller_assente` |
| Tabella eccezioni **`data_quality_dismissals`** (`anomaly_type`/`entity_key`/`dismissed_by`/…) | ❌ assente | `test_blocco_tabella_dismissals_assente` |
| Rotte/voce menu **"Qualità dati ▸ Anomalie"** + badge header | ❌ assenti (esiste solo `admin.dashboard` operativa) | `test_blocco_rotte_qualita_dati_assenti` |
| Viste (lista, confronto/unione duplicati, completamento campi, orfani) | ❌ assenti | — |

> Tutti riconducibili al doc 39 §10 ("Impatti tecnici — NON parte di questo R12").

---

## 2. VALIDATO — le regole di rilevamento reggono sul dato vivo

| Categoria (doc 39) | Cosa dimostra il test | Test |
| --- | --- | --- |
| **Riuso motore** (§0.6/§10) | `detectRowAnomalies`/`cleanTaxCode`/`TAX_CODE_REGEX` esistono in `OdsImportService` | `test_motore_anomalie_esiste_in_ods_import_service` |
| **Validazione CF = import** (§5) | stesso `cleanTaxCode` + regex: "pulito nel pannello = pulito all'import" (spazi/minuscolo tollerati; corti/malformati/assenti scartati) | `test_validazione_cf_identica_allimport` |
| **Duplicati per CF** (§3) | `Student` raggruppati per CF normalizzato, >1 → possibile duplicato (anche se scritto in modo diverso) | `test_duplicati_per_codice_fiscale` |
| **Duplicati per nome+data** (§3) | stesso `first_name`+`last_name`+`birth_date` → quasi certamente stessa persona; data diversa **non** è duplicato | `test_duplicati_per_nome_cognome_e_data` |
| **Nessun falso positivo** (§3) | CF e nomi distinti → zero duplicati | `test_nessun_duplicato_quando_dati_distinti` |
| **Omonimi** (§4) | stesso nome ma CF/data **diversi** → due persone (non duplicato) | `test_omonimi_stesso_nome_dati_diversi` |
| **Escalation a duplicato** (§3/§4) | se CF/data **coincidono**, non è "solo omonimia": sale a duplicato | `test_omonimi_con_dati_coincidenti_sono_duplicato` |
| **🔴 Minore senza tutore fatturazione** (§5) | minorenne (age<18) senza alcun `student_guardian.is_billing_contact`; il maggiorenne non è flaggato | `test_critico_minore_senza_tutore_fatturazione` |
| **🔴 Nessun contatto** (§5) | tutore senza alcuna `cell_*`/`email_*` → comunicazione impossibile (R9) | `test_critico_nessun_contatto_su_tutore` |
| **🔴 Consenso privacy mancante** (§5, GDPR) | `Guardian.privacy_consent` false/null isolato dai consenzienti | `test_critico_consenso_privacy_mancante` |
| **🟠 CF assente/invalido + data nascita mancante** (§5) | CF non conforme alla regola import e `birth_date` nullo | `test_critico_cf_assente_o_invalido_e_data_mancante` |
| **Orfano: contratto senza iscrizione** (§6, attività) | `Contract` il cui allievo ha 0 `Enrollment` | `test_orfano_contratto_senza_iscrizione` |
| **Orfano: fattura senza righe** (§6, attività) | `Invoice` senza alcun `InvoiceItem` | `test_orfano_fattura_senza_righe` |
| **Orfano: allievo senza anno** (§6) | `Student` senza alcuno `StudentYear` | `test_orfano_allievo_senza_anno` |
| **Orfano: tutore senza allievi** (§6) | `Guardian` con zero righe `student_guardian` | `test_orfano_tutore_senza_allievi` |
| **Orfano + soft-delete** (§3/§6) | allievo **archiviato** → falso orfano se naive; scanner corretto usa `withTrashed()` | `test_orfano_contratto_riferimento_allievo_e_soft_delete` |
| **Gravità a 3 livelli** (§0.4/§2) | 🔴 critica → 🟠 da sistemare → 🟡 cosmetica ordina la lista (si lavora dall'alto) | `test_anomalie_ordinate_per_gravita` |
| **Stato pulito** (§8) | dati corretti → zero anomalie su tutte le categorie | `test_stato_pulito_nessuna_anomalia` |
| **Substrato** (§1) | tutte le entità scansionate esistono a schema | `test_substrato_entita_presenti` |

> Lo **scanner** del design (`DataQualityScanner`) è esercitato come **spec eseguibile** dentro il test (metodi privati `duplicatesByTaxCode`/`duplicatesByNameBirth`/`homonyms`/`taxCodeIsValid` + query orfani): quando verrà implementato, basterà spostare quella logica nel servizio mantenendo gli stessi risultati.

---

## 3. Scoperte rilevanti per l'implementazione

1. **SoftDeletes ⇒ falsi orfani.** `Student` e `Contract` usano `SoftDeletes`, e il design §3/§6 chiede esplicitamente "archivia, non cancella". Conseguenza: un allievo **archiviato** rende i suoi contratti/fatture "orfani" sotto una `whereDoesntHave('student')` ingenua. Lo scanner **deve** usare `withTrashed()` (o uno stato dedicato "genitore archiviato") per non scommerciare falsi positivi sugli archiviati. Test: `test_orfano_contratto_riferimento_allievo_e_soft_delete`.
2. **FK cascade ⇒ dangling impossibile dai flussi normali.** `contracts.student_id`, `payments.invoice_id`, ecc. sono `constrained()->onDelete('cascade')`/`restrict`: un riferimento realmente *dangling* non nasce dai percorsi applicativi, **solo** da import parziali/migrazioni legacy. La query di rilevamento resta la stessa (`whereDoesntHave`), ma il caso "puntatore al nulla" è raro e di origine esterna.
3. **"Pagamento senza fattura" non esiste a schema.** `payments.invoice_id` è obbligatorio e vincolato → l'orfano §6 "pagamento senza piano/fattura" del design non è materializzabile come *dangling* qui; l'orfano realistico è semmai **`PaymentPlan`/`Invoice` senza pagamento** (credito sospeso, rimanda a R5), non il pagamento stesso.
4. **Validazione CF unica, già pronta.** `cleanTaxCode` + `TAX_CODE_REGEX` sono protetti ma riusabili: l'estrazione in `DataQualityRuleset` è meccanica, senza riscrivere parsing. È la conferma operativa del principio §0.6 ("pulito qui = pulito all'import").
5. **Campi indirizzo/contatto allievo vivono nelle note.** `Student` non ha colonne `email`/`phone`/`address`: l'import li confluisce nelle note dell'anno (vedi `normalizeStudentData`). I contatti "veri" stanno sul `Guardian` → la completezza-contatto si valuta sul tutore primario, non sull'allievo (coerente col test).

---

## 4. Ordine di implementazione suggerito (fase sviluppo)

1. **`DataQualityRuleset`**: estrai `detectRowAnomalies` + `cleanTaxCode` + `TAX_CODE_REGEX` + indici `$duplicateCfs`/`$homonymNames` da `OdsImportService` in un servizio condiviso; rifattorizza l'import perché lo usi (regressione coperta da `OdsImportServiceTest`).
2. **`DataQualityScanner`** (sola lettura): `duplicates()`/`homonyms()`/`missingCriticalFields()`/`orphans()` con `withTrashed()` dove serve (§3.1); query aggregate + conteggio cache-abile per il badge.
3. **`data_quality_dismissals`** + logica eccezioni con **chiave stabile** (es. `student:142|388`) e decadenza se il dato cambia (§7).
4. **`StudentMergeService::merge`**: ribalta FK su `$keep`, archivia `$absorb` (soft-delete + `merged_into_id`), **log reversibile**, in transazione (§3, §10).
5. **`Admin\DataQualityController`** + rotte sotto policy segreteria/admin; **badge header**.
6. **Viste**: lista per gravità, confronto/unione duplicati con anteprima, completamento campo a fuoco, orfani (ricollega/archivia/ignora).

---

## 5. Esito

**REPORT con BLOCCO parziale.** Le regole di rilevamento del design sono **validate** sul dato vivo per tutte e quattro le categorie (25 test, suite 140/140 verde), col **riuso dimostrato** del motore di `OdsImportService`. L'E2E su UI/controller del pannello **resta bloccato** dall'assenza di ruleset/scanner/controller/merge service/tabella eccezioni (§1), tutto riconducibile al doc 39 §10 esplicitamente fuori scope per R12. Nessuna implementazione effettuata: il controllo finale produce un **registro vivo del gap** — più tre scoperte di schema (SoftDeletes/cascade/payment, §3) che orienteranno l'implementazione quando verrà schedulata.
