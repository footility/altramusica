# 05 - Contabilità corsi (AS-IS da ODS/Excel)

Fonte principale: `docs/materiale cliente/Db Contabile 2025-26.ods`

Fogli rilevanti:
- `fatt corsi`
- `pagato`
- `recupero crediti`
- `riepilogo sintetico`

## Cosa permette di fare (oggi, in Excel/ODS)

- riepilogare quanto è stato fatturato per corsi (totali, non fatturato, note, sconto, iscrizione)
- tracciare invii (contratto, mail, fatture) come processo manuale controllabile
- gestire dilazioni/scadenze (numero dilazioni, scadenza 1/2)
- tracciare pagamenti su griglia mensile e verifiche (con crediti/debiti)
- calcolare dovuto vs pagato vs da saldare (anche per orchestra/coro quando presente)
- supportare recupero crediti (campi invio/sollecito e contatore giorni)

## Dati/colonne rilevanti (estratto)

Da `fatt corsi`:
- `dettagli corsi`, descrizioni corsi 1/2/3 e tipologie
- `tot complessivo fatturato`, `Tot non fatturato`, `sconto`, `€ fatt`
- `Data invio contratto`, `data invio mail`
- `n. dilazioni`, `Scadenza 1`, `Scadenza 2`

Da `pagato`:
- `Crediti/debiti`
- griglia mesi (settembre…)
- `verificato il`

Da `recupero crediti`:
- `pagato tot`, `tot complessivo da saldare`
- `data invio`, `data soll 1`, `conta giorni`

Da `riepilogo sintetico`:
- `pagato tot`, `tot anno dovuto`, `tot da saldare`
- riepilogo rate (1°/2°/3°/4°) e note

