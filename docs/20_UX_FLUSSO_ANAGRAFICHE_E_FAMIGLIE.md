# 20 — UX: Flusso anagrafiche e famiglie (R1 · Design UX)

> Attività Footility **#8521** — *R1 · Design UX — Flusso anagrafiche e famiglie* (progetto Gestionale Altramusica).
> Obiettivo: definire il **flusso operativo** (non lo schema dati) per aprire una scheda studente,
> collegare un genitore (anche con più figli) e vedere **la famiglia in un colpo solo**.
> Deliverable: wireframe (ASCII) + percorsi click. Mappa su attività Fase 2 **F2-01** (anagrafiche) e **F2-02** (nuclei familiari).

---

## 0. Principi di design

1. **Le viste seguono il flusso, non lo schema DB.** L'operatrice di segreteria ragiona per *persone* e *famiglie*, non per tabelle. La scheda studente è il **hub**: tutto ciò che serve durante una telefonata o un'iscrizione è raggiungibile da lì.
2. **Un solo gesto per collegare.** Collegare un genitore non deve richiedere di uscire dalla scheda studente: si fa in **un modal** (cerca-o-crea), senza navigare via.
3. **La famiglia è bidirezionale.** Dalla scheda studente vedo genitori + fratelli; dalla scheda genitore vedo tutti i figli. Stessa informazione, due porte d'ingresso.
4. **Niente doppioni silenziosi.** Prima di creare un nuovo genitore il sistema propone i possibili duplicati (stesso cognome / cellulare / email / CF). Vedi §6.
5. **Minorenne vs maggiorenne** cambia la UI: per il minorenne il *contatto di fatturazione* è di norma un genitore; per il maggiorenne può essere lo studente stesso.

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| Anagrafica studente (`students/show`) | ✅ esiste, mostra dati anno + box "Genitori/Tutori" **in sola lettura** |
| Anagrafica genitore (`guardians/show`) | ✅ esiste, mostra "Studenti Associati" **in sola lettura** |
| Pivot `student_guardian` (`relationship_type`, `is_primary`, `is_billing_contact`) | ✅ esiste a livello dati |
| **UI per collegare/scollegare genitore↔studente** | ❌ **assente** — il pivot è popolato **solo** da `OdsImportService` |
| Vista "famiglia" (fratelli, nucleo) | ❌ assente |
| Deduplica / gestione omonimi in fase di creazione | ❌ assente |

> Conclusione: la segreteria oggi **non può** collegare una famiglia da interfaccia. Questo design copre quel buco.

---

## 2. Mappa entità (in chiave UX)

```
        ┌─────────────┐        student_guardian        ┌──────────────┐
        │  STUDENTE   │◄───────(ruolo, primario, ──────►│  GENITORE/   │
        │ (persona)   │         fatturazione)           │   TUTORE     │
        └─────┬───────┘   N : N (un genitore → +figli)  └──────────────┘
              │
              │ per anno scolastico
              ▼
        ┌─────────────┐
        │ StudentYear │  stato, codice, note, consensi, follow-up
        └─────────────┘
```

**FAMIGLIA / NUCLEO** = insieme degli studenti che condividono almeno un genitore.
Non è un'entità separata: è una **vista** derivata dal grafo studente↔genitore. Etichetta UI: **"Famiglia"**.

---

## 3. Flusso 1 — Aprire la scheda studente

**Percorso click (happy path):**

```
Sidebar ▸ Studenti
   └─ Elenco Studenti  (ricerca: nome / cognome / codice; filtro: anno, stato)
        └─ click riga  ▸  oppure  azione 👁 "Visualizza"
             └─ SCHEDA STUDENTE (show)   ← HUB
```

Percorsi alternativi che atterrano sulla stessa scheda:
- da **Dashboard** ▸ alert "anagrafiche incomplete" ▸ click nome.
- da **scheda genitore** ▸ tabella figli ▸ click nome figlio.
- da **risultati ricerca globale** (futuro) ▸ click studente.

**Wireframe — Scheda studente (riorganizzata attorno al flusso):**

```
┌───────────────────────────────────────────────────────────────────────────┐
│  ‹ Studenti      Mario Rossi            [Anno: 2025/26 ▾]   [✎ Modifica]    │
│                  🟢 Iscritto · cod. A-0142 · 🧒 minore (12)                  │
├──────────────────────────────────────────────┬────────────────────────────┤
│  ANAGRAFICA                                    │  AZIONI RAPIDE             │
│  Nato il 03/05/2013 · CF RSSMRA…               │  [+ Nuova iscrizione]     │
│  Scuola provenienza · Come ci ha conosciuto    │  [+ Nuovo contratto]      │
│                                                │  [+ Nuova fattura]        │
├──────────────────────────────────────────────┤                            │
│  👪 FAMIGLIA                       [+ Collega] │  NOTE                     │
│  ┌───────────────────────────────────────────┐│  …                        │
│  │ Anna Rossi          Madre · ★Primario  ✎ ✕ ││                            │
│  │ 📞 333 1234567  ✉ anna@…  💶 Fatturazione  ││  PRIVACY                  │
│  ├───────────────────────────────────────────┤│  Consenso ✅ · Foto ✅    │
│  │ Luca Rossi          Padre              ✎ ✕ ││                            │
│  │ 📞 333 7654321  ✉ luca@…                   ││                            │
│  └───────────────────────────────────────────┘│                            │
│  Fratelli nel nucleo:  • Giulia Rossi (A-0143) │                            │
│                                                │                            │
├──────────────────────────────────────────────┤                            │
│  ISCRIZIONI / CONTRATTI / FATTURE  (tab/anno)  │                            │
└──────────────────────────────────────────────┴────────────────────────────┘
```

Differenze rispetto a oggi: il box "Genitori/Tutori" diventa **"Famiglia"**, **editabile** (azioni `+ Collega`, `✎`, `✕`), con badge ruolo/primario/fatturazione e **riga "Fratelli nel nucleo"** derivata dai genitori condivisi.

---

## 4. Flusso 2 — Collegare un genitore (cerca-o-crea)

Il gesto chiave dell'attività. Parte **dalla scheda studente**, resta nel contesto.

**Percorso click:**

```
Scheda studente ▸ box Famiglia ▸ [+ Collega genitore]
   └─ MODAL "Collega genitore/tutore"
        ├─ (A) cerco un genitore già esistente  → seleziono → imposto ruolo → [Collega]
        └─ (B) non esiste → [+ Crea nuovo]      → form rapido → [Crea e collega]
```

**Wireframe — Modal "Collega genitore":**

```
┌──────────────── Collega genitore / tutore a Mario Rossi ─────────────────┐
│                                                                          │
│  Cerca persona già in anagrafica                                         │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ 🔍 cognome, telefono, email o codice fiscale…                      │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│  Risultati:                                                              │
│   ○ Anna Rossi   · 📞 333 1234567 · 👶 1 figlio collegato                │
│   ○ Anna Russo   · ✉ a.russo@…   · 👶 2 figli collegati                  │
│                                                                          │
│  ─────────────────────  oppure  ─────────────────────                    │
│  [ + Crea nuovo genitore/tutore ]                                        │
│                                                                          │
│  ── Quando selezioni / crei, imposta il collegamento: ──                 │
│  Ruolo:  ( ) Madre  ( ) Padre  ( ) Tutore  ( ) Altro                     │
│  [ ] Contatto primario        [ ] Contatto di fatturazione               │
│                                                                          │
│                                          [ Annulla ]   [ Collega ]       │
└──────────────────────────────────────────────────────────────────────────┘
```

**Sotto-flusso (B) — crea nuovo (form rapido nello stesso modal):**

```
   Nome*  Cognome*   Cellulare    Email    Codice fiscale
   ▸ all'invio: il sistema controlla i DUPLICATI (vedi §6) prima di creare.
```

**Regole UX:**
- Il **ruolo** è del *collegamento* (pivot), non del genitore: la stessa persona può essere "Madre" per un figlio e — teoricamente — comparire diversamente, ma di norma il ruolo è stabile.
- **Contatto primario**: max 1 per studente; selezionarne uno nuovo de-seleziona il precedente (con conferma inline).
- **Contatto di fatturazione**: chi riceve le fatture; per minorenni default = primario genitore.
- Dopo `[Collega]`: ritorno alla scheda studente, box Famiglia aggiornato, toast "Anna Rossi collegata come Madre".

---

## 5. Flusso 3 — Genitore con più figli

Stessa azione, **direzione inversa**: dalla scheda genitore collego un altro figlio. Utile quando si iscrive il secondo fratello.

**Percorso click:**

```
Sidebar ▸ Genitori/Tutori ▸ Elenco ▸ scheda genitore
   └─ box "Figli / studenti collegati" ▸ [+ Collega figlio]
        └─ MODAL "Collega studente"  (cerca studente esistente o creane uno)
             └─ imposto ruolo del genitore verso quel figlio → [Collega]
```

**Wireframe — Scheda genitore (editabile):**

```
┌───────────────────────────────────────────────────────────────────────────┐
│  ‹ Genitori     Anna Rossi              Madre            [✎ Modifica]       │
│                 📞 333 1234567 · ✉ anna@… · 💶 fatturazione                 │
├──────────────────────────────────────────────┬────────────────────────────┤
│  CONTATTI                                      │  AZIONI                   │
│  Cell, email, indirizzo…                       │  [+ Collega figlio]       │
├──────────────────────────────────────────────┤  [⤳ Unisci duplicato]     │
│  👶 FIGLI / STUDENTI COLLEGATI    [+ Collega]  │                            │
│  ┌───────────────────────────────────────────┐│                            │
│  │ Mario Rossi    🟢 Iscritto · A-0142  ✎ ✕  ││                            │
│  │ Giulia Rossi   🟡 Interessata · A-0143 ✎ ✕││                            │
│  └───────────────────────────────────────────┘│                            │
│  → Questi due studenti formano un NUCLEO.      │                            │
└──────────────────────────────────────────────┴────────────────────────────┘
```

`✎` su una riga = cambia ruolo/flag di **quel** collegamento. `✕` = scollega (con conferma; non cancella la persona).

---

## 6. Flusso 4 — Vedere la famiglia in un colpo solo

Obiettivo dell'attività: *"come si vede in 1 colpo la famiglia"*. Soluzione: un **pannello Famiglia** coerente, raggiungibile da entrambe le schede, che mostra **genitori + tutti i figli + stato di ciascuno**, con azioni rapide.

Non serve una nuova entità/tabella: è una **vista** sul grafo (studenti che condividono ≥1 genitore).

**Wireframe — Pannello / scheda "Famiglia Rossi":**

```
┌──────────────────────────── 👪 Famiglia Rossi ────────────────────────────┐
│  ADULTI                                                                    │
│   • Anna Rossi   Madre · ★Primario · 💶 Fatturazione   📞333… ✉anna@…      │
│   • Luca Rossi   Padre                                  📞333… ✉luca@…      │
│                                                                            │
│  FIGLI                                                                     │
│   • Mario Rossi   🟢 Iscritto    · cod. A-0142 · Pianoforte    [apri ›]    │
│   • Giulia Rossi  🟡 Interessata · cod. A-0143 · Canto         [apri ›]    │
│                                                                            │
│  In un colpo:  2 figli · 1 iscritto · 1 da convertire · fatture a Anna     │
│  [+ Collega adulto]   [+ Aggiungi figlio]                                  │
└────────────────────────────────────────────────────────────────────────────┘
```

**Come ci si arriva:**
- inline come box espandibile nella scheda studente ("vedi tutta la famiglia ›");
- come stessa scheda dal lato genitore (i "figli collegati" *sono* il nucleo);
- (opzionale Fase 2) link diretto `/admin/families/{guardian}` che renderizza questo pannello.

---

## 7. Stati e casi limite

| Caso | Comportamento UX |
| --- | --- |
| **Studente maggiorenne** | Il flag "Contatto di fatturazione" può puntare allo studente stesso; collegare un genitore resta possibile ma non obbligatorio. |
| **Genitore unico per più figli** | Collegando il 2° figlio si riusa la **stessa** persona (no doppione). Il nucleo si forma automaticamente. |
| **Omonimi / possibili duplicati** | In creazione, se cognome+cellulare o email o CF coincidono → banner "Forse esiste già: *Anna Rossi*" con `[Usa esistente]` / `[Crea comunque]`. |
| **Scollegamento** | `✕` rimuove solo il legame (pivot), **non** la persona. Conferma: "Scollegare Anna da Mario? Resterà collegata a Giulia." |
| **Due "primari"** | Impedito: impostarne uno nuovo declassa il precedente, con avviso inline. |
| **Genitore senza contatti** | Consentito ma segnalato (badge "anagrafica incompleta" — coerente con alert dashboard). |
| **Nucleo con cognomi diversi** | Il "nome famiglia" è derivato dal genitore primario; resta modificabile/etichettabile a parole. |

---

## 8. Microcopy (etichette IT)

- Box: **"Famiglia"** (non "Genitori/Tutori" — più vicino al modo di pensare della segreteria).
- Pulsanti: **"+ Collega genitore"**, **"+ Collega figlio"**, **"+ Crea nuovo"**, **"Unisci duplicato"**.
- Badge ruolo: **Madre / Padre / Tutore / Altro**; flag: **★ Primario**, **💶 Fatturazione**.
- Toast: *"Anna Rossi collegata come Madre."* · *"Collegamento rimosso."*
- Conferma scollegamento: *"Scollegare {genitore} da {studente}? La persona non verrà eliminata."*

---

## 9. Impatti tecnici (per chi implementa — NON parte di questo R1)

Il design **non** richiede modifiche allo schema (il pivot `student_guardian` ha già `relationship_type`, `is_primary`, `is_billing_contact`). Servono solo i **flussi mancanti**:

- Endpoint per **attach/detach/update** del legame (es. `POST/DELETE /admin/students/{student}/guardians`), con `relationship_type`, `is_primary`, `is_billing_contact`. Riusare la logica già presente in `OdsImportService` (attach con pivot) e il fix pivot del commit `08f30dd`.
- Endpoint di **ricerca genitori** (autocomplete: cognome/cell/email/CF) per il modal.
- Box "Famiglia" editabile in `students/show` + box "Figli" editabile in `guardians/show`.
- (Fase 2) **merge duplicati** guidato e vista `/admin/families/{…}`.

---

## 10. Checklist di accettazione (Definition of Done del design)

- [x] Definito come si **apre** la scheda studente (percorso click + alternativi). — §3
- [x] Definito come si **collega un genitore** restando nel contesto (modal cerca-o-crea, ruoli). — §4
- [x] Coperto il **genitore con più figli** (direzione inversa dal lato genitore). — §5
- [x] Definita la **vista famiglia in un colpo** (pannello bidirezionale). — §6
- [x] Coperti **stati e casi limite** (maggiorenne, omonimi, scollegamento, primario unico). — §7
- [x] Wireframe ASCII per ogni schermata/modal chiave + microcopy IT. — §3–§8
- [x] Indicati impatti tecnici minimi senza implementare (le viste seguono il flusso). — §9
