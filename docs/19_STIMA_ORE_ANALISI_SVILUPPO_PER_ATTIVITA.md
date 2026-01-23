# 19 — Stima ore (Analisi vs Sviluppo) per attività (Fasi 1–3)

Obiettivo: stimare **ore ragionevoli** per ogni attività, separando:

- **Analisi**: allineamento con cliente, chiarimenti su casi reali, definizione regole/edge-case, verifiche su dati/sorgenti, accettazione.
- **Sviluppo**: implementazione, test manuale, rifiniture minime e messa in opera.

Nota Footility:
- In **Fase 1** lo **sviluppo** è già modellato come effort “DU” (minuti) e l’**analisi** è modellata come “manuale” (minuti).
- In **Fase 2–3** (al momento) non abbiamo DEV UNIT “di fase”, quindi in preventivo il totale è imputato come **manuale**; qui però manteniamo la separazione Analisi/Sviluppo per ragionare correttamente.

---

## FASE 1 — Migrazione 1:1 del vecchio sistema

Qui: **Analisi = minuti manuali**; **Sviluppo = minuti DU** (derivati come: totale preventivo − manuale).

| Attività | Analisi | Sviluppo | Totale |
|---|---:|---:|---:|
| 497 — Anagrafiche Studenti | 2h 30m | 6h 18m | 8h 48m |
| 498 — Genitori/Tutori | 1h 00m | 4h 24m | 5h 24m |
| 499 — Corsi e Iscrizioni | 2h 00m | 8h 30m | 10h 30m |
| 500 — Contratti Studenti | 1h 00m | 3h 24m | 4h 24m |
| 501 — Contabilità Corsi | 2h 30m | 10h 24m | 12h 54m |
| 502 — Contabilità Accessori/Noleggi/Cauzioni | 1h 00m | 4h 12m | 5h 12m |
| 503 — Accessori/Noleggi/Libri/Esami | 2h 30m | 10h 54m | 13h 24m |
| 504 — Docenti/Lavoratori | 1h 00m | 5h 12m | 6h 12m |
| 505 — Calendario Annuale | 1h 00m | 9h 18m | 10h 18m |
| 506 — Modulo Anagrafica e Disponibilità | 1h 00m | 2h 00m | 3h 00m |
| 508 — Documenti e Modelli Contratti | 1h 00m | 2h 48m | 3h 48m |
| 509 — Caricamento dati iniziali dai fogli esistenti | 2h 30m | 13h 48m | 16h 18m |

---

## FASE 2 — Ottimizzazione e alleggerimento operativo

Stima “ragionevole” (Analisi + Sviluppo).  
Per ora il totale è imputato come manuale in preventivo, finché non associamo DEV UNIT dedicate alla Fase 2.

| Attività | Analisi | Sviluppo | Totale |
|---|---:|---:|---:|
| 507 — Statistiche Storiche | 1h | 4h | 5h |
| 510 — Semplificazione anagrafiche studenti e contatti | 2h | 6h | 8h |
| 511 — Gestione famiglie più semplice | 1h | 4h | 5h |
| 512 — Corsi e iscrizioni più chiari anno per anno | 2h | 5h | 7h |
| 513 — Supporto alla creazione dell’orario (disponibilità) | 2h | 6h | 8h |
| 514 — Pagamenti, scadenze e solleciti più chiari | 2h | 6h | 8h |
| 515 — Materiali, noleggi e cauzioni più ordinati | 1h 30m | 4h 30m | 6h |
| 516 — Calendario scolastico più gestibile | 1h 30m | 4h 30m | 6h |
| 517 — Documenti e modelli: gestione più pratica | 1h | 3h | 4h |
| 518 — Pulizia e controllo qualità dei dati | 3h | 9h | 12h |

---

## FASE 3 — Evoluzioni avanzate

Stima “ragionevole” (Analisi + Sviluppo) per attività “MVP” (non enterprise).  
Anche qui: per ora il totale è imputato come manuale in preventivo.

| Attività | Analisi | Sviluppo | Totale |
|---|---:|---:|---:|
| 519 — Gestione richieste iniziali (primo contatto) | 2h | 8h | 10h |
| 520 — Preiscrizioni e rinnovi anno scolastico | 3h | 12h | 15h |
| 521 — Contratti: invio, accettazione e tracciamento | 3h | 12h | 15h |
| 522 — Pagamenti avanzati e gestione casi reali | 3h | 14h | 17h |
| 523 — Comunicazioni: invii mirati e messaggi rapidi | 4h | 18h | 22h |
| 524 — Lezioni, recuperi e gestione aule (operatività docente) | 3h | 14h | 17h |
| 525 — Registro docenti e presenze | 4h | 18h | 22h |
| 526 — Compensi docenti: regole, extra e consuntivi | 4h | 20h | 24h |
| 527 — Report avanzati per direzione e bilancio sociale | 3h | 12h | 15h |
| 528 — Visione economica d’insieme (entrate/uscite) | 3h | 14h | 17h |
| 529 — Area personale per famiglie (opzionale) | 4h | 20h | 24h |

