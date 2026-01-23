# 18 — Stima task (15 min–1h) per attività Footility (Fasi 1–3)

Obiettivo: scomporre ogni attività in **task operativi** ad alto livello (non legati a Laravel), coerenti con lo **stato AS‑IS** dei fogli ODS/Excel.  
Ogni task è stimato tra **15 min e 1 ora**.  

## Note sul metodo

- **Task “Base (incluso)”**: necessari per avere una versione “essenziale ma funzionante” coerente con la fase.
- **Task “Opzionale”**: miglioramenti/affinamenti che si possono decidere “on the road”.
- La “stima attività” che portiamo a preventivo è la somma dei task **Base (incluso)**.

---

## FASE 1 — Migrazione 1:1 del vecchio sistema (task base)

### 497 — Anagrafiche Studenti (Base: 150 min)
- **(30m)** Normalizzazione campi anagrafici: cosa è “persona” vs cosa è “dell’anno”
- **(15m)** Regole stato studente nell’anno (prospect/interested/enrolled/withdrawn)
- **(15m)** Ricerca e filtri minimi (nome/cognome/cf + anno + stato)
- **(30m)** Gestione note operative + privacy/consensi per anno
- **(30m)** Controllo casi “dati sporchi” (omonimi, CF mancanti) e comportamento atteso
- **(30m)** Revisione “campi critici” (stato, contatti, note) e coerenza anno per anno

### 498 — Genitori/Tutori (Base: 60 min)
- **(15m)** Regole di collegamento studente ↔ tutore (primario / fatturazione)
- **(15m)** Schermata “collegamenti” chiara (vedere/modificare)
- **(15m)** Verifica duplicati e criteri minimi per evitare doppioni
- **(15m)** Validazioni minime contatti (email/telefono) + casi incompleti

### 499 — Corsi e Iscrizioni (Base: 120 min)
- **(30m)** Definizione “corso catalogo” vs “corso dell’anno” (tariffe/orari/docente variabili)
- **(30m)** Regole iscrizione studente a 1..N corsi nell’anno (date/stato)
- **(15m)** Lista iscrizioni con filtri anno/studente/corso/stato
- **(15m)** Form iscrizione (scelte coerenti e campi minimi)
- **(30m)** Regole minime calcolo “totale iscrizione” (solo base, senza automatismi avanzati)

### 500 — Contratti Studenti (Base: 60 min)
- **(15m)** Stati contratto e significato operativo (draft/sent/signed/…)
- **(15m)** Collegamento contratto ↔ studente ↔ anno
- **(15m)** Vista “elenco” con filtri minimi e azioni base
- **(15m)** Gestione archiviazione/ricerca contratto (minimo)

### 501 — Contabilità Corsi (Base: 150 min)
- **(30m)** Regole minime “fattura ↔ studente ↔ anno” + cosa significa “pagata”
- **(30m)** Scadenzario e stato pagato/saldo (versione semplice)
- **(30m)** Crediti/Debiti per studente (vista sintetica)
- **(30m)** Gestione rate/piani (minimo, senza automatismi esterni)
- **(30m)** Regole casi reali minimi (pagamenti parziali/ritardi/note)

### 502 — Contabilità Accessori/Noleggi/Cauzioni (Base: 60 min)
- **(15m)** Regole base cauzione (aperta/chiusa) e scadenze
- **(15m)** Collegamento anno + studente + evento economico
- **(15m)** Vista riepilogo “cauzioni e scadenze” (semplice)
- **(15m)** Casi anomali (reso parziale, note) gestiti come note

### 503 — Accessori/Noleggi/Libri/Esami (Base: 150 min)
- **(30m)** Definizione dati minimi per noleggio (inizio/fine/stato/cauzione)
- **(30m)** Flusso “distribuzione libri/materiali” (assegnazione + quantità)
- **(15m)** Collegamenti rapidi da studente verso accessori/noleggi/libri
- **(15m)** Liste con filtri minimi (anno + studente)
- **(30m)** Esami: rappresentazione minima e come recuperarli dai file (anche come nota se non strutturabile)
- **(30m)** Pulizia casi reali (reso/rotture/cauzioni) gestita con note e stati minimi

### 504 — Docenti/Lavoratori (Base: 60 min)
- **(15m)** Campi minimi anagrafica docente + stato attivo/non attivo
- **(15m)** Disponibilità (se presente) e uso operativo
- **(15m)** Liste e ricerca (minimo)
- **(15m)** Collegamento docente ↔ corsi dell’anno (coerente)

### 505 — Calendario Annuale (Base: 60 min)
- **(15m)** Definizione calendario “giorni lezione” e sospensioni (per anno)
- **(15m)** Vista calendario base + filtri anno
- **(15m)** Regole minime di sospensione (range date + motivo)
- **(15m)** Allineamento con disponibilità e iscrizioni (solo coerenza, non automazioni)

### 506 — Modulo Anagrafica e Disponibilità (Base: 60 min)
- **(15m)** Regole deduplica risposte modulo su studente persona
- **(15m)** Scrittura disponibilità come dati operativi (anno corrente)
- **(15m)** Gestione note “impegni/preferenze” come testo strutturato minimo
- **(15m)** Report import (quanti importati/saltati + motivi)

### 508 — Documenti e Modelli Contratti (Base: 60 min)
- **(15m)** Archivio documenti con metadati minimi (anno/studente/tipo)
- **(15m)** Upload + download (minimo)
- **(15m)** Collegamenti da studente/contratto ai documenti
- **(15m)** Regole base naming/ricerca

### 509 — Caricamento dati iniziali dai fogli esistenti (Base: 150 min)
- **(30m)** Mappa sorgenti ODS → cosa importiamo in quale sezione
- **(30m)** Regole deduplica e casi “dati sporchi” (minimo)
- **(30m)** Verifica “campi critici” importati (CF, stato anno, iscrizioni, pagamenti)
- **(30m)** Report import generale (conteggi e anomalie)
- **(30m)** Verifica incrociata (campioni) con fogli originali per confermare 1:1

---

## FASE 2 — Ottimizzazione e alleggerimento operativo (task base, opzionale)

### 507 — Statistiche Storiche (Base: 60 min)
- **(30m)** Definizione indicatori (cosa serve davvero al cliente)
- **(30m)** Vista/tabella + confronto anni (minimo)

### 510 — Semplificazione anagrafiche studenti e contatti (Base: 90 min)
- **(30m)** Riduzione campi e regole: cosa è obbligatorio vs opzionale
- **(30m)** Flusso “aggiornamento dati” più veloce (meno passaggi)
- **(30m)** Regole anti‑duplicato più chiare (senza “bloccare” lavoro)

### 511 — Gestione famiglie (genitori/tutori) più semplice (Base: 60 min)
- **(30m)** Interfaccia rapida per collegare/scollegare contatti
- **(30m)** Gestione “contatto fatturazione” e casi reali

### 512 — Corsi e iscrizioni più chiari anno per anno (Base: 90 min)
- **(30m)** Standardizzazione nomi/etichette corsi dell’anno (leggibilità)
- **(30m)** Riduzione errori: controlli “soft” (warning) su date/stati
- **(30m)** Shortcut operativi (da studente → iscrizione in 2 click)

### 513 — Supporto alla creazione dell’orario (disponibilità) (Base: 90 min)
- **(30m)** Vista disponibilità per studente (pulita)
- **(30m)** Vista disponibilità per docente (pulita)
- **(30m)** Evidenziare conflitti base (senza algoritmo di assegnazione)

### 514 — Pagamenti, scadenze e solleciti più chiari (Base: 120 min)
- **(30m)** Riepilogo scadenze imminenti “operativo”
- **(30m)** Stato pagamenti più leggibile (pagato/parziale/scaduto)
- **(30m)** Note/flag “sollecito” (manuale, senza invii automatici)
- **(30m)** Filtri e ricerca per trovare rapidamente i casi critici

### 515 — Materiali, noleggi e cauzioni più ordinati (Base: 90 min)
- **(30m)** Liste unificate e filtri per anno/studente
- **(30m)** Stato cauzioni e scadenze più chiaro
- **(30m)** Riduzione passaggi per registrare consegna/reso

### 516 — Calendario scolastico più gestibile (Base: 90 min)
- **(30m)** Inserimento sospensioni più rapido e controlli base
- **(30m)** Vista “cicli/settimane” (minimo) se utile all’operatività
- **(30m)** Allineamento con giorni lezione (errori evidenti)

### 517 — Documenti e modelli: gestione più pratica (Base: 60 min)
- **(30m)** Ricerca e filtri per anno/tipo/studente
- **(30m)** Standardizzazione nomi + archiviazione più veloce

### 518 — Pulizia e controllo qualità dei dati (Base: 150 min)
- **(60m)** Report “anomalie” (duplicati, campi critici mancanti)
- **(45m)** Correzioni guidate (senza bloccare l’operatività)
- **(45m)** Regole minime di prevenzione (warning invece di errori)

---

## FASE 3 — Evoluzioni avanzate (solo backlog, non incluso)

In Fase 3 manteniamo un approccio “minimo” per stimare: **task base di analisi/definizione** (non sviluppo pieno),
così il cliente capisce cosa significa ogni evoluzione senza vincolarci troppo.

### 519 — Gestione richieste iniziali (primo contatto) (Base: 60 min)
- **(15m)** Definizione flusso (da richiesta a contatto gestito)
- **(15m)** Dati minimi da raccogliere e regole di qualità
- **(15m)** Stati e passaggi operativi (pipeline semplice)
- **(15m)** Criteri di successo e cosa resta manuale

### 520 — Preiscrizioni e rinnovi anno scolastico (Base: 60 min)
- **(15m)** Regole “rinnovo” e cosa si porta da un anno al successivo
- **(15m)** Flusso operativo (liste e azioni minime)
- **(15m)** Eccezioni (ritiri, pause, cambi corso)
- **(15m)** Criteri di successo e rischi

### 521 — Contratti: invio, accettazione e tracciamento (Base: 60 min)
- **(15m)** Definizione step e stati (da bozza a firmato)
- **(15m)** Regole archiviazione e tracciamento (manuale vs automatico)
- **(15m)** Gestione eccezioni (modifiche/annullamenti)
- **(15m)** Criteri di successo

### 522 — Pagamenti avanzati e gestione casi reali (Base: 60 min)
- **(15m)** Casi reali da supportare (parziali, ritardi, accordi)
- **(15m)** Regole di stato e riconciliazione
- **(15m)** Vista operativa “cosa fare oggi”
- **(15m)** Criteri di successo

### 523 — Comunicazioni: invii mirati e messaggi rapidi (Base: 60 min)
- **(15m)** Tipi comunicazione (avvisi, scadenze, info)
- **(15m)** Segmentazione destinatari (regole)
- **(15m)** Tracciamento minimo (inviato/non inviato) anche manuale
- **(15m)** Criteri di successo

### 524 — Lezioni, recuperi e gestione aule (operatività docente) (Base: 60 min)
- **(15m)** Regole recuperi/spostamenti
- **(15m)** Conflitti aula/docente e gestione minima
- **(15m)** Flusso operativo (da richiesta a lezione aggiornata)
- **(15m)** Criteri di successo

### 525 — Registro docenti e presenze (anche per progetti) (Base: 60 min)
- **(15m)** Dati minimi registro e presenze
- **(15m)** Regole “chi compila cosa” e quando
- **(15m)** Stati/approvazioni minime
- **(15m)** Criteri di successo

### 526 — Compensi docenti: regole, extra e consuntivi (Base: 60 min)
- **(15m)** Regole di calcolo (minime) e fonti dati
- **(15m)** Gestione extra e casi reali
- **(15m)** Output necessario (riepiloghi)
- **(15m)** Criteri di successo

### 527 — Report avanzati per direzione e bilancio sociale (Base: 60 min)
- **(15m)** Elenco report prioritari (max 3)
- **(15m)** Definizione indicatori e fonti
- **(15m)** Layout minimo e frequenza
- **(15m)** Criteri di successo

### 528 — Visione economica d’insieme (entrate/uscite) (Base: 60 min)
- **(15m)** Dati richiesti e perimetro
- **(15m)** Regole entrate/uscite (minime)
- **(15m)** Vista riepilogo “direzionale”
- **(15m)** Criteri di successo

### 529 — Area personale per famiglie (opzionale) (Base: 60 min)
- **(15m)** Cosa vede la famiglia (minimo)
- **(15m)** Dati accessibili e privacy
- **(15m)** Flusso richieste/risposte (manuale vs automatico)
- **(15m)** Criteri di successo

