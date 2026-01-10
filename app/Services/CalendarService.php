<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\CalendarLesson;
use App\Models\CalendarSuspension;
use Carbon\Carbon;

class CalendarService
{
    /**
     * Genera tutti i giorni di lezione per un anno scolastico
     */
    public function generateLessonsForYear(AcademicYear $year, array $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']): int
    {
        $count = 0;
        $currentDate = $year->start_date->copy();
        $endDate = $year->end_date->copy();

        while ($currentDate <= $endDate) {
            $dayName = strtolower($currentDate->format('l')); // monday, tuesday, etc.
            
            if (in_array($dayName, $daysOfWeek)) {
                // Verifica se è in una sospensione
                $isSuspended = CalendarSuspension::forYear($year->id)
                    ->where('start_date', '<=', $currentDate)
                    ->where('end_date', '>=', $currentDate)
                    ->exists();

                CalendarLesson::updateOrCreate(
                    [
                        'academic_year_id' => $year->id,
                        'date' => $currentDate->format('Y-m-d'),
                    ],
                    [
                        'day_of_week' => $dayName,
                        'is_active' => !$isSuspended,
                    ]
                );
                $count++;
            }

            $currentDate->addDay();
        }

        return $count;
    }

    /**
     * Applica sospensioni a un anno scolastico
     */
    public function applySuspension(AcademicYear $year, string $name, Carbon $startDate, Carbon $endDate, string $notes = ''): CalendarSuspension
    {
        $suspension = CalendarSuspension::create([
            'academic_year_id' => $year->id,
            'name' => $name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'notes' => $notes,
        ]);

        // Disattiva i giorni di lezione nella sospensione
        CalendarLesson::forYear($year->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->update(['is_active' => false]);

        return $suspension;
    }

    /**
     * Conta le settimane di lezione per un giorno specifico in un periodo
     */
    public function countWeeksForDay(AcademicYear $year, string $dayOfWeek, Carbon $startDate, Carbon $endDate = null): int
    {
        return CalendarLesson::countActiveWeeks(
            $year->id,
            $dayOfWeek,
            $startDate,
            $endDate ?? $year->end_date
        );
    }

    /**
     * Calcola il numero totale di lezioni per un corso
     */
    public function calculateTotalLessons(AcademicYear $year, string $dayOfWeek, Carbon $startDate, Carbon $endDate = null): int
    {
        return $this->countWeeksForDay($year, $dayOfWeek, $startDate, $endDate);
    }
}

