# Copertura codice vs funzionalità AS‑IS (Fase 1)

Questo documento serve come **mappa 1:1** tra le 12 sezioni AS‑IS (`docs/01..12`) e ciò che oggi è già presente nel codice Laravel (model/controller/view/migration), evidenziando i gap da colmare in Fase 1.

## Regola architetturale (Master vs Annuale)

- **Master**: entità stabili (non legate ad `academic_year_id`).
- **Annuale/Operativa**: record “nell’anno” (legate ad `academic_year_id` oppure derivabili da un record annuale come `Contract`/`Invoice`).

## A01 — Anagrafiche Studenti

- **Presente**
  - Model “persona”: `app/Models/Student.php`
  - Model “nell’anno”: `app/Models/StudentYear.php`
  - Controller: `app/Http/Controllers/Admin/StudentController.php`
  - Views: `resources/views/admin/students/*`
  - Anno scolastico: `app/Models/AcademicYear.php`, `Admin/AcademicYearController.php`, `resources/views/admin/academic-years/*`
- **Gap (da colmare)**
  - Rifiniture minori su filtri/ricerca e validazioni (AS‑IS completo già coperto).

## A02 — Genitori/Tutori

- **Presente**
  - Model: `app/Models/Guardian.php` + pivot `student_guardian`
  - Controller: `app/Http/Controllers/Admin/GuardianController.php`
  - Views: `resources/views/admin/guardians/*`
- **Gap**
  - Pivot: in `GuardianController@update` usa `sync($studentIds)` ma perde i pivot fields (`relationship_type`, `is_primary`, `is_billing_contact`) → va gestito.

## A03 — Corsi e Iscrizioni

- **Presente**
  - Model “catalogo”: `app/Models/Course.php`
  - Model “offerta annuale”: `app/Models/CourseOffering.php`
  - Iscrizioni: `app/Models/Enrollment.php`
  - Controllers: `Admin/CourseController.php`, `Admin/EnrollmentController.php`
  - Views: `resources/views/admin/courses/*`, `resources/views/admin/enrollments/*`
- **Gap / criticità**
  - Rifiniture minori su filtri/listati e coerenza dei campi opzionali (AS‑IS completo già coperto).

## A04 — Contratti Studenti

- **Presente**
  - Model: `app/Models/Contract.php` (+ token link)
  - Controller: `Admin/ContractController.php`
  - Views: `resources/views/admin/contracts/*`
  - Collegamento fatture: route `admin.contracts.create-invoice`
- **Gap**
  - Nessun gap bloccante: CRUD “Documenti” presente (archivio/upload/filtri minimi).

## A05 — Contabilità Corsi

- **Presente**
  - Models: `Invoice`, `InvoiceItem`, `Payment`, `PaymentPlan`, `CreditNote`
  - Controller: `Admin/InvoiceController.php` (include creazione piano pagamenti + registrazione pagamento)
  - Views: `resources/views/admin/invoices/*`
- **Gap**
  - UI CRUD mancanti per: `InvoiceItem`, `Payment`, `PaymentPlan`, `CreditNote` (attualmente gestiti solo tramite azioni su invoice).
  - Reportistica AS‑IS (scadenzario/crediti/debiti) ancora da aggiungere.

## A06 — Contabilità Accessori/Noleggi/Cauzioni

- **Presente**
  - Models: `InstrumentRental`, `InvoiceItem` (supporta `instrument_rental`)
- **Gap**
  - Rifiniture reportistiche/collegamenti contabili specifici (parte base già operativa).

## A07 — Accessori/Noleggi/Libri/Esami

- **Presente**
  - Libri: model `Book` + migration `books`
  - Distribuzione libri: model `BookDistribution` + migration `book_distributions`
  - Noleggi: model `InstrumentRental` + migration `instrument_rentals`
  - Esami: model `Exam` + controller `Admin/ExamController.php` + views `admin/exams/*`
  - Strumenti (cespiti): model `Instrument` + controller `Admin/InstrumentController.php` + views `admin/instruments/*`
- **Gap**
  - “Accessori” come entità dedicata (oltre a libri/noleggi) resta volutamente veicolata su contabilità/righe, salvo scelta futura.

## A08 — Docenti/Lavoratori

- **Presente**
  - Model `Teacher` + controller `Admin/TeacherController.php` + views `admin/teachers/*`
  - Disponibilità: `TeacherAvailability` + controller/views
- **Gap**
  - Verifica campi fiscali/documentali da fogli ODS (da completare in fase CRUD completion).

## A09 — Calendario Annuale

- **Presente**
  - `Admin/CalendarController.php`, `Admin/LessonCalendarController.php` + views in `resources/views/admin/calendar/*` e `resources/views/admin/lessons/calendar.blade.php`
  - Models: `CalendarLesson`, `CalendarSuspension`
- **Gap**
  - Allineamento “cicli 11 settimane” e “lezioni libere” AS‑IS (da verificare).

## A10 — Modulo Anagrafica e Disponibilità (import)

- **Presente**
  - `StudentAvailability` + UI admin
  - Import ODS: `app/Services/OdsImportService.php`, comando `php artisan ods:import`
- **Gap**
  - Rifiniture sull’import XLSX (reportistica e casi sporchi) se emergono nuove casistiche.

## A11 — Statistiche Storiche

- **Presente**
  - Nessuna vista dedicata (oltre dashboard base).
- **Gap**
  - Pagina admin “Statistiche” + import/seed dati storici.

## A12 — Documenti e Modelli Contratti

- **Presente**
  - Model/migration `Document`
- **Gap**
  - Nessun gap bloccante: CRUD “Documenti” presente; resta possibile evoluzione su filtri/report più avanzati.

