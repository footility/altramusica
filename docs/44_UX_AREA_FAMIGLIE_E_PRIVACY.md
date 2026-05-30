# 44 — UX: Perimetro area famiglie + privacy (R13 · Design UX)

> Attività Footility **#8536** — *R13 · Perimetro area famiglie + privacy* (Gestionale Altramusica).
> Obiettivo: definire **cosa vede la famiglia** (dati dello studente, scadenze, documenti), **cosa NON vede**, il **flusso di login famiglia** e la **privacy dei minori**.
> ⚠️ **Greenfield onesto**: oggi **non esiste** alcuna area famiglie. L'unico login è il backoffice (`User` con ruoli Spatie `admin`/`teacher`, vedi `routes/web.php` → `Auth::routes()` + prefix `admin`). I tutori (`Guardian`) sono **solo anagrafica**: nessun `user_id`, nessuna password, nessun accesso. Questo documento progetta il perimetro; l'implementazione è **fuori da R13** (vedi §8).
> Base AS-IS rilevante: `Guardian` (contatti + `privacy_consent`), pivot `student_guardian` (`is_primary`, `is_billing_contact`, `relationship_type`), `student_years` (`privacy_consent_at`, `photo_consent_at`, `privacy_policy_version`, `withdrawn_at`), `students.birth_date` (→ minore), `students.anonymized_at`, pagine pubbliche `privacy.policy` / `privacy.cookies`.

---

## 0. Principi

1. **Privacy-by-design, perimetro minimo.** La famiglia vede **solo** i dati dei propri figli, e **solo** i campi che le servono per stare al passo (scadenze, documenti, calendario). Tutto il resto è escluso per default (whitelist, non blacklist).
2. **Sola lettura.** L'area famiglie **non modifica** nulla del gestionale: niente edit anagrafica, niente cancellazioni, niente scritture contabili. Al massimo **scarica** un documento o **carica** un allegato richiesto (es. scansione contratto firmato) in un'area di *upload sospeso* che la segreteria convalida — non scrive direttamente sulle entità.
3. **Nessun dato di terzi.** Mai mostrare alla famiglia i dati di **altri studenti**, **altri tutori** (incluso il co-genitore se non pertinente), **docenti** (oltre il nome), **compensi**, **margini**, **note interne**, **dati di qualità/anomalie**.
4. **Lo scoping è server-side.** Cosa vede la famiglia si decide con **query filtrate sul `guardian_id` autenticato** + **policy**, mai nascondendo client-side. Una richiesta fuori perimetro è **404/403**, non un campo svuotato.
5. **Minori al centro.** Lo studente è quasi sempre **minorenne**: il titolare del consenso e l'interlocutore è il **tutore**, non il minore. Niente login al minore. La foto e i dati sensibili seguono il **consenso registrato** (`student_years.photo_consent`/`privacy_consent`).
6. **Consenso esplicito e versionato.** L'accesso famiglia presuppone consenso privacy registrato (`privacy_consent_at` + `privacy_policy_version`). Se l'informativa cambia versione, alla prima login si **ri-chiede** l'accettazione.
7. **Tracciabilità.** Ogni accesso famiglia e ogni download di documento è **loggato** (riuso/estensione di `LoginLog`): chi, quando, cosa. Serve per GDPR (registro accessi) e per fiducia.
8. **Coerente col ritiro/retention.** Studente ritirato (`withdrawn_at`) o anonimizzato (`anonymized_at`) → accesso famiglia **sospeso** per quella scheda secondo la policy retention già introdotta in `add_privacy_consent_and_retention_fields`.

---

## 1. Chi è "la famiglia" (modello d'accesso)

- **Identità = `Guardian`**, non `Student`. Il login è del **tutore** (genitore/tutore legale). Un `Guardian` può avere **più figli** (pivot `student_guardian`) → un solo accesso vede tutte le proprie schede.
- **Account separato dal backoffice.** Il tutore **non** è un `User` admin/teacher. Due strade (vedi §8 per la scelta tecnica):
  - **A — `guardian_id` su `users`** (un `User` con ruolo `family` collegato al `Guardian`), riusa Spatie + `Auth::routes`.
  - **B — guard separato `family`** con tabella credenziali dedicata su `guardians` (`email`/`password`/`remember_token`).
  - **Raccomandata: A** — meno superficie, riusa l'auth esistente, isola via **ruolo `family` + middleware**.
- **Solo tutori con consenso e contatto valido.** L'invito si genera solo per `Guardian` con `email_1` valida e `privacy_consent = true`. Il **referente** naturale è il pivot con `is_primary = true`; gli altri tutori possono essere invitati separatamente (vedi §5 multi-tutore).

---

## 2. Flusso di login famiglia

Niente self-registration: l'accesso è **su invito della segreteria** (la scuola controlla chi entra).

```
SEGRETERIA                          TUTORE                           SISTEMA
────────────                        ──────                           ───────
Scheda tutore →                                                       
[ Invita all'area famiglie ] ─────────────────────────────────────► genera token monouso
                                                                      (scadenza 7 gg) + email
                              ◄── email "Attiva il tuo accesso" ─────
                              click link ──────────────────────────► valida token
                              imposta password + accetta privacy v.X ► crea credenziale family
                                                                      (privacy_consent_at, version)
                              login (email + password) ─────────────► sessione guard "family"
                                                                      LoginLog (chi/quando)
                              [ recupero password ] ─────────────────► reset link standard
```

- **Invito**: bottone nella scheda tutore (backoffice). Genera **token monouso a scadenza** + email transazionale (riuso SMTP reale già usato per le comunicazioni R9). Stato invito visibile in segreteria: *Invitato / Attivo / Scaduto / Mai invitato*.
- **Attivazione**: il tutore imposta la password e **accetta l'informativa** (registra versione + timestamp). Niente account "vuoti".
- **Login**: email + password sul guard `family`. **Rate-limit** + **LoginLog**. Opzionale 2FA via OTP email in fase 2.
- **Recupero password**: flusso standard Laravel sul guard family.
- **Ri-consenso**: se `privacy_policy_version` corrente > quella accettata, primo step post-login = ri-accettazione.
- **Revoca**: la segreteria può **disattivare** l'accesso (studente ritirato, richiesta cancellazione, abuso) senza toccare l'anagrafica.

---

## 3. Cosa VEDE la famiglia (perimetro — whitelist)

Tutto **scopato sui propri figli** (studenti legati al `guardian_id` via pivot) e in **sola lettura**.

| Area | Cosa vede | Note di scoping |
| --- | --- | --- |
| **Profilo studente** | Nome, data di nascita, corso/i e livello dell'**anno corrente**, docente assegnato (**solo nome**) | Solo i figli del tutore. Niente CF di terzi, niente note interne. |
| **Iscrizioni** | Corso, anno accademico, stato (attiva/sospesa/ritirata) | `enrollments` filtrate per studente; etichette pulite, non stati interni grezzi. |
| **Scadenze e pagamenti** | Rate **dovute** e **pagate** dei propri figli, importo, scadenza, stato (da pagare/pagato/in ritardo) | Da `payment_plans`/`invoices`/`payments`. **Solo il dovuto della famiglia**, mai margini, listini, compensi. |
| **Documenti** | Documenti **condivisi con la famiglia**: contratto firmato, ricevute/fatture intestate, informative, attestati | `documents` filtrati per studente **e** con flag *visibile alla famiglia* (vedi §8). Download loggato. |
| **Calendario lezioni** | Le lezioni dei **propri figli**: data, ora, aula, docente (nome), sospensioni/recuperi che li riguardano | Da `calendar_lessons`/recuperi (R7), filtrate per iscrizione del figlio. |
| **Materiali/noleggi** | Strumenti a noleggio e libri assegnati al figlio, con stato cauzione | `instrument_rentals`/`book_distributions` filtrati per studente. |
| **Esami** | Esami sostenuti/programmati del figlio, esito se pubblicato | `exams` filtrati per studente; esito solo se la scuola lo pubblica. |
| **Consensi** | I **propri** consensi privacy/foto e la versione accettata, con possibilità di **leggere** l'informativa | Da `student_years.*_consent`. La **modifica** del consenso è un'azione tracciata (vedi §5). |

**Schermata "home famiglia" (bozza):**

```
┌─ Area famiglie · Anna Rossi ───────────────────────────────────────────────┐
│  I tuoi ragazzi:   [ Mario (Pianoforte) ]   [ Sofia (Canto) ]               │
│                                                                             │
│  ▸ Prossime lezioni        Lun 16:00 · Aula 2 · M° Bianchi (Mario)          │
│  ▸ Scadenze                Rata 2/4 · 180 € · entro 10/06  ● da pagare      │
│  ▸ Documenti               Contratto 2025/26 (firmato) · Ricevuta #142  ⤓   │
│  ▸ Consensi                Privacy ✓ (v.2026-05) · Foto ✓                   │
│                                                                             │
│  [ Scarica un documento ]   [ Carica scansione richiesta ]                  │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Cosa NON vede la famiglia (esclusioni esplicite)

- **Altri studenti / altre famiglie** — qualsiasi scheda non legata al proprio `guardian_id`.
- **Altri tutori** oltre sé stesso, salvo il minimo necessario (vedi §5: il co-genitore vede gli stessi figli ma non i recapiti dell'altro se non già condivisi).
- **Dati economici interni**: listini di costo, **compensi docenti** (R8), **margini**, riconciliazione bancaria (R5), incassato aggregato, bilancio sociale (R11).
- **Note interne / qualità dati**: pannello anomalie (R12), merge (R12), note segreteria, flag operativi.
- **Backoffice**: nessuna rotta `admin.*`, nessuna anagrafica modificabile, nessun calendario globale, nessun elenco docenti.
- **Dati sensibili di terzi**: CF/recapiti di altri tutori o studenti, anche se compaiono nella stessa fattura/contratto → **mascherati** o omessi nella vista famiglia.
- **Stati grezzi**: enum interni, id tecnici, timestamp di sistema → la famiglia vede etichette leggibili, non il modello.

> Regola di taglio: se un campo non serve alla famiglia per **capire una scadenza, scaricare un documento o seguire le lezioni del figlio**, **non si mostra**.

---

## 5. Privacy minori

- **Nessun login al minore.** L'interlocutore è il **tutore**. Lo studente minorenne non ha credenziali.
- **Minore = `birth_date`** che dà età < 18 alla data corrente: dato derivato, non un flag separato. Per i minori la vista famiglia **non espone mai** dati a soggetti diversi dai tutori collegati.
- **Consenso del tutore, versionato.** Foto/uso immagini e trattamento seguono `student_years.photo_consent`/`privacy_consent` + `*_consent_at` + `privacy_policy_version`. La famiglia può **vedere** lo stato e **revocare/aggiornare** il consenso foto: l'azione è **tracciata** (chi/quando/da→a) e notificata alla segreteria — non è una scrittura silenziosa sull'anagrafica.
- **Foto e materiali condivisi**: una foto/attestato compare nell'area famiglie **solo** se `photo_consent = true` per l'anno pertinente.
- **Multi-tutore / genitori separati.** Più `Guardian` possono essere legati allo stesso `Student` (pivot). Ognuno vede **gli stessi dati del figlio**, ma:
  - non vede i **recapiti dell'altro tutore** se non già marcati condivisi;
  - il **referente fatturazione** (`is_billing_contact`) è l'unico a cui si associa il dato di pagamento come "intestatario", gli altri vedono la scadenza ma non i dati fiscali altrui.
  - In caso di affido esclusivo, la segreteria può **escludere** un tutore dall'invito senza alterare l'anagrafica.
- **Ritiro/anonimizzazione**: `withdrawn_at` valorizzato → accesso famiglia per quella scheda **sospeso** secondo retention; `anonymized_at` → scheda non più accessibile (dati anonimizzati).
- **Diritti GDPR**: l'area espone un punto per **richiedere** export/cancellazione dei dati del figlio; la richiesta arriva in segreteria (workflow umano), non è un'auto-cancellazione.

---

## 6. Mappa permessi e scoping

- **Guard `family`** isolato dal guard backoffice: le rotte `family.*` non condividono sessione né middleware con `admin.*`.
- **Scoping di base**: ogni query parte da `auth('family')->user()->guardian->students()` → mai un `Model::find($id)` non filtrato. Accesso a un id fuori perimetro = **404**.
- **Policy per entità** (`view` solo): `StudentPolicy`, `DocumentPolicy`, `InvoicePolicy`, `EnrollmentPolicy`, ecc. ritornano `true` per il guard family **solo** se la riga appartiene a un figlio del tutore **e** (per i documenti) ha il flag *visibile alla famiglia*.
- **Nessun permesso di scrittura** assegnato al ruolo `family`. Gli unici POST ammessi: upload sospeso, aggiornamento consenso foto, richiesta GDPR — tutti con audit.

---

## 7. Microcopy (IT)

- Invito: **"Attiva il tuo accesso all'area famiglie"**, **"Il link scade tra 7 giorni"**.
- Home: **"I tuoi ragazzi"**, **"Prossime lezioni"**, **"Scadenze"**, **"Documenti"**, **"Consensi"**.
- Scadenze: **"Da pagare"**, **"Pagato"**, **"In ritardo"**, **"Rata 2 di 4 · entro il 10/06"**.
- Consensi: **"Consenso privacy accettato il … (v. …)"**, **"Consenso uso immagini"**, **"Aggiorna consenso"**.
- Documenti: **"Scarica"**, **"Carica la scansione richiesta"**, **"In attesa di convalida dalla segreteria"**.
- GDPR: **"Richiedi una copia dei dati"**, **"Richiedi la cancellazione dei dati"** (→ "La segreteria ti ricontatterà").
- Errori perimetro: **"Documento non disponibile"** (mai "non autorizzato a vedere lo studente #388").

---

## 8. Impatti tecnici (per chi implementa — NON parte di R13)

Niente di tutto questo esiste oggi: l'area famiglie va **costruita**. Elementi minimi:

- **Identità famiglia**: opzione A — colonna `guardian_id` nullable su `users` + ruolo Spatie `family`; oppure opzione B — guard `family` con credenziali su `guardians` (`email`, `password`, `remember_token`, `invited_at`, `activated_at`). **Raccomandata A**.
- **Invito/attivazione**: tabella `guardian_invitations` (token monouso, `expires_at`, stato) o riuso del meccanismo password-reset; bottone "Invita" nella scheda tutore (backoffice); email transazionale (SMTP reale già configurato in R9).
- **Flag visibilità documento**: colonna `documents.visible_to_family` (default `false`) — un documento entra nell'area famiglie **solo** se esplicitamente condiviso. Senza questo flag, **nessun** documento è esposto (fail-safe).
- **Rotte/guard**: gruppo `family.*` con prefix dedicato e middleware `auth:family`, separato da `admin.*`. Pagine: home, scadenze, documenti, calendario, consensi, richieste GDPR.
- **Policy `view`** per studente/documento/fattura/iscrizione/calendario/esame/noleggio, sempre scopate su `guardian->students()`.
- **Audit**: estendere `LoginLog` (già presente) per accessi family + log download documenti e cambi consenso.
- **Consenso versionato**: il valore corrente di `privacy_policy_version` va in `Setting`; confronto alla login per il ri-consenso.
- **Retention**: rispettare `withdrawn_at`/`anonymized_at` per sospendere/chiudere l'accesso (coerente con `add_privacy_consent_and_retention_fields`).
- **Upload sospeso**: area di staging (es. `family_uploads`) che la segreteria convalida prima di promuovere a `documents` — la famiglia non scrive mai direttamente sulle entità del gestionale.
- **Nessuna PII fuori dal sistema, nessuna libreria nuova**: riusa auth Laravel + Spatie + SMTP + schema esistente. Il portale è una **vista filtrata**, non un nuovo modello dati.

---

## 9. Checklist di accettazione (Definition of Done del design)

- [x] **Cosa vede la famiglia** definito per ogni area (profilo, iscrizioni, scadenze, documenti, calendario, materiali, esami, consensi), tutto sola lettura e scopato sui figli. — §3
- [x] **Cosa NON vede** elencato esplicitamente (altri studenti/tutori, compensi/margini, note interne, backoffice, dati di terzi, stati grezzi). — §4
- [x] **Flusso login famiglia** completo: invito su token monouso → attivazione con consenso → login guard `family` → recupero → revoca → ri-consenso. — §2
- [x] **Privacy minori**: nessun login al minore, consenso del tutore versionato (foto/privacy), multi-tutore e genitori separati, ritiro/anonimizzazione, diritti GDPR. — §5
- [x] **Scoping server-side** (query su `guardian_id` + policy, fuori perimetro = 404), guard isolato dal backoffice. — §0.4, §6
- [x] **Greenfield dichiarato**: l'area non esiste; identità famiglia, invito, flag visibilità documento, rotte/guard, audit, retention come impatti tecnici **senza implementare**. — §1, §8
- [x] **Coerenza con AS-IS**: riuso `Guardian`/pivot/`student_years`/consensi/`LoginLog`/SMTP R9/retention, nessuna libreria nuova, nessuna PII fuori dal sistema. — §0, §8
