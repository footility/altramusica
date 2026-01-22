# 03 - Demo operativa (codice) dalla Fase 1

Questo documento descrive cosa esiste davvero nel repository Laravel, in modo 1:1 con il codice.

## Struttura backend (admin)

Routes principali:
- `routes/web.php` (prefisso `admin`, middleware `auth`)

Controller admin:
- `app/Http/Controllers/Admin/AcademicYearController.php`
- `app/Http/Controllers/Admin/StudentController.php`
- `app/Http/Controllers/Admin/GuardianController.php`
- `app/Http/Controllers/Admin/TeacherController.php`
- `app/Http/Controllers/Admin/CourseController.php`
- `app/Http/Controllers/Admin/EnrollmentController.php`
- `app/Http/Controllers/Admin/ContractController.php`
- `app/Http/Controllers/Admin/InvoiceController.php`
- `app/Http/Controllers/Admin/CalendarController.php`
- `app/Http/Controllers/Admin/LessonCalendarController.php`
- `app/Http/Controllers/Admin/AttendanceController.php`
- `app/Http/Controllers/Admin/InstrumentController.php`
- `app/Http/Controllers/Admin/ExamController.php`
- `app/Http/Controllers/Admin/ExtraActivityController.php`
- `app/Http/Controllers/Admin/ClassroomController.php`
- `app/Http/Controllers/Admin/CommunicationController.php`
- `app/Http/Controllers/Admin/TeacherHourController.php`

Views:
- `resources/views/admin/*` (CRUD standard: index/create/edit/show)
- layout: `resources/views/layouts/admin.blade.php`

## Modelli dati (esempi principali)

- `app/Models/Student.php` (relazioni con `AcademicYear`, `Guardian`, `Enrollment`, `Invoice`, `Contract`, …)
- `app/Models/Guardian.php` (pivot `student_guardian`)
- `app/Models/Teacher.php`
- `app/Models/Contract.php`
- `app/Models/Invoice.php`, `app/Models/Payment.php`, `app/Models/PaymentPlan.php`

## Import dati (seeders)

Lo stato dei seeders va considerato parte della “demo operativa” perché la Fase 1 dipende dal caricamento dati da ODS.

Riferimento: `docs/ANALISI_COLONNE_ODS_COMPLETA.md` e i file in `docs/materiale cliente/`.

Nota: in questa fase l’obiettivo è poter rigenerare il DB e ricaricare i dati (processo ripetibile) per validare la migrazione.

