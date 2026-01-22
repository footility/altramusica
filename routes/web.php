<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Auth::routes();

// Teacher routes
Route::middleware(['auth'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('register', [\App\Http\Controllers\Teacher\RegisterController::class, 'index'])->name('register.index');
    Route::get('register/{lesson}', [\App\Http\Controllers\Teacher\RegisterController::class, 'show'])->name('register.show');
    Route::post('register/{lesson}/attendance', [\App\Http\Controllers\Teacher\RegisterController::class, 'updateAttendance'])->name('register.attendance');
});

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
    Route::resource('first-contacts', \App\Http\Controllers\Admin\FirstContactController::class)->only(['index', 'show']);
    Route::post('first-contacts/{firstContact}/convert', [\App\Http\Controllers\Admin\FirstContactController::class, 'convert'])->name('first-contacts.convert');
    Route::post('first-contacts/{firstContact}/dismiss', [\App\Http\Controllers\Admin\FirstContactController::class, 'dismiss'])->name('first-contacts.dismiss');
    Route::get('first-contacts/{firstContact}/generate-link', [\App\Http\Controllers\Admin\FirstContactController::class, 'generateLink'])->name('first-contacts.generate-link');
    Route::resource('schedule-proposals', \App\Http\Controllers\Admin\ScheduleProposalController::class);
    Route::post('schedule-proposals/{scheduleProposal}/accept', [\App\Http\Controllers\Admin\ScheduleProposalController::class, 'accept'])->name('schedule-proposals.accept');
    Route::post('schedule-proposals/{scheduleProposal}/reject', [\App\Http\Controllers\Admin\ScheduleProposalController::class, 'reject'])->name('schedule-proposals.reject');
    Route::resource('communications', \App\Http\Controllers\Admin\CommunicationController::class);
    Route::get('communications/bulk/create', [\App\Http\Controllers\Admin\CommunicationController::class, 'bulk'])->name('communications.bulk');
    Route::post('communications/bulk', [\App\Http\Controllers\Admin\CommunicationController::class, 'sendBulk'])->name('communications.send-bulk');
    Route::resource('attendances', \App\Http\Controllers\Admin\AttendanceController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::resource('teacher-hours', \App\Http\Controllers\Admin\TeacherHourController::class)->only(['index', 'show']);
    Route::post('teacher-hours/calculate', [\App\Http\Controllers\Admin\TeacherHourController::class, 'calculate'])->name('teacher-hours.calculate');
    Route::post('teacher-hours/calculate-all', [\App\Http\Controllers\Admin\TeacherHourController::class, 'calculateAll'])->name('teacher-hours.calculate-all');
    Route::post('teacher-hours/{teacherHour}/approve', [\App\Http\Controllers\Admin\TeacherHourController::class, 'approve'])->name('teacher-hours.approve');
    Route::post('teacher-hours/{teacherHour}/mark-paid', [\App\Http\Controllers\Admin\TeacherHourController::class, 'markAsPaid'])->name('teacher-hours.mark-paid');
    Route::resource('guardians', \App\Http\Controllers\Admin\GuardianController::class);
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
    Route::resource('enrollments', \App\Http\Controllers\Admin\EnrollmentController::class);
    Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
    Route::post('invoices/{invoice}/payment-plan', [\App\Http\Controllers\Admin\InvoiceController::class, 'createPaymentPlan'])->name('invoices.payment-plan');
    Route::post('invoices/{invoice}/payment', [\App\Http\Controllers\Admin\InvoiceController::class, 'recordPayment'])->name('invoices.payment');
    Route::post('contracts/{contract}/create-invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'createFromContract'])->name('contracts.create-invoice');
    Route::resource('instruments', \App\Http\Controllers\Admin\InstrumentController::class);
    Route::resource('contracts', \App\Http\Controllers\Admin\ContractController::class);
    Route::post('contracts/{contract}/send', [\App\Http\Controllers\Admin\ContractController::class, 'send'])->name('contracts.send');
    Route::post('contracts/{contract}/sign', [\App\Http\Controllers\Admin\ContractController::class, 'sign'])->name('contracts.sign');
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    Route::resource('extra-activities', \App\Http\Controllers\Admin\ExtraActivityController::class);
    Route::resource('classrooms', \App\Http\Controllers\Admin\ClassroomController::class);

    // AS-IS missing CRUDs (Fase 1)
    Route::resource('books', \App\Http\Controllers\Admin\BookController::class);
    Route::resource('book-distributions', \App\Http\Controllers\Admin\BookDistributionController::class);
    Route::resource('instrument-rentals', \App\Http\Controllers\Admin\InstrumentRentalController::class);
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);
});
