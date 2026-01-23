# 19 — Stima ore (Analisi vs Sviluppo) per attività (Fasi 1–3)

Obiettivo: stimare **ore ragionevoli** per ogni attività, separando:

- **Analisi**: allineamento con cliente, chiarimenti su casi reali, definizione regole/edge-case, verifiche su dati/sorgenti, accettazione.
- **Sviluppo**: implementazione, test manuale, rifiniture minime e messa in opera.

Regola Footility (come da impostazione attuale):
- **Fase 1**: lo **sviluppo** è quello calcolato dalle **DEV UNIT (DU)**; nel **manuale** inseriamo solo l’**eccedenza** rispetto alle DU (tipicamente: Analisi / allineamenti / accettazione).  
  Esempio: DU=1h, totale realistico=3h → manuale=2h.
- **Fase 2–3**: **nessuna DU**. In preventivo inseriamo **tutto come manuale** (Analisi+Sviluppo). Nel documento manteniamo comunque la separazione.

---

## FASE 1 — Migrazione 1:1 funzionalità ODS (con miglioramento dati/relazioni in Laravel)

Qui: **Sviluppo = DU** (minuti già calcolati); **Manuale = eccedenza** (qui la trattiamo come Analisi).  
Le ore “Sviluppo” sono derivate come: \( \text{DU} = \text{Totale preventivo} - \text{Manuale} \).

| Attività | Analisi (manuale) | Sviluppo (DU) | Totale |
|---|---:|---:|---:|
| 497 — Anagrafiche Studenti | 5h 00m | 6h 18m | 11h 18m |
| 498 — Genitori/Tutori | 2h 00m | 4h 24m | 6h 24m |
| 499 — Corsi e Iscrizioni | 4h 00m | 8h 30m | 12h 30m |
| 500 — Contratti Studenti | 3h 00m | 3h 24m | 6h 24m |
| 501 — Contabilità Corsi | 5h 00m | 10h 24m | 15h 24m |
| 502 — Contabilità Accessori/Noleggi/Cauzioni | 3h 00m | 4h 12m | 7h 12m |
| 503 — Accessori/Noleggi/Libri/Esami | 4h 00m | 10h 54m | 14h 54m |
| 504 — Docenti/Lavoratori | 2h 00m | 5h 12m | 7h 12m |
| 505 — Calendario Annuale | 4h 00m | 9h 18m | 13h 18m |
| 506 — Modulo Anagrafica e Disponibilità | 2h 00m | 2h 00m | 4h 00m |
| 508 — Documenti e Modelli Contratti | 2h 00m | 2h 48m | 4h 48m |
| 509 — Caricamento dati iniziali dai fogli esistenti | 6h 00m | 13h 48m | 19h 48m |

---

## FASE 2 — Ottimizzazione e alleggerimento operativo

Stima “ragionevole” (Analisi + Sviluppo).  
Per ora il totale è imputato come manuale in preventivo, finché non associamo DEV UNIT dedicate alla Fase 2.

| Attività | Analisi | Sviluppo | Totale |
|---|---:|---:|---:|
| 507 — Statistiche Storiche | 1h | 2h | 3h |
| 510 — Semplificazione anagrafiche studenti e contatti | 2h | 4h | 6h |
| 511 — Gestione famiglie (genitori/tutori) più semplice | 1h 30m | 2h 30m | 4h |
| 512 — Corsi e iscrizioni più chiari anno per anno | 2h | 4h | 6h |
| 513 — Supporto alla creazione dell’orario (disponibilità) | 2h | 4h | 6h |
| 514 — Pagamenti, scadenze e solleciti più chiari | 2h | 4h | 6h |
| 515 — Materiali, noleggi e cauzioni più ordinati | 1h 30m | 3h 30m | 5h |
| 516 — Calendario scolastico più gestibile | 1h 30m | 3h 30m | 5h |
| 517 — Documenti e modelli: gestione più pratica | 1h | 3h | 4h |
| 518 — Pulizia e controllo qualità dei dati | 3h | 7h | 10h |

---

## FASE 3 — Evoluzioni avanzate

Stima “ragionevole” (Analisi + Sviluppo) per attività “MVP” (senza servizi esterni, salvo decisioni successive).  
In preventivo: tutto manuale (Analisi+Sviluppo), perché in Fase 3 non devono esserci DU.

| Attività | Analisi | Sviluppo | Totale |
|---|---:|---:|---:|
| 519 — Gestione richieste iniziali (primo contatto) | 2h | 6h | 8h |
| 520 — Preiscrizioni e rinnovi anno scolastico | 3h | 7h | 10h |
| 521 — Contratti: invio, accettazione e tracciamento | 3h | 7h | 10h |
| 522 — Pagamenti avanzati e gestione casi reali | 3h | 9h | 12h |
| 523 — Comunicazioni: invii mirati e messaggi rapidi | 3h | 9h | 12h |
| 524 — Lezioni, recuperi e gestione aule (operatività docente) | 3h | 9h | 12h |
| 525 — Registro docenti e presenze | 3h | 9h | 12h |
| 526 — Compensi docenti: regole, extra e consuntivi | 4h | 12h | 16h |
| 527 — Report avanzati per direzione e bilancio sociale | 2h | 8h | 10h |
| 528 — Visione economica d’insieme (entrate/uscite) | 3h | 9h | 12h |
| 529 — Area personale per famiglie (opzionale) | 4h | 12h | 16h |

