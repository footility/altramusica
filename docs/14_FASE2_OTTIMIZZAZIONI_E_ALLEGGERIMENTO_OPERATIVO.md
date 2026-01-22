# 14 - Fase 2: Ottimizzazioni / Alleggerimento operativo (post-migrazione 1:1 ODS)

Obiettivo: dopo la **Fase 1 (migrazione 1:1 + CRUD)**, ridurre la complessità operativa “alla Barbara Tripodi” (regole implicite nei fogli, duplicazioni, testo libero usato come campi), **normalizzare** i dati e rendere più chiari i workflow.

Vincoli:
- In questa fase si progettano e stimano attività e task; **nessuna modifica codice qui** (serve come base Footility).
- Non si introducono servizi esterni obbligatori (email/SMS/SDI) se non come evoluzione opzionale.

## Attività Fase 2 (macro) + task pratici

### F2-01 — Normalizzazione anagrafiche e contatti (Student persona + profilo annuale)

**Problema da ODS**: dati strutturati (indirizzo, email, telefoni) e stato operativo finiscono in campi “note” o sparsi; deduplica e storico non sono chiari.

**Task**:
- Definire modello “persona” (`Student`) separato da modello “nell’anno” (es. `StudentYearProfile` o equivalente) con stato/anno, note operative, follow-up.
- Normalizzare contatti: recapiti come entità (o campi dedicati) invece di stringhe nelle note; decidere cosa è del minore vs del genitore.
- Migrazione dati: estrarre dai campi note i valori “indirizzo/tel/email” e portarli in campi dedicati (con report di ambiguità).
- Storico contatti: introdurre log eventi (data contatto, esito, note brevi) al posto del solo testo libero.
- Regole di deduplica esplicite (CF, nome+cognome, gestione omonimi) + tool di riconciliazione manuale.

### F2-02 — Genitori/Tutori come entità riusabile (riduzione duplicazioni)

**Problema da ODS**: genitori embedded nello studente → duplicazioni se un genitore ha più figli; aggiornamenti incoerenti.

**Task**:
- Stabilire “Contatto adulto” unico e collegabile a più studenti (ruolo: madre/padre/tutore, billing contact, primaria).
- Normalizzare recapiti genitori (più telefoni/email; preferenze comunicazione).
- Riconciliare duplicati: stessa persona genitore presente su più studenti con varianti (tool + regole di merge).
- UI operativa: vista “famiglia” (studenti collegati a un contatto) + azioni rapide.

### F2-03 — Corsi: catalogo vs offerta annuale vs iscrizione (Course / CourseOffering / Enrollment)

**Problema da ODS**: “corso” è contemporaneamente definizione, tariffa, orario, docente, aula, anno; questo genera duplicazioni e retro-propagazione.

**Task**:
- Definire `Course` come catalogo (strumento/discipline) e spostare tariffe/regole/orari su `CourseOffering` (per anno).
- Definire `Enrollment` come iscrizione dello studente a una offering, con eventuali override (docente/aula/orari).
- Normalizzare “laboratorio teoria / orchestra / coro” come offerte/attività coerenti (evitare campi speciali sparsi).
- Riconciliare “sigle corso” e naming: regole univoche e pulizia (evitare duplicati `CC-1`, ecc.).
- UI: flusso guidato “crea offerta anno” → “iscrivi studenti” → “genera contratto/fattura”.

### F2-04 — Pianificazione e disponibilità: da testo libero a vincoli utilizzabili

**Problema da ODS**: disponibilità e preferenze sono testo libero e griglie; difficile usarle per pianificare senza errori.

**Task**:
- Modello unico di disponibilità (studente + docente) con fasce orarie strutturate e note separate.
- Import robusto da XLSX modulo: mapping stabile, gestione revisioni, controllo duplicati e conferme.
- Introduzione “vincoli” (impegni fissi, preferenze) con priorità e validazioni.
- Vista operativa di incrocio: disponibilità studente ↔ docente ↔ aula (senza algoritmi avanzati, solo supporto decisionale).

### F2-05 — Contabilità: workflow espliciti (pagato/scadenze/solleciti) invece di griglie ODS

**Problema da ODS**: fogli “pagato” e “recupero crediti” sono un gestionale parallelo con regole implicite (verificato il, conteggio giorni, ecc.).

**Task**:
- Modello “scadenza” e “stato rata” coerente (piani pagamento) con calcolo saldo e arretrati.
- Eventi contabili: pagamenti come eventi (data/importo/metodo/note), non celle mensili.
- Recupero crediti come workflow: stati + log solleciti + prossima azione, con report operativi.
- Riconciliazione: strumenti per allineare “dovuto vs pagato vs da saldare” senza formule disperse.

### F2-06 — Noleggi/Cauzioni/Accessori: entità a righe (n-entries) + stati operativi

**Problema da ODS**: Accessori 1..7, Libri 1..7, fatture accessori 1..5, cauzione/reso/scadenza come colonne → struttura non normalizzata.

**Task**:
- Modello “line item” unico per accessori/libri (n righe) collegato a studente/anno e opzionalmente a fattura.
- Noleggio strumento con stati: attivo, scaduto, rinnovato, chiuso; scadenze e deposito cauzione separati.
- Cauzione: deposito, fatturazione, reso, trattenute (se presenti) come eventi/record separati.
- Inventario minimo per strumenti/libri: disponibilità, assegnazioni, storico.

### F2-07 — Calendario annuale: cicli, sospensioni e “lezioni libere” come oggetti

**Problema da ODS**: “1°/2°/3° ciclo 11 sett” e “lezioni libere” sono concetti forti ma oggi poco strutturati e import fragile.

**Task**:
- Modellare “ciclo didattico”, “settimana didattica”, “sospensione”, “lezione libera” con regole chiare.
- Generazione calendario guidata da configurazione (range anno + cicli + sospensioni) con preview e conferma.
- Collegamento con lezioni effettive (registri/docenti) in modo consistente.

### F2-08 — Documenti: processo (template → generazione → invio manuale → archivio) con metadati

**Problema da materiale**: esistono modelli e archivi PDF → workflow reale, oggi fuori dal sistema.

**Task**:
- Catalogo template (tipi, versioni, validità per anno) + metadati.
- Generazione documenti da dati (contratto, noleggio) con versioning e archiviazione.
- Registro “inviato”: data invio, canale (manuale), destinatari, note.
- Ricerca/filtri: per studente/anno/tipo/stato.

### F2-09 — Data Quality & riconciliazione (strumenti per ripulire la migrazione)

**Problema trasversale**: dopo la migrazione 1:1, emergono duplicati, campi “sporchi”, relazioni mancanti e record orfani.

**Task**:
- Report “anomalie dati” per sezione (duplicati CF, omonimi, corsi duplicati, contratti senza iscrizione, fatture senza righe, ecc.).
- Strumenti di merge guidato (studenti, genitori, corsi) con log decisioni.
- Regole di validazione minime + indicatori (campi obbligatori mancanti).
- Checklist di chiusura migrazione (qualità) per rendere il sistema stabile per l’uso quotidiano.

## Note: come usare questo documento in Footility

- Ogni “F2-xx” è una **attività**.
- Ogni bullet “Task” è un **task** (sottotask) da stimare e schedulare.
- Le attività possono essere associate alle stesse 12 aree AS‑IS (A01..A12), ma qui sono raggruppate per **riduzione complessità** (non per foglio).

