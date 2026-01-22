<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Auth::routes();

// NOTE (Fase 1): rimosse le rotte "teacher/register" (registro docente) perché non AS-IS.

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
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
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
    Route::resource('enrollments', \App\Http\Controllers\Admin\EnrollmentController::class);
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
    Route::post('invoices/{invoice}/payment-plan', [\App\Http\Controllers\Admin\InvoiceController::class, 'createPaymentPlan'])->name('invoices.payment-plan');
    Route::post('invoices/{invoice}/payment', [\App\Http\Controllers\Admin\InvoiceController::class, 'recordPayment'])->name('invoices.payment');
    Route::get('payment-plans', [\App\Http\Controllers\Admin\PaymentPlanController::class, 'index'])->name('payment-plans.index');
    Route::get('accounting/balances', [\App\Http\Controllers\Admin\AccountingReportController::class, 'balances'])->name('accounting.balances');
    Route::post('contracts/{contract}/create-invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'createFromContract'])->name('contracts.create-invoice');
    Route::resource('instruments', \App\Http\Controllers\Admin\InstrumentController::class);
    Route::resource('contracts', \App\Http\Controllers\Admin\ContractController::class);
    Route::post('contracts/{contract}/send', [\App\Http\Controllers\Admin\ContractController::class, 'send'])->name('contracts.send');
    Route::post('contracts/{contract}/sign', [\App\Http\Controllers\Admin\ContractController::class, 'sign'])->name('contracts.sign');
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    // NOTE (Fase 1): rimosso extra-activities (evolutivo, non AS-IS).
    Route::resource('classrooms', \App\Http\Controllers\Admin\ClassroomController::class);

    // AS-IS missing CRUDs (Fase 1)
    Route::resource('books', \App\Http\Controllers\Admin\BookController::class);
    Route::resource('book-distributions', \App\Http\Controllers\Admin\BookDistributionController::class);
    Route::resource('instrument-rentals', \App\Http\Controllers\Admin\InstrumentRentalController::class);
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);
});
