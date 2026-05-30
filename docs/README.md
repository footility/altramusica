# Documentazione Gestionale L'Altramusica

La documentazione è organizzata per **funzionalità/aree** (Studenti, Genitori, ...),
ed è aderente ai dati presenti in `docs/materiale cliente/`.

## Documenti (numerati)

- `00_INDICE_FUNZIONALITA_ASIS.md`
- `00_CHECKLIST_COPERTURA_SEZIONI_VS_ANALISI_COLONNE.md`
- `01_ANAGRAFICHE_STUDENTI.md`
- `02_GENITORI_TUTORI.md`
- `03_CORSI_E_ISCRIZIONI.md`
- `04_CONTRATTI_STUDENTI.md`
- `05_CONTABILITA_CORSI.md`
- `06_CONTABILITA_ACCESSORI_NOLEGGI_CAUZIONI.md`
- `07_ACCESSORI_NOLEGGI_LIBRI.md`
- `08_DOCENTI_LAVORATORI.md`
- `09_CALENDARIO_ANNUALE.md`
- `10_MODULO_ANAGRAFICA_E_DISPONIBILITA.md`
- `11_STATISTICHE_STORICHE.md`
- `12_DOCUMENTI_E_MODELLI_CONTRATTI.md`
- `13_COPERTURA_CODICE_VS_ASIS.md`
- `14_FASE2_OTTIMIZZAZIONI_E_ALLEGGERIMENTO_OPERATIVO.md`
- `15_ATTIVITA_FASE2_FOOTILITY.md`
- `20_UX_FLUSSO_ANAGRAFICHE_E_FAMIGLIE.md` (R1 · Design UX — anagrafiche e famiglie)
- `21_UX_FLUSSO_CORSI_ISCRIZIONI_RINNOVI.md` (R2 · Design UX — corsi, iscrizioni, rinnovi)
- `23_UX_ARCHIVIO_DOCUMENTI_RICERCA_RAPIDA.md` (R10 · Design UX — archivio documenti con ricerca rapida)
- `24_R10_CONTROLLO_FINALE_VALIDAZIONE_DOCUMENTI.md` (R10 · Controllo finale — validazione E2E documenti/modelli)
- `25_UX_VISTA_CONTABILITA_PER_STUDENTE.md` (R4 · Design UX — vista contabilità per studente)
- `26_UX_GENERATORE_CALENDARIO_E_RECUPERI.md` (R7 · Design UX — generatore calendario a cicli + recuperi/spostamenti)
- `27_R7_CONTROLLO_FINALE_VALIDAZIONE_CALENDARIO.md` (R7 · Controllo finale — validazione E2E calendario/recuperi)
- `28_UX_MATERIALI_NOLEGGI_LIBRI_ESAMI.md` (R6 · Design UX — materiali, noleggi, libri ed esami: vista unificata + cauzioni con stato + catalogo libri pulito)
- `29_R6_CONTROLLO_FINALE_VALIDAZIONE_MATERIALI.md` (R6 · Controllo finale — validazione E2E materiali/noleggi/libri/esami)
- `30_UX_FLUSSO_CONTRATTO_E_FIRMA.md` (R3 · Design UX — flusso contratto: timeline proposta→inviato→firmato + firma mock con upload scansione, innesto webhook firma digitale)
- `31_R3_CONTROLLO_FINALE_VALIDAZIONE_CONTRATTI.md` (R3 · Controllo finale — validazione E2E contratti + firma mock)
- `32_UX_REGISTRO_PRESENZE_E_MOTORE_COMPENSI.md` (R8 · Design UX — registro presenze a tap + motore compensi configurabile soci/non-soci/forfait, tariffe DA CONFERMARE, consuntivo mensile)
- `33_R8_CONTROLLO_FINALE_VALIDAZIONE_REGISTRO_COMPENSI.md` (R8 · Controllo finale — validazione substrato + flussi presenze/compenso/override; BLOCCO implementazione documentato)
- `34_UX_COMUNICAZIONI_MIRATE_E_LOG.md` (R9 · Design UX — comunicazioni mirate: composer + segmento corso/anno/stato, email SMTP reale, SMS/WhatsApp in bozza con copia, log invii per tracciabilità)
- `35_R9_CONTROLLO_FINALE_VALIDAZIONE_COMUNICAZIONI.md` (R9 · Controllo finale — validazione flussi segmentazione/consenso/dedup/email reale/bozza SMS-WhatsApp/log; BLOCCO implementazione documentato)
- `36_UX_RICONCILIAZIONE_MANUALE_E_IMPORT_CSV.md` (R5 · Design UX — riconciliazione manuale: import CSV estratto conto con dry-run/anomalie, schermata di match estratto conto↔scadenze con drag/click, parziali e storni gestiti come eventi reversibili)
- `37_UX_DASHBOARD_DIREZIONALE_E_BILANCIO_SOCIALE.md` (R11 · Design UX — dashboard direzionale: KPI iscritti/retention/cassa/entrate-uscite con delta anno-su-anno, serie storica multi-anno, cohort retention, flusso cassa con uscite parziali, export bilancio sociale CSV/PDF aggregato GDPR)
- `38_R11_CONTROLLO_FINALE_VALIDAZIONE_DASHBOARD_DIREZIONALE.md` (R11 · Controllo finale — validazione flussi KPI/delta/retention cohort/serie storica/cassa parziale/maturato-incassato/export bilancio sociale; BLOCCO implementazione documentato)
- `39_UX_PANNELLO_QUALITA_DATI_E_ANOMALIE.md` (R12 · Design UX — pannello qualità dati: duplicati/omonimi/campi critici mancanti/record orfani, azioni guidate non bloccanti (unisci con anteprima+log, completa col campo a fuoco, ricollega/archivia orfani), tabella eccezioni per falsi positivi, riuso del motore anomalie di `OdsImportService`)
- `40_R12_CONTROLLO_FINALE_VALIDAZIONE_QUALITA_DATI_ANOMALIE.md` (R12 · Controllo finale — report anomalie su tutte le entità: validazione regole su dato vivo (duplicati CF/nome, omonimi, campi critici mancanti, orfani contratto-senza-iscrizione/fattura-senza-righe) + riuso motore `OdsImportService`; scoperte SoftDeletes/cascade; BLOCCO implementazione documentato)
- `41_UX_MERGE_GUIDATO_E_LOG_REVERSIBILE.md` (R12 · Design UX — merge guidato studenti/genitori/corsi: anteprima dry-run + conferma + log decisioni con snapshot, reversibile per N giorni; ribaltamento FK, dedup pivot `student_guardian`, archiviazione soft-delete invece di delete; estensione a tutori (richiede SoftDeletes su Guardian) e corsi (ripunta `course_offerings`))
- `42_R12_CONTROLLO_FINALE_VALIDAZIONE_MERGE_GUIDATO.md` (R12 · Controllo finale — validazione semantica merge su schema reale: anteprima/ribaltamento FK/dedup pivot/archiviazione/log con snapshot/reversibilità entro N giorni/atomicità + estensione tutori/corsi; scoperte (Guardian senza SoftDeletes, vincolo unique pivot, course_offerings restrict); BLOCCO implementazione documentato)
- `43_R12_CONTROLLO_FINALE_VALIDAZIONE_QUALITA_DATI.md` (R12 · Controllo finale OMBRELLO — consolida anomalie (#8533) + merge (#8534) e valida le VALIDAZIONI WARNING del §0.1 "Segnalare, non bloccare" sui flussi reali (POST store): dato sporco accolto senza errore bloccante e poi rilevabile (loop segnala→rileva); BLOCCO = surfacing del warning (avviso inline + badge) assente; bug latente trovato nei store (chiave hidden mancante))
- `44_UX_AREA_FAMIGLIE_E_PRIVACY.md` (R13 · Design UX — perimetro area famiglie + privacy: cosa vede la famiglia (profilo figlio/scadenze/documenti/calendario, sola lettura, scopato sui figli) e cosa NON vede (altri studenti-tutori, compensi/margini, note interne, backoffice); flusso login famiglia su invito a token monouso → attivazione con consenso versionato → guard `family` isolato; privacy minori (nessun login al minore, consenso del tutore foto/privacy, multi-tutore/genitori separati, ritiro/anonimizzazione, diritti GDPR); greenfield dichiarato — l'area non esiste, impatti tecnici (identità famiglia, flag `visible_to_family`, rotte/guard, audit, retention) senza implementare)
- `45_R12_VALIDAZIONI_PREVENTIVE_E_CHECKLIST_CHIUSURA_MIGRAZIONE.md` (R12 · Controllo finale — validazioni preventive come **warning** (non bloccanti) sui flussi reali `store` + **checklist di chiusura migrazione** eseguibile sul dato vivo (gate strutturali bloccanti vs warning annotativi, verdetto di certificabilità, loop segnala→risolvi→ri-certifica, riuso motore CF di `OdsImportService`); scoperta (FK `contracts.student_id` garantisce G10 a zero); BLOCCO implementazione documentato (scanner/comando/report persistito/rotte assenti) — attività #8535)

## Allegati sorgenti

- `docs/materiale cliente/` contiene i file ODS/Excel originali.


