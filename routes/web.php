<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Auth::routes();

// Pagine privacy/cookie pubbliche (accessibili senza autenticazione)
Route::get('/privacy-policy', [\App\Http\Controllers\Public\PrivacyController::class, 'policy'])->name('privacy.policy');
Route::get('/cookie-policy', [\App\Http\Controllers\Public\PrivacyController::class, 'cookies'])->name('privacy.cookies');

// NOTE (Fase 1): rimosse le rotte "teacher/register" (registro docente) perché non AS-IS.

Route::middleware(['auth', 'block.family'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Academic Years
    Route::resource('academic-years', \App\Http\Controllers\Admin\AcademicYearController::class);
    Route::post('academic-years/{academicYear}/set-active', [\App\Http\Controllers\Admin\AcademicYearController::class, 'setActive'])->name('academic-years.set-active');
    
    // Calendar
    Route::get('calendar', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/events', [\App\Http\Controllers\Admin\CalendarController::class, 'events'])->name('calendar.events');
    Route::post('calendar/generate', [\App\Http\Controllers\Admin\CalendarController::class, 'generate'])->name('calendar.generate');
    Route::get('calendar/suspensions/create', [\App\Http\Controllers\Admin\CalendarController::class, 'createSuspension'])->name('calendar.suspensions.create');
    Route::post('calendar/suspensions', [\App\Http\Controllers\Admin\CalendarController::class, 'storeSuspension'])->name('calendar.suspensions.store');
    Route::delete('calendar/suspensions/{suspension}', [\App\Http\Controllers\Admin\CalendarController::class, 'destroySuspension'])->name('calendar.suspensions.destroy');
    
    // Lesson Calendar (visualizzazione calendario lezioni)
    Route::get('lessons/calendar', [\App\Http\Controllers\Admin\LessonCalendarController::class, 'index'])->name('lessons.calendar');
    Route::get('lessons/calendar/events', [\App\Http\Controllers\Admin\LessonCalendarController::class, 'events'])->name('lessons.calendar.events');
    
    // Resources
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::resource('student-availability', \App\Http\Controllers\Admin\StudentAvailabilityController::class);
    Route::resource('teacher-availability', \App\Http\Controllers\Admin\TeacherAvailabilityController::class);
    // NOTE (Fase 1): rimosse rotte extra non AS-IS:
    // - first-contacts (primo contatto evolutivo)
    // - schedule-proposals (proposte orarie evolutive)
    // - communications (comunicazioni evolutive)
    // - attendances (registro/presenze evolutivo)
    // - teacher-hours (conto ore evolutivo)
    Route::resource('guardians', \App\Http\Controllers\Admin\GuardianController::class);
    // R13 — invito del tutore all'area famiglie (token monouso)
    Route::post('guardians/{guardian}/invite', [\App\Http\Controllers\Admin\GuardianInvitationController::class, 'store'])->name('guardians.invite');
    // R13 (#8539) — canale richieste famiglia: inbox segreteria + thread + stati
    Route::get('family-requests', [\App\Http\Controllers\Admin\FamilyRequestController::class, 'index'])->name('family-requests.index');
    Route::get('family-requests/{familyRequest}', [\App\Http\Controllers\Admin\FamilyRequestController::class, 'show'])->name('family-requests.show');
    Route::post('family-requests/{familyRequest}/reply', [\App\Http\Controllers\Admin\FamilyRequestController::class, 'reply'])->name('family-requests.reply');
    Route::patch('family-requests/{familyRequest}/status', [\App\Http\Controllers\Admin\FamilyRequestController::class, 'updateStatus'])->name('family-requests.status');
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
    Route::resource('enrollments', \App\Http\Controllers\Admin\EnrollmentController::class);
    // Contabilità — riservata a chi ha i permessi (la segreteria ne è esclusa).
    Route::middleware('permission:invoices.view')->group(function () {
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
        Route::post('invoices/{invoice}/payment-plan', [\App\Http\Controllers\Admin\InvoiceController::class, 'createPaymentPlan'])->name('invoices.payment-plan');
        Route::post('invoices/{invoice}/payment', [\App\Http\Controllers\Admin\InvoiceController::class, 'recordPayment'])->name('invoices.payment');
        Route::post('contracts/{contract}/create-invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'createFromContract'])->name('contracts.create-invoice');
    });
    Route::get('payment-plans', [\App\Http\Controllers\Admin\PaymentPlanController::class, 'index'])->middleware('permission:payment-plans.view')->name('payment-plans.index');
    Route::get('accounting/balances', [\App\Http\Controllers\Admin\AccountingReportController::class, 'balances'])->middleware('permission:accounting.view')->name('accounting.balances');
    Route::resource('instruments', \App\Http\Controllers\Admin\InstrumentController::class);
    Route::resource('contracts', \App\Http\Controllers\Admin\ContractController::class);
    Route::post('contracts/{contract}/send', [\App\Http\Controllers\Admin\ContractController::class, 'send'])->name('contracts.send');
    Route::post('contracts/{contract}/sign', [\App\Http\Controllers\Admin\ContractController::class, 'sign'])->name('contracts.sign');
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    // NOTE (Fase 1): rimosso extra-activities (evolutivo, non AS-IS).
    Route::resource('classrooms', \App\Http\Controllers\Admin\ClassroomController::class);

    // ACL — griglia ruoli/permessi configurabile dall'amministratore (solo chi gestisce ACL)
    Route::middleware('permission:acl.view')->group(function () {
        Route::get('acl', [\App\Http\Controllers\Admin\AclController::class, 'index'])->name('acl.index');
        Route::put('acl/{role}', [\App\Http\Controllers\Admin\AclController::class, 'update'])->name('acl.update');
        Route::post('acl/roles', [\App\Http\Controllers\Admin\AclController::class, 'storeRole'])->name('acl.roles.store');

        // Log accessi minimo
        Route::get('login-logs', [\App\Http\Controllers\Admin\LoginLogController::class, 'index'])->name('login-logs.index');
    });

    // AS-IS missing CRUDs (Fase 1)
    Route::resource('books', \App\Http\Controllers\Admin\BookController::class);
    Route::resource('book-distributions', \App\Http\Controllers\Admin\BookDistributionController::class);
    Route::resource('instrument-rentals', \App\Http\Controllers\Admin\InstrumentRentalController::class);
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);
});

/*
|--------------------------------------------------------------------------
| R13 — Area famiglie (#8537)
|--------------------------------------------------------------------------
| Guard web + ruolo `family`, isolata dal backoffice via prefix `famiglia`
| e middleware. Sola lettura, scopata sui figli del tutore (vedi
| docs/44_UX_AREA_FAMIGLIE_E_PRIVACY.md).
*/
Route::prefix('famiglia')->name('family.')->group(function () {
    // Accesso su invito + login (solo ospiti).
    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Family\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [\App\Http\Controllers\Family\AuthController::class, 'login'])->name('login.attempt');
        Route::get('attiva/{token}', [\App\Http\Controllers\Family\InvitationController::class, 'show'])->name('invitation.show');
        Route::post('attiva/{token}', [\App\Http\Controllers\Family\InvitationController::class, 'activate'])->name('invitation.activate');
    });

    Route::post('logout', [\App\Http\Controllers\Family\AuthController::class, 'logout'])->name('logout');

    // Area autenticata: solo account con ruolo family.
    Route::middleware(['auth', 'role:family'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Family\DashboardController::class, 'index'])->name('dashboard');
        Route::get('studente/{student}', [\App\Http\Controllers\Family\DashboardController::class, 'student'])->name('student');
        Route::get('documenti/{document}/download', [\App\Http\Controllers\Family\DocumentController::class, 'download'])->name('document.download');

        // R13 (#8539) — canale richieste/messaggi verso la segreteria.
        Route::get('richieste', [\App\Http\Controllers\Family\FamilyRequestController::class, 'index'])->name('requests.index');
        Route::get('richieste/nuova', [\App\Http\Controllers\Family\FamilyRequestController::class, 'create'])->name('requests.create');
        Route::post('richieste', [\App\Http\Controllers\Family\FamilyRequestController::class, 'store'])->name('requests.store');
        Route::get('richieste/{familyRequest}', [\App\Http\Controllers\Family\FamilyRequestController::class, 'show'])->name('requests.show');
        Route::post('richieste/{familyRequest}/messaggi', [\App\Http\Controllers\Family\FamilyRequestController::class, 'reply'])->name('requests.reply');
    });
});
