# Riepilogo Sistema Seeder

## ✅ Seeder Completati e Funzionanti

### 1. AdminSeeder
- **Scopo**: Crea utente admin per accesso sistema
- **Dati**: Utente fisso
- **Output**: `admin@altramusica.test` / `password`

### 2. AcademicYearSeeder
- **Scopo**: Crea anno accademico attivo
- **Dati**: Anno 2025-26 (1 settembre 2025 - 31 agosto 2026)
- **Output**: Anno accademico attivo

### 3. TeacherSeeder
- **Scopo**: Importa docenti da `dati lavoratori 25-26.ods`
- **File**: `docs/materiale cliente/dati lavoratori 25-26.ods`
- **Foglio**: `2025-26`
- **Dati Importati**: 
  - Anagrafica completa (nome, cognome, CF, P.IVA, IBAN)
  - Recapiti (telefono, email, indirizzo)
  - Ruolo e inquadramento
- **Output**: ~26 docenti importati
- **Note**: Crea anche utenti User per docenti con email

### 4. StudentSeeder
- **Scopo**: Importa studenti da `db 2025-26 gestionale.ods`
- **File**: `docs/materiale cliente/db 2025-26 gestionale.ods`
- **Foglio**: `dati`
- **Dati Importati**:
  - Anagrafica studenti (nome, cognome, CF, data nascita, età)
  - Stato (prospect, interested, enrolled, withdrawn)
  - Note e informazioni varie
  - Importa anche genitori associati
- **Output**: ~306 studenti importati
- **Note**: Importa automaticamente anche i genitori

### 5. GuardianSeeder
- **Scopo**: Verifica relazioni studenti-genitori
- **Dati**: Relazioni già create da StudentSeeder
- **Output**: Statistiche relazioni

### 6. ContractSeeder ✅
- **Scopo**: Importa contratti da `Db Contratti 25-26.ods`
- **File**: `docs/materiale cliente/Db Contratti 25-26.ods`
- **Foglio**: `Contratti`
- **Dati Importati**:
  - Codice contratto
  - Stato (draft, sent, signed)
  - Date (invio, firma)
  - Corsi associati (fino a 3 corsi + orchestra)
  - Rate (1°, 2°, 3° rata)
  - Note e privacy
- **Output**: Contratti importati (in progress)
- **Note Speciali**: 
  - ✅ **RISOLTO**: Gestisce formule esterne che referenziano file gestionale
  - Le formule vengono risolte leggendo direttamente dal file gestionale
  - Esempio: `='file://.../db%202025-26%20gestionale.ods'#$dati!H2` → legge H2 dal file gestionale

## ⚠️ Seeder da Completare

### 7. InvoiceSeeder
- **Scopo**: Importa fatture da `Db Contabile 2025-26.ods`
- **File**: `docs/materiale cliente/Db Contabile 2025-26.ods`
- **Fogli**: 9 fogli (fatt corsi, rate, etc.)
- **Stato**: Struttura base creata, logica da implementare
- **Complessità**: Rate multiple, note di credito, importi non fatturati

### 8. InstrumentSeeder
- **Scopo**: Importa strumenti e noleggi
- **File**: 
  - `db 2025-26 gestionale.ods` (strumenti per studente)
  - `Db Accessori 2025-26.ods` (noleggi dettagliati)
- **Stato**: Struttura base creata, logica da implementare

### 9. ExamSeeder
- **Scopo**: Importa esami da `Db Accessori 2025-26.ods`
- **File**: `docs/materiale cliente/Db Accessori 2025-26.ods`
- **Foglio**: `accessori`
- **Stato**: Struttura base creata, logica da implementare
- **Complessità**: Fino a 5 esami per studente

### 10. CalendarSeeder
- **Scopo**: Importa calendario lezioni e sospensioni
- **File**: `docs/materiale cliente/Calendario 2025-26.ods`
- **Fogli**: 3 fogli
- **Stato**: Struttura base creata, logica da implementare
- **Complessità**: Struttura con formule Excel da analizzare

## Tecniche Implementate

### 1. Risoluzione Formule Esterne
**Problema**: File ODS con formule che referenziano altri file.

**Soluzione**:
```php
// Rileva formule esterne
if (strpos($cellValue, 'file://') !== false) {
    // Estrai riferimento: #$dati!H2
    preg_match('/#\$dati!([A-Z]+)(\d+)/', $cellValue, $matches);
    $refCol = $matches[1]; // H
    $refRow = $matches[2]; // 2
    // Leggi dal file referenziato
    $value = $gestionaleSheet->getCell($refCol . $refRow)->getValue();
}
```

### 2. Mapping Dinamico Colonne
- Lettura automatica header da file ODS
- Mapping case-insensitive
- Supporto varianti nomi colonne

### 3. Gestione Errori Robusta
- Try-catch per ogni riga
- Log errori senza bloccare importazione
- Statistiche finali (importati, saltati, errori)

### 4. Normalizzazione Dati
- Parsing date multiple formati
- Validazione email
- Pulizia stringhe (trim, uppercase per CF)
- Gestione valori null/vuoti

## Ordine di Esecuzione

L'ordine nel `DatabaseSeeder` è ottimizzato per dipendenze:

1. **AdminSeeder** - Base, nessuna dipendenza
2. **AcademicYearSeeder** - Base, nessuna dipendenza
3. **TeacherSeeder** - Base, necessario per corsi futuri
4. **StudentSeeder** - Base per relazioni
5. **GuardianSeeder** - Dipende da studenti
6. **ContractSeeder** - Dipende da studenti
7. **InvoiceSeeder** - Dipende da contratti/studenti
8. **InstrumentSeeder** - Indipendente
9. **ExamSeeder** - Dipende da studenti
10. **CalendarSeeder** - Dipende da anno accademico

## Comando di Esecuzione

```bash
php artisan migrate:refresh --seed
```

Questo comando:
1. Resetta il database (drop e ricrea tabelle)
2. Esegue tutte le migration
3. Esegue tutti i seeder nell'ordine corretto

## Statistiche Importazione

Dopo esecuzione completa:
- ✅ **Admin**: 1 utente
- ✅ **Anno Accademico**: 1 attivo (2025-26)
- ✅ **Docenti**: 26
- ✅ **Studenti**: 306
- ✅ **Genitori**: 365
- ✅ **Relazioni**: 417
- ✅ **Contratti**: 306 importati
- ⚠️ **Fatture**: Da implementare
- ⚠️ **Strumenti**: Da implementare
- ⚠️ **Esami**: Da implementare
- ⚠️ **Calendario**: Da implementare

## Note Tecniche

- I seeder sono **idempotenti** (usano `firstOrCreate`)
- Possono essere eseguiti più volte senza duplicati
- I file ODS vengono riletti per ogni seeder (può essere ottimizzato)
- Gli errori vengono loggati ma non bloccano l'esecuzione
- Le relazioni vengono create automaticamente dove possibile

## Prossimi Passi

1. ✅ Completare ContractSeeder (COMPLETATO - 306 contratti)
2. ⚠️ Implementare InvoiceSeeder (logica rate multiple)
3. ⚠️ Implementare InstrumentSeeder (noleggi)
4. ⚠️ Implementare ExamSeeder (esami multipli)
5. ⚠️ Implementare CalendarSeeder (analisi struttura)
6. ⚠️ Ottimizzare performance (batch processing)
7. ⚠️ Aggiungere validazione dati più robusta

