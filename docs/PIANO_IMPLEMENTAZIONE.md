# Piano di Implementazione Completa

## Stato Attuale
- ✅ Database schema (29 migrations)
- ✅ 25 Models con relazioni base
- ✅ Componenti riutilizzabili (FormField, DataTable, FilterBar)
- ✅ AcademicYear model e migration
- ⚠️ CRUD parziali (solo Students/Guardians base)

## Priorità Implementazione (Ordine Cliente)

### FASE 1 - CRITICO (Fine Agosto/Primi Settembre)
1. ✅ AcademicYear - Completare CRUD
2. ⏳ Primo Contatto - Form pubblico + gestione
3. ⏳ Iscrizione e Corsi - CRUD completo con logica
4. ⏳ Calendario Lezioni - CRUD + sospensioni
5. ⏳ Gestione Contratti - Workflow completo
6. ⏳ Gestione Fatturazione - Logica rate/pagamenti

### FASE 2 - Primi Settembre
7. ⏳ Proposta Oraria - Composizione orari
8. ⏳ Comunicazione base - Email

### FASE 3 - Fine Settembre/Ottobre
9. ⏳ Registro Elettronico
10. ⏳ Gestione Presenze
11. ⏳ Conto Orario Insegnanti
12. ⏳ Attività Extra (Orchestra)

## Componenti da Creare

### Blade Components
- ✅ FormField (text, select, textarea, date, checkbox)
- ✅ DataTable (con paginazione)
- ✅ FilterBar
- ⏳ Modal (riutilizzabile)
- ⏳ Card (statistiche dashboard)
- ⏳ Badge (stati)

### Services (Logica Business)
- ⏳ AcademicYearService
- ⏳ EnrollmentService (calcolo costi, rate)
- ⏳ ContractService (workflow, generazione PDF)
- ⏳ InvoiceService (generazione, rate, pagamenti)
- ⏳ CalendarService (calcolo settimane, sospensioni)
- ⏳ CommunicationService (email, SMS)

## Note Implementazione
- Tutti i form devono usare `old()` per mantenere valori
- Tutti i controller devono essere semplici, logica nei Services
- Nessuna ridondanza dati, solo relazioni
- Export sempre disponibile (Excel/CSV)

