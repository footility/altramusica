# 45 — R12 · Validazioni preventive (warning) + checklist chiusura migrazione

> Attività Footility **#8535** — *R12 · Validazioni preventive (warning) + checklist chiusura migrazione* (Gestionale Altramusica).
> Esito: **REPORT con BLOCCO parziale** (coerente con #8532/#8533/#8534/#8551). R12 è **solo design**: le validazioni preventive e la checklist di chiusura migrazione sono **specificate e validate sul dato vivo**, ma lo **strato applicativo** (scanner, comando, report persistito, pannello) **non esiste** e non è parte di R12.
> Riferimenti design: [`39_…`](39_UX_PANNELLO_QUALITA_DATI_E_ANOMALIE.md) (§0.1 «Segnalare, non bloccare»), [`41_…`](41_UX_MERGE_GUIDATO_E_LOG_REVERSIBILE.md). Controllo ombrello R12: [`43_…`](43_R12_CONTROLLO_FINALE_VALIDAZIONE_QUALITA_DATI.md). Motore: [`app/Services/OdsImportService.php`](../app/Services/OdsImportService.php).
> Test: [`tests/Feature/ValidazioniPreventiveChecklistChiusuraMigrazioneE2EValidationTest.php`](../tests/Feature/ValidazioniPreventiveChecklistChiusuraMigrazioneE2EValidationTest.php) — **10/10 PASS**.
> Trascrizioni: parte 1 r.116-122 (dati sporchi / CF ripetuti), 454-474 (pulizia anagrafiche).

---

## 0. Sintesi

L'attività chiede due cose, complementari e distinte da #8551 (che fotografava la mancanza del *surfacing* del warning):

1. **Validazioni preventive minime come warning** — l'insieme **minimo** di regole che, *al salvataggio*, dovrebbero **segnalare** un dato debole **senza mai bloccare** (§0.1 del design). Qui le verifico **sul flusso reale** (`POST store`): le regole preventive sono effettivamente **di livello warning** — il dato sporco entra sempre, nessun 422.
2. **Checklist di chiusura migrazione** — l'insieme di **gate verificabili sul dato vivo** che certifica «la migrazione ODS → gestionale è di qualità sufficiente per il go-live». La checklist è **eseguibile** (riusa il motore CF di `OdsImportService`): semino un dataset sporco realistico e dimostro che **ogni gate rileva** e che il **verdetto di certificabilità** è corretto.

E un terzo pezzo onesto, come i fratelli R12:

3. **Fotografa il blocco** — `DataQualityScanner` / `MigrationClosureService`, il comando artisan di chiusura, la tabella `migration_closure_reports` e le rotte/voce di menu **sono assenti**. La logica c'è (qui, nel test), il guscio applicativo no.

---

## 1. VALIDATO — Catalogo regole preventive (tutte WARNING, mai bloccanti)

Verificato via **POST ai controller `store`** (utente `admin`, `acl:sync`). Ogni regola è **preventiva** (scatta al salvataggio) ma **non bloccante** (warning):

| Regola preventiva | Campo reale | Comportamento atteso | Test |
| --- | --- | --- | --- |
| **CF deve essere valido** (`TAX_CODE_REGEX`) | `students.tax_code` | CF malformato accolto, segnalato | `test_validazione_preventiva_cf_non_valido_non_blocca_il_save` |
| **Minore deve avere un tutore** | `students.birth_date` + pivot `student_guardian` | minore senza tutore accolto, segnalato | `test_validazione_preventiva_minore_senza_tutore_non_blocca_il_save` |
| **Tutore deve avere consenso/contatti** | `guardians.privacy_consent`, `cell_*`/`email_*` | tutore senza consenso/contatti accolto | `test_catalogo_regole_preventive_sono_tutte_warning_mai_blocking` |

> Conferma a codice: le `validate()` dei `store` sono **permissive by design** (CF `nullable|string|max:16`, niente regex/unique; `birth_date` nullable; `privacy_consent` booleano incluso `false`). Le regole preventive **non possono essere bloccanti** senza riscrivere i controller → coerente con §0.1.

---

## 2. VALIDATO — Checklist di chiusura migrazione (gate eseguibili sul dato vivo)

La checklist distingue **gate strutturali** (rompono l'integrità → **bloccano** la certificazione) da **gate warning** (qualità residua → **annotano**, non bloccano, §0.1). Tutti calcolati sul **dato vivo** riusando `cleanTaxCode` + `TAX_CODE_REGEX` dell'import («pulito nella checklist = pulito all'import»).

| Gate | Classe | Cosa rileva | Sorgente |
| --- | --- | --- | --- |
| **G1** allievi senza anno | 🔴 strutturale | `Student::doesntHave('years')` (import parziale) | schema |
| **G9** fatture senza righe | 🔴 strutturale | `Invoice::doesntHave('items')` (orfano §6) | schema |
| **G10** contratti orfani | 🔴 strutturale | contratto con `student_id` non risolvibile | schema |
| **G2** CF assente | 🟡 warning | `tax_code` null/vuoto | import |
| **G3** CF non valido | 🟡 warning | `!TAX_CODE_REGEX(cleanTaxCode)` | import |
| **G4** CF duplicato | 🟡 warning | stesso CF su nominativi diversi (`$duplicateCfs`) | import |
| **G5** omonimi | 🟡 warning | stesso nome+cognome su più record | import |
| **G6** minore senza tutore | 🟡 warning | età < 18 e zero tutori | schema |
| **G7** tutore senza contatti | 🟡 warning | nessun `cell_*`/`email_*` | schema/GDPR |
| **G8** tutore senza consenso | 🟡 warning | `privacy_consent` false/null | schema/GDPR |

**Verdetto** (`isCertificabile`): la migrazione è **certificabile sse tutti i gate strutturali sono a zero**. I warning **non bloccano** mai — sono debito qualità residuo da mostrare, in linea con «segnalare, non bloccare».

Test che lo dimostrano:

- `test_checklist_rileva_warning_su_dataset_sporco` — su dataset sporco seminato, G2..G8 contano correttamente (4 CF assenti, ≥1 non valido, 1 CF duplicato, 1 gruppo omonimi, 1 minore senza tutore, tutore senza contatti/consenso).
- `test_checklist_rileva_gate_strutturali_orfani` — G1 (allievo senza anno) e G9 (fattura senza righe) = 1 ciascuno.
- `test_verdetto_certificabile_con_solo_warning` — solo warning aperti ⇒ **certificabile**.
- `test_verdetto_non_certificabile_con_gate_strutturale_aperto` — un solo orfano strutturale ⇒ **NON certificabile**.
- `test_loop_risolvi_orfano_poi_certificabile` — risolto l'orfano (aggancio l'anno) ⇒ **ri-certificabile** (loop segnala → risolvi → ri-certifica).

### Scoperta — la FK su `contracts.student_id` garantisce G10 a zero

Provando a inserire un contratto «dangling» (verso un allievo inesistente), lo **schema vivo lo rifiuta** (`FOREIGN KEY constraint failed`). Quindi il gate G10, per il caso «id inesistente», è **garantito a zero dal DB**: resta una regola dello scanner solo per casi non FK-protetti (es. allievo archiviato/soft-deleted in futuro). Documentato in `test_checklist_rileva_gate_strutturali_orfani`.

---

## 3. BLOCCO — manca il guscio applicativo (scanner + chiusura + report)

La **logica** della checklist è qui ed è verde; manca tutto ciò che la rende un prodotto:

| Componente (design §10) | Stato | Coperto da assert |
| --- | --- | --- |
| `App\Services\DataQualityScanner` (sola lettura, ruleset condiviso) | ❌ assente | `test_blocco_scanner_e_report_chiusura_assenti` |
| `App\Services\MigrationClosureService` (calcolo gate + verdetto) | ❌ assente | `test_blocco_scanner_e_report_chiusura_assenti` |
| Comando artisan `migration:closure` / `quality:certify` | ❌ assente | `test_blocco_scanner_e_report_chiusura_assenti` |
| Tabella `migration_closure_reports` (report persistito/firmato) | ❌ assente | `test_blocco_scanner_e_report_chiusura_assenti` |
| Rotte `admin.data-quality.*` / `admin.migration-closure.*` + voce menu | ❌ assenti (solo `admin.dashboard`) | `test_blocco_rotte_pannello_qualita_assenti` |

---

## 4. Cosa serve per chiudere (quando R12 passerà in implementazione)

1. **`DataQualityScanner`**: estrae i 10 gate di §2 come ruleset condiviso (riusa `OdsImportService`).
2. **`MigrationClosureService`**: calcola la checklist + il verdetto `certificabile` (strutturali a zero) e **persiste** un report datato/firmato in `migration_closure_reports`.
3. **Comando** `php artisan migration:closure` per la certificazione in CI/console.
4. **Surfacing** (vedi #8551): warning inline al salvataggio + badge «Qualità dati» + pannello `admin.data-quality.*` con la checklist e il verdetto.

> Nota: §2 ha già la **forma eseguibile** della checklist (`closureChecklist()` nel test). Il porting a `MigrationClosureService` è meccanico — la semantica è già validata sul dato vivo.
