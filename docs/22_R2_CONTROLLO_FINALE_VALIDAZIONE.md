# 22 — R2 · Controllo finale: Validazione corsi/iscrizioni/rinnovi

> Attività Footility **#8541** — *R2 · Controllo finale*.
> Test E2E area Didattica eseguito su ciò che esiste in codice, con gap documentati
> rispetto al design R2 ([`21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md`](21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md)).
> Test automatico: [`tests/Feature/DidatticaE2EValidationTest.php`](../tests/Feature/DidatticaE2EValidationTest.php) — **4/4 PASS**.

## Esito sintetico

| Flusso | Stato | Note |
| --- | --- | --- |
| Catalogo → offerta annuale | ✅ OK | `CourseType`/`Course` → `CourseOffering` per anno, CRUD presente. |
| Iscrizione studente | ✅ OK | `EnrollmentService::createEnrollment` crea l'iscrizione e calcola `total_amount`. |
| Calcolo costo | ✅ OK | 30 lezioni × 20€ = **600€**; verificato dal calendario lezioni. |
| Sconti (% + importo fisso) | ✅ OK | 600 −10% −45€ = **495€**; sconti manuali per riga. |
| Filtri lista iscrizioni | ✅ OK | anno (default = attivo), studente, offerta, stato, ricerca cognome — tutti verificati. |
| **Rinnovo anno→anno** | 🟥 **BLOCCO** | Nessuna route/azione `renew`, nessun tracciamento rinnovo. |
| **Sconto fratelli (derivato)** | 🟥 **BLOCCO** | Non modellato; il legame nucleo è rilevabile ma nessun automatismo. |

## Cosa è stato testato (E2E)

1. **Crea offerta annuale** 2025/26 (Pianoforte individuale, lun 15:00, 20€/lezione) con calendario di 30 lunedì attivi.
2. **Iscrivi studente** (Mario Rossi) via `EnrollmentService` → iscrizione persistita, costo calcolato a 600€.
3. **Sconti** → 495€ con −10% e −45€ credito (entrambi manuali per riga).
4. **Filtri lista** → tutti i filtri di `EnrollmentController@index` rispondono correttamente.
5. **Rinnovo con fratello** → setup nucleo (due fratelli, stesso genitore) + iscrizione anno precedente; verificato che **non esiste** alcun meccanismo di rinnovo né sconto fratelli.

## Gap che bloccano il flusso completo (coerenti con R2 §1)

Il design R2 dichiarava già questi punti come `❌ assente`; il controllo finale lo conferma:

- **Rinnovo** (R2 §5): manca route/azione `renew`, manca metodo su `EnrollmentController`, manca
  `renewed_from_enrollment_id` per la continuità storica.
- **Sconto fratelli** (R2 §6a): manca campo/regola derivata; oggi solo sconti manuali (`discount_percentage`/`discount_amount`).
- **Iscrizione contestuale "in pochi click"** (R2 §4): oggi solo form generico `enrollments/create`.
- **Crediti residui spendibili** (R2 §6b): `CreditNote` è nota di credito su fattura, non saldo riutilizzabile.

## Raccomandazione

Catalogo → offerta → iscrizione → contabilità di base è **validato e solido**. Il
flusso di **rinnovo (fratelli + crediti)** resta da implementare come da R2 §9: è il
prossimo blocco di lavoro, non un bug. Nessun blocco sul codice esistente.
