# Preventivo Dettagliato - Sistema Gestionale L'Altramusica

**Data:** Dicembre 2024  
**Tariffa Oraria:** € 60,00/ora  
**Valutazione:** Sviluppo umano (tempo/linee di codice realistiche)

---

## Tabella Preventivo

| Funzionalità | Descrizione | Ore Stimate | Costo Stimato |
|--------------|-------------|-------------|---------------|
| **FASE 0 - INFRASTRUTTURA BASE** | | | |
| Setup progetto e ambiente | Configurazione Laravel, database, autenticazione base, struttura cartelle, configurazione server | 16 | € 960,00 |
| Gestione delle utenze | Sistema di autenticazione, registrazione utenti, gestione ruoli (admin, segreteria, docente, genitore, studente), middleware autorizzazioni | 24 | € 1.440,00 |
| Controllo Accessi e Autorizzazioni | Sistema permessi granulari per area, gestione password, autenticazione multi-fattore opzionale, sessioni | 16 | € 960,00 |
| Sicurezza e Integrità dei Dati | Implementazione GDPR, backup automatici, crittografia dati sensibili, log audit, cancellazione dati differenziata | 20 | € 1.200,00 |
| **FASE 1 - ANagrafiche e Dati Base** | | | |
| Gestione Esercizio/Anno Scolastico | Sistema multi-esercizio (1 sett - 31 ago), switch tra esercizi, gestione preiscrizioni trasferimento automatico, categorizzazione studenti per esercizio, data ultimo contatto | 20 | € 1.200,00 |
| Gestione Studenti | CRUD completo anagrafica studenti, gestione primo contatto -> cliente, codice fiscale allievo, disponibilità oraria, livello (0-8 ABRSM), strumento, tipo studente (iscritto/nuovo/preiscritto), note didattiche/amministrative, gestione privacy | 32 | € 1.920,00 |
| Gestione Genitori/Tutore | CRUD anagrafica genitori, primo e secondo genitore, delega recupero minori, dati contatto, gestione privacy, relazioni multiple | 20 | € 1.200,00 |
| Anagrafica Fornitori e Clienti | CRUD fornitori, integrazione per cassetto fiscale, categorizzazione spese, gestione IBAN, documenti | 16 | € 960,00 |
| Gestione Docenti | CRUD anagrafica docenti, categorizzazione soci/non soci, contratti docenti, documenti (CF, IBAN, ritenute), note contrattuali | 24 | € 1.440,00 |
| **FASE 2 - PRIMO CONTATTO E ISCRIZIONI** | | | |
| Primo Contatto | Form pubblico con link precompilati, raccolta dati iniziali (nome, data nascita, telefono, mail, note), categorizzazione interessati/prospect, conversione prospect -> studente, gestione link invio | 24 | € 1.440,00 |
| Calendario Lezioni | CRUD calendario con giornate attive, gestione sospensioni strategiche, calcolo automatico settimane in base a data inizio e calendario, visualizzazione calendario annuale | 20 | € 1.200,00 |
| Iscrizione e Corsi | CRUD corsi (regolari, brevi, pacchetti), CRUD iscrizioni, logica calcolo settimane/costi, gestione lezioni libere, pacchetti lezioni, schedulazione classi, monitoraggio livelli ABRSM | 40 | € 2.400,00 |
| Proposta Oraria | Sistema composizione orari, matching disponibilità allievi/insegnanti, proposta definitiva con giorno/orario/classe/insegnante precisi, gestione conflitti | 32 | € 1.920,00 |
| **FASE 3 - CONTRATTI E FATTURAZIONE** | | | |
| Gestione Contratti | Workflow completo (draft/sent/signed), generazione PDF contratti (regolari, brevi, tempo estivo, noleggio), link precompilati per accettazione, tracking stato, monitoraggio scadenze, gestione documenti legali/privacy | 40 | € 2.400,00 |
| Gestione Fatturazione | Sistema fatturazione elettronica (solo a pagamento avvenuto), generazione proforme, gestione rate variabili (non solo 3), rateizzazione flessibile con scadenze personalizzate, calcolo automatico rate in base a settimane/calendario | 48 | € 2.880,00 |
| Gestione Pagamenti | Tracciamento pagamenti, importazione estratti conto CSV, file cassa e banca, gestione crediti e acconti, note di credito, compensazione crediti tra fratelli, gestione pagamenti parziali/eccessivi | 32 | € 1.920,00 |
| Recupero Crediti | Sistema solleciti automatici configurabili, sospensione automatismi per casi particolari, monitoraggio pagamenti in ritardo, report recupero crediti, comunicazioni massive per solleciti | 24 | € 1.440,00 |
| **FASE 4 - DIDATTICA E REGISTRO** | | | |
| Registro Elettronico | Sistema registro per presenze lezioni, accesso insegnanti/supplenti, tracking presenze allievi, conteggio lezioni effettuate, countdown lezioni rimanenti, visualizzazione calendario lezioni per docente | 32 | € 1.920,00 |
| Gestione Presenze | Registrazione presenze allievi, monitoraggio frequenza, gestione recuperi lezioni, storico presenze, report assenze | 20 | € 1.200,00 |
| Conto Orario Insegnanti | Calcolo automatico conto orario basato su lezioni effettuate, prospetto previsionale lezioni, gestione pagamenti rateizzati insegnanti, compensi differenziati soci/non soci, voci forfettarie aggiuntive, personalizzazione valore orario, gestione bonus a consuntivo | 40 | € 2.400,00 |
| Gestione Supplenti | Sistema supplenze con accesso registro (account provvisori), trasferimento lezione insegnante -> supplente per calcolo pagamenti, gestione ritenute d'acconto prestazioni occasionali, documenti supplenti | 24 | € 1.440,00 |
| Gestione Aule | CRUD aule e spazi, verifica disponibilità aule per prenotazioni, gestione recuperi lezioni, autorizzazione utilizzo aule, calendario utilizzo aule | 16 | € 960,00 |
| **FASE 5 - ATTIVITÀ EXTRA E COMUNICAZIONI** | | | |
| Attività Extra (Orchestra/Coro) | CRUD attività extracurriculari, gestione eventi e iscrizioni, filtri composizione gruppi (livello, strumento, iscrizione progetto), convocazioni con calendario 12 date/anno, gestione presenze prove orchestra, costi relativi | 32 | € 1.920,00 |
| Generazione Attestati | Generazione attestati frequenza per crediti scolastici basati su corsi frequentati e presenze registrate, template personalizzabili, export PDF | 16 | € 960,00 |
| Comunicazione e Privacy | Sistema comunicazioni integrate (email, SMS, WhatsApp), comunicazioni massive con filtri, template personalizzabili, log comunicazioni, doppio canale (email ufficiale per contratti/fatture, SMS per convocazioni), gestione privacy foto, tracking come clienti conoscono istituto | 40 | € 2.400,00 |
| **FASE 6 - MAGAZZINO E STRUMENTI** | | | |
| Gestione Accessori e Strumenti Musicali | CRUD strumenti musicali, gestione noleggio con cauzioni e rate, gestione acquisto/vendita, magazzino strumenti (cespiti) con codifica, valore acquisto, stato (noleggio/vendita), contratto noleggio strumenti, gestione libri didattici (inventario, tracking vendite, valore complessivo venduto), gestione rimanenze | 32 | € 1.920,00 |
| **FASE 7 - INTEGRAZIONI E REPORTISTICA** | | | |
| Integrazione Cassetto Fiscale | Integrazione per importazione fatture attive e passive, incrocio e categorizzazione spese, importazione fatture insegnanti, sincronizzazione periodica | 32 | € 1.920,00 |
| Flusso di Cassa | Visualizzazione flusso di cassa con entrate e uscite, inserimento pagamenti cassa, importazione estratti conto, riepiloghi mensili banca/cassa, visione andamento economico per programmazione spese, grafici andamento | 24 | € 1.440,00 |
| Pianificazione Annuale e Reportistica | Dashboard statistiche (nuovi/vecchi iscritti, preiscrizioni, rinnovi, corsi regolari vs brevi, distribuzione età, partecipazione orchestra), export dati con selezione campi, confronto dati multi-anno per bilancio sociale, grafici e report tabellari, reportistica personalizzabile | 40 | € 2.400,00 |
| Preiscrizioni | Gestione campagna preiscrizioni a fine anno, quota iscrizione pagata a fine anno come diritto prelazione (di competenza esercizio successivo), gestione crediti tra fratelli per preiscrizioni non utilizzate, workflow preiscrizione -> iscrizione | 20 | € 1.200,00 |
| Gestione Esami | CRUD esami, costi iscrizione, pianificazione esami, allineamento livelli ABRSM, gestione risultati | 16 | € 960,00 |
| **FASE 8 - IMPORT E MIGRAZIONE DATI** | | | |
| Import Dati Storici | Analisi database esistenti (ODS, Excel), creazione script import, mapping dati, validazione import, import anno 2024, gestione import anni precedenti | 32 | € 1.920,00 |
| **FASE 9 - TESTING E DOCUMENTAZIONE** | | | |
| Testing Completo | Test funzionali, test integrazione, test utente, correzione bug, test performance | 40 | € 2.400,00 |
| Documentazione | Documentazione tecnica, manuale utente, documentazione API, guide operative | 24 | € 1.440,00 |
| Formazione | Formazione personale (48 ore come da preventivo originale), sessioni di training, supporto iniziale | 48 | € 2.880,00 |

---

## Riepilogo Costi

| Categoria | Ore Totali | Costo Totale |
|----------|-----------|--------------|
| Infrastruttura Base | 76 | € 4.560,00 |
| Anagrafiche e Dati Base | 132 | € 7.920,00 |
| Primo Contatto e Iscrizioni | 116 | € 6.960,00 |
| Contratti e Fatturazione | 144 | € 8.640,00 |
| Didattica e Registro | 132 | € 7.920,00 |
| Attività Extra e Comunicazioni | 88 | € 5.280,00 |
| Magazzino e Strumenti | 32 | € 1.920,00 |
| Integrazioni e Reportistica | 116 | € 6.960,00 |
| Import e Migrazione Dati | 32 | € 1.920,00 |
| Testing e Documentazione | 112 | € 6.720,00 |
| **TOTALE** | **980 ore** | **€ 58.800,00** |

---

## Note sul Preventivo

### Assunzioni
- Tariffa oraria: € 60,00/ora (media freelance Laravel/PHP)
- Stime basate su sviluppo umano tradizionale (non AI-assisted)
- Include tempo per analisi, sviluppo, testing, debugging, documentazione
- Non include costi hosting, domini, servizi esterni (fatturazione elettronica, SMS gateway, etc.)

### Variabilità
- Le ore possono variare del ±20% in base a:
  - Complessità reale dei dati esistenti
  - Richieste di personalizzazione aggiuntive
  - Integrazioni con sistemi esterni non previsti
  - Modifiche in corso d'opera

### Fasi Critiche (Ordine Cliente)
- **Fase 1 (Fine Agosto)**: Anagrafiche, Primo Contatto, Iscrizioni, Contratti, Fatturazione
- **Fase 2 (Primi Settembre)**: Proposta Oraria
- **Fase 3 (Fine Settembre/Ottobre)**: Registro, Presenze, Conto Orario, Orchestra

### Servizi Inclusi
- Setup iniziale e configurazione
- Correzione bug durante sviluppo
- Assistenza durante implementazione
- Formazione personale (48 ore)

### Servizi Non Inclusi / A Parte
- Hosting e dominio (da € 30/anno)
- Servizi esterni (fatturazione elettronica, SMS gateway, etc.)
- Manutenzione post-consegna (da € 50/mese)
- Import anni precedenti 2024 (€ 50 cad.)
- Modifiche funzionali post-consegna

---

## Confronto con Preventivo Originale

| Voce | Preventivo Originale | Preventivo Dettagliato | Differenza |
|------|---------------------|----------------------|------------|
| Prezzo Base | € 5.000,00 | € 58.800,00 | +€ 53.800,00 |
| Ore Stimate | ~83 ore (implicite) | 980 ore | +897 ore |

**Motivazione Differenza:**
- Preventivo originale era indicativo e generico
- Richieste dettagliate emerse durante consulenza (transcript)
- Funzionalità aggiuntive non previste inizialmente:
  - Sistema multi-esercizio complesso
  - Rateizzazione flessibile avanzata
  - Integrazione cassetto fiscale
  - Sistema comunicazioni multi-canale
  - Gestione supplenti e conto orario complesso
  - Reportistica avanzata multi-anno
- Complessità operativa reale superiore alle stime iniziali

---

## Raccomandazioni

1. **Sviluppo Incrementale**: Implementare per fasi critiche, iniziando da Fase 1
2. **Prioritizzazione**: Concentrarsi su funzionalità essenziali per inizio anno scolastico
3. **Raffinamento con AI**: Utilizzare AI per ridurre tempi sviluppo del 30-40% (stima finale: 600-700 ore)
4. **Valutazione Alternative**: Considerare sviluppo modulare con consegne parziali
5. **Budget Flessibile**: Prevedere margine per modifiche e aggiustamenti

---

**Preventivo valido fino al:** 31 Gennaio 2025  
**Tutti gli importi sono da considerarsi esenti da IVA**

