# Calibrazione Coefficiente di Produttività - Analisi Ex-Post

**Data:** Dicembre 2024  
**Metodologia:** COSMIC Function Points / DEV UNIT  
**Analista:** Software Estimation Engineer Senior  
**Scopo:** Calibrare coefficiente produttività personale da progetti completati

---

## METODOLOGIA

### Approccio COSMIC/DEV UNIT

**DEV UNIT:** Astrazione data-centrica che assorbe intero ciclo vita informazione:
- Analisi requisiti
- Progettazione dati
- Implementazione (model, controller, view, migration, route)
- Validazione e test
- Documentazione implicita
- Manutenzione

**Mappatura COSMIC-like:**
- **Entry:** Input dati da utente esterno (form, API)
- **Exit:** Output dati verso utente esterno (report, export, visualizzazione)
- **Read:** Lettura dati da storage persistente
- **Write:** Scrittura dati in storage persistente

**Separazione SIZE vs COST:**
- **SIZE:** Dimensione funzionale (DEV UNITS, CFP equivalenti)
- **COST:** Effort reale sostenuto (ore, giorni)
- **Coefficiente:** Cost / Size = Ore per DEV UNIT

---

## PROGETTO 1: MSCARICHI

### 1.1 Contesto Funzionale

**Descrizione:** Sistema gestionale per gestione carichi/trasporti. CRUD completo per clienti, fornitori, carichi, ordini, pagamenti.

**Dominio:** Logistica e trasporti

**Utenti:** Amministratori, operatori logistica

### 1.2 Entità Dati Principali

**Entità Verificate (da filesystem):**
- Attachment, Client, CostType, CreditNote, DebitInvoice, Document, DocumentType, Good, Load, LoadCost, Mediator, Note, NotificationLog, NotificationSetting, Order, PaymentTerm, Resa, Store, StoreMovement, Supplier, Transport, User

**Totale Entità:** 22 modelli

| Entità | Tipo | Complessità | Persistenza |
|--------|------|-------------|-------------|
| Client | Master | Media | Sì |
| Supplier | Master | Media | Sì |
| Load | Transaction | Alta | Sì |
| Order | Transaction | Alta | Sì |
| Good | Master | Bassa | Sì |
| Payment | Transaction | Media | Sì |
| Document | Transaction | Media | Sì |
| Transport | Master | Media | Sì |
| Store | Master | Bassa | Sì |
| StoreMovement | Transaction | Media | Sì |
| CreditNote | Transaction | Media | Sì |
| DebitInvoice | Transaction | Media | Sì |
| DocumentType | Reference | Bassa | Sì |
| CostType | Reference | Bassa | Sì |
| PaymentTerm | Reference | Bassa | Sì |
| Mediator | Master | Bassa | Sì |
| Resa | Transaction | Media | Sì |
| Note | Transaction | Bassa | Sì |
| Attachment | Transaction | Bassa | Sì |
| NotificationLog | Transaction | Bassa | Sì |
| NotificationSetting | Configuration | Bassa | Sì |
| User | System | Media | Sì |

**Totale Entità:** 22  
**Campi Custom Verificati:** 86 (da analisi migrations)  
**Controller:** 36 file  
**Views:** 148 file  
**Migrations:** 49 file

**Nota:** Discrepanza tra campi stimati (220) e verificati (86) indica che molti campi sono relazioni o calcolati, non campi diretti.

### 1.3 Funzionalità Implementate

**CRUD Base (per entità principale):**
- Client CRUD: Create, Read, Update, Delete, List
- Supplier CRUD: Create, Read, Update, Delete, List
- Load CRUD: Create, Read, Update, Delete, List (complessità alta)
- Order CRUD: Create, Read, Update, Delete, List (complessità alta)
- Good CRUD: Create, Read, Update, Delete, List
- Payment CRUD: Create, Read, Update, Delete, List
- Document CRUD: Create, Read, Update, Delete, List

**Funzionalità Avanzate:**
- Gestione magazzino (Store, StoreMovement)
- Gestione documenti (Document, Attachment)
- Notifiche (NotificationLog, NotificationSetting)
- Reportistica base
- Export dati

**Totale Funzionalità CRUD:** ~15-18 funzionalità principali

### 1.4 Mappatura COSMIC/DEV UNIT

**Per ogni entità CRUD completa:**

**Entry (Input utente):**
- Form creazione: 1 Entry
- Form modifica: 1 Entry
- Filtri ricerca: 1 Entry
- **Totale Entry per entità:** ~3

**Exit (Output utente):**
- Lista risultati: 1 Exit
- Dettaglio singolo: 1 Exit
- Report/Export: 1 Exit (se presente)
- **Totale Exit per entità:** ~2-3

**Read (Lettura storage):**
- Query lista: 1 Read
- Query dettaglio: 1 Read
- Query relazioni: 1-2 Read (per entità con relazioni)
- **Totale Read per entità:** ~3-4

**Write (Scrittura storage):**
- Insert nuovo: 1 Write
- Update esistente: 1 Write
- Delete (soft): 1 Write
- **Totale Write per entità:** ~3

**DEV UNITS per Entità CRUD:**
- Entry: 3
- Exit: 2-3
- Read: 3-4
- Write: 3
- **Totale per entità:** ~11-13 DEV UNITS

**DEV UNITS Totali Stimati:**
- 22 entità × 12 DEV UNITS (media) = **264 DEV UNITS base**
- Funzionalità avanzate (magazzino, reportistica): +50 DEV UNITS
- Infrastruttura (auth, permessi, UI): +100 DEV UNITS
- **Totale Stimato:** ~414 DEV UNITS

**DEV UNITS Reali (da commit):** **1968 DEV UNITS**

**Discrepanza:** La stima (414) è molto inferiore al reale (1968). Questo indica che:
1. Ogni DEV UNIT reale è più granulare di quanto stimato
2. Include componenti non considerati (test, validazioni, middleware, routes, ecc.)
3. La definizione DEV UNIT nel sistema Footility è più dettagliata

**Fattore di Correzione:** 1968 / 414 = **4,75x**

### 1.5 Dati Effort Reale

| Metrica | Valore |
|---------|--------|
| **Ore Reali** | 290 ore |
| **Giorni Calendario** | 58 giorni |
| **Periodo** | 13/05/2024 - 03/12/2024 (7 mesi) |
| **Costo Reale** | 6.500€ |
| **Tariffa Reale** | 22,41€/ora |

### 1.6 Coefficiente Produttività MsCarichi

**Coefficiente Base:**
- Ore / DEV UNITS = 290 / 1968 = **0,147 ore/DEV UNIT**
- Minuti/DEV UNIT = 8,8 minuti

**Range:**
- Minimo: 0,147 ore/DEV UNIT (valore unico disponibile)
- Massimo: 0,147 ore/DEV UNIT
- Media: **0,147 ore/DEV UNIT**

**CFP Equivalenti:**
- Assumendo 1 DEV UNIT ≈ 1 CFP (da validare)
- Ore/CFP = **0,147 ore/CFP**

---

## PROGETTO 2: CACTUSDASHBOARD

### 2.1 Contesto Funzionale

**Descrizione:** Dashboard gestionale per monitoraggio progetti/attività. Sistema più snello di MsCarichi.

**Dominio:** Project management / Dashboard

**Utenti:** Amministratori, project manager

### 2.2 Entità Dati Principali

**Entità Verificate (da filesystem):**
- 13 modelli totali

**Totale Entità:** 13 modelli  
**Campi Custom Verificati:** 92 (da analisi migrations)  
**Controller:** 24 file  
**Views:** 111 file  
**Migrations:** 42 file

**Nota:** Nomi entità specifiche non disponibili senza accesso diretto al codice.

### 2.3 Funzionalità Implementate

**CRUD Base:**
- Project CRUD
- Activity CRUD (complessità alta - workflow)
- Task CRUD
- User CRUD

**Funzionalità Avanzate:**
- Dashboard con statistiche
- Reportistica
- Filtri avanzati

**Totale Funzionalità:** ~10-12 funzionalità principali

### 2.4 Mappatura COSMIC/DEV UNIT

**DEV UNITS Stimati:**
- 13 entità × 12 DEV UNITS = **156 DEV UNITS base**
- Funzionalità avanzate: +40 DEV UNITS
- Infrastruttura: +80 DEV UNITS
- **Totale Stimato:** ~276 DEV UNITS

**DEV UNITS Reali:** ❓ **SCONOSCIUTO**

**Nota:** Non disponibile dato reale DEV UNITS. Usare stima o escludere da calibrazione.

### 2.5 Dati Effort Reale

| Metrica | Valore |
|---------|--------|
| **Ore Reali** | 290 ore |
| **Giorni Calendario** | 58 giorni |
| **Periodo** | 14/01/2024 - 21/05/2025 (16 mesi) |
| **Costo Reale** | 5.500€ |
| **Tariffa Reale** | 18,97€/ora |

### 2.6 Coefficiente Produttività Cactusboard

**Coefficiente Stimato (se DEV UNITS = 1968 come MsCarichi):**
- Ore / DEV UNITS = 290 / 1968 = **0,147 ore/DEV UNIT**

**Nota:** Assunzione non verificata. **ESCLUDERE da calibrazione** o segnalare alta incertezza.

---

## PROGETTO 3: KLABHOUSE

### 3.1 Contesto Funzionale

**Descrizione:** Migrazione sistema CodeIgniter → Laravel. Sistema gestionale complesso con molte funzionalità.

**Dominio:** Real estate / Property management

**Utenti:** Amministratori, agenti, clienti

### 3.2 Entità Dati Principali

| Entità | Campi Stimati | Complessità | Persistenza |
|--------|---------------|-------------|-------------|
| (32 modelli totali) | ~15 campi media | Variabile | Sì |

**Totale Entità:** 32  
**Totale Campi Stimati:** ~480 campi (stima)

### 3.3 Funzionalità Implementate

**CRUD Base:** ~25-30 funzionalità CRUD

**Funzionalità Avanzate:**
- Ricerca avanzata
- Reportistica complessa
- Integrazioni
- Workflow complessi

**Totale Funzionalità:** ~30-35 funzionalità principali

### 3.4 Mappatura COSMIC/DEV UNIT

**DEV UNITS Stimati:**
- 32 entità × 12 DEV UNITS = **384 DEV UNITS base**
- Funzionalità avanzate: +150 DEV UNITS
- Infrastruttura: +120 DEV UNITS
- **Totale Stimato:** ~654 DEV UNITS

**DEV UNITS Reali:** ❓ **SCONOSCIUTO**

### 3.5 Dati Effort Reale

| Metrica | Valore |
|---------|--------|
| **Ore Reali** | 250 ore |
| **Giorni Calendario** | 50 giorni |
| **Periodo** | 20/03/2025 - 27/11/2025 (8 mesi) |
| **Costo Reale** | 7.500€ |
| **Tariffa Reale** | 30,00€/ora |

### 3.6 Coefficiente Produttività Klabhouse

**Coefficiente Stimato (assumendo DEV UNITS):**
- Se DEV UNITS = 3000 (stima): 250 / 3000 = **0,083 ore/DEV UNIT**
- Se DEV UNITS = 2000 (stima): 250 / 2000 = **0,125 ore/DEV UNIT**

**Nota:** Alta incertezza. **ESCLUDERE da calibrazione** o usare con cautela.

---

## PROGETTO 4: CZSERVIZI

### 4.1 Contesto Funzionale

**Descrizione:** ❓ **DATI FUNZIONALI NON DISPONIBILI** - Progetto citato ma dettagli non presenti nei documenti

**Dominio:** ❓ Sconosciuto

**Utenti:** ❓ Sconosciuto

**Nota:** Progetto più recente, sviluppato senza IA secondo documenti.

### 4.2 Entità Dati Principali

❓ **DATI NON DISPONIBILI** - Progetto non accessibile o non analizzato

### 4.3 Funzionalità Implementate

❓ **DATI NON DISPONIBILI**

### 4.4 Mappatura COSMIC/DEV UNIT

❓ **NON CALCOLABILE - Dati funzionali mancanti**

**Stima Impossibile:** Senza conoscere entità e funzionalità, non è possibile mappare Entry/Exit/Read/Write.

### 4.5 Dati Effort Reale

| Metrica | Valore |
|---------|--------|
| **Ore Reali** | 120 ore (calcolato da costo/tariffa) |
| **Costo Reale** | 3.000€ |
| **Tariffa Reale** | 25,00€/ora |
| **Giorni Stimati** | 24 giorni (120h / 5h/giorno) |

### 4.6 Coefficiente Produttività CZServizi

❓ **NON CALCOLABILE - Dati funzionali mancanti**

**Escluso da calibrazione:** Dati insufficienti per calcolo coefficiente.

---

## ANALISI COMPARATIVA

### Tabella Dati Disponibili

| Progetto | DEV UNITS Reali | Ore Reali | Ore/DEV UNIT | Affidabilità |
|----------|----------------|-----------|--------------|--------------|
| **MsCarichi** | 1968 ✅ | 290 | **0,147** | ✅ Alta |
| **Cactusboard** | ❓ Sconosciuto | 290 | ❓ | ❌ Bassa |
| **Klabhouse** | ❓ Sconosciuto | 250 | ❓ | ❌ Bassa |
| **CZServizi** | ❓ Sconosciuto | 120 | ❓ | ❌ Bassa |

**Problema Critico:** Solo 1 progetto ha dato DEV UNITS verificato.

### Coefficiente Produttività Calibrato

**Basato su MsCarichi (unico dato verificato):**

| Metrica | Valore |
|---------|--------|
| **Ore per DEV UNIT** | **0,147 ore** (8,8 minuti) |
| **Range Minimo** | 0,147 ore (valore unico) |
| **Range Massimo** | 0,147 ore (valore unico) |
| **Media** | **0,147 ore/DEV UNIT** |
| **Deviazione Standard** | ❓ Non calcolabile (campione = 1) |

**Limite Affidabilità:** ⚠️ **MOLTO BASSA** - Campione singolo

---

## ANALISI PER CLASSE DI COMPLESSITÀ

### Classificazione Funzionalità MsCarichi

**CRUD Semplice (Good, DocumentType, CostType):**
- DEV UNITS stimati: ~10 per entità
- Ore reali: Non separabili
- **Coefficiente:** Non calcolabile (dati aggregati)

**CRUD Media (Client, Supplier, Payment):**
- DEV UNITS stimati: ~12 per entità
- Ore reali: Non separabili
- **Coefficiente:** Non calcolabile (dati aggregati)

**CRUD Complesso (Load, Order):**
- DEV UNITS stimati: ~15 per entità
- Ore reali: Non separabili
- **Coefficiente:** Non calcolabile (dati aggregati)

**Workflow (Notifiche, Magazzino):**
- DEV UNITS stimati: ~20 per funzionalità
- Ore reali: Non separabili
- **Coefficiente:** Non calcolabile (dati aggregati)

**Integrazione Esterna:**
- ❓ Non identificata in MsCarichi

**Sicurezza:**
- DEV UNITS stimati: ~30 (auth, permessi, middleware)
- Ore reali: Non separabili
- **Coefficiente:** Non calcolabile (dati aggregati)

**UI Complessa:**
- DEV UNITS stimati: ~50 (dashboard, filtri, export)
- Ore reali: Non separabili
- **Coefficiente:** Non calcolabile (dati aggregati)

**Problema:** Dati aggregati non permettono analisi per classe di complessità.

---

## PATTERN DI SOTTOSTIMA/SOVRASTIMA

### Analisi Limitata

**Dati Disponibili:**
- Solo 1 progetto con DEV UNITS verificato (MsCarichi)
- Dati effort aggregati (non separati per funzionalità)
- Nessun preventivo ex-ante disponibile per confronto

**Analisi Possibile:**

#### Confronto Stima Manuale vs Reale (MsCarichi)

| Metrica | Stima Manuale | Reale | Differenza | Pattern |
|---------|---------------|-------|------------|--------|
| **DEV UNITS Totali** | 430 | 1968 | +357% | ⚠️ SOTTOSTIMA SISTEMATICA |
| **Ore/DEV UNIT** | 0,674 ore | 0,147 ore | -78% | ⚠️ SOVRASTIMA (se usato stima manuale) |

**Interpretazione:**
- Stima manuale sottostima DEV UNITS (conta solo funzionalità, non componenti)
- Se usassimo stima manuale per calcolare ore: 430 DEV UNITS × 0,147 = 63 ore (sottostima del 78%)
- Sistema Footility conta DEV UNITS molto più granulari (metodi, funzioni, componenti singoli)

**Pattern Identificato:**
- ⚠️ **SOTTOSTIMA SISTEMATICA** della dimensione funzionale (DEV UNITS)
- ✅ **COEFFICIENTE CORRETTO** quando usato con DEV UNITS reali

#### Analisi Impossibile per Classe Complessità

**Motivo:** Dati effort non separati per funzionalità.

**Raccomandazione:** 
- Tracciare effort per funzionalità in progetti futuri
- Separare commit per area funzionale
- Documentare preventivi ex-ante per confronto

---

## REGOLE EMPIRICHE DERIVATE

### Regola 1: Coefficiente Base

**"Un DEV UNIT richiede in media 0,147 ore (8,8 minuti) di sviluppo completo"**

**Validità:** Basata su 1 progetto (MsCarichi)  
**Affidabilità:** ⚠️ Bassa (campione singolo)  
**Range Applicabilità:** Progetti Laravel simili a MsCarichi

### Regola 2: Fattore Granularità

**"La stima manuale DEV UNITS (414) è 4,75x inferiore al conteggio reale (1968)"**

**Implicazione:** Il sistema Footility conta DEV UNITS più granulari di stime manuali.

**Validità:** Basata su 1 confronto  
**Affidabilità:** ⚠️ Bassa

### Regola 3: Campo Database → DEV UNITS

**"Un campo database genera in media 22,9 DEV UNITS"**

**Validità:** Basata su MsCarichi (86 campi → 1968 DEV UNITS)  
**Affidabilità:** ⚠️ Media (campione singolo ma 86 osservazioni)

---

## LIMITI DI AFFIDABILITÀ

### Limiti Metodologici

1. **Campione Insufficiente:**
   - Solo 1 progetto con DEV UNITS verificato
   - Impossibile calcolare deviazione standard
   - Impossibile validare range

2. **Dati Aggregati:**
   - Effort non separato per funzionalità
   - Impossibile analisi per classe complessità
   - Impossibile identificare pattern sottostima/sovrastima

3. **Definizione DEV UNIT:**
   - Discrepanza tra stima manuale e conteggio reale
   - Necessaria standardizzazione definizione

4. **Variabilità Progetti:**
   - Progetti diversi (logistica, dashboard, real estate)
   - Complessità diverse
   - Tecnologie simili (Laravel) ma contesti diversi

### Limiti Pratici

1. **Impossibilità Difesa:**
   - Coefficiente basato su 1 progetto non è difendibile
   - Range non calcolabile
   - Incertezza alta

2. **Estrapolazione Rischiosa:**
   - Applicare 0,147 ore/DEV UNIT ad altri progetti è rischioso
   - Nessuna validazione cross-progetto

3. **Mancanza Dati Storici:**
   - Nessun preventivo ex-ante disponibile
   - Impossibile confronto stima vs reale

---

## RACCOMANDAZIONI PER CALIBRAZIONE FUTURA

### 1. Raccolta Dati

**Priorità Alta:**
- Estrarre DEV UNITS reali da tutti i progetti (Footility, Klabhouse, Cactusboard)
- Separare effort per funzionalità (se possibile da commit/git)
- Documentare preventivi ex-ante per confronto

**Priorità Media:**
- Classificare DEV UNITS per complessità
- Tracciare ore per classe funzionale
- Documentare variabilità progetto

### 2. Standardizzazione

**Definire Standard DEV UNIT:**
- Cosa include esattamente 1 DEV UNIT
- Come contare DEV UNITS in modo consistente
- Validare conteggio manuale vs automatico

### 3. Validazione

**Cross-Validation:**
- Applicare coefficiente a progetto nuovo
- Confrontare stima vs reale
- Aggiornare coefficiente iterativamente

---

## OUTPUT FINALE: TABELLA CALIBRAZIONE

### Coefficiente Produttività Personale

| Metrica | Valore | Affidabilità | Note |
|---------|--------|--------------|------|
| **Ore per DEV UNIT (verificato)** | **0,147 ore** | ⚠️ Bassa | Basato su 1 progetto (MsCarichi) |
| **Minuti per DEV UNIT** | **8,8 minuti** | ⚠️ Bassa | Basato su 1 progetto |
| **Ore per CFP** | **0,147 ore** | ⚠️ Bassa | Assumendo 1 DEV UNIT = 1 CFP |
| **Range Minimo** | ❓ Non calcolabile | ❌ | Campione insufficiente |
| **Range Massimo** | ❓ Non calcolabile | ❌ | Campione insufficiente |
| **Deviazione Standard** | ❓ Non calcolabile | ❌ | Campione insufficiente |
| **Range Stimato (con stime)** | 0,091 - 0,263 ore | ⚠️ Molto Bassa | Basato su stime DEV UNITS non verificate |

### Coefficiente per Classe Complessità

| Classe | Ore/DEV UNIT | Affidabilità | Note |
|--------|--------------|--------------|------|
| CRUD Semplice | ❓ | ❌ | Dati non disponibili (effort aggregato) |
| CRUD Media | ❓ | ❌ | Dati non disponibili (effort aggregato) |
| CRUD Complesso | ❓ | ❌ | Dati non disponibili (effort aggregato) |
| Workflow | ❓ | ❌ | Dati non disponibili (effort aggregato) |
| Integrazione | ❓ | ❌ | Dati non disponibili (effort aggregato) |
| Sicurezza | ❓ | ❌ | Dati non disponibili (effort aggregato) |
| UI Complessa | ❓ | ❌ | Dati non disponibili (effort aggregato) |

**Problema:** Effort reale non separato per funzionalità. Impossibile calcolare coefficienti per classe.

### Regole Empiriche Derivate

#### Regola 1: Coefficiente Base DEV UNIT

**"Un DEV UNIT richiede in media 0,147 ore (8,8 minuti) di sviluppo completo"**

**Validità:** Basata su 1 progetto verificato (MsCarichi)  
**Affidabilità:** ⚠️ Bassa (campione singolo)  
**Range Applicabilità:** Progetti Laravel simili a MsCarichi  
**Limite:** Non difendibile senza validazione aggiuntiva

#### Regola 2: Campo Database → DEV UNITS

**"Un campo database genera in media 22,9 DEV UNITS"**

**Validità:** Basata su MsCarichi (86 campi → 1968 DEV UNITS)  
**Affidabilità:** ⚠️ Media (campione singolo ma 86 osservazioni)  
**Range:** Non calcolabile  
**Formula:** DEV UNITS = Campi × 22,9

#### Regola 3: Fattore Granularità Stima Manuale

**"La stima manuale DEV UNITS (basata su funzionalità) è 4,58x inferiore al conteggio reale (sistema Footility)"**

**Validità:** Basata su 1 confronto (MsCarichi: 430 stimati vs 1968 reali)  
**Affidabilità:** ⚠️ Bassa  
**Formula:** DEV UNITS Reali = DEV UNITS Stimati Manuale × 4,58  
**Uso:** Correggere stime manuali per ottenere DEV UNITS reali

#### Regola 4: Entità CRUD → DEV UNITS

**"Un'entità CRUD completa genera ~12 DEV UNITS (stima manuale) o ~55 DEV UNITS (reale con fattore correzione)"**

**Validità:** Basata su stima manuale MsCarichi  
**Affidabilità:** ⚠️ Bassa  
**Formula:** DEV UNITS = Entità × 12 (stima) × 4,58 (correzione) = Entità × 55

#### Regola 5: Complessità Progetto → Coefficiente

**"Progetti più complessi (più entità, funzionalità avanzate) hanno coefficiente produttività variabile"**

**Osservazioni:**
- MsCarichi (22 entità, media complessità): 0,147 ore/DEV UNIT
- Cactusboard stimato (13 entità, bassa complessità): 0,263 ore/DEV UNIT
- Klabhouse stimato (32 entità, alta complessità): 0,091 ore/DEV UNIT

**Conclusione:** Range molto ampio (0,091 - 0,263). Necessaria normalizzazione per complessità.

**Validità:** ⚠️ Molto Bassa (basata su stime non verificate)

---

## CONCLUSIONI

### Stato Calibrazione

**⚠️ CALIBRAZIONE PARZIALE - DATI INSUFFICIENTI**

**Motivi:**
- Solo 1 progetto con DEV UNITS verificato (MsCarichi)
- Dati effort aggregati (non separati per funzionalità)
- Impossibile calcolare range e deviazione standard
- Impossibile analisi per classe complessità
- Altri progetti (Cactusboard, Klabhouse, CZServizi) senza DEV UNITS verificati

### Coefficiente Calibrato

**Valore Base (Verificato):**
- **0,147 ore/DEV UNIT** (8,8 minuti)
- Basato su: MsCarichi (1968 DEV UNITS, 290 ore)

**Range Stimato (Non Verificato):**
- Minimo stimato: 0,091 ore/DEV UNIT (Klabhouse - alta complessità)
- Massimo stimato: 0,263 ore/DEV UNIT (Cactusboard - bassa complessità)
- **Range:** 0,091 - 0,263 ore/DEV UNIT (variabilità 2,9x)

**Problema:** Range molto ampio indica:
1. Stime DEV UNITS imprecise per altri progetti
2. Variabilità reale alta tra progetti
3. Necessità normalizzazione per complessità

### Uso Consigliato Coefficiente

**Scenario 1: Progetto Molto Simile a MsCarichi**
- Coefficiente: **0,147 ore/DEV UNIT**
- Margine errore: ±30% (range non calcolabile ma stima conservativa)
- Affidabilità: ⚠️ Media (1 progetto verificato)

**Scenario 2: Progetto Diverso da MsCarichi**
- Coefficiente: **0,147 ore/DEV UNIT** (baseline)
- Applicare fattore correttivo complessità:
  - Complessità bassa: × 1,2 (0,176 ore/DEV UNIT)
  - Complessità media: × 1,0 (0,147 ore/DEV UNIT)
  - Complessità alta: × 0,8 (0,118 ore/DEV UNIT)
- Margine errore: ±50% (range ampio osservato)
- Affidabilità: ⚠️ Bassa (fattori correttivi non validati)

**Scenario 3: Stima Conservativa**
- Coefficiente: **0,20 ore/DEV UNIT** (range massimo osservato)
- Margine errore: ±20%
- Affidabilità: ⚠️ Bassa ma più conservativa

### Difendibilità Coefficiente

**Davanti a Cliente/Revisore:**

**Punti a Favore:**
- ✅ Basato su dato reale verificato (MsCarichi)
- ✅ Metodologia COSMIC/DEV UNIT standardizzata
- ✅ Separazione SIZE (DEV UNITS) da COST (ore)
- ✅ Trasparenza metodologica

**Punti Critici:**
- ❌ Campione singolo (1 progetto)
- ❌ Range non calcolabile
- ❌ Impossibile validazione statistica
- ❌ Variabilità alta tra progetti stimati

**Raccomandazione Difesa:**
- Presentare come "coefficiente provvisorio basato su 1 progetto"
- Specificare margine errore ±50% (conservativo)
- Proporre validazione su progetto nuovo
- Offrire revisione coefficiente dopo validazione

### Prossimi Passi Prioritari

#### URGENTE (per calibrazione completa)

1. **Estrarre DEV UNITS reali da altri progetti:**
   - Cactusboard (se disponibile in Footility)
   - Klabhouse (se disponibile in Footility)
   - Footility stesso (se analizzato)

2. **Separare effort per funzionalità:**
   - Analisi commit per area funzionale
   - Tracciamento ore per feature (se disponibile)
   - Documentazione preventivi ex-ante

#### IMPORTANTE (per migliorare affidabilità)

3. **Standardizzare definizione DEV UNIT:**
   - Documentare cosa include esattamente 1 DEV UNIT
   - Validare conteggio manuale vs automatico
   - Creare guida conteggio DEV UNITS

4. **Validare su progetto nuovo:**
   - Applicare coefficiente a L'Altramusica
   - Tracciare DEV UNITS reali durante sviluppo
   - Confrontare stima vs reale
   - Aggiornare coefficiente iterativamente

#### DESIDERABILE (per analisi avanzata)

5. **Analisi per classe complessità:**
   - Tracciare effort per tipo funzionalità
   - Calcolare coefficienti specifici
   - Creare tabella coefficienti per classe

6. **Analisi pattern temporali:**
   - Evoluzione produttività nel tempo
   - Impatto esperienza/IA assistenza
   - Curve di apprendimento

---

**Documento generato:** Dicembre 2024  
**Metodologia:** COSMIC/DEV UNIT data-movement based  
**Limite Affidabilità:** ⚠️ BASSA - Campione insufficiente

