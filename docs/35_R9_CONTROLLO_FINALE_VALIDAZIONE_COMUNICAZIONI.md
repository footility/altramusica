# 35 — R9 · Controllo finale — Validazione comunicazioni mirate

> Attività Footility **#8548** — *R9 · Controllo finale — Validazione comunicazioni* (Gestionale Altramusica).
> Esito: **REPORT con BLOCCO parziale**. Validati i **flussi del design** (segmentazione, consenso GDPR, dedup, email reale, bozza SMS/WhatsApp, log) a livello dati+framework; l'E2E su controller/UI **non è eseguibile** perché il modulo comunicazioni è dormiente (vedi §1).
> Riferimenti: design [`34_UX_COMUNICAZIONI_MIRATE_E_LOG.md`](34_UX_COMUNICAZIONI_MIRATE_E_LOG.md) (#8529).
> Test: [`tests/Feature/ComunicazioniMirateE2EValidationTest.php`](../tests/Feature/ComunicazioniMirateE2EValidationTest.php) — **19/19 PASS**, suite **95/95**.
> Trascrizioni: parte 1 r.74-76, 178-180 — parte 2 r.10.

---

## 0. Sintesi

R9 (#8529) è **solo design**: il §10 del doc 34 dichiara esplicitamente che l'implementazione **non è parte di R9**. La tabella `communications` **esiste** (migration `2025_12_11` + `2025_12_22`) ma è **dormiente** — nessun model `Communication`, nessun `CommunicationService`, nessuna classe `Mailable` (la cartella `app/Mail` non esiste), nessun controller, nessuna rotta (in `routes/web.php` la risorsa è commentata *"communications evolutivo"*), nessuna vista. Manca anche la tabella `communication_recipients` (esito per-destinatario, §10) e in `.env` resta `MAIL_MAILER=log` (non SMTP reale).

Per questo un E2E su controller/UI come nelle R3/R6/R7 **non è possibile** (= il *"blocco"* previsto dalla richiesta). Il controllo finale fa quindi due cose oneste:

1. **Valida i flussi del design**: dimostra che entità e tabella dormiente **supportano davvero** il design, esercitando a livello DB + framework i quattro gesti richiesti — **componi messaggio**, **segmenta destinatari** (corso/anno/stato), **invia email reale**, **prepara bozza SMS/WhatsApp** — con **risoluzione contatti famiglia + consenso GDPR + dedup fratelli** e **log per tracciabilità**.
2. **Fotografa il blocco**: assert che model/service/Mailable/recipients/rotte sono assenti → il test resta un **registro vivo del gap** da colmare in implementazione.

---

## 1. BLOCCO — cosa manca per un E2E reale (fase implementazione, non R9)

| Componente | Stato | Coperto da assert |
| --- | --- | --- |
| Model `App\Models\Communication` (mappa `communications`) | ❌ assente | `test_blocco_model_communication_assente` |
| Service `App\Services\CommunicationService::resolveRecipients()` | ❌ assente (qui solo come **spec** nel test) | `test_blocco_service_risoluzione_destinatari_assente` |
| Classe `App\Mail\CommunicationMail` (invio email reale) | ❌ assente (cartella `app/Mail` non esiste) | `test_blocco_mailable_communicationmail_assente` |
| Tabella `communication_recipients` (esito per-destinatario, §10) | ❌ assente | `test_blocco_tabella_communication_recipients_assente` |
| Colonne design su `communications` (`is_operational`, `segment` snapshot, contatori, stati `draft/queued/manual_*`) | ❌ assenti | `test_blocco_schema_dormiente_non_copre_design` |
| Rotte `admin.communications.*` (index/create/store/mark-sent/preview) | ❌ commentate "evolutivo" | `test_blocco_rotte_communications_assenti` |
| Controller + viste (composer, drawer destinatari, log) | ❌ assenti | — |
| `MAIL_MAILER=smtp` + credenziali in `.env` (oggi `=log`) | ❌ da configurare per invio reale in prod | — (nota §3) |

> Tutti riconducibili al doc 34 §10 ("Impatti tecnici — NON parte di questo R9").

---

## 2. VALIDATO — i flussi del design reggono sul dato esistente

- **Substrato pronto**: tabella `communications` con `type` (enum `email|whatsapp|sms|phone|letter`), `subject`, `message`, `recipients` (JSON), `status`, `sent_by_user_id`, `sent_at`, `template_name`, `error_message`. I tre canali del design (`email/sms/whatsapp`) sono accettati dall'enum.
- **Segmentazione** (§2/§3): segmento come **AND** di filtri opzionali su entità esistenti — **corso** (`CourseOffering`), **anno** (`AcademicYear`), **stato iscrizione** (`Enrollment.status`: active/suspended/…) **oppure** **stato anagrafica** (`StudentYear.status`: prospect/interested/enrolled/withdrawn). Verificati i casi reali: *iscritti attivi a un corso*, *interessati anagrafica*, *iscritti sospesi*.
- **Risoluzione contatti famiglia** (§5): segmento → studenti → **genitori** (`student_guardian`) → recapiti (`Guardian.email_*` / `cell_*`). Regola **"tutti i genitori" vs "solo primario"** (default tutti).
- **Consenso GDPR** (§0.4/§5, Privacy #1): guardian senza `privacy_consent` → **escluso con motivo**; flag **"comunicazione di servizio (operativa)"** → **bypassa** il filtro consenso per avvisi necessari.
- **Dedup fratelli** (§5): stesso indirizzo per più studenti → **un solo** invio (no doppioni).
- **Esclusioni trasparenti** (§3/§5): guardian senza email / senza cellulare valido → finiscono negli **esclusi col motivo**, non spariscono silenziosamente.
- **Email reale** (§4/§6): l'invio attraversa l'intero stack del mailer Laravel e **produce messaggi reali** nel transport (in test `array`, analogo verificabile di SMTP), **1 mail per destinatario** con merge field `{{studente}}` risolti; l'invio viene **loggato** (chi/quando/canale/destinatari).
- **Bozza SMS/WhatsApp** (§4): **nessun invio dal sistema** — si generano **testo finale**, **numeri normalizzati E.164** (`333 1234567` → `+393331234567`) e **link `wa.me`** (`https://wa.me/<num>?text=<urlencoded>`); numeri malformati → esclusi; la bozza è **loggata** senza produrre alcun messaggio reale (transport vuoto su quel canale).

---

## 3. Note di configurazione (DA SISTEMARE in implementazione)

- **Invio email reale in produzione**: oggi `MAIL_MAILER=log` (in `.env`); il design (§4/§10) richiede `MAIL_MAILER=smtp` + host/porta/credenziali. In ambiente di **test** il mailer è `array` (da `phpunit.xml`), quindi l'invio è verificabile end-to-end senza spedire davvero. Il `from` può attingere a `Setting` (nome/indirizzo mittente).
- **Snapshot segmento + flag operativa**: il design (§10) prevede di salvare il **segmento usato** (JSON leggibile) e un flag `is_operational` sulla riga di log; lo schema dormiente attuale non li ha ancora (→ migration in implementazione).
- **Esito per-destinatario**: il design separa `communication_recipients` (uno per contatto, con `status/error/sent_at`) per l'aggancio al **futuro gateway SMS/WhatsApp** (§9); oggi i destinatari vivono solo come JSON in `communications.recipients`.

---

## 4. Raccomandazione

Il design è solido e **poggia interamente su dati esistenti** (contatti su `Guardian`, segmenti su `CourseOffering`/`Enrollment`/`StudentYear`, consensi `privacy_consent`, ACL Spatie). L'implementazione può procedere a basso rischio: **(1)** model `Communication` + tabella `communication_recipients` + colonne `is_operational`/`segment`/contatori; **(2)** `CommunicationService::resolveRecipients()` (riusa la spec di risoluzione/consenso/dedup/E.164 del test); **(3)** `CommunicationMail` (`ShouldQueue`) + `MAIL_MAILER=smtp`; **(4)** controller + composer/drawer/log + rotte `admin.communications.*`; **(5)** azioni manuali SMS/WA (`[Segna come inviati]` → `manual_done`). Il test di questo R9 fa già da **specifica eseguibile** e da rete di sicurezza per quei passi; il gateway (§9) resta Fase 2 senza cambi di schema.
