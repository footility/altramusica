# Organizzazione Fasi - DEV UNIT per Footility

**Data:** Dicembre 2024  
**Obiettivo:** Organizzare attività in fasi per inserimento in Footility  
**Metodologia:** DEV UNIT calcolate manualmente da analisi ODS

---

## PRINCIPIO ORGANIZZAZIONE

### FASE 1: Traduzione 1:1 ODS → DB Normalizzato/Ingegnerizzato
**Obiettivo:** Trasportare il DB attuale (ODS) in un DB normalizzato e ingegnerizzato, con CRUD base per ogni funzionalità esistente, per permettere l'uso digitale dei dati attuali.

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

### FASE 2-3: Evoluzioni Avanzate
**Obiettivo:** Evolvere il sistema a vero gestionale con le richieste avanzate fatte dal cliente nelle conversazioni.

**Include:**
- Workflow avanzati
- Automazioni
- Integrazioni esterne
- Reportistica avanzata
- Funzionalità evolutive richieste dal cliente

---

## FASE 1: TRADUZIONE 1:1 ODS → DB NORMALIZZATO

### GRUPPO 1: ANAGRAFICHE BASE

#### Attività 1.1: Studenti
**Descrizione:** CRUD completo per gestione studenti con tutti i campi presenti negli ODS

**DEV UNIT:** 146
- DB_campi: 64
- DB_relazioni: 10
- CRUD: 7
- Workflow: 2
- UI_form: 35
- UI_lista: 28
- UI_stampa: 0

**Colonne ODS coperte:**
- A-D: Contratti/Pagamenti (collegati)
- E: Stato
- F: Data arrivo
- G-H: Cognome/Nome
- I: Info
- J: Come conosciuto
- K-L: Interessi corsi
- M-N: Note
- O: Data ultimo
- P-X: Disponibilità
- Y-AH: Strumenti (collegati)
- AI-AL: Livelli
- AM-AS: Orchestra/Coro (collegati)
- AT: Dati
- AU-AW: Età/Nato/Minore
- AX+: Genitori (collegati)

---

#### Attività 1.2: Genitori/Tutori
**Descrizione:** CRUD completo per gestione genitori/tutori con tutti i campi presenti negli ODS

**DEV UNIT:** 70
- DB_campi: 22
- DB_relazioni: 2
- CRUD: 7
- Workflow: 1
- UI_form: 22
- UI_lista: 16
- UI_stampa: 0

**Colonne ODS coperte:**
- AX+: Tutte le colonne genitori (cognome, nome, cell, mail, etc.)

---

#### Attività 1.3: Docenti
**Descrizione:** CRUD completo per gestione docenti con tutti i campi presenti negli ODS (dati lavoratori)

**DEV UNIT:** 86
- DB_campi: 28
- DB_relazioni: 3
- CRUD: 7
- Workflow: 1
- UI_form: 25
- UI_lista: 22
- UI_stampa: 0

**Colonne ODS coperte:**
- Tutte le colonne da `dati lavoratori 25-26.ods`

---

#### Attività 1.4: Anno Scolastico
**Descrizione:** CRUD per gestione anni scolastici/esercizi

**DEV UNIT:** 39
- DB_campi: 11
- DB_relazioni: 2
- CRUD: 7
- Workflow: 1
- UI_form: 8
- UI_lista: 10
- UI_stampa: 0

---

### GRUPPO 2: DISPONIBILITÀ E ORARI

#### Attività 1.5: Disponibilità Studenti
**Descrizione:** CRUD per gestione disponibilità oraria studenti

**DEV UNIT:** 46
- DB_campi: 13
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: 10
- UI_lista: 14
- UI_stampa: 0

**Colonne ODS coperte:**
- P: Disponibilità
- Q: Lu (Lunedì)
- R: Ma (Martedì)
- S: Me (Mercoledì)
- T: Gio (Giovedì)
- U: Ve (Venerdì)
- V: Sab (Sabato)
- W: Lab (Laboratorio)
- X: Note disponibilità

---

#### Attività 1.6: Disponibilità Docenti
**Descrizione:** CRUD per gestione disponibilità oraria docenti

**DEV UNIT:** 46
- DB_campi: 13
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: 10
- UI_lista: 14
- UI_stampa: 0

---

#### Attività 1.7: Calendario Lezioni
**Descrizione:** CRUD per gestione calendario lezioni

**DEV UNIT:** 50
- DB_campi: 13
- DB_relazioni: 3
- CRUD: 7
- Workflow: 2
- UI_form: 10
- UI_lista: 15
- UI_stampa: 0

**Colonne ODS coperte:**
- Tutte le colonne da `Calendario 2025-26.ods`

---

#### Attività 1.8: Sospensioni Calendario
**Descrizione:** CRUD per gestione sospensioni/festività calendario

**DEV UNIT:** 36
- DB_campi: 9
- DB_relazioni: 0
- CRUD: 7
- Workflow: 1
- UI_form: 6
- UI_lista: 13
- UI_stampa: 0

---

### GRUPPO 3: CORSI E ISCRIZIONI

#### Attività 1.9: Tipi Corso
**Descrizione:** CRUD per gestione tipi di corso

**DEV UNIT:** ~40 (da calcolare)
- DB_campi: ~12
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: ~12
- UI_lista: ~7
- UI_stampa: 0

---

#### Attività 1.10: Corsi
**Descrizione:** CRUD per gestione corsi

**DEV UNIT:** 66
- DB_campi: 18
- DB_relazioni: 3
- CRUD: 7
- Workflow: 1
- UI_form: 15
- UI_lista: 22
- UI_stampa: 0

---

#### Attività 1.11: Iscrizioni
**Descrizione:** CRUD per gestione iscrizioni studenti a corsi

**DEV UNIT:** 55
- DB_campi: 15
- DB_relazioni: 2
- CRUD: 7
- Workflow: 1
- UI_form: 12
- UI_lista: 18
- UI_stampa: 0

**Colonne ODS coperte:**
- I: Info
- K: A che corso sei interessato?
- L: Ti piacerebbe provare anche altri strumenti?

---

### GRUPPO 4: CONTRATTI E FATTURAZIONE

#### Attività 1.12: Contratti
**Descrizione:** CRUD per gestione contratti (solo visualizzazione/modifica dati esistenti)

**DEV UNIT:** 86
- DB_campi: 28
- DB_relazioni: 3
- CRUD: 7
- Workflow: 2
- UI_form: 25
- UI_lista: 21
- UI_stampa: 0

**Colonne ODS coperte:**
- D: Contratto
- Tutte le colonne da `Db Contratti 25-26.ods`

---

#### Attività 1.13: Fatture
**Descrizione:** CRUD per gestione fatture (solo visualizzazione/modifica dati esistenti)

**DEV UNIT:** 64
- DB_campi: 18
- DB_relazioni: 3
- CRUD: 7
- Workflow: 1
- UI_form: 15
- UI_lista: 20
- UI_stampa: 0

**Colonne ODS coperte:**
- Tutte le colonne da `Db Contabile 2025-26.ods` (fatture corsi)

---

#### Attività 1.14: Fatture Accessori
**Descrizione:** CRUD per gestione fatture accessori

**DEV UNIT:** 53
- DB_campi: 15
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: 12
- UI_lista: 17
- UI_stampa: 0

**Colonne ODS coperte:**
- Tutte le colonne da `Db Contabile 2025-26.ods` (fatture accessori)

---

#### Attività 1.15: Pagamenti
**Descrizione:** CRUD per gestione pagamenti

**DEV UNIT:** 48
- DB_campi: 13
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: 10
- UI_lista: 16
- UI_stampa: 0

**Colonne ODS coperte:**
- B: Pagato
- Tutte le colonne da `Db Contabile 2025-26.ods` (pagamenti)

---

### GRUPPO 5: DIDATTICA E VALUTAZIONE

#### Attività 1.16: Livelli Studenti
**Descrizione:** CRUD per gestione livelli studenti (ABRSM)

**DEV UNIT:** 41
- DB_campi: 11
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: 8
- UI_lista: 13
- UI_stampa: 0

**Colonne ODS coperte:**
- AI: Livello
- AJ: Livello str.
- AK: Livello teoria
- AL: Musica di insieme

---

#### Attività 1.17: Esami
**Descrizione:** CRUD per gestione esami

**DEV UNIT:** 54
- DB_campi: 15
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: 12
- UI_lista: 18
- UI_stampa: 0

**Colonne ODS coperte:**
- Tutte le colonne da `Db Accessori 2025-26.ods` (esami)

---

### GRUPPO 6: ATTIVITÀ EXTRA

#### Attività 1.18: Orchestra/Coro
**Descrizione:** CRUD per gestione iscrizioni orchestra/coro

**DEV UNIT:** 47
- DB_campi: 13
- DB_relazioni: 2
- CRUD: 7
- Workflow: 1
- UI_form: 10
- UI_lista: 14
- UI_stampa: 0

**Colonne ODS coperte:**
- AM: Conf (Conferma orchestra)
- AN: Orch 1
- AO: PYO
- AP: Conf coro
- AQ: Coro
- AR: Data ultima convocazione
- AS: Note orchestra

---

### GRUPPO 7: MAGAZZINO

#### Attività 1.19: Strumenti
**Descrizione:** CRUD per gestione strumenti (catalogo)

**DEV UNIT:** ~50 (da calcolare)
- DB_campi: ~15
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: ~15
- UI_lista: ~11
- UI_stampa: 0

---

#### Attività 1.20: Noleggi Strumenti
**Descrizione:** CRUD per gestione noleggi strumenti agli studenti

**DEV UNIT:** 59
- DB_campi: 18
- DB_relazioni: 2
- CRUD: 7
- Workflow: 1
- UI_form: 15
- UI_lista: 16
- UI_stampa: 0

**Colonne ODS coperte:**
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

---

#### Attività 1.21: Accessori
**Descrizione:** CRUD per gestione accessori

**DEV UNIT:** 53
- DB_campi: 15
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: 12
- UI_lista: 17
- UI_stampa: 0

**Colonne ODS coperte:**
- Tutte le colonne da `Db Accessori 2025-26.ods` (accessori 1-7)

---

#### Attività 1.22: Libri
**Descrizione:** CRUD per gestione libri e distribuzione

**DEV UNIT:** 54
- DB_campi: 15
- DB_relazioni: 2
- CRUD: 7
- Workflow: 1
- UI_form: 12
- UI_lista: 17
- UI_stampa: 0

**Colonne ODS coperte:**
- Tutte le colonne da `Db Accessori 2025-26.ods` (libri 1-15)

---

#### Attività 1.23: Contratti Docenti
**Descrizione:** CRUD per gestione contratti docenti

**DEV UNIT:** 59
- DB_campi: 18
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: 15
- UI_lista: 17
- UI_stampa: 0

**Colonne ODS coperte:**
- Tutte le colonne da `dati lavoratori 25-26.ods` (contratti docenti)

---

#### Attività 1.24: Conto Orario Docenti
**Descrizione:** CRUD per gestione conto orario docenti (solo visualizzazione dati)

**DEV UNIT:** ~50 (da calcolare)
- DB_campi: ~15
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: ~12
- UI_lista: ~14
- UI_stampa: 0

**Colonne ODS coperte:**
- C: Conto orario
- Dati da `dati lavoratori 25-26.ods`

---

### RIEPILOGO FASE 1

| Gruppo | Attività | DEV UNIT |
|--------|----------|----------|
| **1. Anagrafiche Base** | 1.1-1.4 | 341 |
| **2. Disponibilità e Orari** | 1.5-1.8 | 178 |
| **3. Corsi e Iscrizioni** | 1.9-1.11 | ~161 |
| **4. Contratti e Fatturazione** | 1.12-1.15 | 251 |
| **5. Didattica e Valutazione** | 1.16-1.17 | 95 |
| **6. Attività Extra** | 1.18 | 47 |
| **7. Magazzino** | 1.19-1.24 | ~325 |
| **TOTALE FASE 1** | **24 attività** | **~1.398 DEV UNIT** |

---

## FASE 2: EVOLUZIONI AMMINISTRATIVE

### GRUPPO 1: INFRASTRUTTURA AVANZATA

#### Attività 2.1: Gestione Utenze Avanzata
**Descrizione:** Sistema completo gestione utenti con ruoli e permessi granulari

**DEV UNIT:** ~60
- DB_campi: ~15
- DB_relazioni: 3
- CRUD: 7
- Workflow: 2
- UI_form: ~15
- UI_lista: ~16
- UI_stampa: 0

---

#### Attività 2.2: Controllo Accessi Granulare
**Descrizione:** Sistema permessi granulari per ogni funzionalità

**DEV UNIT:** ~40
- DB_campi: ~10
- DB_relazioni: 2
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: 0
- UI_stampa: 0

---

### GRUPPO 2: CONTRATTI EVOLUTIVI

#### Attività 2.3: Workflow Contratti Avanzato
**Descrizione:** Workflow invio/firma contratti con token e tracking

**DEV UNIT:** ~50
- DB_campi: ~8
- DB_relazioni: 1
- CRUD: 0
- Workflow: 4
- UI_form: 0
- UI_lista: ~15
- UI_stampa: 0

---

#### Attività 2.4: Modelli Contratto
**Descrizione:** Gestione modelli contratto personalizzabili

**DEV UNIT:** ~40
- DB_campi: ~10
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: ~10
- UI_lista: ~11
- UI_stampa: 0

---

#### Attività 2.5: Generazione PDF Contratti
**Descrizione:** Generazione PDF contratti da modelli

**DEV UNIT:** ~50
- DB_campi: 0
- DB_relazioni: 0
- CRUD: 0
- Workflow: 1
- UI_form: 0
- UI_lista: 0
- UI_stampa: ~49

---

#### Attività 2.6: Invio Email Contratti
**Descrizione:** Invio automatico email contratti con link firma

**DEV UNIT:** ~30
- DB_campi: ~5
- DB_relazioni: 1
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: 0
- UI_stampa: 0

---

### GRUPPO 3: FATTURAZIONE EVOLUTIVA

#### Attività 2.7: Fatturazione da Contratti
**Descrizione:** Generazione automatica fatture da contratti firmati

**DEV UNIT:** ~50
- DB_campi: ~8
- DB_relazioni: 2
- CRUD: 0
- Workflow: 3
- UI_form: 0
- UI_lista: ~15
- UI_stampa: 0

---

#### Attività 2.8: Piani Pagamento
**Descrizione:** Gestione rateizzazione flessibile fatture

**DEV UNIT:** ~40
- DB_campi: ~10
- DB_relazioni: 2
- CRUD: 0
- Workflow: 2
- UI_form: ~10
- UI_lista: 0
- UI_stampa: 0

---

#### Attività 2.9: Generazione PDF Fatture
**Descrizione:** Generazione PDF fatture

**DEV UNIT:** ~30
- DB_campi: 0
- DB_relazioni: 0
- CRUD: 0
- Workflow: 1
- UI_form: 0
- UI_lista: 0
- UI_stampa: ~29

---

#### Attività 2.10: Automazioni Solleciti
**Descrizione:** Sistema automatico solleciti pagamenti scaduti

**DEV UNIT:** ~40
- DB_campi: ~8
- DB_relazioni: 2
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: ~10
- UI_stampa: 0

---

#### Attività 2.11: Recupero Crediti
**Descrizione:** Gestione avanzata recupero crediti

**DEV UNIT:** ~30
- DB_campi: ~8
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: ~8
- UI_lista: ~5
- UI_stampa: 0

---

### GRUPPO 4: INTEGRAZIONI ESTERNE

#### Attività 2.12: Integrazione SDI Fatturazione Elettronica
**Descrizione:** Integrazione Sistema di Interscambio per fatturazione elettronica

**DEV UNIT:** ~80
- DB_campi: ~10
- DB_relazioni: 1
- CRUD: 0
- Workflow: 3
- UI_form: 0
- UI_lista: ~15
- UI_stampa: 0

---

#### Attività 2.13: Integrazione Cassetto Fiscale
**Descrizione:** Integrazione cassetto fiscale per categorizzazione spese

**DEV UNIT:** ~100
- DB_campi: ~15
- DB_relazioni: 2
- CRUD: 0
- Workflow: 3
- UI_form: 0
- UI_lista: ~20
- UI_stampa: 0

---

#### Attività 2.14: Fornitori
**Descrizione:** CRUD completo gestione fornitori

**DEV UNIT:** ~50
- DB_campi: ~15
- DB_relazioni: 2
- CRUD: 7
- Workflow: 0
- UI_form: ~15
- UI_lista: ~11
- UI_stampa: 0

---

### RIEPILOGO FASE 2

| Gruppo | Attività | DEV UNIT |
|--------|----------|----------|
| **1. Infrastruttura Avanzata** | 2.1-2.2 | ~100 |
| **2. Contratti Evolutivi** | 2.3-2.6 | ~170 |
| **3. Fatturazione Evolutiva** | 2.7-2.11 | ~190 |
| **4. Integrazioni Esterne** | 2.12-2.14 | ~230 |
| **TOTALE FASE 2** | **14 attività** | **~690 DEV UNIT** |

---

## FASE 3: EVOLUZIONI DIDATTICHE

### GRUPPO 1: PRIMO CONTATTO E PREISCRIZIONI

#### Attività 3.1: Primo Contatto Pubblico
**Descrizione:** Form pubblico per primo contatto con generazione link precompilati

**DEV UNIT:** ~60
- DB_campi: ~15
- DB_relazioni: 2
- CRUD: 7
- Workflow: 1
- UI_form: ~15
- UI_lista: ~12
- UI_stampa: 0

---

#### Attività 3.2: Conversione Prospect → Studente
**Descrizione:** Workflow conversione primo contatto in studente

**DEV UNIT:** ~30
- DB_campi: 0
- DB_relazioni: 1
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: 0
- UI_stampa: 0

---

#### Attività 3.3: Preiscrizioni
**Descrizione:** Sistema preiscrizioni per anno successivo

**DEV UNIT:** ~50
- DB_campi: ~12
- DB_relazioni: 2
- CRUD: 7
- Workflow: 1
- UI_form: ~12
- UI_lista: ~17
- UI_stampa: 0

---

### GRUPPO 2: PROPOSTA ORARIA

#### Attività 3.4: Proposta Oraria Avanzata
**Descrizione:** Sistema composizione orari con algoritmo matching disponibilità

**DEV UNIT:** ~80
- DB_campi: ~15
- DB_relazioni: 3
- CRUD: 7
- Workflow: 3
- UI_form: ~15
- UI_lista: ~20
- UI_stampa: 0

---

#### Attività 3.5: Accettazione/Rifiuto Proposte
**Descrizione:** Workflow accettazione/rifiuto proposte orarie

**DEV UNIT:** ~30
- DB_campi: ~5
- DB_relazioni: 1
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: ~10
- UI_stampa: 0

---

### GRUPPO 3: REGISTRO EVOLUTO

#### Attività 3.6: Registro Elettronico Avanzato
**Descrizione:** Registro elettronico completo con accesso docenti

**DEV UNIT:** ~70
- DB_campi: ~15
- DB_relazioni: 5
- CRUD: 7
- Workflow: 2
- UI_form: ~15
- UI_lista: ~24
- UI_stampa: 0

---

#### Attività 3.7: Presenze Avanzate
**Descrizione:** Gestione presenze con recuperi e supplenze

**DEV UNIT:** ~60
- DB_campi: ~12
- DB_relazioni: 3
- CRUD: 7
- Workflow: 2
- UI_form: ~12
- UI_lista: ~18
- UI_stampa: 0

---

#### Attività 3.8: Recuperi Lezioni
**Descrizione:** Gestione avanzata recuperi lezioni perse

**DEV UNIT:** ~50
- DB_campi: ~12
- DB_relazioni: 3
- CRUD: 0
- Workflow: 2
- UI_form: ~12
- UI_lista: ~19
- UI_stampa: 0

---

#### Attività 3.9: Gestione Supplenti
**Descrizione:** Sistema gestione supplenti con account provvisori

**DEV UNIT:** ~50
- DB_campi: ~12
- DB_relazioni: 3
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: ~15
- UI_stampa: 0

---

#### Attività 3.10: Conto Orario Docenti Avanzato
**Descrizione:** Calcolo automatico conto orario da presenze con bonus/forfait

**DEV UNIT:** ~70
- DB_campi: ~15
- DB_relazioni: 3
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: ~20
- UI_stampa: ~15

---

#### Attività 3.11: Configurazione Compensi Docenti
**Descrizione:** CRUD configurazione compensi personalizzati docenti

**DEV UNIT:** ~40
- DB_campi: ~10
- DB_relazioni: 1
- CRUD: 7
- Workflow: 0
- UI_form: ~10
- UI_lista: ~12
- UI_stampa: 0

---

### GRUPPO 4: COMUNICAZIONI E ATTIVITÀ

#### Attività 3.12: Comunicazioni Evolute
**Descrizione:** Sistema comunicazioni multi-canale (email, SMS, WhatsApp)

**DEV UNIT:** ~80
- DB_campi: ~15
- DB_relazioni: 3
- CRUD: 0
- Workflow: 2
- UI_form: ~15
- UI_lista: ~18
- UI_stampa: 0

---

#### Attività 3.13: Template Comunicazioni
**Descrizione:** Editor template comunicazioni personalizzabili

**DEV UNIT:** ~50
- DB_campi: ~10
- DB_relazioni: 1
- CRUD: 7
- Workflow: 1
- UI_form: ~10
- UI_lista: ~14
- UI_stampa: 0

---

#### Attività 3.14: Integrazione SMS/WhatsApp
**Descrizione:** Integrazione gateway SMS/WhatsApp (Twilio)

**DEV UNIT:** ~60
- DB_campi: ~8
- DB_relazioni: 1
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: 0
- UI_stampa: 0

---

#### Attività 3.15: Attività Extra Evolute
**Descrizione:** Gestione avanzata attività extra (orchestra/coro)

**DEV UNIT:** ~40
- DB_campi: ~8
- DB_relazioni: 1
- CRUD: 0
- Workflow: 2
- UI_form: ~8
- UI_lista: 0
- UI_stampa: 0

---

#### Attività 3.16: Generazione Attestati PDF
**Descrizione:** Generazione PDF attestati frequenza

**DEV UNIT:** ~40
- DB_campi: 0
- DB_relazioni: 0
- CRUD: 0
- Workflow: 1
- UI_form: 0
- UI_lista: 0
- UI_stampa: ~39

---

### GRUPPO 5: REPORTISTICA E ANALISI

#### Attività 3.17: Reportistica Avanzata
**Descrizione:** Reportistica con grafici e confronti multi-anno

**DEV UNIT:** ~100
- DB_campi: 0
- DB_relazioni: 0
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: 0
- UI_stampa: ~98

---

#### Attività 3.18: Flusso di Cassa
**Descrizione:** Visualizzazione flusso di cassa con aggregazioni

**DEV UNIT:** ~70
- DB_campi: 0
- DB_relazioni: 0
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: 0
- UI_stampa: ~68

---

#### Attività 3.19: Export Excel/CSV Personalizzato
**Descrizione:** Export dati personalizzabile in Excel/CSV

**DEV UNIT:** ~50
- DB_campi: 0
- DB_relazioni: 0
- CRUD: 0
- Workflow: 1
- UI_form: 0
- UI_lista: 0
- UI_stampa: ~49

---

#### Attività 3.20: Statistiche Dashboard
**Descrizione:** Dashboard con statistiche avanzate

**DEV UNIT:** ~60
- DB_campi: 0
- DB_relazioni: 0
- CRUD: 0
- Workflow: 2
- UI_form: 0
- UI_lista: 0
- UI_stampa: ~58

---

### RIEPILOGO FASE 3

| Gruppo | Attività | DEV UNIT |
|--------|----------|----------|
| **1. Primo Contatto e Preiscrizioni** | 3.1-3.3 | ~140 |
| **2. Proposta Oraria** | 3.4-3.5 | ~110 |
| **3. Registro Evoluto** | 3.6-3.11 | ~340 |
| **4. Comunicazioni e Attività** | 3.12-3.16 | ~270 |
| **5. Reportistica e Analisi** | 3.17-3.20 | ~280 |
| **TOTALE FASE 3** | **20 attività** | **~1.140 DEV UNIT** |

---

## RIEPILOGO TOTALE

| Fase | Attività | DEV UNIT | Descrizione |
|------|----------|----------|-------------|
| **FASE 1** | 24 | ~1.398 | Traduzione 1:1 ODS → DB normalizzato con CRUD base |
| **FASE 2** | 14 | ~690 | Evoluzioni amministrative (contratti, fatturazione, integrazioni) |
| **FASE 3** | 20 | ~1.140 | Evoluzioni didattiche (registro, comunicazioni, reportistica) |
| **TOTALE** | **58** | **~3.228 DEV UNIT** | |

---

## FORMATO DATI PER FOOTILITY

### Struttura Attività

```json
{
  "phase": "FASE 1",
  "group": "1. Anagrafiche Base",
  "activity_id": "1.1",
  "title": "Studenti",
  "description": "CRUD completo per gestione studenti con tutti i campi presenti negli ODS",
  "dev_units": {
    "db_campi": 64,
    "db_relazioni": 10,
    "crud": 7,
    "workflow": 2,
    "ui_form": 35,
    "ui_lista": 28,
    "ui_stampa": 0,
    "total": 146
  },
  "ods_columns_covered": [
    "A-D: Contratti/Pagamenti (collegati)",
    "E: Stato",
    "F: Data arrivo",
    "G-H: Cognome/Nome",
    "..."
  ],
  "dependencies": []
}
```

---

## PROSSIMI PASSI

1. ⏳ Verificare calcoli DEV UNIT con ODS reali
2. ⏳ Preparare file JSON/CSV per inserimento in Footility
3. ⏳ Inserire attività in Footility con DEV UNIT
4. ⏳ Footility bilancerà con COSMIC per generare preventivi

---

**Documento creato:** Dicembre 2024  
**Status:** Organizzazione fasi completata, pronto per inserimento in Footility
