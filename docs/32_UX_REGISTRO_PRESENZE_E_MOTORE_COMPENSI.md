# 32 — UX: Registro presenze e motore compensi (R8 · Design UX)

> Attività Footility **#8528** — *R8 · Design UX — Registro presenze e motore compensi* (progetto Gestionale Altramusica).
> Obiettivo: dare all'insegnante un **registro a un tap** per segnare le presenze lezione per lezione, e alla segreteria un
> **motore compensi configurabile** (soci/non-soci, valore orario personalizzabile, voci forfettarie, forzature ±) che
> sfocia in un **consuntivo mensile leggibile** — il "prospetto di previsione" che oggi si fa a mano in Excel.
> Regola d'oro del cliente: **l'insegnante è pagato solo se segna le presenze** (così il registro si compila da sé).
> ⚠️ **Tariffe DA CONFERMARE**: tutti i valori € in questo documento sono **placeholder**; il motore è progettato per
> renderli **configurabili**, non li fissa (vedi §2 e §9, riquadro "Valori da confermare").
> Deliverable: registro tap-presenza (wireframe) + modello concetti presenza/compenso + motore compensi configurabile
> + consuntivo mensile (prospetto) + casi reali + microcopy IT + impatti tecnici (riuso tabelle dormienti, **0 nuove tabelle**).
> Base AS-IS: [`08_DOCENTI_LAVORATORI.md`](08_DOCENTI_LAVORATORI.md) e [`09_CALENDARIO_ANNUALE.md`](09_CALENDARIO_ANNUALE.md).
> Riusa il calendario a cicli di [`26_UX_GENERATORE_CALENDARIO_E_RECUPERI.md`](26_UX_GENERATORE_CALENDARIO_E_RECUPERI.md) (R7),
> le iscrizioni di [`21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md`](21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md) (R2)
> e la contabilità per studente di [`25_UX_VISTA_CONTABILITA_PER_STUDENTE.md`](25_UX_VISTA_CONTABILITA_PER_STUDENTE.md) (R4).

---

## 0. Principi di design

1. **La presenza è un tap, non un form.** L'insegnante apre la lezione del giorno e tocca il nome: presente → assente → ritardo → giustificato, ciclando sullo stesso bottone. Niente salva/conferma per ogni riga: lo stato si scrive subito, in modo ottimistico. Il registro deve potersi compilare **dal telefono, in aula, in 10 secondi**.
2. **Pagato = segnato.** È la leva del cliente: l'insegnante viene retribuito **solo per le lezioni di cui ha preso le presenze**. Il motore compensi conta le lezioni *con registro compilato*, non quelle a calendario. Questo trasforma il registro da adempimento a interesse personale dell'insegnante.
3. **Presenza dell'allievo ≠ paga dell'insegnante.** In un corso regolare l'insegnante è pagato **anche se l'allievo è assente** (la lezione c'è stata). La presenza dell'allievo serve alla *segreteria* (monitorare frequenza, attestati, pacchetti); la presenza/erogazione della *lezione* serve al *compenso*. Sono due fatti distinti, registrati nello stesso gesto.
4. **Il motore è configurabile, non cablato.** Soci e non-soci hanno tariffe diverse (siamo una cooperativa); ogni insegnante può avere un **valore orario personalizzato** e **voci forfettarie** (supervisione progetto, funzioni). Le **tariffe sono DA CONFERMARE**: il design le tratta come dati di configurazione, mai come costanti nel codice.
5. **Forzare in ± è normale, non un'eccezione sporca.** "Hai segnato la presenza ma la lezione non l'hai fatta": la segreteria deve poter **togliere** quella voce dal conto. E aggiungere voci una tantum. La forzatura manuale è una funzione di prima classe, tracciata (chi/quando/perché).
6. **Il consuntivo è una previsione onesta, non un cedolino al centesimo.** Il cliente non paga al centesimo del giorno: concorda con l'insegnante **quanti pagamenti** vuole nell'anno, divide il totale in parti uguali (arrotondate per difetto), e l'**ultimo va a saldo** dopo verifica. Il consuntivo mensile è il prospetto leggibile da mandare all'insegnante.
7. **Niente nuove tabelle.** Lo schema esiste già — **dormiente**: `attendances` (presenze per lezione) e `teacher_hours` (conto orario per periodo, con `hourly_rate`/`base_amount`/`bonus_amount`/`forfait_amount`/`total_amount`/`status`). Questo design li **risveglia** e li mette in scena; non aggiunge tabelle, al più una tabella di **listino tariffe** se la configurazione soci/non-soci non sta in `Setting` (§10).

---

## 1. Stato attuale (gap rilevati nel codice)

| Cosa | Stato oggi |
| --- | --- |
| Tabella `attendances` (`lesson_id`, `student_id`, `status` enum `present/absent/late/excused`, `notes`, unique `lesson+student`) | ✅ **esiste** (migration `2025_12_22_162048`) |
| Tabella `teacher_hours` (`period_start/end`, `lessons_count`, `hours_total`, `hourly_rate`, `base_amount`, `bonus_amount`, `forfait_amount`, `total_amount`, `status` `draft/calculated/approved/paid`, `approved_by/at`) | ✅ **esiste** (migration `2025_12_22_213027`) |
| `Lesson` (`course_offering_id`, `teacher_id`, `substitute_teacher_id`, `date`, `time_start/end`, `completed`, `classroom_id`) + scope `forTeacher` | ✅ esiste |
| `Teacher.contract_type` (string libero) | ⚠️ esiste ma **non tipizzato** socio/non-socio |
| **Model `Attendance`** | ❌ **assente** — la tabella c'è ma nessun model la usa |
| **Relazione `Lesson::attendances()`** | ❌ **rimossa**: commento esplicito *"Fase 1: rimosse relazioni verso moduli extra non AS-IS (presenze)"* |
| **Model `TeacherHour`** + controller + viste | ❌ **assente** — tabella ricca ma **dormiente**, nessuna UI |
| **Registro presenze (tap per allievo)** | ❌ assente — nessuna vista lezione con elenco allievi |
| **Erogazione lezione dell'insegnante** (`completed`) collegata al compenso | ⚠️ il flag `completed` esiste ma non è usato come driver di paga |
| **Listino tariffe soci/non-soci + valore orario per docente + forfait** | ❌ assente — nessuna configurazione |
| **Forzature ± sul conto** | ❌ assente |
| **Consuntivo/prospetto mensile per insegnante** | ❌ assente |
| **Compenso al supplente** per la lezione coperta | ❌ assente (il dato `substitute_teacher_id` c'è, ma non guida il compenso) |
| **Categoria socio/non-socio tipizzata** | ❌ assente |

> Conclusione: lo **schema dati esiste già per metà** (`attendances` + `teacher_hours`) ma è **dormiente** — nessun model, nessun controller, nessuna vista. R8 lo **risveglia** disegnando i due gesti (tap-presenza, motore compensi) e il consuntivo. Realizzabile **senza nuove tabelle**, salvo eventualmente un piccolo **listino tariffe** se non si usa `Setting` (§10).

---

## 2. Modello concetti ↔ linguaggio cliente

Il cliente ragiona per "conto orario", "soci/non-soci", "forfait", "prospetto". Allineamento ai dati:

| Concetto cliente | Dove vive (dato) | Note |
| --- | --- | --- |
| **Presenza allievo** (c'è / assente / ritardo / giustificato) | `attendances.status` | serve a segreteria/attestati; **non** determina la paga in corso regolare |
| **Lezione erogata** (l'ho fatta) | `lessons.completed` + esistenza presenze | **driver del compenso**: pagato = lezione completata e segnata |
| **Insegnante effettivo** (titolare o supplente) | `lessons.teacher_id` / `substitute_teacher_id` | il compenso va a **chi ha fatto** la lezione |
| **Socio / non-socio** | (nuovo) categoria docente | tariffa differenziata; oggi `contract_type` è libero → da tipizzare |
| **Valore orario** | `teacher_hours.hourly_rate` (+ default da listino) | personalizzabile per docente; **DA CONFERMARE** |
| **Conto base** (ore × tariffa) | `teacher_hours.base_amount` | calcolato dalle lezioni segnate del periodo |
| **Forfait** (supervisione/funzione) | `teacher_hours.forfait_amount` | voce fissa, non oraria |
| **Forzatura / bonus / storno** | `teacher_hours.bonus_amount` (anche negativo) + `notes` | aggiunte/tagli manuali tracciati |
| **Totale periodo** | `teacher_hours.total_amount` | `base + forfait + bonus` |
| **Stato consuntivo** | `teacher_hours.status` | `draft → calculated → approved → paid` |
| **Prospetto di previsione** | aggregazione `teacher_hours` per docente/anno | leggibile, da inviare all'insegnante |

> ⚠️ **Valori da confermare con il cliente** (placeholder in tutto il doc): tariffa oraria **socio** €/h, tariffa oraria **non-socio** €/h, eventuale tariffa **funzioni speciali**, arrotondamento (per difetto a €), durata standard lezione (30/45/60 min) per la conversione ore. Il motore li legge dalla **configurazione**, non li fissa.

---

## 3. Registro presenze — il tap (deliverable centrale lato insegnante)

Vista lezione: l'insegnante apre la lezione del giorno e vede **solo l'essenziale** — gli allievi iscritti, un bottone-stato per ciascuno, e un'unica conferma "lezione fatta". Pensata mobile-first.

**Vista lezione (mobile, tap-presenza):**
```
┌──────────────────────────────────────────────┐
│ ‹ Oggi   Pianoforte · Sala 2 · 17:00–17:30    │
│          Mar 26/05 · titolare: Anna Bianchi    │
├──────────────────────────────────────────────┤
│  PRESENZE                          3 allievi   │
│                                                │
│  Mario Rossi            [ ✅ Presente   ]      │
│  Giulia Verdi           [ 🟠 Ritardo    ]      │
│  Luca Neri              [ ⚪ — segna     ]      │
│                                                │
│  (tocca lo stato per ciclare:                  │
│   Presente → Assente → Ritardo → Giustific.)   │
├──────────────────────────────────────────────┤
│  ☐ Sostituisco un collega (sono il supplente)  │
│  Note lezione (opz.) [____________________]    │
│                                                │
│            [  ✓ Lezione fatta — chiudi  ]      │
└──────────────────────────────────────────────┘
```

**Regole UX:**
- **Tap ciclico** sul bottone stato: `Presente → Assente → Ritardo → Giustificato → (di nuovo Presente)`. Default visivo "— segna" finché non si tocca. Salvataggio **ottimistico** per riga (`attendances` upsert su `lesson+student`).
- **"Lezione fatta — chiudi"** marca `lessons.completed = true`: è il gesto che **attiva il compenso** (principio §2). Finché non è chiusa, la lezione **non entra** nel conto orario.
- **Supplenza**: la spunta "Sostituisco un collega" registra che chi compila è il **supplente** → il compenso di quella lezione andrà a lui, non al titolare (§7, caso supplente). Account provvisorio del supplente vede **solo** quella lezione, non lo storico del titolare (richiesta cliente).
- **Lezione di gruppo / orchestra** (più insegnanti, molti allievi): elenco allievi del gruppo, **uno solo** degli insegnanti presenti prende le presenze; gli altri compaiono come co-presenti (vedi §7).
- **Presenza allievo ≠ paga**: marcare un allievo "Assente" **non** toglie il compenso in corso regolare; serve solo a frequenza/attestati/pacchetti.

### 3b. Vista segreteria — registro per lezione/giorno
La segreteria vede lo stesso dato in chiave di controllo: per giorno/corso, quali lezioni sono **chiuse** (compensabili) e quali **aperte** (in attesa di registro), così sa quale insegnante deve ancora compilare (= non sarà pagato finché non compila).
```
Lun 25/05  · Pianoforte 17:00  Anna B.   ● chiusa   3/3 segnati
           · Violino   18:00   Marco V.  ○ aperta   — nessun registro  ⚠ non compensata
```

---

## 4. Pacchetti e recuperi (caso "5 lezioni")

Caso reale del cliente: l'allievo compra **5 lezioni da 30'**; solo la prima è a calendario, le altre l'insegnante le piazza nei buchi e le segna man mano. Quando il pacchetto si esaurisce, il corso si chiude.

**Regole UX:**
- Ogni presenza segnata su una lezione del pacchetto **scala il contatore** (5 → 4 → 3 …); il consuntivo studente (R4) e quello insegnante leggono lo stesso conteggio.
- Quando restano **≤ 1 lezione**, avviso a segreteria *"Pacchetto in esaurimento — proporre rinnovo"* (è interesse dell'insegnante far ricontattare la segreteria per vendere altre 5 lezioni).
- Il **recupero gratuito** (lezione persa che l'insegnante decide di rifare): si segna come lezione erogata; sul **compenso** può contare o no a seconda dell'accordo → gestito come voce normale, eventualmente **stornata** con forzatura (§6) se non dovuta.

---

## 5. Motore compensi — configurazione (lato segreteria)

Tre livelli di configurazione, dal generale al particolare. **Tutti i € sono placeholder DA CONFERMARE.**

**Wireframe — Configurazione compensi:**
```
┌──── Compensi · Configurazione (anno 2025/26) ────────────────────────┐
│  LISTINO BASE (DA CONFERMARE)                                         │
│   Tariffa oraria socio        [  __,__ € ] /h                         │
│   Tariffa oraria non-socio    [  __,__ € ] /h                         │
│   Funzioni speciali           [ + aggiungi voce ]                     │
│   Durata lezione standard     [ 30 min ▾ ]   Arrotondamento [ per difetto ▾ ]
├───────────────────────────────────────────────────────────────────────┤
│  PER INSEGNANTE (override del listino)                                │
│   Anna Bianchi · socia        valore orario [ ereditato ▾ ]           │
│   Marco Verdi  · non-socio    valore orario [  __,__ € ]  (override)   │
│   Sara Gialli  · socia        valore orario [ ereditato ▾ ]           │
│                               + forfait: "Supervisione orchestra" [ __,__ € / anno ]
└───────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Eredità**: ogni docente eredita la tariffa di categoria (socio/non-socio); l'**override** per-docente vince. Mostrato esplicitamente ("ereditato" vs "override") per non perdere il filo.
- **Categoria socio/non-socio** tipizzata sul docente (oggi `contract_type` libero → enum/flag dedicato). È la prima discriminante di tariffa.
- **Forfait**: voce **annua/periodica fissa**, slegata dalle ore (es. "Supervisione orchestra", "Coordinamento progetto"). Confluisce in `forfait_amount`.
- **Funzioni speciali**: voci di listino aggiuntive (es. esami, saggi) assegnabili a un docente con tariffa propria.

---

## 6. Forzature ± (aggiunte e storni tracciati)

Il conto si deve poter **forzare in positivo e in negativo**, con traccia. Vive in `teacher_hours.bonus_amount` (può essere negativo) + `notes`.

| Azione | Effetto | Esempio cliente |
| --- | --- | --- |
| **Aggiungi voce (+)** | `bonus_amount += x` | extra una tantum, rimborso, premio |
| **Storna voce (−)** | `bonus_amount -= x` | *"hai segnato la presenza ma la lezione non l'hai fatta"* → tolgo quella lezione |
| **Forfait** | confluisce in `forfait_amount` | supervisione progetto a forfait |
| **Nota motivazione** | testo obbligatorio sulla forzatura | tracciabilità chi/quando/perché |

```
┌──── Forzatura conto · Marco Verdi · maggio 2026 ──────────┐
│  Tipo      ( ) Aggiungi   (•) Storna                       │
│  Importo   [  18,00 € ]                                    │
│  Motivo *  [ lezione 14/05 segnata ma non erogata ]        │
│                                  [ Annulla ] [ Applica ]   │
└────────────────────────────────────────────────────────────┘
```
> La forzatura **non** riscrive il calcolo base: lo affianca come riga tracciata, così il consuntivo resta leggibile (base + forfait ± forzature = totale).

---

## 7. Stati e casi reali

| Caso | Comportamento UX |
| --- | --- |
| **Lezione regolare, allievo presente** | tap Presente; "Lezione fatta" → `completed`; entra nel conto del titolare alla sua tariffa. |
| **Lezione regolare, allievo assente** | tap Assente; la lezione **resta compensata** (corso regolare); l'allievo risulta assente per frequenza/attestati. |
| **Insegnante non segna le presenze** | lezione **aperta**, **non compensata**; segreteria vede ⚠; è la leva che spinge l'insegnante a compilare. |
| **Supplenza** | il supplente apre la lezione, spunta "Sostituisco un collega" → compenso di quella lezione al **supplente**; titolare escluso per quella data. Supplente vede solo quella lezione. |
| **Gruppo / orchestra (più docenti)** | uno solo prende le presenze degli allievi; i co-docenti presenti sono registrati → ognuno matura il proprio compenso per la seduta. |
| **Pacchetto 5 lezioni** | ogni presenza scala il contatore; a esaurimento, avviso rinnovo; compenso per lezione effettivamente segnata. |
| **Recupero gratuito** | segnato come erogato; se non dovuto sul compenso, **storno** via forzatura (§6). |
| **Forzatura negativa** | lezione segnata ma non fatta → storno tracciato; totale periodo si abbassa. |
| **Forfait progetto** | nessuna ora, ma voce `forfait_amount` nel periodo concordato. |
| **Consuntivo già approvato** | `status=approved/paid` → registro di quel periodo **bloccato**; modifiche solo riaprendo (azione confermata). |

---

## 8. Consuntivo mensile / prospetto di previsione (deliverable lato segreteria)

Il "prospetto di previsione" che oggi si fa a mano: per insegnante, per periodo, leggibile e inviabile. Non al centesimo: previsione arrotondata, ultimo pagamento a saldo.

**Wireframe — Consuntivo insegnante (mese):**
```
┌──── Consuntivo · Anna Bianchi · socia · maggio 2026 ────────────────┐
│  Stato: ● Calcolato        [ Approva ]  [ Esporta PDF ]  [ Invia ]   │
├──────────────────────────────────────────────────────────────────────┤
│  Lezioni segnate (chiuse)        24 lezioni · 12,0 h                 │
│  Valore orario (socia)           __,__ €/h          base   ___,__ € │
│  Forfait "Supervisione orchestra"                   forf.  ___,__ € │
│  Forzature                       +0,00 / −18,00 €   adj.   −18,00 € │
│  ─────────────────────────────────────────────────────────────────  │
│  TOTALE PERIODO                                     ▶ ____,__ €     │
├──────────────────────────────────────────────────────────────────────┤
│  PIANO PAGAMENTI (concordato: 9 mensilità)                          │
│   maggio = quota ____,__ € (arrot. per difetto) · saldo a giugno    │
└──────────────────────────────────────────────────────────────────────┘
```

**Regole UX:**
- **Solo lezioni chiuse** (segnate) entrano nel conto (§2). Le aperte sono escluse e segnalate come "in attesa di registro".
- **Piano pagamenti concordato**: l'insegnante sceglie quante rate nell'anno (mensile / trimestrale / 2 volte / solo a giugno). Il motore divide il totale previsto in parti uguali **arrotondate per difetto**; **l'ultima rata va a saldo** dopo verifica dell'insegnante.
- **Stati**: `draft` (in compilazione) → `calculated` (numeri pronti) → `approved` (segreteria conferma, periodo bloccato) → `paid` (pagato). Approvare blocca il registro di quel periodo.
- **Esporta/Invia**: PDF leggibile dell'insegnante (oggi fatto a mano in Excel). L'invio email reale è materia Fase 2; R8 produce il prospetto.
- **Vista d'insieme economica** (richiesta cliente "uscite insegnanti"): elenco di tutti i docenti con totale periodo + stato → quanto sto per pagare questo mese.

---

## 9. Innesto futuro (NON parte di R8)

- **Area insegnante self-service**: oggi il prospetto lo manda la segreteria; domani l'insegnante consulta il proprio conto orario in tempo reale (il dato c'è già aggregato in `teacher_hours`). Richiede solo una vista read-only filtrata sul docente.
- **Tariffe confermate → listino versionato**: quando il cliente conferma i valori, il listino può diventare versionato per anno (storicizzare le tariffe), senza ridisegnare il motore.
- **Collegamento contabilità uscite** (R4/contabilità): i `total_amount` approvati alimentano il quadro entrate/uscite ("pagare gli insegnanti") già evocato nelle trascrizioni — aggancio naturale, non in R8.
- **Generazione lettere d'incarico/contratti insegnante**: il cliente le fa "alla vecchia maniera"; eventuale automazione futura riusa il flusso contratto di [`30_UX_FLUSSO_CONTRATTO_E_FIRMA.md`](30_UX_FLUSSO_CONTRATTO_E_FIRMA.md) (R3).

> R8 si ferma al **gesto** (tap-presenza), al **motore configurabile** e al **consuntivo**. Self-service insegnante e listino versionato sono direzioni documentate, non implementate.

---

## 10. Impatti tecnici (per chi implementa — NON parte di questo R8)

Il design **risveglia tabelle esistenti**; nessuna nuova tabella obbligatoria:

- **Model `Attendance`** (mancante): mappa la tabella `attendances` già presente; ripristinare `Lesson::attendances()` (oggi rimossa) e `belongsTo(Lesson/Student)`. Upsert per `lesson_id+student_id` (unique già a DB).
- **Registro tap**: vista lezione mobile-first con upsert ottimistico riga per riga; `lessons.completed=true` come trigger di compensabilità. Account supplente con policy che limita la visibilità alla sola lezione coperta.
- **Model `TeacherHour`** (mancante): mappa `teacher_hours` (schema già ricco: `hourly_rate`, `base_amount`, `bonus_amount`, `forfait_amount`, `total_amount`, `status`, `approved_by/at`). Controller + viste consuntivo.
- **Servizio di calcolo**: aggrega le lezioni **chiuse e segnate** del periodo per docente effettivo (titolare o supplente), moltiplica per la tariffa risolta (override docente → categoria → listino), somma forfait e forzature → scrive `teacher_hours`. Idempotente per (docente, periodo).
- **Tariffe**: tariffa socio/non-socio + durata standard + arrotondamento in `Setting` (chiave/valore) **oppure** una piccola tabella `compensation_rates` se serve storicizzazione per anno — **unica eventuale tabella nuova**, decisione da prendere alla conferma tariffe.
- **Categoria socio/non-socio**: tipizzare (enum/flag dedicato sul `Teacher`); oggi `contract_type` è stringa libera.
- **Forzature**: confluiscono in `bonus_amount` (segno) + `notes`; nessuna struttura nuova richiesta per il MVP.
- **Piano pagamenti docente**: numero rate concordato + arrotondamento per difetto + ultima a saldo — logica di presentazione sul totale `teacher_hours`, non un nuovo schema.

---

## 11. Checklist di accettazione (Definition of Done del design)

- [x] **Registro presenze a tap** (ciclo Presente/Assente/Ritardo/Giustificato), mobile-first, salvataggio ottimistico. — §3
- [x] **Regola "pagato = segnato"**: solo lezioni chiuse e segnate entrano nel compenso. — §2, §3, §8
- [x] **Presenza allievo distinta dalla paga insegnante** (assente ≠ lezione non pagata in regolare). — §0, §7
- [x] **Supplenza**: compenso al supplente, accesso limitato alla lezione coperta. — §3, §7
- [x] **Lezioni di gruppo/orchestra** con un solo rilevatore presenze. — §3, §7
- [x] **Pacchetti e recuperi** (contatore 5 lezioni, avviso rinnovo, storno recupero non dovuto). — §4
- [x] **Motore compensi configurabile**: listino socio/non-socio, override per docente, forfait, funzioni — **tariffe DA CONFERMARE** come placeholder. — §2, §5
- [x] **Forzature ±** tracciate (aggiunta/storno con motivo). — §6
- [x] **Consuntivo mensile leggibile** = prospetto di previsione, stati `draft→calculated→approved→paid`, piano pagamenti concordato (arrot. per difetto, ultima a saldo). — §8
- [x] **Casi reali** coperti (regolare, assente, non segnata, supplenza, gruppo, pacchetto, recupero, forzatura, forfait, periodo bloccato). — §7
- [x] **Wireframe ASCII** (registro tap, configurazione, forzatura, consuntivo) + microcopy IT. — §3–§8
- [x] **Impatti tecnici minimi**: risveglio di `attendances` + `teacher_hours` (tabelle dormienti), **0 nuove tabelle** obbligatorie (al più listino tariffe alla conferma). — §10
