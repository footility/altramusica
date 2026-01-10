# Script Archiviati - Integrazione Footility

Questa cartella contiene script che facevano riferimento al progetto Footility per integrazione/importazione dati.

## Script Archiviati

Tutti gli script in questa cartella hanno dipendenze dirette da Footility:
- Include autoload/bootstrap di Footility
- Chiamate API Footility
- Accesso diretto al database Footility

## Uso Storico

Questi script sono stati utilizzati durante la fase iniziale di analisi e importazione dati del progetto L'Altramusica in Footility per:
- Analisi ODS e creazione E/R models
- Generazione DEV UNITS da schema ODS
- Importazione attività e preventivi
- Associazione attività ↔ DEV UNITS

## Note

- **Progetto ID Footility:** 13 (L'Altramusica)
- **Quotation ID Footility:** 1 (preventivo iniziale)
- **API Base URL:** `https://footility.test/api/v1`
- **Token richiesto:** `FOOTILITY_API_TOKEN` (env variable)

## Possibile Riuso Futuro

Questi script possono servire come riferimento per:
- Pattern analisi ODS → E/R
- Pattern generazione DEV UNITS
- Pattern associazione attività-dev units
- Struttura dati per import

**NON utilizzare direttamente** senza adattare path e dipendenze.
