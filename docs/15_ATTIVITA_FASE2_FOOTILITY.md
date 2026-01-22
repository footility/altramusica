# 15 - Attività Footility - Fase 2 (Ottimizzazione / Alleggerimento operativo)

Queste attività rappresentano la **Fase 2**: evoluzione dal gestionale “tradotto da ODS” a un sistema **più normalizzato, snello e intelligente**, riducendo duplicazioni, testo libero e regole implicite.

Nota: le attività **non** includono riferimenti a file/sheet/colonne (quelli restano nella documentazione AS‑IS `docs/01..12`).

## Elenco attività (da creare in Footility)

### F2-01 — Ottimizzazione Anagrafiche Studenti e Contatti

**Descrizione (breve)**: rendere l’anagrafica studente più pulita e strutturata, separando i dati stabili dai dati dell’anno e trasformando “note” in campi utili (ricerca, filtri, storicizzazione).

**Task (pratici)**:
- Separare dati “persona” vs dati “nell’anno” (stato, note operative, follow-up)
- Normalizzare recapiti (indirizzi, email, telefoni) e ridurre uso del testo libero
- Introdurre storico contatti (timeline/eventi) per follow‑up e tracciamento
- Regole chiare di deduplica e gestione omonimi

### F2-02 — Ottimizzazione Genitori/Tutori e Nuclei Familiari

**Descrizione (breve)**: ridurre duplicazioni e incoerenze rendendo genitori/tutori entità riusabili e collegabili a più studenti, con ruoli e contatti strutturati.

**Task (pratici)**:
- Entità “contatto adulto” unica, riusabile, con ruoli e preferenze
- Unificare/bonificare duplicati e varianti (merge guidato)
- Vista “famiglia” e azioni rapide di collegamento/scollegamento

### F2-03 — Ristrutturazione Corsi (Catalogo) e Offerte Annuali

**Descrizione (breve)**: rendere chiara la differenza tra corso “di catalogo” e corso “dell’anno”, evitando duplicazioni e retro‑propagazione di tariffe/regole.

**Task (pratici)**:
- Definire catalogo corsi (stabile) e offerte annuali (tariffe/regole/orari)
- Iscrizione come record operativo con override (docente/aula/orari)
- Normalizzare laboratorio/orchestra/coro in un modello coerente
- Convenzioni di naming/codici per evitare duplicati

### F2-04 — Pianificazione e Disponibilità (Studenti/Docenti) più utilizzabile

**Descrizione (breve)**: trasformare disponibilità e preferenze in dati strutturati e riutilizzabili, riducendo testo libero e ambiguità.

**Task (pratici)**:
- Modello unico di disponibilità (fasce orarie) + note separate
- Import robusto risposte modulo + gestione revisioni
- Vista operativa per incrocio disponibilità (studente↔docente↔aula)

### F2-05 — Ottimizzazione Contabilità: Scadenze, Pagato e Recupero Crediti

**Descrizione (breve)**: rendere espliciti workflow e stati contabili (rate/pagamenti/solleciti) riducendo le logiche “a griglia” e le regole implicite.

**Task (pratici)**:
- Pagamenti come eventi; saldo e arretrati calcolabili in modo coerente
- Recupero crediti come workflow con stati e log solleciti
- Report operativi (priorità, prossime azioni, anomalie)

### F2-06 — Accessori / Noleggi / Cauzioni / Libri: Normalizzazione e Stati Operativi

**Descrizione (breve)**: passare da “colonne ripetute” a righe normalizzate e stati operativi chiari per noleggi e cauzioni.

**Task (pratici)**:
- Linee accessori/libri come righe (n voci), collegabili a fatture
- Noleggio con stati (attivo/scaduto/chiuso) e scadenze
- Cauzione come deposito/reso con tracciamento eventi
- Inventario minimo e storico assegnazioni

### F2-07 — Calendario Didattico: Cicli, Sospensioni e Lezioni Libere

**Descrizione (breve)**: strutturare ciclo didattico e calendario annuale per ridurre gestione manuale e incoerenze.

**Task (pratici)**:
- Oggetti “ciclo”, “settimana didattica”, “sospensione”, “lezione libera”
- Generazione calendario guidata da configurazione con preview/conferma
- Collegamento coerente a lezioni/registro

### F2-08 — Documenti: Template, Generazione e Archivio Operativo

**Descrizione (breve)**: strutturare il processo documentale (template→generazione→invio manuale→archivio) con metadati e ricerca.

**Task (pratici)**:
- Catalogo template (tipi, versioni, validità per anno)
- Generazione documenti e versioning
- Registro invii (manuale) e archivio con filtri per studente/anno/tipo

### F2-09 — Qualità Dati e Riconciliazione Post‑Migrazione

**Descrizione (breve)**: ridurre errori e “sporco” dei dati emersi dopo la migrazione, con strumenti di analisi e merge guidato.

**Task (pratici)**:
- Report anomalie (duplicati, record orfani, relazioni mancanti)
- Merge guidato (studenti, genitori, corsi) con log decisioni
- Validazioni minime e checklist di chiusura qualità

