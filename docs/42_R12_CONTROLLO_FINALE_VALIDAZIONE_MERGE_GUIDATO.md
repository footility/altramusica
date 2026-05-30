# 42 — R12 · Controllo finale — Merge guidato studenti/genitori/corsi con log

> Attività Footility **#8534** — *R12 · Merge guidato studenti/genitori/corsi con log* (Gestionale Altramusica).
> Esito: **REPORT con BLOCCO parziale** (coerente con #8532/#8533). Validata la **semantica del merge** come *spec eseguibile* sul **dato vivo** e sullo **schema reale** — ribaltamento FK, dedup pivot, archiviazione soft-delete, anteprima dry-run, log con snapshot, reversibilità entro N giorni, estensione a tutori e corsi. L'E2E su controller/UI **non è eseguibile**: i merge service, la tabella `merge_logs`, le colonne `merged_into_id` e le rotte sono **assenti** (doc 39 §10, esplicitamente fuori scope R12).
> Riferimenti: design [`41_UX_MERGE_GUIDATO_E_LOG_REVERSIBILE.md`](41_UX_MERGE_GUIDATO_E_LOG_REVERSIBILE.md). Approfondisce il §3 del doc 39 ([`39_…`](39_UX_PANNELLO_QUALITA_DATI_E_ANOMALIE.md), #8532) e il §1/§4 del doc 40 ([`40_…`](40_R12_CONTROLLO_FINALE_VALIDAZIONE_QUALITA_DATI_ANOMALIE.md), #8533, che già elencava `StudentMergeService` come item di fase sviluppo).
> Test: [`tests/Feature/MergeGuidatoReversibileE2EValidationTest.php`](../tests/Feature/MergeGuidatoReversibileE2EValidationTest.php) — **15/15 PASS**, suite **155/155**.

---

## 0. Sintesi

Il merge è l'unica azione del pannello qualità dati che *distrugge* informazione, quindi la più delicata. La parte rischiosa — **ribaltare correttamente tutte le FK senza violare i vincoli, archiviare invece di cancellare, e poter tornare indietro** — è stata **validata sullo schema reale** come spec eseguibile. L'implementazione vera e propria (servizi + tabella log + rotte) **non è parte di R12** (doc 39 §10). Il controllo finale fa due cose oneste:

1. **Valida la semantica del merge** sul dato vivo: anteprima sola-lettura, ribaltamento FK di tutte le relazioni dell'assorbito, dedup del pivot `student_guardian` sul vincolo `unique`, archiviazione via `SoftDeletes`, log con snapshot, revert entro la finestra (e blocco oltre), atomicità (rollback su errore), più l'estensione a **tutori** e **corsi**.
2. **Fotografa il blocco**: assert che merge service / `merge_logs` / `merged_into_id` / rotte del §8 del design sono assenti → registro vivo del gap.

---

## 1. BLOCCO — cosa manca per un E2E reale (fase implementazione, non R12)

| Componente | Stato | Coperto da assert |
| --- | --- | --- |
| `StudentMergeService` (`preview`/`merge`, transazione, log) | ❌ assente | `test_blocco_merge_services_assenti` |
| `GuardianMergeService` | ❌ assente (+ manca prerequisito SoftDeletes su `Guardian`) | `test_blocco_merge_services_assenti`, `test_blocco_colonne_merged_into_assenti` |
| `CourseMergeService` (o `MergeService` generico) | ❌ assente | `test_blocco_merge_services_assenti` |
| Tabella **`merge_logs`** (`entity`/`keep_id`/`absorb_id`/`snapshot`/`performed_by`/`performed_at`/`revertible_until`) | ❌ assente | `test_blocco_tabella_merge_logs_assente` |
| Colonne **`merged_into_id`** su `students`/`guardians`/`courses` | ❌ assenti | `test_blocco_colonne_merged_into_assenti` |
| **`SoftDeletes` (`deleted_at`) su `guardians`** per archiviare un tutore | ❌ assente | `test_blocco_colonne_merged_into_assenti` |
| Rotte/azioni **`merge.preview` / `merge` / `merge.revert`** | ❌ assenti (esiste solo `admin.dashboard`) | `test_blocco_rotte_merge_assenti` |

> Tutti riconducibili al doc 39 §10 e al doc 41 §8 ("Impatti tecnici — NON parte di R12").

---

## 2. VALIDATO — la semantica del merge regge sullo schema reale

| Aspetto (doc 41) | Cosa dimostra il test | Test |
| --- | --- | --- |
| **Substrato FK** (§3) | tutte le 10 relazioni 1:N dell'allievo + pivot + offerte esistono a schema | `test_substrato_relazioni_student_presenti` |
| **Archiviazione** (§0.3/§3) | `SoftDeletes` c'è su `Student` e `Course`, **non** su `Guardian` (gap noto) | `test_substrato_softdeletes_per_archiviazione` |
| **Anteprima dry-run** (§2) | conta cosa si sposta/archivia **senza scrivere** (nessuna riga toccata) | `test_anteprima_conta_senza_scrivere` |
| **Ribaltamento FK + archivia** (§3) | iscrizioni/contratti/fatture/anni passano al mantenuto, assorbito soft-deleted (non sparito) | `test_merge_ribalta_tutte_le_fk_e_archivia_lassorbito` |
| **Dedup pivot** (§0.7/§3) | tutore condiviso → legame doppio scartato, niente violazione `unique(student_id,guardian_id)` | `test_merge_dedup_pivot_tutori_condivisi` |
| **Log decisione** (§4) | chi/quando/keep→absorb/snapshot + finestra = performed_at + N giorni | `test_log_decisione_contiene_chi_quando_e_finestra` |
| **Reversibile entro N gg** (§5) | revert ripunta le FK all'assorbito e lo ri-attiva; keep torna vuoto | `test_reversibile_entro_n_giorni_ripristina_tutto` |
| **Definitivo oltre la finestra** (§0.5/§5) | revert con `revertible_until` scaduto → bloccato | `test_revert_oltre_la_finestra_e_bloccato` |
| **Atomicità** (§0.6) | errore a metà → rollback totale, nessun merge parziale | `test_merge_e_atomico_rollback_su_errore` |
| **Estensione tutori** (§3/§6) | merge `guardian_id` sul pivot con dedup sui figli condivisi | `test_merge_tutori_ripunta_pivot_con_dedup` |
| **Estensione corsi** (§3/§6) | ripunta `course_offerings.course_id` (FK `restrict`), poi soft-delete del corso | `test_merge_corsi_ripunta_offerte_e_archivia` |

> La logica del merge vive nel test come **spec eseguibile** (`previewStudentMerge`/`mergeStudents`/`revertStudentMerge`): quando i servizi verranno implementati basterà spostarla nei service mantenendo gli stessi risultati.

---

## 3. Scoperte rilevanti per l'implementazione

1. **`Guardian` non ha `SoftDeletes`.** `Student` e `Course` sì; il tutore no. Per "archivia, non cancella" (doc 39 §3) un merge di tutori richiede **prima** una migrazione che aggiunga `deleted_at` (+ `merged_into_id`) a `guardians`. Senza, l'unica via sarebbe l'hard-delete — vietato dal principio.
2. **Vincolo `unique(student_id, guardian_id)` sul pivot.** Ribaltare ingenuamente il pivot fa esplodere il merge quando le due schede condividono un tutore (caso comune: stessa mamma su entrambe). Il merge **deve** deduplicare: il legame già presente sul mantenuto si scarta, non si sposta. Coperto da `test_merge_dedup_pivot_tutori_condivisi`.
3. **`course_offerings.course_id` è `onDelete('restrict')`.** Un corso con offerte non si può archiviare/eliminare prima di aver ripuntato le offerte. L'ordine corretto è: ripunta `course_offerings` → poi soft-delete del corso. Le iscrizioni seguono l'offerta (`hasManyThrough`), quindi non vanno toccate.
4. **Lo snapshot è la chiave della reversibilità.** Il log non basta che dica "spostate 3 righe": deve contenere **gli id** delle righe mosse per poterle ripuntare all'assorbito senza ambiguità. Il revert è quindi una funzione dello snapshot, non una "ricostruzione euristica".
5. **Finestra come `Setting`, non costante.** "N giorni" va parametrizzato (`merge.revertible_days`, default 30) e collegato al job di retention che pota gli snapshot scaduti — riusa l'infrastruttura retention introdotta in `add_privacy_consent_and_retention_fields`.

---

## 4. Ordine di implementazione suggerito (fase sviluppo)

1. **Migrazioni**: `merge_logs` (struttura doc 41 §4); `merged_into_id` su `students`/`guardians`/`courses`; `SoftDeletes` su `guardians`.
2. **`StudentMergeService`**: `preview()` (sola lettura) + `merge()` (transazione, ribalta le 10 relazioni + pivot con dedup, soft-delete + `merged_into_id`, scrive `merge_logs` con snapshot) + `revert()` (entro `revertible_until`).
3. **`GuardianMergeService`** e **`CourseMergeService`** (o `MergeService` generico parametrico) riusando lo stesso scheletro.
4. **`Setting`** `merge.revertible_days` + job retention che pota gli snapshot scaduti.
5. **Controller/rotte** `merge.preview`/`merge`/`merge.revert` sotto policy segreteria/admin, innestati dal pannello qualità dati (doc 39 §3).
6. **Viste**: confronto/anteprima (doc 41 §2), elenco unioni recenti con "Annulla unione" (§5).

---

## 5. Esito

**REPORT con BLOCCO parziale.** La semantica del merge guidato — anteprima, ribaltamento FK, dedup pivot, archiviazione reversibile, log con snapshot, finestra di N giorni, atomicità, estensione a tutori e corsi — è **validata** sullo schema reale (15 test, suite 155/155 verde). L'E2E su UI/controller **resta bloccato** dall'assenza di servizi/tabella `merge_logs`/`merged_into_id`/rotte (§1), tutto fuori scope R12 per il doc 39 §10. Nessuna implementazione effettuata: il controllo finale produce un **registro vivo del gap** + cinque scoperte di schema (§3, su tutte: `Guardian` senza SoftDeletes e il vincolo unique del pivot) che orienteranno l'implementazione quando verrà schedulata.
