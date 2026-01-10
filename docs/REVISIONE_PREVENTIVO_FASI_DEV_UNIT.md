# Revisione Preventivo - Separazione in Fasi con DEV UNIT

**Data:** Dicembre 2024  
**Obiettivo:** Rivedere preventivo separandolo in fasi, con Fase 1 = traduzione 1:1 ODS → digitale

---

## 1. ANALISI FOOTILITY E NUOVE CONSIDERAZIONI

### 1.1 Dati Footility Aggiornati

**Progetto Footility:**
- **LOC Totali:** 30.000 LOC
- **Commit Totali:** 609
- **Giorni Lavorativi:** 77 giorni
- **Ore Stimate:** 385 ore (77 × 5h)
- **Ratio LOC/Ora:** 77,9 LOC/ora
- **Periodo:** 02/05/2024 - 13/12/2025

**Breakdown LOC:**
- Controllers: 6.042 LOC
- Models: 2.062 LOC
- Views: 11.965 LOC
- Migrations: 1.879 LOC
- Seeders: 143 LOC
- JavaScript/Vue: 2.432 LOC
- CSS: 1.250 LOC
- Routes: 378 LOC
- Middleware: 267 LOC
- Services: 3.582 LOC

### 1.2 Impatto su Analisi Retrospettiva

**Prima (3 progetti):**
- Media LOC/Ora: 53,9
- Ore Consigliate L'Altramusica: 725 ore

**Dopo (4 progetti con Footility):**
- Media LOC/Ora: 59,9
- Ore Consigliate L'Altramusica: 700 ore

**Vantaggi Inclusione Footility:**
1. ✅ Campione aumentato da 3 a 4 progetti
2. ✅ Gap temporali colmati (progetto 2024-2025)
3. ✅ Progetto grande (30.000 LOC, simile a L'Altramusica stimato 28.000 LOC)
4. ✅ Conferma pattern alta produttività (77,9 LOC/ora)
5. ✅ Dashboard gestionale simile a L'Altramusica

### 1.3 Valori COSMIC Footility

**⚠️ NOTA:** Non è stata trovata analisi COSMIC specifica per Footility nei documenti disponibili.

**Raccomandazione:** Eseguire analisi COSMIC su Footility per:
- Calcolare CFP (COSMIC Function Points)
- Confrontare con altri progetti
- Validare correlazione CFP/DEV UNIT
- Aggiornare stime L'Altramusica

**Stima Indicativa (basata su pattern altri progetti):**
- Se Footility ha ratio simile a Klabhouse (103,1 LOC/ora) e CFP ~500-600
- Ratio CFP/Ore: ~1,3-1,6 CFP/ora
- Questo confermerebbe pattern progetti recenti

---

## 2. ANALISI ODS PER FASE 1 (TRADUZIONE 1:1)

### 2.1 File ODS Disponibili

1. **`db 2025-26 gestionale.ods`** (file principale)
   - Foglio: `dati`
   - Righe: 485
   - Colonne totali: 1024
   - Colonne con header: ~200+

2. **`Db Contratti 25-26.ods`**
   - 416 righe
   - Colonne multiple per contratti

3. **`Db Contabile 2025-26.ods`**
   - Fatture corsi
   - Fatture accessori
   - Pagamenti
   - Recupero crediti
   - Riepilogo sintetico

4. **`Db Accessori 2025-26.ods`**
   - Esami
   - Accessori 1-7
   - Libri 1-15
   - Noleggi strumenti dettagliati

5. **`Calendario 2025-26.ods`**
   - Calendario lezioni
   - Sospensioni
   - Festività

6. **`dati lavoratori 25-26.ods`**
   - Docenti
   - Contratti docenti
   - Archivio insegnanti-supplenti

### 2.2 Colonne ODS da Tradurre (Fase 1)

#### File Principale: `db 2025-26 gestionale.ods`

**✅ Già Importate:**
- Studenti: Cognome, Nome, Cod. Fiscale, Nato il, Età, Minore, Data arrivo, Come conosciuto, Note, Stato
- Genitori: Cognome/Nome Genitore 1-2, Cell 1-2, Mail 1-2

**❌ NON Importate (da implementare in Fase 1):**

**Disponibilità Oraria:**
- P: Disponibilità
- Q: Lu (Lunedì)
- R: Ma (Martedì)
- S: Me (Mercoledì)
- T: Gio (Giovedì)
- U: Ve (Venerdì)
- V: Sab (Sabato)
- W: Lab (Laboratorio)
- X: Note disponibilità

**Strumenti:**
- Y: Fornitore strumento
- Z: Noleggio/proprietà
- AA: Provenienza
- AB: Tipo
- AC: Marca
- AD: Modello
- AE: Misura
- AF: Cod
- AG: Da cambiare
- AH: Note strumento

**Livelli e Competenze:**
- AI: Livello
- AJ: Livello str.
- AK: Livello teoria
- AL: Musica di insieme

**Orchestra e Coro:**
- AM: Conf (Conferma orchestra)
- AN: Orch 1
- AO: PYO
- AP: Conf coro
- AQ: Coro
- AR: Data ultima convocazione
- AS: Note orchestra

**Contratti e Pagamenti:**
- A: Richiesta pagamento
- B: Pagato
- C: Conto orario
- D: Contratto

**Corsi e Iscrizioni:**
- I: Info
- K: A che corso sei interessato?
- L: Ti piacerebbe provare anche altri strumenti?

**Altri Dati:**
- AT: Dati (dati aggiuntivi)
- Fogli separati: Età scolare, Grafico

#### File Separati da Tradurre

**Db Contabile 2025-26.ods:**
- ❌ Fatture accessori (non importato)
- ❌ Pagato (non importato)
- ❌ Recupero crediti (non importato)
- ❌ Riepilogo sintetico (non importato)

**Db Accessori 2025-26.ods:**
- ❌ Accessori 1-7 (non importato)
- ❌ Libri 1-15 (non importato)
- ❌ Noleggi strumenti dettagliati (non importato)

**dati lavoratori 25-26.ods:**
- ❌ Contratti docenti dettagliati (non importato)
- ❌ Archivio insegnanti-supplenti (non importato)

---

## 3. RISTRUTTURAZIONE PREVENTIVO IN FASI

### 3.1 Principio Base

**FASE 1: Traduzione 1:1 ODS → Digitale**
- **Obiettivo:** Tradurre l'attuale DB ODS in digitale
- **Approccio:** Traduzione il più possibile 1:1 dell'attuale DB
- **Ingegnerizzazione:** Dati, relazioni, formule in modo che il sistema funzioni UGUALE ad adesso
- **CRUD Essenziale:** Per ogni informazione presente negli ODS
- **Nessuna funzionalità evolutiva:** Solo traduzione digitale, no workflow avanzati, automazioni, PDF

**FASE 2-3: Evoluzioni Future**
- Funzionalità che emergono dalla conversazione con cliente
- Workflow avanzati
- Automazioni
- Integrazioni esterne
- Reportistica avanzata

### 3.2 FASE 1: Traduzione 1:1 ODS → Digitale

**Scopo:** Sistema digitale che funziona UGUALE all'attuale sistema ODS

**Include:**
- ✅ Tutti i modelli dati necessari per rappresentare tutte le colonne ODS
- ✅ Relazioni database normalizzate ma che preservano i dati ODS
- ✅ CRUD essenziale per ogni entità (index, create, store, show, edit, update, destroy)
- ✅ Form per inserimento/modifica di tutti i campi ODS
- ✅ Liste per visualizzazione/filtro di tutti i dati ODS
- ✅ Import dati da ODS esistenti
- ✅ Validazione base (formato dati, obbligatorietà)

**Esclude:**
- ❌ Workflow avanzati (es. invio email contratti)
- ❌ Automazioni (es. solleciti automatici)
- ❌ Generazione PDF (es. contratti, fatture)
- ❌ Integrazioni esterne (es. SDI, Cassetto Fiscale)
- ❌ Reportistica avanzata (es. grafici, confronti multi-anno)
- ❌ Funzionalità evolutive (es. primo contatto pubblico, proposta oraria avanzata)

**Metodologia DEV UNIT:**
- **DEV_DB_campi:** Ogni colonna ODS → campo database = 1 DEV UNIT
- **DEV_DB_relazioni:** Ogni relazione FK necessaria = 1 DEV UNIT
- **DEV_LOGIC_CRUD:** CRUD base = 7 DEV UNIT (index, create, store, show, edit, update, destroy)
- **DEV_LOGIC_workflow:** Workflow import/validazione = 1 DEV UNIT
- **DEV_UI_form:** Ogni campo form = 1 DEV UNIT
- **DEV_UI_lista:** Ogni colonna lista + filtro = 1 DEV UNIT
- **DEV_UI_stampa:** Ogni campo export/stampa = 0 DEV UNIT (Fase 1 non include stampa)

### 3.3 Calcolo DEV UNIT Fase 1

**Basato su:** `docs/dev-unit/reali/DEV_UNIT_REALI_ODS_LARAVEL.md`

**DEV UNIT REALI (già calcolate):** 730 DEV UNIT

**Breakdown:**
- DB_campi: 226 DEV UNIT
- DB_relazioni: 43 DEV UNIT
- CRUD: 67 DEV UNIT
- Workflow: 13 DEV UNIT
- UI_form: 191 DEV UNIT
- UI_lista: 190 DEV UNIT
- UI_stampa: 0 DEV UNIT

**⚠️ NOTA:** Questo calcolo include solo le colonne ODS già analizzate. Serve verifica completa di tutte le colonne ODS per assicurarsi che tutte siano incluse.

**Colonne ODS da Aggiungere (non incluse nel calcolo attuale):**

1. **Disponibilità Oraria Completa** (colonne P-X)
   - ~9 colonne aggiuntive
   - ~9 DEV UNIT (campi) + ~9 DEV UNIT (form) + ~9 DEV UNIT (lista) = ~27 DEV UNIT

2. **Strumenti Collegati a Studenti** (colonne Y-AH)
   - ~10 colonne aggiuntive
   - ~10 DEV UNIT (campi) + ~10 DEV UNIT (form) + ~10 DEV UNIT (lista) = ~30 DEV UNIT

3. **Livelli Studenti Completi** (colonne AI-AL)
   - ~4 colonne aggiuntive
   - ~4 DEV UNIT (campi) + ~4 DEV UNIT (form) + ~4 DEV UNIT (lista) = ~12 DEV UNIT

4. **Orchestra/Coro Collegati** (colonne AM-AS)
   - ~7 colonne aggiuntive
   - ~7 DEV UNIT (campi) + ~7 DEV UNIT (form) + ~7 DEV UNIT (lista) = ~21 DEV UNIT

5. **Contratti/Pagamenti dal File Principale** (colonne A-D)
   - ~4 colonne aggiuntive
   - ~4 DEV UNIT (campi) + ~4 DEV UNIT (form) + ~4 DEV UNIT (lista) = ~12 DEV UNIT

6. **Corsi/Iscrizioni dal File Principale** (colonne I, K, L)
   - ~3 colonne aggiuntive
   - ~3 DEV UNIT (campi) + ~3 DEV UNIT (form) + ~3 DEV UNIT (lista) = ~9 DEV UNIT

7. **File Separati:**
   - Db Contabile completo: ~50 DEV UNIT (fatture accessori, pagamenti, recupero crediti)
   - Db Accessori completo: ~40 DEV UNIT (accessori, libri, noleggi)
   - dati lavoratori completo: ~30 DEV UNIT (contratti docenti, archivio)

**TOTALE AGGIUNTIVO STIMATO:** ~231 DEV UNIT

**TOTALE FASE 1 RIVISTO:** 730 + 231 = **~961 DEV UNIT**

### 3.4 FASE 2: Evoluzioni Amministrative

**Include:**
- Primo contatto pubblico
- Gestione utenze avanzata
- Controllo accessi granulare
- Contratti evolutivi (workflow invio/firma)
- Modelli contratto
- Generazione PDF contratti
- Fatturazione evolutiva (rateizzazione, piani pagamento)
- Automazioni sollecito
- Fornitori
- Integrazione SDI (fatturazione elettronica)
- Integrazione Cassetto Fiscale

**DEV UNIT IPOTETICHE (già calcolate):** ~350 DEV UNIT

### 3.5 FASE 3: Evoluzioni Didattiche

**Include:**
- Registro evoluto
- Recuperi lezioni avanzati
- Conto orario docenti avanzato
- Gestione supplenze
- Attività extra evolute
- Esami avanzati
- Attestati frequenza (PDF)
- Comunicazioni evolute
- Reportistica avanzata
- Flusso di cassa
- Preiscrizioni

**DEV UNIT IPOTETICHE (già calcolate):** ~360 DEV UNIT

---

## 4. PREVENTIVO RIVISTO PER FASI

### 4.1 FASE 1: Traduzione 1:1 ODS → Digitale

**DEV UNIT:** ~961 DEV UNIT

**Conversione DEV UNIT → Ore:**
- **Opzione A:** Usare ratio progetti completati
  - Media DEV UNIT/Ore: Non disponibile (serve calcolo)
  - **Raccomandazione:** Calcolare ratio da progetti completati con DEV UNIT

- **Opzione B:** Usare ratio LOC/Ora
  - LOC stimati Fase 1: ~15.000-18.000 LOC (basato su 961 DEV UNIT × ~15-20 LOC/DEV UNIT)
  - Ratio LOC/Ora (4 progetti): 59,9 LOC/ora
  - **Ore:** 15.000 ÷ 59,9 = **~250 ore** (minimo)
  - **Ore:** 18.000 ÷ 59,9 = **~300 ore** (massimo)
  - **Media:** **~275 ore**

- **Opzione C:** Usare analisi pragmatica lavoro residuo
  - Lavoro già fatto: ~580 ore (75-80% strutturale)
  - Fase 1 corrisponde a ~60-70% del lavoro strutturale
  - **Ore:** 580 × 0.65 = **~377 ore**

**Stima Finale Fase 1:**
- **Minimo:** 250 ore
- **Realistico:** 275-300 ore
- **Massimo:** 377 ore
- **Raccomandazione:** **~300 ore**

**Costo (tariffa €30/ora):** € 9.000,00

### 4.2 FASE 2: Evoluzioni Amministrative

**DEV UNIT:** ~350 DEV UNIT

**Stima Ore:**
- Basata su analisi preventivo originale: ~144 ore (Contratti/Fatturazione) + ~76 ore (Infrastruttura) = **~220 ore**
- Con pattern consolidati e IA: **~150-180 ore**

**Costo (tariffa €30/ora):** € 4.500,00 - € 5.400,00

### 4.3 FASE 3: Evoluzioni Didattiche

**DEV UNIT:** ~360 DEV UNIT

**Stima Ore:**
- Basata su analisi preventivo originale: ~132 ore (Didattica) + ~88 ore (Attività Extra) + ~116 ore (Integrazioni) = **~336 ore**
- Con pattern consolidati e IA: **~200-250 ore**

**Costo (tariffa €30/ora):** € 6.000,00 - € 7.500,00

### 4.4 TOTALE PREVENTIVO RIVISTO

| Fase | DEV UNIT | Ore | Costo (€30/ora) |
|------|----------|-----|-----------------|
| **Fase 1: Traduzione 1:1** | ~961 | ~300 | € 9.000,00 |
| **Fase 2: Evoluzioni Amministrative** | ~350 | ~150-180 | € 4.500,00 - € 5.400,00 |
| **Fase 3: Evoluzioni Didattiche** | ~360 | ~200-250 | € 6.000,00 - € 7.500,00 |
| **TOTALE** | **~1.671** | **~650-730** | **€ 19.500,00 - € 21.900,00** |

**Confronto con Preventivo Originale:**
- Preventivo originale: 852 ore, € 25.560,00
- Preventivo rivisto: 650-730 ore, € 19.500,00 - € 21.900,00
- **Riduzione:** ~15-25% ore, ~15-25% costo

---

## 5. PROSSIMI PASSI

### 5.1 Verifiche Necessarie

1. **Analisi COSMIC Footility:**
   - Eseguire analisi COSMIC completa su Footility
   - Calcolare CFP
   - Confrontare con altri progetti
   - Validare correlazione CFP/DEV UNIT

2. **Verifica Completa Colonne ODS:**
   - Analizzare tutte le colonne di tutti i file ODS
   - Verificare che tutte le colonne siano incluse nel calcolo DEV UNIT Fase 1
   - Aggiornare calcolo DEV UNIT se necessario

3. **Calcolo Ratio DEV UNIT/Ore:**
   - Calcolare ratio DEV UNIT/Ore da progetti completati
   - Validare stime Fase 1 con ratio reale

4. **Validazione Fase 1:**
   - Verificare che Fase 1 copra tutte le funzionalità attuali ODS
   - Assicurarsi che sistema funzioni UGUALE ad adesso

### 5.2 Documentazione da Creare

1. **Analisi Completa Colonne ODS:**
   - Documento dettagliato con tutte le colonne di tutti i file ODS
   - Mappatura colonne ODS → campi database
   - Verifica completezza

2. **Calcolo DEV UNIT Dettagliato Fase 1:**
   - Breakdown per ogni entità
   - Verifica che tutte le colonne ODS siano incluse
   - Calcolo totale aggiornato

3. **Preventivo Dettagliato per Fasi:**
   - Breakdown ore per ogni attività Fase 1
   - Breakdown ore per ogni attività Fase 2
   - Breakdown ore per ogni attività Fase 3

### 5.3 Integrazione con Footility

**Obiettivo:** Footility deve essere in grado di assegnare DEV UNIT alle attività

**Azioni:**
1. Verificare che Footility supporti DEV UNIT
2. Se necessario, estendere Footility per supportare DEV UNIT
3. Importare attività Fase 1 in Footility con DEV UNIT assegnate
4. Validare che Footility calcoli correttamente tempi basati su DEV UNIT

---

## 6. CONCLUSIONI

### 6.1 Ristrutturazione Preventivo

✅ **Separazione in 3 fasi:**
- Fase 1: Traduzione 1:1 ODS → Digitale (~300 ore, € 9.000)
- Fase 2: Evoluzioni Amministrative (~150-180 ore, € 4.500-5.400)
- Fase 3: Evoluzioni Didattiche (~200-250 ore, € 6.000-7.500)

✅ **Metodologia DEV UNIT:**
- Fase 1: ~961 DEV UNIT (traduzione 1:1)
- Fase 2: ~350 DEV UNIT (evoluzioni amministrative)
- Fase 3: ~360 DEV UNIT (evoluzioni didattiche)

✅ **Riduzione rispetto preventivo originale:**
- Ore: ~15-25% riduzione
- Costo: ~15-25% riduzione

### 6.2 Prossimi Passi

1. ⏳ Analisi COSMIC Footility
2. ⏳ Verifica completa colonne ODS
3. ⏳ Calcolo ratio DEV UNIT/Ore
4. ⏳ Documentazione dettagliata Fase 1
5. ⏳ Integrazione con Footility

---

**Documento creato:** Dicembre 2024  
**Prossima revisione:** Dopo analisi COSMIC Footility e verifica completa ODS
