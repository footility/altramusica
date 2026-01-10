# Analisi Completa Colonne ODS - db 2025-26 gestionale.ods

**File:** `db 2025-26 gestionale.ods`  
**Foglio:** `dati`  
**Righe:** 485  
**Colonne totali:** 1024  
**Colonne con header:** ~200+

## Colonne Attualmente Importate

### Studenti (StudentSeeder)
- ✅ Cognome (G)
- ✅ Nome (H)
- ✅ Cod. Fiscale Allievo
- ✅ Nato il (AV)
- ✅ Età (AU)
- ✅ Minore (AW)
- ✅ Data di arrivo (F)
- ✅ Come è venuto a sapere di noi (J)
- ✅ Note prove e didattiche (M)
- ✅ Note varie (N)
- ✅ Data ultimo (O)
- ✅ Stato (E)

### Genitori (StudentSeeder -> importGuardians)
- ✅ Cognome Genitore 1 (AX)
- ✅ Nome Genitore 1
- ✅ Cognome Genitore 2
- ✅ Nome Genitore 2
- ✅ Cell 1 /madre
- ✅ Cell 2/padre
- ✅ Mail 1
- ✅ Mail 2

## Colonne NON Importate (Da Implementare)

### Disponibilità Oraria
- ⚠️ Disponibilità (P)
- ⚠️ Lu (Q) - Lunedì
- ⚠️ Ma (R) - Martedì
- ⚠️ Me (S) - Mercoledì
- ⚠️ Gio (T) - Giovedì
- ⚠️ Ve (U) - Venerdì
- ⚠️ Sab (V) - Sabato
- ⚠️ Lab (W) - Laboratorio
- ⚠️ Note disponibilità (X)

**Status:** Parzialmente importato da `AvailabilitySeeder` (file Excel separato)

### Strumenti
- ❌ Fornitore strumento (Y)
- ❌ Noleggio/proprietà (Z)
- ❌ Provenienza (AA)
- ❌ Tipo (AB)
- ❌ Marca (AC)
- ❌ Mod (AD)
- ❌ Misura (AE)
- ❌ Cod (AF)
- ❌ Da cambiare (AG)
- ❌ Note strumento (AH)

**Status:** Parzialmente importato da `InstrumentSeeder`, ma non collegato agli studenti

### Livelli e Competenze
- ❌ Livello (AI)
- ❌ Livello str. (AJ)
- ❌ Livello teoria (AK)
- ❌ Musica di insieme (AL)

**Status:** NON importato - serve model `StudentLevel`

### Orchestra e Coro
- ❌ Conf (AM) - Conferma orchestra?
- ❌ Orch 1 (AN)
- ❌ PYO (AO)
- ❌ Conf coro (AP)
- ❌ Coro (AQ)
- ❌ Data ultima convocazione (AR)
- ❌ Note orchestra (AS)

**Status:** Parzialmente importato (ExtraActivity), ma non collegato agli studenti

### Contratti e Pagamenti
- ❌ Richiesta pagamento (A)
- ❌ Pagato (B)
- ❌ Conto orario (C)
- ❌ Contratto (D)

**Status:** Importato da `ContractSeeder` (file separato), ma colonne A-D non mappate

### Corsi e Iscrizioni
- ❌ Info (I) - Informazioni corso?
- ❌ A che corso sei interessato? (K)
- ❌ Ti piacerebbe provare anche altri strumenti? (L)

**Status:** NON importato - serve collegamento corsi/iscrizioni

### Altri Dati
- ❌ Dati (AT) - Dati aggiuntivi?
- ❌ Età scolare (foglio separato)
- ❌ Grafico (foglio separato)

## File ODS Separati

### Db Contratti 25-26.ods
- ✅ Importato da `ContractSeeder`
- ⚠️ 416 righe, molte colonne

### Db Contabile 2025-26.ods
- ⚠️ Fatture corsi (parzialmente importato)
- ❌ Fatture accessori (non importato)
- ❌ Pagato (non importato)
- ❌ Recupero crediti (non importato)
- ❌ Riepilogo sintetico (non importato)

### Db Accessori 2025-26.ods
- ⚠️ Esami (parzialmente importato)
- ❌ Accessori 1-7 (non importato)
- ❌ Libri 1-15 (non importato)
- ❌ Noleggi strumenti dettagliati (non importato)

### Calendario 2025-26.ods
- ⚠️ Parzialmente importato (solo struttura base)

### dati lavoratori 25-26.ods
- ⚠️ Docenti (parzialmente importato)
- ❌ Contratti docenti dettagliati (non importato)
- ❌ Archivio insegnanti-supplenti (non importato)

## Gap Principali

1. **Livelli Studenti** - Non importato, serve model `StudentLevel`
2. **Collegamento Strumenti-Studenti** - Strumenti importati ma non collegati
3. **Collegamento Orchestra/Coro-Studenti** - Attività importate ma non iscritti
4. **Iscrizioni Corsi** - Non importate dal file principale
5. **Dati Contabili Completi** - Solo fatture base, mancano accessori, pagamenti, recupero crediti
6. **Dati Lavoratori Completi** - Solo anagrafica docenti, mancano contratti e dettagli

## Raccomandazioni

1. Creare model `StudentLevel` per livelli ABRSM
2. Migliorare `InstrumentSeeder` per collegare strumenti agli studenti
3. Migliorare `ExtraActivitySeeder` per importare iscrizioni orchestra/coro
4. Creare `EnrollmentSeeder` per importare iscrizioni corsi
5. Completare `InvoiceSeeder` per accessori e pagamenti
6. Creare `TeacherContractSeeder` per contratti docenti

