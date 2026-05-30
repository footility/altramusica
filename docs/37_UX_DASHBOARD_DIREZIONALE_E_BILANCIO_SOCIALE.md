# 37 — UX: Dashboard direzionale + bilancio sociale (R11 · Design UX)

> Attività Footility **#8531** — *R11 · Design UX — Dashboard direzionale e bilancio sociale* (progetto Gestionale Altramusica).
> Obiettivo: una **vista per la direzione** (non per la segreteria operativa) che risponde a 4 domande in un colpo d'occhio —
> **quanti iscritti per anno**, **quanti restano (retention)**, **com'è il flusso di cassa**, **entrate vs uscite** — con **confronto tra anni** e un **export rapido** che diventa la base del **bilancio sociale**.
> Deliverable: wireframe (ASCII) di dashboard direzionale + serie storica + cohort retention + flusso cassa, casi reali, microcopy IT, impatti tecnici.
> **Non duplica** la dashboard operativa già esistente (homepage admin: lezioni settimana, scadenze 7gg, alert anagrafiche — commit *Dashboard #1*): quella guarda **oggi**, questa guarda **gli anni**.
> Base AS-IS: [`11_STATISTICHE_STORICHE.md`](11_STATISTICHE_STORICHE.md) (foglio `grafico` dell'ODS, serie 2008-09…2025-26). Riusa i numeri contabili di [`25_UX_VISTA_CONTABILITA_PER_STUDENTE.md`](25_UX_VISTA_CONTABILITA_PER_STUDENTE.md) (R4), gli incassi di [`36_UX_RICONCILIAZIONE_MANUALE_E_IMPORT_CSV.md`](36_UX_RICONCILIAZIONE_MANUALE_E_IMPORT_CSV.md) (R5) e i compensi docenti di [`32_UX_REGISTRO_PRESENZE_E_MOTORE_COMPENSI.md`](32_UX_REGISTRO_PRESENZE_E_MOTORE_COMPENSI.md) (R8).

---

## 0. Principi di design

1. **Direzione ≠ segreteria.** La dashboard operativa (homepage) serve a *fare* (chi paga questa settimana, quali anagrafiche correggere). Questa serve a *decidere*: andamento iscritti, tenuta del corpo allievi, salute economica. Poche metriche, grandi, leggibili, con il **confronto anno-su-anno** sempre presente. Niente liste di lavoro qui.
2. **Ogni numero ha il suo anno precedente accanto.** Un KPI da solo non dice nulla: *312 iscritti* conta solo se sai che l'anno prima erano *290* (**+7,6%**). Ogni card mostra **valore + delta vs anno precedente** (freccia ↑/↓ e %), perché la direzione ragiona per **tendenza**, non per fotografia.
3. **La serie storica è il cuore, non un di più.** L'ODS già teneva una serie 2008-09…2025-26 (foglio `grafico`). Il design la fa **rivivere** come grafico nativo: l'asse del tempo è l'oggetto principale della pagina, non un widget secondario.
4. **Retention si calcola, non si stima.** "Quanti rimangono" deve essere una **metrica derivata dai dati** (iscritti anno N-1 che si re-iscrivono anno N), non un numero scritto a mano. Si mostra **come** è calcolata (numeratore/denominatore) perché la direzione deve potersene fidare.
5. **Entrate sono reali, uscite sono parziali — dirlo.** Gli **incassi** esistono già (`Payment`, R4/R5). Le **uscite** oggi sono **solo i compensi docenti** (R8, motore compensi — *tabelle da risvegliare*), e mancano del tutto le **altre spese** (affitti, utenze, SIAE…). Il design mostra entrate/uscite ma **marca esplicitamente cosa è coperto e cosa no** (badge *parziale*), invece di fingere un conto economico completo.
6. **Export = bilancio sociale in 1 click.** L'output non è un cruscotto da guardare e basta: è la **fonte** del bilancio sociale (documento associativo annuale). Un pulsante porta via i numeri già aggregati (CSV/foglio + PDF stampabile) con anno e perimetro nel nome file. La direzione non ridigita nulla.
7. **Solo lettura, nessuna scrittura.** Questa vista **non modifica** dati: aggrega e mostra. Niente azioni distruttive, niente registrazione pagamenti qui (quello è R4). Riservata ai ruoli **direzione/admin** (non segreteria base) — coerente con GDPR (dati aggregati, non schede individuali esposte).

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| **Dashboard operativa** (homepage admin: lezioni settimana, scadenze 7gg, alert anagrafiche, KPI anno corrente) | ✅ esiste (`Admin\DashboardController` + commit *Dashboard #1*) — guarda **l'anno corrente**, taglio operativo |
| Iscritti per anno (`StudentYear`: `student_id`, `academic_year_id`, `status`, `withdrawn_at`) | ✅ dato esiste — **conteggio per anno fattibile**, ma la dashboard attuale lo mostra **solo per l'anno attivo** |
| Anni scolastici (`AcademicYear`: `name`, `start_date`, `end_date`, `is_active`) | ✅ esiste — è l'**asse del tempo** del confronto |
| Stati allievo per anno (`enrolled` / `interested` / `prospect` / `withdrawn`) | ✅ esistono — base di **iscritti** (enrolled) e **ritirati** (withdrawn) |
| Incassi reali (`Payment`: `payment_date`, `amount`) | ✅ esiste — base del **flusso di cassa** (entrate) |
| Fatturato/atteso (`Invoice.total_amount`, `PaymentPlan` scadenze) | ✅ esiste — base di **entrate maturate vs incassate** |
| **Confronto multi-anno** (serie storica nativa nel gestionale) | ❌ **assente** — la serie 2008-09…2025-26 vive **solo nell'ODS** (foglio `grafico`), a mano |
| **Retention calcolata** (% iscritti N-1 che si re-iscrivono N) | ❌ **assente** — il dato grezzo c'è (`StudentYear` per anno) ma **nessuna metrica derivata** |
| **Uscite / costi** (compensi docenti + altre spese) | ⚠️ **parziale** — compensi docenti **progettati** in R8 ma tabelle `teacher_hours` *da risvegliare*; **nessuna entità spese generali** (affitti, utenze, SIAE) |
| **Flusso di cassa nel tempo** (entrate−uscite per mese/anno) | ❌ assente come vista — i `Payment` ci sono, manca l'aggregazione temporale |
| **Export bilancio sociale** (CSV/PDF aggregati) | ❌ **assente** — nessun package export in `composer.json`, nessuna rotta di download aggregati |

> Conclusione: il **dato grezzo c'è quasi tutto** (iscritti per anno, incassi, anni). Mancano le **aggregazioni direzionali** (serie storica, retention, cassa nel tempo), una **vista dedicata** separata dall'operativa, e l'**export**. Le **uscite** sono il vero buco: coperte solo in parte (compensi R8) e da marcare onestamente. Nessuna tabella nuova *obbligatoria* per le entrate/iscritti (tutto derivabile); 1 entità opzionale per le **spese generali** se la direzione vuole il conto economico completo (vedi §10).

---

## 2. Architettura della vista (in chiave UX)

```
        DIREZIONE ▸ Cruscotto                     [Anno: 2025/26 ▾]  [vs 2024/25 ▾]
  ┌──────────────────────────────────────────────────────────────────────────────┐
  │  ① KPI DIREZIONE (4 card, valore + delta vs anno prec.)                       │
  │   ┌──────────┐ ┌──────────┐ ┌───────────┐ ┌────────────────┐                  │
  │   │ Iscritti │ │ Retention│ │ Incassato │ │ Entrate−Uscite │                  │
  │   └──────────┘ └──────────┘ └───────────┘ └────────────────┘                  │
  ├──────────────────────────────────────────────────────────────────────────────┤
  │  ② CONFRONTO ANNI (serie storica)   iscritti / incassi per anno scolastico     │
  ├──────────────────────────────────────────────────────────────────────────────┤
  │  ③ RETENTION (cohort)   chi c'era N-1 → rimasto / ritirato / nuovo N            │
  ├──────────────────────────────────────────────────────────────────────────────┤
  │  ④ FLUSSO DI CASSA   entrate vs uscite nel tempo (mese)   ⚠ uscite parziali    │
  ├──────────────────────────────────────────────────────────────────────────────┤
  │                       [ Esporta per bilancio sociale ▸ CSV | PDF ]             │
  └──────────────────────────────────────────────────────────────────────────────┘
```

- **Sorgenti dati (sola lettura, aggregate):**
  - Iscritti/retention → `StudentYear` raggruppato per `academic_year_id` e `status` (+ `withdrawn_at`).
  - Incassi/cassa → `Payment.amount` per `payment_date` (mese/anno).
  - Entrate maturate → `Invoice.total_amount` per anno; **uscite** → compensi R8 (`teacher_hours`, quando attivo) + spese generali (se introdotte).
- **Due selettori in alto a destra**: anno corrente + anno di **confronto** (default = precedente). Cambiarli ricalcola tutte le card e i delta via AJAX, senza ricaricare la pagina.
- **Accesso ristretto**: voce di menu **Direzione ▸ Cruscotto** visibile a ruoli direzione/admin; non alla segreteria base.

---

## 3. ① KPI direzione (le 4 card)

**Wireframe — fascia KPI (valore grande + confronto):**

```
┌─ ISCRITTI 2025/26 ─────┐ ┌─ RETENTION ────────────┐ ┌─ INCASSATO ────────────┐ ┌─ ENTRATE − USCITE ─────┐
│                        │ │                        │ │                        │ │            ⚠ parziale  │
│      312               │ │      78,4 %            │ │     94.300 €           │ │     +31.500 €          │
│   allievi iscritti     │ │  rimasti da 2024/25    │ │   incassi 2025/26      │ │  entrate − compensi    │
│                        │ │                        │ │                        │ │                        │
│  ↑ +22  (+7,6%) vs     │ │  ↓ −3,1 pt vs 2023/24  │ │  ↑ +6.800 € (+7,8%)    │ │  ↑ +4.200 € vs prec.   │
│     2024/25 (290)      │ │  227 rimasti / 290     │ │     vs 2024/25         │ │  (uscite: solo docenti)│
└────────────────────────┘ └────────────────────────┘ └────────────────────────┘ └────────────────────────┘
```

**Regole UX:**
- **Valore grande, delta sotto.** Ogni card: numero principale + **freccia ↑/↓** + variazione assoluta e % rispetto all'anno di confronto. Verde = miglioramento, rosso = peggioramento (ma **retention in calo** è rosso anche se "−3 punti" è piccolo: il colore segue il *significato*, non solo il segno).
- **Iscritti** = `StudentYear` con `status = enrolled` per l'anno selezionato (gli `interested`/`prospect` non sono iscritti; mostrabili come sotto-riga "+18 in valutazione").
- **Retention** = `% allievi iscritti N-1 che risultano iscritti anche N` (def. in §5). La card mostra **anche numeratore/denominatore** ("227 / 290") perché la percentuale da sola non è verificabile.
- **Incassato** = somma `Payment.amount` con `payment_date` dentro l'anno scolastico selezionato (start_date…end_date). È **cassa reale**, non fatturato.
- **Entrate − Uscite** = incassato (o entrate maturate, scegliere base coerente) − uscite **coperte**. Badge **⚠ parziale** sempre presente finché le uscite non includono le spese generali: la sotto-riga dice **cosa** è incluso ("uscite: solo compensi docenti").
- **Nessun anno di confronto disponibile** (primo anno a sistema): i delta diventano "— (nessun anno precedente)", la card resta leggibile.

---

## 4. ② Confronto anni (serie storica)

L'oggetto centrale della pagina: l'andamento **negli anni**, come il foglio `grafico` dell'ODS — ma vivo e filtrabile.

**Wireframe — serie storica (barre + linea):**

```
┌─ Andamento iscritti per anno scolastico ───────────────────  [Iscritti ▾] [Incassi] ┐
│                                                                                       │
│  340 ┤                                                                  ▆            │
│  320 ┤                                                          ▆   ▆   █  312        │
│  300 ┤                                              ▆   ▆   ▆   █   █   █             │
│  280 ┤                          ▆   ▆   ▆   ▆   ▆   █   █   █   █   █   █             │
│  260 ┤      ▆   ▆   ▆   ▆   ▆   █   █   █   █   █   █   █   █   █   █   █             │
│      └──────┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴───┴────────────│
│        '18  '19 '20 '21 '22 '23 …                      '23  '24  '25/26               │
│                                                                                       │
│  ◷ 2025/26: 312 iscritti  ·  +7,6% vs 2024/25  ·  media ultimi 5 anni: 296           │
└───────────────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Una serie per volta, switch in alto**: *Iscritti* (default) / *Incassi* / *Ritirati* / *Nuovi*. Stesso asse temporale (anni scolastici), così la direzione confronta la **stessa storia** da angolazioni diverse.
- **Anno selezionato evidenziato** (barra piena vs barre tenui) + tooltip su hover con valore esatto e delta.
- **Linea di media** (es. "media ultimi 5 anni") come riferimento, per capire se l'anno è sopra/sotto trend.
- **Dati storici pre-gestionale**: gli anni antecedenti alla migrazione (serie ODS 2008-09…) si mostrano se importati come **dato storico** (vedi §10); finché non importati, la serie parte dal primo anno a sistema, con nota "*storico pre-2024 da importare dall'ODS*".
- **Click su un anno** → aggiorna il selettore "Anno" e ricalcola le card §3 (drill-down naturale).

---

## 5. ③ Retention (cohort)

"Quanti rimangono" reso esplicito e verificabile: una **coorte** è l'insieme degli iscritti di un anno; la retention misura quanti di quella coorte ci sono ancora l'anno dopo.

**Definizione (mostrata in pagina):**

> **Retention N** = `iscritti in N-1 che risultano iscritti anche in N` ÷ `iscritti in N-1`.
> Numeratore e denominatore sono **conteggi di `StudentYear` con `status = enrolled`** sullo stesso `student_id`.

**Wireframe — cohort retention 2024/25 → 2025/26:**

```
┌─ Retention 2024/25 → 2025/26 ─────────────────────────────────────────────┐
│                                                                            │
│  Coorte 2024/25:  290 iscritti                                             │
│                                                                            │
│   ██████████████████████████████░░░░░░░░░   227 rimasti        78,4 %      │
│   ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░███████    63 ritirati/non rinn. 21,6 %   │
│                                                                            │
│  Nuovi ingressi 2025/26 (non in coorte):           +85                     │
│  ─────────────────────────────────────────────────────────────────────    │
│  Totale iscritti 2025/26:  227 rimasti + 85 nuovi  =  312                  │
│                                                                            │
│  Per corso ▾   Pianoforte 82% · Chitarra 75% · Canto 71% · Coro/Orch 88%   │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **La somma torna**: *rimasti + nuovi = iscritti anno corrente*. Mostrarlo esplicitamente rende la metrica auto-verificabile (allinea con la card §3).
- **Ritirati = non rinnovati**: chi era iscritto N-1 e in N non ha `StudentYear enrolled` (o ha `withdrawn`). Distinguere, se utile, *ritirati in corso d'anno* (`withdrawn_at` valorizzato) da *non rinnovati* (semplicemente assenti in N).
- **Breakdown per corso/strumento** (opzionale, a discesa): dove si perde di più. Riusa la dimensione corso del grafo R1/R3.
- **Coorte vuota** (primo anno): retention "non calcolabile — serve un anno precedente".

---

## 6. ④ Flusso di cassa — entrate vs uscite

**Wireframe — flusso di cassa per mese (anno scolastico):**

```
┌─ Flusso di cassa 2025/26 ─────────────────────  [Mensile ▾]   ⚠ uscite parziali ┐
│                                                                                  │
│  Entrate (incassi)  ▇▇▇▇▇   Uscite (compensi)  ▽▽▽▽▽                              │
│                                                                                  │
│  set ▇▇▇▇▇▇▇▇▇▇ 14.200   ▽▽▽▽▽ 6.100      saldo +8.100                            │
│  ott ▇▇▇▇▇▇▇ 9.800       ▽▽▽▽▽▽ 7.400     saldo +2.400                            │
│  nov ▇▇▇▇▇▇▇▇ 11.300     ▽▽▽▽▽▽ 7.400     saldo +3.900                            │
│  dic ▇▇▇▇▇ 7.100         ▽▽▽▽ 5.200       saldo +1.900                            │
│  …                                                                               │
│  ─────────────────────────────────────────────────────────────────────────      │
│  Totale entrate 94.300 €   ·   Totale uscite (solo docenti) 62.800 €             │
│  Saldo di cassa  +31.500 €              ⚠ non include affitti/utenze/SIAE/altro   │
└────────────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Entrate = cassa reale** (`Payment` per mese di `payment_date`), non fatturato — la direzione vuole sapere cosa è *entrato davvero*. Una sotto-vista può mostrare **maturato vs incassato** (fatturato `Invoice` vs `Payment`) per leggere il **credito ancora da incassare** (collega a R4/R5).
- **Uscite = solo ciò che il sistema conosce.** Oggi: **compensi docenti** (R8, quando `teacher_hours` è attivo). Il badge **⚠ uscite parziali** e la nota in fondo ("non include affitti/utenze/SIAE/altro") sono **obbligatori e sempre visibili**: meglio un saldo dichiaratamente incompleto che un conto economico falso.
- **Saldo per periodo** accanto a ogni mese, così si vedono i mesi critici (es. estate con pochi incassi).
- **Granularità switchabile**: mensile (default) / trimestrale / annuale.
- Quando (e se) entrano le **spese generali** (§10), il badge *parziale* sparisce e il saldo diventa un vero risultato di esercizio.

---

## 7. Export per bilancio sociale

L'output operativo della pagina: i numeri aggregati pronti per il **bilancio sociale** annuale dell'associazione, senza ridigitazione.

**Wireframe — modal export:**

```
┌──────── Esporta per bilancio sociale ────────────────────────────────────┐
│  Anno          ◉ 2025/26    ○ Confronto 2024/25 vs 2025/26                │
│  Contenuto     ☑ Iscritti per anno e per corso                           │
│                ☑ Retention (coorte, rimasti/ritirati/nuovi)              │
│                ☑ Flusso di cassa (entrate / uscite / saldo, mensile)     │
│                ☑ Entrate maturate vs incassate (credito residuo)         │
│  Formato       ◉ CSV / foglio   ○ PDF stampabile (per la relazione)      │
│                                                                          │
│  ⚠ Le uscite includono i soli compensi docenti (no spese generali).      │
│                                                                          │
│                          [ Annulla ]      [ Esporta ▸ ]                  │
└────────────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Due formati, due scopi**: **CSV/foglio** (la direzione rielabora nel proprio modello di bilancio) e **PDF stampabile** (allegato alla relazione associativa, con intestazione "L'Altra Musica · Bilancio sociale AA 2025/26").
- **Nome file parlante**: `bilancio-sociale_2025-26_altramusica.csv` (anno + perimetro nel nome) → niente file `export(3).csv` ambigui.
- **Il caveat viaggia col file**: l'export riporta in testa la stessa nota *uscite parziali*, così il documento non viene letto come conto economico completo.
- **Solo aggregati, mai schede individuali** nell'export di default (GDPR — coerente con R9/privacy): i nomi degli allievi non finiscono nel bilancio sociale, solo conteggi.
- L'export **rispetta i selettori** correnti (anno + confronto): ciò che vedi è ciò che esporti.

---

## 8. Casi e stati (riepilogo)

| Caso | Comportamento UX |
| --- | --- |
| **Anno corrente vs precedente** | Default: card §3 con delta vs anno precedente; serie §4 con anno evidenziato. |
| **Confronto con anno non adiacente** | Selettore "vs" libero (es. 2025/26 vs 2019/20) → ricalcolo delta su quell'anno. |
| **Primo anno a sistema** (no precedente) | Delta "—", retention "non calcolabile", serie con un solo punto + nota storico ODS. |
| **Storico pre-migrazione** | Anni ODS (2008-09…) mostrati se importati come dato storico; altrimenti serie parziale con nota. |
| **Uscite incomplete** | Badge ⚠ *parziale* + nota "solo compensi docenti" su card §3, flusso §6 ed export §7. |
| **Compensi R8 non ancora attivi** | Uscite = 0 con nota "motore compensi non attivo"; saldo = solo entrate, dichiarato. |
| **Retention per corso** | Breakdown a discesa; corso senza coorte N-1 → "n/d". |
| **Incassato vs maturato** | Sotto-vista §6: differenza = **credito da incassare** (link a R4/R5 riconciliazione). |
| **Ruolo non autorizzato** | Voce di menu nascosta; accesso diretto all'URL → redirect/403 (solo direzione/admin). |
| **Nessun dato nell'anno** | Stati vuoti leggibili ("nessun iscritto/incasso per 2025/26"), non errori. |

---

## 9. Microcopy (etichette IT)

- Sezioni: **"Direzione ▸ Cruscotto"** · **"KPI direzione"** · **"Confronto anni"** · **"Retention"** · **"Flusso di cassa"** · **"Esporta per bilancio sociale"**.
- KPI: **"Iscritti"**, **"Retention"**, **"Incassato"**, **"Entrate − Uscite"**.
- Delta: **"↑ +22 (+7,6%) vs 2024/25"** · **"↓ −3,1 pt vs anno prec."** · **"— (nessun anno precedente)"**.
- Retention: **"rimasti da 2024/25"**, **"ritirati / non rinnovati"**, **"nuovi ingressi"**, **"227 / 290"**.
- Cassa: **"Entrate (incassi)"**, **"Uscite (compensi)"**, **"saldo"**, **"maturato vs incassato"**, **"credito da incassare"**.
- Badge/avvisi: **"⚠ uscite parziali — solo compensi docenti"** · **"⚠ non include affitti/utenze/SIAE/altro"** · **"storico pre-2024 da importare dall'ODS"** · **"non calcolabile (serve un anno precedente)"**.
- Export: **"Esporta ▸"**, **"CSV / foglio"**, **"PDF stampabile"**, nome file **"bilancio-sociale_2025-26_altramusica"**.
- Titolo PDF: **"L'Altra Musica · Bilancio sociale — Anno Accademico 2025/26"**.

---

## 10. Impatti tecnici (per chi implementa — NON parte di questo R11)

Il design **riusa** dati già esistenti (`StudentYear`, `Payment`, `Invoice`, `AcademicYear`); l'unico dato davvero mancante sono le **spese generali** (opzionale) e lo **storico pre-migrazione** (opzionale).

- **Vista/Controller dedicato** `Admin\DirectorDashboardController` (separato da `DashboardController` operativo), rotta sotto policy ruolo **direzione/admin**. Sola lettura.
- **Servizio di aggregazione** `DirectorMetricsService` con metodi cache-abili per anno:
  - `enrollmentsByYear()` → `StudentYear::where('status','enrolled')->groupBy('academic_year_id')->count()` (+ breakdown per corso via `Enrollment`/`CourseOffering`).
  - `retention($yearN)` → set `student_id` iscritti in N-1 ∩ iscritti in N, su denominatore N-1; ritirati = differenza, distinguendo `withdrawn_at` (in corso) da assenza (non rinnovo).
  - `cashFlow($year)` → `Payment::sum('amount')` per mese di `payment_date` dentro `start_date…end_date` dell'`AcademicYear`; uscite = compensi R8 (`teacher_hours`/consuntivo) **se presenti**.
  - `accruedVsCollected($year)` → `Invoice.total_amount` vs `Payment` → credito residuo (link R4/R5).
- **Cache**: le aggregazioni sono pesanti e cambiano lentamente → cache per `academic_year_id` con invalidazione su nuovi `Payment`/`StudentYear` (o TTL giornaliero), così il cruscotto è istantaneo.
- **Uscite complete (opzionale, scelta della direzione)**: 1 tabella **`expenses`** (`academic_year_id`, `date`, `category` — affitto/utenze/SIAE/altro —, `amount`, `notes`) per chiudere il conto economico e togliere il badge *parziale*. Senza questa, le uscite restano = compensi docenti, dichiarato.
- **Storico pre-migrazione (opzionale)**: import una-tantum del foglio `grafico` dell'ODS (serie 2008-09…2023-24) in una tabella **`historical_year_stats`** (`year_label`, `enrolled`, `note`) per estendere il grafico §4 a sinistra. Riusa il pattern dry-run di `OdsImportService`.
- **Export**: `php` stream CSV (nessun package nuovo necessario — `fputcsv`/`StreamedResponse`) per il foglio; PDF via la libreria già in uso per i documenti (R10) o `dompdf` se assente. Solo aggregati, mai PII (GDPR).
- **Frontend grafici**: una libreria JS leggera (es. Chart.js) per barre/linea §4 e §6; degrada a tabella se JS off.

---

## 11. Checklist di accettazione (Definition of Done del design)

- [x] **KPI direzione** (iscritti, retention, incassato, entrate−uscite) con **valore + delta vs anno precedente**. — §3
- [x] **Confronto multi-anno** come serie storica nativa, switchabile (iscritti/incassi/ritirati/nuovi), con media di riferimento. — §4
- [x] **Retention calcolata e verificabile** (coorte N-1→N, rimasti/ritirati/nuovi, somma che torna, breakdown per corso). — §5
- [x] **Flusso di cassa** entrate vs uscite nel tempo, con saldo per periodo e **maturato vs incassato**. — §6
- [x] **Uscite parziali dichiarate** (badge + nota "solo compensi docenti") su card, flusso ed export — niente conto economico finto. — §0.5, §6, §7
- [x] **Export rapido bilancio sociale** (CSV/foglio + PDF), nome file parlante, solo aggregati (GDPR), caveat incluso nel file. — §7
- [x] **Separazione dall'operativa** (vista Direzione dedicata, accesso ruolo direzione/admin, sola lettura). — §0.1, §0.7, §2
- [x] Wireframe ASCII per KPI, serie storica, cohort retention, flusso cassa e modal export + microcopy IT. — §3–§9
- [x] Impatti tecnici senza implementare: servizio aggregazione + cache, riuso dati esistenti, 1 tabella opzionale spese, import storico ODS opzionale, export senza package nuovi. — §10
