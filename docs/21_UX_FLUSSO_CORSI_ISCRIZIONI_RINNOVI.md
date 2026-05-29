# 21 — UX: Flusso corsi, iscrizioni, rinnovi (R2 · Design UX)

> Attività Footility **#8522** — *R2 · Design UX — Flusso corsi, iscrizioni, rinnovi* (progetto Gestionale Altramusica).
> Obiettivo: definire il **flusso operativo** che porta da **catalogo → offerta annuale → iscrizione in pochi click**,
> e il **rinnovo d'anno** (con fratelli e crediti) come **azione singola**.
> Deliverable: wireframe (ASCII) + casi reali (nuovo, rinnovo, cambio corso).
> Mappa sulle attività Fase 2 **F2-03** (catalogo + offerte annuali + iscrizione) e tocca **F2-05** (crediti/contabilità).
> Continua il lavoro di [`20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md`](20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md) (R1): lì la **famiglia**, qui i **corsi**.

---

## 0. Principi di design

1. **Catalogo ≠ offerta ≠ iscrizione.** Tre livelli distinti che la UI non deve confondere:
   - **Corso di catalogo** (`Course` + `CourseType`): cosa insegniamo (Pianoforte, Canto…) — *stabile negli anni*.
   - **Offerta annuale** (`CourseOffering`): quel corso **in questo anno**, con docente, aula, orario, tariffa.
   - **Iscrizione** (`Enrollment`): *questo studente* su *quell'offerta*, con eventuali override e sconti.
2. **Iscrivere = pochi click dal punto giusto.** L'iscrizione parte **dalla scheda studente** (so già chi è) o **dall'offerta** (so già il corso). In entrambi i casi: 1 modal, default precompilati, conferma.
3. **Il rinnovo è un'azione, non un re-inserimento.** A inizio anno la segreteria non deve ridigitare: parte dall'anno precedente e **propone** la stessa offerta (o l'equivalente nel nuovo anno), riportando sconti e crediti. Un click, poi conferma.
4. **Fratelli e crediti sono parte del rinnovo, non passi separati.** Se lo studente ha fratelli iscritti, lo sconto fratelli si applica nel rinnovo; eventuali crediti residui (note di credito / acconti) sono **proposti in detrazione** nella stessa schermata.
5. **Le viste seguono il flusso, non lo schema DB.** L'operatrice ragiona "iscrivo Mario a Pianoforte quest'anno", non "creo un Enrollment con course_offering_id".

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| Catalogo corsi (`Course`, `CourseType` con listino `price_*`) | ✅ esiste (modelli + CRUD `admin/courses`) |
| Offerta annuale (`CourseOffering`: docente, aula, orario, `price_per_lesson`, status) | ✅ esiste a livello dati |
| Iscrizione (`Enrollment`: status, `discount_percentage`, `discount_amount`, `total_amount`) | ✅ esiste + CRUD `admin/enrollments` (index/create/edit/show) |
| Anno scolastico (`AcademicYear`, `getCurrent()`) + `StudentYear` per stato/anno | ✅ esiste |
| Costo iscrizione calcolato (`EnrollmentService::calculateTotalCost`) | ✅ esiste (% poi importo fisso) |
| **Iscrizione "in pochi click" dalla scheda studente** (modal contestuale) | ❌ oggi si passa da `enrollments/create` come form generico |
| **Rinnovo anno → anno come azione singola** (copia offerta, riporta sconti) | ❌ **assente** — nessun flag/logica di rinnovo, niente copy cross-anno |
| **Sconto fratelli** (sibling discount) | ❌ **non modellato** — esistono solo sconti manuali per riga |
| **Crediti residui / acconti** riutilizzabili in iscrizione | ⚠️ parziale — `CreditNote` esiste ma è nota di credito su **fattura**, non credito spendibile su nuova iscrizione |

> Conclusione: il **catalogo→offerta→iscrizione** esiste come dati e CRUD, ma manca la **scorciatoia operativa** (iscrizione contestuale) e tutto il **flusso di rinnovo** (fratelli + crediti). Questo design copre quei buchi senza richiedere modifiche di schema sostanziali (vedi §9).

---

## 2. Mappa entità (in chiave UX)

```
   CATALOGO (stabile)                OFFERTA (per anno)               ISCRIZIONE (per studente)
  ┌───────────────┐  1     N   ┌──────────────────────┐  1     N   ┌──────────────────┐
  │   Course      │───────────►│   CourseOffering     │───────────►│    Enrollment    │
  │  +CourseType  │            │  docente/aula/orario  │            │  status, sconti, │
  │  listino base │            │  price_per_lesson     │            │  total_amount    │
  └───────────────┘            │  ▲ AcademicYear       │            │  ▲ Student       │
                               └──────────────────────┘            └──────────────────┘
                                        │                                   │
                                        └──────── stesso AcademicYear ──────┘
```

- **Catalogo** = il "cosa" (riusato ogni anno). **Offerta** = il "cosa, quest'anno, con chi/dove/quanto". **Iscrizione** = "chi", su un'offerta.
- Il **rinnovo** è: *data un'iscrizione dell'anno N, crea l'iscrizione dell'anno N+1 sull'offerta corrispondente* (stesso `Course`, nuovo `CourseOffering` del nuovo `AcademicYear`).

---

## 3. Flusso 1 — Catalogo → Offerta annuale

Punto di partenza per chi prepara l'anno: dal corso di catalogo si **pubblica l'offerta** dell'anno (tariffe, docente, aula, orario).

**Percorso click:**

```
Sidebar ▸ Corsi
   └─ Catalogo corsi  (lista Course per disciplina/tipo)
        └─ scheda corso ▸ tab "Offerte per anno"  [Anno: 2025/26 ▾]
             └─ [+ Pubblica offerta 2025/26]
                  └─ MODAL offerta: docente, aula, giorno/orario, tariffa, posti max
```

**Wireframe — Scheda corso (catalogo) con offerte per anno:**

```
┌───────────────────────────────────────────────────────────────────────────┐
│  ‹ Corsi     Pianoforte (individuale)        cod. PF · 🎹                    │
│              CourseType: Individuale · 30 min · listino 90€/mese             │
├──────────────────────────────────────────────┬────────────────────────────┤
│  OFFERTE PER ANNO          [Anno: 2025/26 ▾]   │  AZIONI                    │
│  ┌───────────────────────────────────────────┐│  [+ Pubblica offerta]      │
│  │ 🟢 2025/26 · M. Bianchi · Aula 2 · Lun 15h ││  [⎘ Copia da 2024/25]      │
│  │    90€/mese · 6/8 iscritti        [apri ›] ││                            │
│  ├───────────────────────────────────────────┤│  LISTINO BASE              │
│  │ ⚪ 2024/25 · M. Bianchi · Aula 2 (chiusa)  ││  full 90 · ridotto 70      │
│  └───────────────────────────────────────────┘│  annuale 810               │
└──────────────────────────────────────────────┴────────────────────────────┘
```

**Regole UX:**
- **"Copia da anno precedente"** crea l'offerta del nuovo anno precompilando docente/aula/orario/tariffa dall'offerta passata → si conferma o si ritocca. Evita di ridigitare l'intero listino ogni settembre.
- I **posti** (`max_students` / `current_students`) sono visibili sull'offerta: una corsia piena va in stato "completo" e l'iscrizione avvisa (§7).

---

## 4. Flusso 2 — Iscrizione in pochi click

Il gesto chiave. Due porte d'ingresso, **stesso modal**:

```
(A) DA SCHEDA STUDENTE  ─ so chi è, scelgo il corso/offerta
(B) DA OFFERTA          ─ so il corso, scelgo lo studente
```

**Percorso click (A — happy path, il più frequente in segreteria):**

```
Scheda studente ▸ Azioni rapide ▸ [+ Nuova iscrizione]
   └─ MODAL "Iscrivi {studente} — anno 2025/26"
        ├─ 1. Cerca corso/offerta  (🔍 Pianoforte → Lun 15h · M.Bianchi · 6/8)
        ├─ 2. Tariffa precompilata dall'offerta (override possibile)
        ├─ 3. Sconti suggeriti (fratelli §6 / crediti §6) — togglabili
        └─ [Iscrivi]  → Enrollment creato, StudentYear→"enrolled", toast
```

**Wireframe — Modal "Nuova iscrizione" (dalla scheda studente):**

```
┌──────────────── Iscrivi Mario Rossi · Anno 2025/26 ──────────────────────┐
│                                                                          │
│  1) Corso / offerta                                                      │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │ 🔍 cerca corso o disciplina…            (filtra per anno attivo)   │  │
│  └──────────────────────────────────────────────────────────────────┘  │
│   ◉ Pianoforte · Lun 15:00 · M. Bianchi · Aula 2 · 6/8 posti · 90€/m    │
│   ○ Canto       · Mer 17:00 · L. Verdi   · Aula 1 · 4/6 posti · 80€/m    │
│                                                                          │
│  2) Tariffa            90,00 €/mese   ▸ [override ✎]   inizio: 01/10/25  │
│                                                                          │
│  3) Sconti suggeriti                                                     │
│   [✓] Sconto fratelli −10%   (Giulia Rossi già iscritta a Canto)        │
│   [ ] Usa credito residuo: 45,00 €  (nota di credito 2024/25)           │
│                                                                          │
│   Totale stimato: 81,00 €/mese · 729 €/anno                              │
│                                          [ Annulla ]   [ Iscrivi ]       │
└──────────────────────────────────────────────────────────────────────────┘
```

**Percorso click (B — dall'offerta, utile in giornata di open-day / più iscrizioni allo stesso corso):**

```
Scheda corso ▸ offerta 2025/26 ▸ [+ Iscrivi studente]
   └─ MODAL: cerca studente (riusa autocomplete R1) → stessa schermata sconti → [Iscrivi]
```

**Regole UX:**
- **Anno = quello attivo** di default (`AcademicYear::getCurrent()`); cambiabile in testata.
- **Tariffa precompilata** dall'offerta (`price_per_lesson`/listino), con override esplicito → finisce in `discount_amount`/`discount_percentage` o prezzo riga.
- I passi 2 e 3 sono **già compilati**: il caso normale è aprire → confermare → fatto.
- Dopo `[Iscrivi]`: `Enrollment` creato, `StudentYear.status` → `enrolled`, box iscrizioni della scheda studente aggiornato, toast *"Mario iscritto a Pianoforte (2025/26)."*

---

## 5. Flusso 3 — Rinnovo d'anno come azione singola

Obiettivo dell'attività: *rinnovo anno con fratelli/crediti come azione singola*. A inizio anno la segreteria parte dagli iscritti dell'anno scorso e **rinnova**, senza re-inserire nulla.

**Percorso click — rinnovo del singolo studente:**

```
Scheda studente ▸ box Iscrizioni ▸ banner "Anno 2025/26 non ancora iscritto"
   └─ [↻ Rinnova da 2024/25]
        └─ MODAL "Rinnovo 2024/25 → 2025/26" (offerta corrispondente proposta)
             └─ rivedo (offerta/tariffa/sconti/crediti) → [Conferma rinnovo]
```

**Wireframe — Modal "Rinnovo d'anno":**

```
┌──────────── Rinnovo Mario Rossi · 2024/25 → 2025/26 ─────────────────────┐
│                                                                          │
│  Iscrizione anno scorso:  Pianoforte · M. Bianchi · 90€/m               │
│  ▸ Offerta corrispondente 2025/26:                                      │
│     ◉ Pianoforte · Lun 15:00 · M. Bianchi · Aula 2  (confermata)        │
│     ○ scegli un'altra offerta…                                          │
│                                                                          │
│  Sconti / crediti riportati:                                            │
│   [✓] Sconto fratelli −10%   (Giulia rinnova anch'essa)                 │
│   [✓] Credito residuo 45,00 € (chiusura 2024/25)                        │
│                                                                          │
│  Totale stimato: 729 € − 45 € credito = 684 €/anno                       │
│                                       [ Annulla ]  [ Conferma rinnovo ]  │
└──────────────────────────────────────────────────────────────────────────┘
```

**Rinnovo di famiglia (fratelli) — azione singola sul nucleo (collega §6 di R1):**

```
┌──────────── Rinnovo famiglia Rossi · 2025/26 ────────────────────────────┐
│  [✓] Mario Rossi   → Pianoforte · M. Bianchi          729 €              │
│  [✓] Giulia Rossi  → Canto · L. Verdi                 648 €              │
│  Sconto fratelli −10% applicato a entrambi · credito 45€ su Mario        │
│  ─────────────────────────────────────────────────────────────────────  │
│  Totale famiglia 2025/26:  1.332 €     [ Annulla ]  [ Rinnova tutti ]    │
└──────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- Il bottone **Rinnova** appare solo se esiste un'iscrizione nell'anno precedente **e** non già una nel nuovo anno (idempotenza).
- L'**offerta corrispondente** = stessa `Course` nel nuovo `AcademicYear`. Se non pubblicata → avviso *"Nessuna offerta 2025/26 per Pianoforte"* con scorciatoia a "Pubblica offerta" (§3) o "scegli un'altra offerta".
- **Sconto fratelli** e **credito** sono pre-spuntati ma **deselezionabili**: la segreteria mantiene il controllo.
- "Rinnova tutti" sul nucleo = N iscrizioni in una transazione; se una fallisce (offerta piena) si segnala la riga e si procede con le altre.

---

## 6. Fratelli e crediti (le due leve del rinnovo)

### 6a. Sconto fratelli
Oggi **non modellato**. In chiave UX serve solo una **regola derivata dal nucleo** (R1): se lo studente condivide ≥1 genitore con un altro studente **iscritto nello stesso anno**, il modal propone lo sconto fratelli.

```
Rilevamento: nucleo di {studente} ∩ iscritti anno corrente ≥ 1  → suggerisci sconto
Applicazione: come riga sconto sull'Enrollment (discount_percentage / discount_amount)
Microcopy:    "Sconto fratelli −10% (Giulia Rossi già iscritta)"
```

Parametro **% sconto fratelli** = impostazione globale (config/anno), non hardcoded nel flusso.

### 6b. Crediti residui
`CreditNote` esiste come nota di credito su fattura. In chiave UX, un **credito residuo** = importo a favore dello studente/famiglia non ancora speso. Nel modal iscrizione/rinnovo:

```
Se {studente|famiglia} ha credito non utilizzato  → checkbox "Usa credito 45€"
Applicazione: detrazione sul totale (riga credito su fattura collegata)
```

> Nota implementativa (NON parte di R2): se si vuole un vero "borsellino crediti" riutilizzabile servirà un piccolo modello/saldo (vedi §9). Per ora il design mostra il credito **se rilevabile** dalle note di credito aperte.

---

## 7. Stati e casi limite

| Caso | Comportamento UX |
| --- | --- |
| **Nuovo studente, prima iscrizione** | Nessun rinnovo: si usa §4 (Nuova iscrizione). `StudentYear` creato/aggiornato a `enrolled`. |
| **Rinnovo** | §5: parte dall'anno precedente, propone offerta corrispondente, riporta sconti/crediti. |
| **Cambio corso al rinnovo** | Nel modal rinnovo: "scegli un'altra offerta" → si rinnova *come continuità* ma su corso diverso (es. da Pianoforte a Tastiere). Lo storico resta leggibile. |
| **Offerta piena** (`current_students ≥ max_students`) | Iscrizione consentita solo con conferma "Lista d'attesa / sforza posto"; badge "completo" sull'offerta. |
| **Offerta non pubblicata nel nuovo anno** | Rinnovo bloccato sulla riga con CTA "Pubblica offerta 2025/26" (§3). |
| **Già iscritto a quell'offerta** | Bottone iscrizione disabilitato con "già iscritto"; nessun doppione. |
| **Studente con fratello NON ancora rinnovato** | Lo sconto fratelli si propone comunque se il fratello ha iscrizione attiva nell'anno; se entrambi nuovi, lo sconto si applica nel rinnovo di famiglia (§5). |
| **Credito maggiore del dovuto** | Si applica fino a capienza; residuo resta a credito (segnalato). |
| **Annullamento iscrizione** | `Enrollment.status` → `cancelled`; eventuale nota di credito sul fatturato; `StudentYear` non torna automaticamente indietro (resta lo storico). |

---

## 8. Microcopy (etichette IT)

- Livelli: **"Catalogo corsi"**, **"Offerta {anno}"**, **"Iscrizione"** (non "enrollment").
- Pulsanti: **"+ Pubblica offerta"**, **"⎘ Copia da {anno}"**, **"+ Nuova iscrizione"**, **"+ Iscrivi studente"**, **"↻ Rinnova da {anno}"**, **"Rinnova tutti"**.
- Sconti: **"Sconto fratelli −{n}%"**, **"Usa credito {importo}"**.
- Badge offerta: **🟢 attiva** · **⚪ chiusa** · **🟠 completo** (posti esauriti).
- Toast: *"Mario iscritto a Pianoforte (2025/26)."* · *"Rinnovo confermato."* · *"Rinnovati 2 studenti della famiglia Rossi."*
- Banner scheda studente: *"Anno 2025/26 — non ancora iscritto · [↻ Rinnova da 2024/25]"*.

---

## 9. Impatti tecnici (per chi implementa — NON parte di questo R2)

Il design riusa le entità esistenti (`Course`, `CourseType`, `CourseOffering`, `Enrollment`, `AcademicYear`, `StudentYear`, `EnrollmentService`). Servono i **flussi/azioni mancanti**:

- **Iscrizione contestuale**: endpoint/azione "iscrivi" richiamabile da scheda studente e da offerta (riusa `EnrollmentController@store` + `EnrollmentService::calculateTotalCost`), con modal cerca-offerta (autocomplete sulle `CourseOffering` dell'anno attivo).
- **"Copia offerta da anno precedente"**: azione che clona un `CourseOffering` nel nuovo `AcademicYear` (docente/aula/orario/tariffa precompilati).
- **Rinnovo**: azione "renew" che, data un'`Enrollment` dell'anno N, individua l'offerta corrispondente (stessa `Course`, `AcademicYear` N+1) e crea l'`Enrollment` N+1 riportando sconti; variante "famiglia" che itera sul nucleo (grafo studente↔genitore di R1) in transazione.
- **Sconto fratelli (derivato)**: helper che rileva fratelli iscritti nell'anno (via nucleo R1) e suggerisce lo sconto come `discount_percentage`/`discount_amount`; **% configurabile** (no schema nuovo, o piccola tabella `settings`).
- **Crediti**: in fase 1, leggere `CreditNote` aperte per proporre la detrazione; (opzionale Fase 2) introdurre un saldo crediti riutilizzabile.
- **Stato offerta "completo"**: usare `current_students`/`max_students` già presenti per badge e blocco soft.

---

## 10. Checklist di accettazione (Definition of Done del design)

- [x] Definito il flusso **catalogo → offerta annuale** (pubblica/copia offerta). — §3
- [x] Definita l'**iscrizione in pochi click** da due porte (scheda studente / offerta), 1 modal, default precompilati. — §4
- [x] Definito il **rinnovo d'anno come azione singola** (offerta corrispondente proposta). — §5
- [x] **Fratelli** e **crediti** integrati nel rinnovo, non come passi separati. — §5–§6
- [x] Coperti i **casi reali**: nuovo, rinnovo, cambio corso. — §7
- [x] Coperti stati/limite (offerta piena, non pubblicata, doppioni, credito > dovuto). — §7
- [x] Wireframe ASCII per ogni schermata/modal chiave + microcopy IT. — §3–§8
- [x] Indicati impatti tecnici minimi senza implementare; riuso entità esistenti. — §9
