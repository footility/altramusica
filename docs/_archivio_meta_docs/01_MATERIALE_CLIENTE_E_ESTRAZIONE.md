# 01 - Materiale cliente (ODS/Excel) -> estrazione dati e funzionalità

Questo documento è la base: tutto ciò che viene implementato e documentato deve rimanere coerente con i dati e i vincoli presenti nei file in `docs/materiale cliente/`.

## File sorgenti (as-is)

- `db 2025-26 gestionale.ods`
  - anagrafiche studenti e informazioni collegate
  - corsi/iscrizioni (fino a più corsi per studente)
  - parti accessori/strumenti (in base a come sono presenti nel foglio)
- `Db Contratti 25-26.ods`
  - contratti studenti (dati contrattuali e stato)
- `Db Contabile 2025-26.ods`
  - contabilità collegata a studenti/contratti (fatture/pagamenti/estratti, a seconda del foglio)
- `Db Accessori 2025-26.ods`
  - accessori/libri/esami (voci extra rispetto al contratto principale)
- `dati lavoratori 25-26.ods`
  - docenti/lavoratori
- `Calendario 2025-26.ods`
  - calendario lezioni (giorni attivi, sospensioni, struttura annuale)
- `Anagrafica e disponibilità a.s. 2025_26 (Risposte).xlsx`
  - disponibilità (studenti/docenti in base al tracciato effettivo)

## Come si traduce in “aree funzionali”

Le aree del nuovo gestionale (Fase 1) devono coprire:

- anagrafiche (studenti, genitori/tutori, docenti)
- anno scolastico/esercizio
- disponibilità e filtri per costruzione orario
- corsi/iscrizioni (con modifiche frequenti a inizio anno)
- calendario lezioni + sospensioni
- contratti (almeno due tipologie: regolare vs breve)
- fatture/pagamenti (almeno base, con gestione crediti/debiti)
- strumenti/accessori/libri (almeno tracciamento vendite/noleggi e valore)

## Evidenze funzionali dalle trascrizioni (priorità operative)

Le trascrizioni (`docs/Trascrizione gestionale parte 1.txt`, `docs/Trascrizione gestionale parte 2.txt`) evidenziano priorità “reali” di utilizzo:

- necessità di filtri forti per orario e comunicazioni (es. “elementari + violino + lunedì”)
- flusso di lavoro che si muove in parallelo quando uno studente si iscrive (anagrafica + contratto + calendario + pagamenti)
- modifiche frequenti a corsi/orari soprattutto a inizio anno
- attenzione a pagamenti non perfettamente “puliti” (crediti/debiti, pagamenti errati, ripartizioni su fratelli)
- supplenze frequenti e impatto su registro/presenze/conto ore docenti

## Riferimenti tecnici

- Analisi colonne: `docs/ANALISI_COLONNE_ODS_COMPLETA.md`

