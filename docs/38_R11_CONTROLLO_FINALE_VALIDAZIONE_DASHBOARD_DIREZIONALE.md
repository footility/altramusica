# 38 — R11 · Controllo finale — Validazione dashboard direzionale + bilancio sociale

> Attività Footility **#8550** — *R11 · Controllo finale — Validazione dashboard direzionale* (Gestionale Altramusica).
> Esito: **REPORT con BLOCCO parziale**. Validati i **flussi del design** (KPI iscritti + delta anno-su-anno, retention cohort, serie storica multi-anno, flusso di cassa con uscite parziali, maturato vs incassato, export bilancio sociale aggregato) a livello dati+framework; l'E2E su controller/UI direzionale **non è eseguibile** perché vista, servizio e tabelle opzionali sono assenti (vedi §1).
> Riferimenti: design [`37_UX_DASHBOARD_DIREZIONALE_E_BILANCIO_SOCIALE.md`](37_UX_DASHBOARD_DIREZIONALE_E_BILANCIO_SOCIALE.md) (#8531). Riusa dati di R4 [`25_…`](25_UX_VISTA_CONTABILITA_PER_STUDENTE.md), R5 [`36_…`](36_UX_RICONCILIAZIONE_MANUALE_E_IMPORT_CSV.md), R8 [`32_…`](32_UX_REGISTRO_PRESENZE_E_MOTORE_COMPENSI.md), base AS-IS [`11_STATISTICHE_STORICHE.md`](11_STATISTICHE_STORICHE.md).
> Test: [`tests/Feature/DashboardDirezionaleE2EValidationTest.php`](../tests/Feature/DashboardDirezionaleE2EValidationTest.php) — **20/20 PASS**, suite **115/115**.
> Trascrizioni: parte 2 r.42-54 — parte 1 r.116-122, 454-474.

---

## 0. Sintesi

R11 (#8531) è **solo design**: il §10 del doc 37 dichiara esplicitamente che l'implementazione **non è parte di R11**. Il **dato grezzo c'è quasi tutto** (`StudentYear`, `Payment`, `Invoice`, `AcademicYear` + tabella `teacher_hours` dormiente da R8), ma mancano le **aggregazioni direzionali**, la **vista dedicata** separata dall'operativa, l'**export** e le tabelle **opzionali** (`expenses` per il conto economico completo, `historical_year_stats` per lo storico pre-migrazione ODS).

Per questo un E2E su controller/UI come nelle R3/R6/R7 **non è possibile** (= il *"blocco"* previsto dalla richiesta). Il controllo finale fa quindi due cose oneste:

1. **Valida i flussi del design** sul substrato esistente: KPI iscritti + delta anno-su-anno, retention cohort (rimasti/ritirati/nuovi con somma che torna + breakdown per corso), serie storica multi-anno con media di riferimento, flusso di cassa mensile entrate (`Payment`) vs uscite **parziali** (solo compensi docenti), maturato vs incassato (credito residuo), export CSV aggregato per bilancio sociale (solo conteggi/GDPR, caveat nel file, nome file parlante).
2. **Fotografa il blocco**: assert che controller/service/tabelle opzionali/rotte del §10 sono assenti → registro vivo del gap da colmare in implementazione.

---

## 1. BLOCCO — cosa manca per un E2E reale (fase implementazione, non R11)

| Componente | Stato | Coperto da assert |
| --- | --- | --- |
| Controller `Admin\DirectorDashboardController` (vista Direzione, sola lettura, separata dall'operativa) | ❌ assente (esiste solo `DashboardController` operativo) | `test_blocco_director_dashboard_controller_assente` |
| Service `App\Services\DirectorMetricsService` (`enrollmentsByYear`/`retention`/`cashFlow`/`accruedVsCollected`, cache-abile) | ❌ assente (qui solo come **spec** nel test) | `test_blocco_director_metrics_service_assente` |
| Tabella **opzionale** `expenses` (spese generali: affitti/utenze/SIAE/altro) | ❌ assente → uscite restano **parziali**, badge obbligatorio | `test_blocco_tabella_expenses_assente` |
| Tabella **opzionale** `historical_year_stats` (storico pre-2024 dal foglio `grafico` ODS) | ❌ assente → serie §4 parte dal primo anno a sistema | `test_blocco_tabella_historical_year_stats_assente` |
| Rotte/voce di menu **"Direzione ▸ Cruscotto"** + export (`admin.director.*`, export bilancio) | ❌ assenti (c'è solo `admin.dashboard` operativa) | `test_blocco_rotte_direzione_assenti` |
| Viste (KPI, serie storica, cohort, flusso cassa, modal export) + libreria grafici JS | ❌ assenti | — |
| Policy ruolo **direzione/admin** sulla vista (accesso ristretto, GDPR aggregati) | ❌ da definire in implementazione | — (nota §3) |

> Tutti riconducibili al doc 37 §10 ("Impatti tecnici — NON parte di questo R11").

---

## 2. VALIDATO — i flussi del design reggono sul dato esistente

| Flusso (doc 37) | Cosa dimostra il test | Test |
| --- | --- | --- |
| **KPI Iscritti** (§3) | conta solo `StudentYear.status = enrolled` (interested/prospect esclusi) | `test_kpi_iscritti_conta_solo_enrolled` |
| **Delta anno-su-anno** (§0.2/§3) | valore assoluto + % vs anno precedente (4→5 = +1, +25%) | `test_kpi_delta_anno_su_anno` |
| **Primo anno a sistema** (§3/§8) | nessun precedente → delta `null` ("—"), nessun crash | `test_kpi_delta_assente_se_nessun_anno_precedente` |
| **Retention cohort** (§5) | rimasti + nuovi = totale anno corrente (somma che torna); rate 3/4 = 75% | `test_retention_cohort_somma_torna` |
| **Ritirato in corso** (§5) | `withdrawn_at` valorizzato → non è "rimasto", rate 0% | `test_retention_ritirato_in_corso_non_e_rimasto` |
| **Coorte vuota** (§5/§8) | nessun anno precedente con iscritti → rate `null` (non calcolabile) | `test_retention_coorte_vuota_non_calcolabile` |
| **Breakdown per corso** (§5) | retention per strumento (Pianoforte 2/2, Chitarra 1/2) | `test_retention_breakdown_per_corso` |
| **Serie storica** (§4) | iscritti per anno ordinati sull'asse tempo + media di riferimento | `test_serie_storica_iscritti_per_anno` |
| **Cassa — entrate** (§6) | `Payment` aggregati per mese di `payment_date` (stesso mese → somma) | `test_cashflow_entrate_per_mese` |
| **Cassa — uscite parziali** (§0.5/§6) | uscite = solo `teacher_hours` (compensi); badge `parziale` sempre vero | `test_cashflow_uscite_solo_compensi_e_badge_parziale` |
| **Compensi non attivi** (§8) | senza `teacher_hours` → uscite 0, saldo = solo entrate, dichiarato | `test_cashflow_senza_compensi_uscite_zero` |
| **Maturato vs incassato** (§6) | `Invoice.total_amount` − `Payment.amount` = credito residuo | `test_maturato_vs_incassato_credito_residuo` |
| **Export bilancio sociale** (§7) | CSV aggregato, caveat "uscite parziali" nel file, **nessuna PII** (GDPR) | `test_export_bilancio_sociale_csv_aggregato_e_caveat` |
| **Nome file parlante** (§7) | `bilancio-sociale_2025-26_altramusica.csv` (anno + perimetro) | `test_export_nome_file_parlante` |
| **Substrato** (§1) | tabelle iscritti/incassi/anni presenti, enum stati coerente | `test_substrato_tabelle_direzionali_presenti` |

> Il **motore metriche** del design (`DirectorMetricsService`) è esercitato come **spec eseguibile** dentro il test (metodi `enrollmentsByYear`/`retention`/`cashFlow`/`accruedVsCollected`): quando verrà implementato, basterà spostare quella logica nel service mantenendo gli stessi risultati.

---

## 3. Note di configurazione e ordine di implementazione suggerito

- **Uscite sempre dichiarate parziali** finché non esiste la tabella `expenses`: il badge **⚠ uscite parziali — solo compensi docenti** è obbligatorio su card §3, flusso §6 ed export §7 (il caveat **viaggia anche nel file** CSV/PDF). Niente conto economico finto.
- **Compensi R8 dormienti**: la tabella `teacher_hours` esiste (migration `2025_12_22`) ma il motore compensi non è ancora attivo (model `TeacherHour` assente — vedi doc 33). Finché non attivo, le uscite restano = 0 con nota "motore compensi non attivo".
- **GDPR**: l'export di default contiene **solo aggregati** (conteggi), mai nomi allievi — coerente con R9/privacy. La vista è riservata ai ruoli **direzione/admin** (non segreteria base).
- **Nessun package nuovo** per l'export: CSV via `fputcsv`/`StreamedResponse` nativo; PDF via la libreria già in uso per i documenti (R10) o `dompdf` se assente.
- **Ordine suggerito** (fase implementazione): (1) `DirectorMetricsService` + cache per `academic_year_id`; (2) `DirectorDashboardController` + rotte sotto policy ruolo; (3) viste KPI/serie/cohort/cassa + libreria grafici JS; (4) export CSV/PDF con caveat; (5) **opzionale** tabella `expenses` (toglie il badge parziale) e import storico ODS in `historical_year_stats` (estende la serie a sinistra).

---

## 4. Esito

**REPORT con BLOCCO parziale.** I flussi direzionali del design sono **validati** sul substrato dati esistente (20 test, suite 115/115 verde). L'E2E su UI/controller direzionale **resta bloccato** dall'assenza di vista, servizio e tabelle opzionali (§1), tutto riconducibile al doc 37 §10 esplicitamente fuori scope per R11. Nessuna implementazione effettuata: il controllo finale produce un **registro vivo del gap** da colmare quando l'attività di sviluppo verrà schedulata.
