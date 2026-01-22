# 00 - Checklist copertura sezioni (AS-IS) vs ANALISI_COLONNE_ODS_COMPLETA

Scopo: verificare che le **12 sezioni AS‑IS** coprano tutti i blocchi funzionali presenti nei file ODS/XLSX.

Riferimento: `docs/ANALISI_COLONNE_ODS_COMPLETA.md`

## Nota importante su fogli “file:///…”

In più file ODS esistono fogli con nome del tipo `file:///...#dati` o `file:///...#accessori`.
Dall’analisi risultano “header” che in realtà sono **valori di esempio** (es. `Agostini`, `Juliette`, `def`, `ok`, `0`, `14`), quindi questi fogli vanno considerati **derivati/di supporto** e **non rappresentano funzionalità nuove**.

Quindi, ai fini di copertura, consideriamo come fogli “primari”:
- `db 2025-26 gestionale.ods`: `dati` (197), `età_scolare` (3), `grafico` (23)
- `Db Contratti 25-26.ods`: `Contratti` (59)
- `Db Contabile 2025-26.ods`: `fatt corsi` (98), `pagato` (89), `recupero crediti` (145), `riepilogo sintetico` (65), `fatt accessori` (97)
- `Db Accessori 2025-26.ods`: `accessori` (62)
- `dati lavoratori 25-26.ods`: `2025-26` (35), `archivio insegnanti-supplenti` (28)
- `Calendario 2025-26.ods`: `Sheet1` (5)
- XLSX modulo: coperto dai file “Risposte del modulo 1” (non incluso nell’analisi markdown)

## Copertura per sezione (high level)

1) `01_ANAGRAFICHE_STUDENTI.md`
- Copre: anagrafica, stato, primo contatto, note, contatti, indirizzo
- Da includere anche come sotto-funzionalità: livelli (strumento/teoria), indicatori minore/età (formule), tracking contatto/conferme se usati.

2) `02_GENITORI_TUTORI.md`
- Copre: genitore 1/2 (nominativi + contatti) embedded nella riga studente

3) `03_CORSI_E_ISCRIZIONI.md`
- Copre: corso 1/2/3, docente/aula/giorno/orario, lab teoria, tipologia (regolare/stile libero), prezzi e rate da settimane
- Da includere anche: orchestra/coro come “iscrizioni extra” se presenti nel foglio `dati`.

4) `04_CONTRATTI_STUDENTI.md`
- Copre: codice/stato contratto, riepilogo contrattuale e rate (1/2/3 + totali) + invio/ritorno/sollecito (sezione contratti)

5) `05_CONTABILITA_CORSI.md`
- Copre: fatturazioni corsi, pagato mensile, riepilogo saldi, recupero crediti/solleciti, dilazioni/scadenze
- Da includere anche: gestione iscrizione e sconto (voci collegate) e orchestra/coro quando fatturata.

6) `06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md`
- Copre: fatturato accessori, non fatturato, cauzioni/scadenze/reso, storico “Fatt 1..”
- Da includere: fatturazione libri/esami se gestita nello stesso foglio contabile accessori.

7) `07_ACCESSORI_NOLEGGI_LIBRI.md`
- Copre: accessori per studente, noleggi, acquisto strumento, libri
- Da includere: esami (e bollo) se presenti nel foglio accessori.

8) `08_DOCENTI_LAVORATORI.md`
- Copre: anagrafica docenti/lavoratori, ruoli/corsi, inquadramento, IBAN e dati fiscali, disponibilità, archivio supplenti

9) `09_CALENDARIO_ANNUALE.md`
- Copre: struttura anno a cicli e “lezioni libere”
- Nota: per sospensioni/date specifiche serve analisi mirata del layout.

10) `10_MODULO_ANAGRAFICA_E_DISPONIBILITA.md`
- Copre: raccolta anagrafica+disponibilità da modulo, scelte (lab/orchestra), preferenze, privacy

11) `11_STATISTICHE_STORICHE.md`
- Copre: serie storica iscritti e indicatori (corso1, coro/orch)

12) `12_DOCUMENTI_E_MODELLI_CONTRATTI.md`
- Copre: modelli ODT, contratti inviati (PDF), contratti noleggio (doc)

