# 04 - Contratti studenti (AS-IS da ODS/Excel)

Fonti principali:
- `docs/materiale cliente/Db Contratti 25-26.ods` → foglio `Contratti`
- in supporto: dati da `db 2025-26 gestionale.ods` (anagrafica + corsi)

## Cosa permette di fare (oggi, in Excel/ODS)

- tracciare un codice contratto e uno stato
- avere un riepilogo contrattuale per studente (anagrafica + contatti + indirizzo)
- collegare per corso (1/2/3): data inizio, descrizione, tipologia, rate 1/2/3 e totali

## Dati/colonne rilevanti (estratto)

Da `Db Contratti 25-26.ods`:
- `codice contratto`
- `stato`
- `Cognome`, `Nome`, `data di nascita`, `cod. fiscale allievo`
- genitori e contatti (`cognome/nome genitore 1/2`, `cell`, `mail`)
- indirizzo (`Indirizzo allievo`, `cap`, `Città`)
- per corso 1/2/3: `data inizio`, `Corso`, `descrizione`, `tipologia`, `1 rata`, `2 rata`, `3 rata`, `tot`

