---
name: Migrazione AS-IS -> Footility
overview: Validare tutte le sezioni AS-IS contro l’analisi colonne ODS e tradurle in 12 attività di migrazione (con task) inserite nella Fase 1 del preventivo Footility.
todos:
  - id: coverage-check
    content: Creare checklist di copertura colonne (ANALISI_COLONNE_ODS_COMPLETA.md) -> sezioni 01..12 e integrare i task mancanti per coprire l’AS-IS
    status: completed
  - id: reset-project-activities
    content: Cancellare attività esistenti nel Project 13 e ricreare 12 attività (una per sezione)
    status: completed
    dependencies:
      - coverage-check
  - id: create-tasks
    content: Creare i task (funzionalità) sotto ciascuna delle 12 attività, con riferimenti a file/sheet/colonne
    status: completed
    dependencies:
      - reset-project-activities
  - id: reset-quotation-phases
    content: Eliminare fasi del quotation 20, ricreare Fase 1/2/3 (2 e 3 vuote) e associare le 12 attività alla Fase 1
    status: completed
    dependencies:
      - create-tasks
---

# Migrazione AS-IS (ODS) -> 12 attività + task su Footility (Fase 1)

## Obiettivo

- Tradurre le **12 sezioni AS-IS** (docs 01..12) in **12 attività Footility** (Project 13) con **task = funzionalità**, validate contro [`docs/ANALISI_COLONNE_ODS_COMPLETA.md`](docs/ANALISI_COLONNE_ODS_COMPLETA.md).
- Inserire queste 12 attività nella **Fase 1** del preventivo (**quotation 20**).
- Ricreare Fase 2 e Fase 3 come placeholder **vuote** (come da tua scelta).

## Input sorgenti

- Documenti per sezione: [`docs/00_INDICE_FUNZIONALITA_ASIS.md`](docs/00_INDICE_FUNZIONALITA_ASIS.md) e [`docs/01_ANAGRAFICHE_STUDENTI.md`](docs/01_ANAGRAFICHE_STUDENTI.md) … [`docs/12_DOCUMENTI_E_MODELLI_CONTRATTI.md`](docs/12_DOCUMENTI_E_MODELLI_CONTRATTI.md)
- Analisi colonne: [`docs/ANALISI_COLONNE_ODS_COMPLETA.md`](docs/ANALISI_COLONNE_ODS_COMPLETA.md)

## Strategia di validazione (anti “pezzi persi”)

- Costruire una checklist di copertura **colonna -> sezione** per ogni file/sheet rilevante in `ANALISI_COLONNE_ODS_COMPLETA.md` (es. `db 2025-26 gestionale.ods/dati`, `età_scolare`, `Db Contabile 2025-26.ods/fatt corsi`, `pagato`, `recupero crediti`, `fatt accessori`, …).
- Per ogni sezione 01..12:
- confrontare i task previsti con le colonne reali;
- aggiungere task mancanti per blocchi funzionali (rate 1–4, orchestra/coro, noleggio/cauzioni, privacy, solleciti, ecc.).

## Operazioni Footility (via API)

Useremo gli endpoint definiti in Footility:

- Attività: `POST /api/activities`, `DELETE /api/activities/batch` (Project 13) — vedi [`app/Http/Controllers/Api/ActivityController.php`](/Users/mistre/develop/footility/footility/app/Http/Controllers/Api/ActivityController.php).
- Task: `POST /api/tasks` (con `activity_id`) — vedi [`app/Http/Controllers/Api/TaskController.php`](/Users/mistre/develop/footility/footility/app/Http/Controllers/Api/TaskController.php).
- Fasi preventivo: `DELETE /api/quotations/20/phases`, `POST /api/quotations/20/phases` — vedi [`app/Http/Controllers/Api/QuotationPhaseController.php`](/Users/mistre/develop/footility/footility/app/Http/Controllers/Api/QuotationPhaseController.php).
- Associazione attività a Fase 1: `POST /api/quotation-phases/{phaseId}/activities` — vedi [`app/Http/Controllers/Api/QuotationActivityController.php`](/Users/mistre/develop/footility/footility/app/Http/Controllers/Api/QuotationActivityController.php).

## Sequenza di esecuzione

1. **Censire stato attuale**

- Recuperare tutte le attività del Project 13.
- Recuperare le fasi del quotation 20.

2. **Pulizia**

- Eliminare le attività esistenti del Project 13 (batch delete).
- Eliminare tutte le fasi del quotation 20 (cascata su quotation activities).

3. **Creazione 12 attività (Project 13)**

- Creare 12 attività con titolo = sezione (senza “Migrazione”).
- **Descrizione attività**: descrive la funzionalità AS-IS come requisito (es. “Gestione anagrafica studenti con campi anagrafici, stato operativo, tracciamento primo contatto, disponibilità settimanale, adesione laboratorio e orchestra/coro”). **Non menzionare “migrazione”**: il contesto è dato dalla fase.

4. **Creazione task per attività**

- Per ogni attività, creare task = funzionalità/feature di sviluppo (es. "Gestione campo stato operativo", "Calcolo età e minore", "Tracciamento disponibilità settimanale").
- **Non includere riferimenti a file/sheet/colonne**: la tracciabilità è già nella documentazione (docs/01..12).
- Task ordinati (campo `order`) e status `pending`.

5. **Ricreare fasi preventivo (quotation 20)**

- Creare 3 fasi: Fase 1 (migrazione), Fase 2 (placeholder vuota), Fase 3 (placeholder vuota) con nomi/descrizioni coerenti.
- Aggiungere le 12 attività alla Fase 1 nell’ordine 01..12.

6. **Verifica finale**

- GET quotation 20 e controllo che:
- Fase 1 contenga esattamente 12 attività con titoli corretti;
- Fase 2 e 3 esistano ma siano vuote;
- il Project 13 abbia 12 attività e i task popolati.

## Output

- Project 13: 12 attività + task completi.
- Quotation 20: Fase 1 popolata con le 12 attività; Fase 2 e 3 vuote.
- Checklist interna di copertura colonne (per segnalare eventuali gap prima di “chiudere” la validazione).