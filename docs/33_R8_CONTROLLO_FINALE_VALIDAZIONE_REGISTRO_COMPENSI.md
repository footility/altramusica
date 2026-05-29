# 33 — R8 · Controllo finale — Validazione registro/presenze/compensi

> Attività Footility **#8547** — *R8 · Controllo finale — Validazione registro/presenze/compensi* (Gestionale Altramusica).
> Esito: **REPORT con BLOCCO parziale**. Validato il **substrato** (tabelle dormienti) e i **flussi del design** a livello dati; l'E2E su controller/UI **non è eseguibile** perché il modulo è interamente dormiente (vedi §1).
> Riferimenti: design [`32_UX_REGISTRO_PRESENZE_E_MOTORE_COMPENSI.md`](32_UX_REGISTRO_PRESENZE_E_MOTORE_COMPENSI.md) (#8528).
> Test: [`tests/Feature/RegistroPresenzeCompensiE2EValidationTest.php`](../tests/Feature/RegistroPresenzeCompensiE2EValidationTest.php) — **17/17 PASS**, suite **76/76**.
> Trascrizioni: parte 1 r.168-220, parte 2 r.108-114.

---

## 0. Sintesi

R8 (#8528) è **solo design**: il §10 del doc 32 dichiara esplicitamente che l'implementazione **non è parte di R8**. Le tabelle `attendances` e `teacher_hours` **esistono** (migration `2025_12_22`) ma sono **dormienti** — nessun model, nessun controller, nessuna rotta (in `routes/web.php` le risorse sono commentate *"attendances/teacher-hours evolutivo"*), nessuna vista, nessun servizio di calcolo.

Per questo un E2E su controller/UI come nelle R3/R6/R7 **non è possibile** (= il *"blocco"* previsto dalla richiesta). Il controllo finale fa quindi due cose oneste:

1. **Valida il substrato e i flussi**: dimostra che le tabelle dormienti **supportano davvero** il design, esercitando a livello DB + spec-motore i tre gesti richiesti (registra presenze / calcola compenso / override manuale), con **tariffe DA CONFERMARE lette da configurazione** (`Setting`), mai cablate.
2. **Fotografa il blocco**: assert che model/relazione/rotte sono assenti → il test resta un **registro vivo del gap** da colmare in implementazione.

---

## 1. BLOCCO — cosa manca per un E2E reale (fase implementazione, non R8)

| Componente | Stato | Coperto da assert |
| --- | --- | --- |
| Model `App\Models\Attendance` (mappa `attendances`) | ❌ assente | `test_blocco_model_attendance_e_teacherhour_assenti` |
| Model `App\Models\TeacherHour` (mappa `teacher_hours`) | ❌ assente | idem |
| Relazione `Lesson::attendances()` | ❌ rimossa in Fase 1 | `test_blocco_relazione_lesson_attendances_assente` |
| Rotte `admin.attendances.*` / `admin.teacher-hours.*` | ❌ commentate "evolutivo" | `test_blocco_rotte_presenze_e_compensi_assenti` |
| Controller + viste (registro tap, configurazione, consuntivo) | ❌ assenti | — |
| Servizio di calcolo compensi (idempotente per docente/periodo) | ❌ assente (qui solo come **spec** nel test) | — |
| Categoria socio/non-socio **tipizzata** sul `Teacher` | ❌ `contract_type` stringa libera | `test_blocco_categoria_socio_non_tipizzata` |
| Policy supplente (accesso alla sola lezione coperta, niente storico/economici) | ❌ assente | — |

> Tutti riconducibili al doc 32 §10 ("Impatti tecnici — NON parte di questo R8").

---

## 2. VALIDATO — il substrato dormiente regge il design

- **Tabelle pronte**: `attendances` (`lesson_id`, `student_id`, `status` enum, `notes`, unique `lesson+student`) e `teacher_hours` (schema ricco: `hourly_rate`/`base_amount`/`bonus_amount`/`forfait_amount`/`total_amount`/`status`/`approved_by`/`approved_at`).
- **Registra presenze** (§3): tap ciclico `present→absent→late→excused`, **upsert** idempotente per `lesson+student`, **vincolo unique** attivo a DB.
- **Pagato = segnato** (§2): solo lezioni **chiuse (`completed`) E segnate** entrano nel conto; aperte o non segnate escluse. Una lezione aperta+segnata e una chiusa-ma-non-segnata **non** sono compensate.
- **Presenza allievo ≠ paga** (§0/§7): allievo `absent` su lezione chiusa → lezione **resta compensata**.
- **Compenso al supplente** (§7): `forTeacher` copre `teacher_id` e `substitute_teacher_id` → il conto della lezione coperta va al supplente.
- **Tariffe DA CONFERMARE** (§2/§5): tariffa risolta **override docente → categoria → listino**, letta da `Setting`. Senza configurazione la tariffa è **0,00 € placeholder** (non un valore cablato); cambiare la `Setting` cambia il calcolo; socio vs non-socio differenziati; override per-docente vince sull'eredità.
- **Override manuale / forzature ±** (§6): storno negativo tracciato con **motivo obbligatorio** (`bonus_amount` < 0 + `notes`), base intatta; bonus positivo e **forfait** confluiscono nel totale (`total = base + forfait + bonus`).
- **Piano pagamenti** (§8): rate **arrotondate per difetto**, **ultima a saldo**, nessun centesimo perso — pura presentazione sul `total_amount`.

---

## 3. Chiavi di configurazione proposte (DA CONFERMARE col cliente)

Usate dal test come spec del futuro motore (in `Setting`, key/value):

```
compensi.tariffa_oraria_socio              €/h   (DA CONFERMARE → default 0,00)
compensi.tariffa_oraria_non_socio          €/h   (DA CONFERMARE → default 0,00)
compensi.durata_lezione_standard_min       min   (default 30)
compensi.docente.{id}.categoria            socio | non_socio
compensi.docente.{id}.tariffa_oraria       €/h   (override; null = ereditato)
```

> In fase di conferma tariffe si potrà valutare una tabella `compensation_rates` versionata per anno (doc 32 §10) — **unica eventuale tabella nuova**.

---

## 4. Raccomandazione

Il design è solido e il **substrato c'è**: l'implementazione può procedere a basso rischio (0 nuove tabelle obbligatorie). Ordine suggerito: **(1)** model `Attendance` + ripristino `Lesson::attendances()`; **(2)** registro tap mobile con upsert ottimistico e `completed` come trigger; **(3)** model `TeacherHour` + servizio di calcolo (riusa la spec del test); **(4)** consuntivo/prospetto + forzature; **(5)** policy supplente. Il test di questo R8 fa già da **specifica eseguibile** e da rete di sicurezza per quei passi.
