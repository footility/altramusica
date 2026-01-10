<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Services\CalendarService;
use Carbon\Carbon;

class EnrollmentService
{
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Calcola il costo totale di un'iscrizione
     */
    public function calculateTotalCost(Enrollment $enrollment): float
    {
        $course = $enrollment->course;
        $academicYear = $enrollment->academicYear ?? AcademicYear::getCurrent();
        
        if (!$academicYear || !$course) {
            return 0;
        }

        // Calcola numero di settimane
        $weeks = $this->calendarService->countWeeksForDay(
            $academicYear,
            $course->day_of_week,
            $enrollment->start_date,
            $enrollment->end_date
        );

        // Calcola costo totale
        $totalCost = $weeks * $course->price_per_lesson * $course->lessons_per_week;

        // Applica sconti
        if ($enrollment->discount_percentage) {
            $totalCost = $totalCost * (1 - $enrollment->discount_percentage / 100);
        }
        
        if ($enrollment->discount_amount) {
            $totalCost = $totalCost - $enrollment->discount_amount;
        }

        return max(0, $totalCost);
    }

    /**
     * Crea una nuova iscrizione con calcolo automatico costi
     */
    public function createEnrollment(array $data): Enrollment
    {
        $enrollment = Enrollment::create($data);
        
        // Calcola e aggiorna il totale
        $totalCost = $this->calculateTotalCost($enrollment);
        $enrollment->update(['total_amount' => $totalCost]);

        return $enrollment;
    }

    /**
     * Aggiorna un'iscrizione e ricalcola i costi
     */
    public function updateEnrollment(Enrollment $enrollment, array $data): Enrollment
    {
        $enrollment->update($data);
        
        // Ricalcola il totale
        $totalCost = $this->calculateTotalCost($enrollment);
        $enrollment->update(['total_amount' => $totalCost]);

        return $enrollment;
    }
}

