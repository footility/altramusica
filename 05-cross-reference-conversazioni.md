# Cross-reference Preventivo Altramusica (#20) ↔ conversazioni cliente

Generato: 2026-04-30. Fonti principali: `docs/Trascrizione gestionale parte 1.txt`, `docs/Trascrizione gestionale parte 2.txt`, `docs/18_STIMA_TASK_FASI_1_2_3.md`, `docs/19_STIMA_ORE_ANALISI_SVILUPPO_PER_ATTIVITA.md`.

> Nota: il preventivo #20 è stato riorganizzato in wrapper modulari. Le voci panoramica da 1h non sono fonti funzionali autonome: erano duplicati tecnici del vecchio salvataggio e sono state rimosse, tranne le tre voci di consulenza/analisi rese esplicite nel wrapper evolutivo.

---

## Parte A — Preventivo → conversazioni

### Wrapper: Anagrafiche, famiglie e dati di contatto

- **[✓] Semplificazione anagrafiche studenti e contatti** _sviluppo_
  - Origine: parte 1 r.58-76, r.160-168; parte 1 r.312-322 per dati del primo contatto e passaggio a iscrizione.
  - Note: la cliente distingue dati anagrafici, dati logistici, dati didattici e dati contabili; serve una scheda consultabile senza duplicare lavoro.

- **[✓] Gestione famiglie (genitori/tutori) più semplice** _sviluppo_
  - Origine: parte 1 r.262-276 su due genitori, minori, referente da contattare, deleghe e semplificazione.
  - Note: rilevante anche per fatturazione, emergenze, privacy minori e fratelli.

### Wrapper: Corsi, iscrizioni, rinnovi e contratti

- **[✓] Corsi e iscrizioni più chiari anno per anno** _sviluppo_
  - Origine: parte 1 r.32-38, r.160-168; parte 2 r.6-18.
  - Note: la distinzione corso, iscrizione, anno, calendario e stato dello studente è fondativa.

- **[✓] Gestione richieste iniziali (primo contatto)** _sviluppo_
  - Origine: parte 1 r.312-322 e r.338; parte 2 r.30-34.
  - Note: copre interessati, vecchi iscritti, dati minimi, note e passaggio al cliente/iscritto.

- **[✓] Preiscrizioni e rinnovi anno scolastico** _sviluppo_
  - Origine: parte 1 r.356; parte 2 r.18-34.
  - Dipendenze: richiede corsi/iscrizioni chiari e struttura famiglia, perché gestisce rinnovi, fratelli, crediti e stati.

- **[✓] Contratti: invio, accettazione e tracciamento** _sviluppo_
  - Origine: parte 1 r.162-166, r.322-328, r.392-398, r.546-550; parte 2 r.142-156.
  - Dipendenze: richiede modelli/documenti e corsi/iscrizioni, perché il contratto riassume corso, periodo, rate e accettazioni.

### Wrapper: Contabilità, pagamenti, materiali e noleggi

- **[✓] Contabilità Corsi** _sviluppo_
  - Origine: parte 1 r.76, r.160-166, r.392-404, r.508-534.
  - Note: include scadenze, rate, pagato/non pagato, recupero crediti e vista per studente.

- **[✓] Contabilità Accessori/Noleggi/Cauzioni** _sviluppo_
  - Origine: parte 2 r.76-90, r.178-180.
  - Note: cauzioni, contratti noleggio e materiali devono restare collegati allo studente ma distinguibili dal corso.

- **[✓] Accessori/Noleggi/Libri/Esami** _sviluppo_
  - Origine: parte 2 r.76-90 e r.142.
  - Note: richieste esplicite su libri venduti, strumenti codificati, rimanenze, cespiti ed esami.

- **[✓] Pagamenti, scadenze e solleciti più chiari** _sviluppo_
  - Origine: parte 1 r.398-404, r.508-534.
  - Note: il sistema deve aiutare ma lasciare blocchi manuali per situazioni sensibili.

- **[✓] Materiali, noleggi e cauzioni più ordinati** _sviluppo_
  - Origine: parte 2 r.76-90.
  - Note: è l'ottimizzazione operativa della stessa area materiali/noleggi già presente nell'as-is.

- **[✓] Pagamenti avanzati e gestione casi reali** _integrazione_
  - Origine: parte 1 r.424-442, r.456-474, r.508-534.
  - Dipendenze: richiede una base chiara di scadenze e stati pagamento.
  - Note: Satispay, estratti conto, pagamenti parziali/eccessivi e riconciliazione restano soggetti a verifica vendor.

### Wrapper: Calendario, orari, lezioni e recuperi

- **[✓] Supporto alla creazione dell'orario (disponibilità)** _sviluppo_
  - Origine: parte 1 r.32-38, r.62-76, r.172-174; parte 2 r.6-12.
  - Note: non è un algoritmo automatico completo, ma un supporto filtrabile su disponibilità e vincoli.

- **[✓] Calendario scolastico più gestibile** _sviluppo_
  - Origine: parte 1 r.168-174; parte 2 r.142.
  - Note: include giorni attivi, sospensioni e coerenza con lezioni/corsi.

- **[✓] Lezioni, recuperi e gestione aule (operatività docente)** _sviluppo_
  - Origine: parte 1 r.196-204, r.210-216, r.236.
  - Dipendenze: richiede calendario scolastico e disponibilità.

### Wrapper: Docenti, registro e compensi

- **[✓] Registro docenti e presenze (anche per progetti)** _sviluppo_
  - Origine: parte 1 r.168-180, r.196-204, r.210-216.
  - Dipendenze: richiede lezioni/recuperi perché le presenze si basano sulle lezioni effettive.

- **[✓] Compensi docenti: regole, extra e consuntivi** _sviluppo_
  - Origine: parte 1 r.180-188, r.216-220; parte 2 r.108-114.
  - Dipendenze: richiede registro e presenze validate.

### Wrapper: Comunicazioni e messaggi mirati

- **[✓] Comunicazioni: invii mirati e messaggi rapidi** _integrazione_
  - Origine: parte 1 r.74-76, r.178-180; parte 2 r.10.
  - Dipendenze: richiede anagrafiche pulite e corsi/iscrizioni segmentabili.
  - Note: email ufficiali, SMS e WhatsApp hanno natura diversa; costi e consensi sono esterni al puro sviluppo.

### Wrapper: Documenti, modelli e archiviazione

- **[✓] Documenti e Modelli Contratti** _sviluppo_
  - Origine: parte 1 r.162-166, r.322-328; parte 2 r.142-180.
  - Note: modelli studenti, contratti brevi/regolari, contratti docenti e noleggio.

- **[✓] Documenti e modelli: gestione più pratica** _sviluppo_
  - Origine: parte 2 r.142-180.
  - Note: ottimizzazione di ricerca, archiviazione e uso quotidiano.

### Wrapper: Area personale famiglie

- **[✓] Area personale per famiglie (opzionale)** _sviluppo_
  - Origine: parte 1 r.166 e r.236; parte 1 r.180 nota che una app/calendario personale interessa solo relativamente.
  - Dipendenze: famiglia, contratti e pagamenti devono essere strutturati prima.
  - Note: correttamente opzionale/futuribile.

### Wrapper: Report, statistiche e visione direzionale

- **[✓] Statistiche Storiche** _sviluppo_
  - Origine: parte 2 r.42-54.
  - Note: richieste per bilancio sociale e confronto storico.

- **[✓] Report avanzati per direzione e bilancio sociale** _sviluppo_
  - Origine: parte 2 r.42-54.
  - Dipendenze: richiede statistiche storiche e iscrizioni/corsi strutturati.

- **[✓] Visione economica d'insieme (entrate/uscite)** _integrazione_
  - Origine: parte 1 r.116-122, r.454-474.
  - Dipendenze: richiede contabilità corsi e pagamenti avanzati.
  - Note: la cliente non chiede sostituzione del commercialista, ma una vista gestionale del flusso.

### Wrapper: Import iniziale e qualità dati

- **[✓] Pulizia e controllo qualità dei dati** _analisi_
  - Origine: parte 1 r.58-62, r.334-384; docs 18/19 su caricamento dati e anomalie.
  - Note: passaggio necessario per trasformare i fogli esistenti in base dati affidabile.

### Wrapper: Analisi evolutiva e moduli futuri

- **[✓] DU moduli futuri (non quotato)** _analisi_
  - Origine: parte 2 r.212-250 e r.268-272 sul percorso incrementale e possibilità di distribuire lavoro/pagamenti.
  - Note: mantenuto come riserva/analisi, da chiarire nel titolo commerciale se "non quotato" crea ambiguità.

- **[✓] Consulenza e analisi funzionale Fase 1** _analisi_
  - Origine: docs 19 Fase 1; parte 1 r.384.
  - Note: mantenuta come voce reale per validazione della migrazione iniziale.

- **[✓] Consulenza e analisi Fase 2** _analisi_
  - Origine: docs 19 Fase 2; parte 2 r.212-250.
  - Note: chiarisce priorità delle ottimizzazioni.

- **[✓] Consulenza e analisi Fase 3** _analisi_
  - Origine: docs 19 Fase 3; parte 2 r.212-250.
  - Note: chiarisce moduli evolutivi, canali esterni e dipendenze.

---

## Parte B — Voci rimosse come duplicati tecnici

Queste voci da 1h erano panoramiche/duplicati del vecchio `proposal_detail` e sono state rimosse dalla struttura finale:

- `infrastruttura_e_anno_scolastico`
- `anagrafiche_studenti_e_famiglie`
- `catalogo_corsi_e_iscrizioni`
- `calendario_e_disponibilita`
- `docenti_anagrafica_base`
- `contratti_e_contabilita_registrazione_dati`
- `strumenti_accessori_ed_esami`
- `import_dati_da_fogli_excelods`
- `workflow_contratti`
- `contabilita_operativa`
- `calendario_avanzato_e_orario`
- `dashboard_e_ottimizzazioni`
- `gestione_docenti_completa`
- `report_e_statistiche`
- `comunicazioni_e_notifiche`
- `area_famiglie_online`

Le tre voci da 1h di consulenza/analisi sono state invece conservate, perché non rappresentano una funzionalità duplicata ma lavoro di allineamento.

---

## Parte C — Criticità non fatturate

- **Backup e ripristino**: non discusso in dettaglio nelle call, ma necessario prima del go-live.
- **GDPR, minori e retention**: fortemente rilevante per scuola, famiglie, contratti, pagamenti e comunicazioni; da validare con consulente privacy.
- **Ruoli staff/docenti/supplenti**: emerge in parte 1 r.32-38 e r.210-216, ma va chiuso in modo operativo.
- **Bandi o finanziamenti**: la struttura modulare può aiutare, ma ammissibilità e rendicontazione non sono state definite.
- **Accessibilità area pubblica/famiglie**: da valutare se moduli pubblici o area famiglie diventano centrali.
