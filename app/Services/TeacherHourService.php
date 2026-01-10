<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\TeacherHour;
use App\Models\Lesson;
use App\Models\AcademicYear;
use Carbon\Carbon;

class TeacherHourService
{
    /**
     * Calcola conto orario per un docente in un periodo
     */
    public function calculateHours(Teacher $teacher, AcademicYear $academicYear, $periodStart = null, $periodEnd = null)
    {
        if (!$periodStart) {
            $periodStart = $academicYear->start_date;
        }
        if (!$periodEnd) {
            $periodEnd = $academicYear->end_date;
        }
        
        // Ottieni lezioni completate nel periodo
        $lessons = Lesson::where('teacher_id', $teacher->id)
            ->orWhere('substitute_teacher_id', $teacher->id)
            ->where('completed', true)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get();
        
        $lessonsCount = $lessons->count();
        $hoursTotal = 0;
        
        foreach ($lessons as $lesson) {
            if ($lesson->time_start && $lesson->time_end) {
                $start = Carbon::parse($lesson->time_start);
                $end = Carbon::parse($lesson->time_end);
                $hoursTotal += $start->diffInMinutes($end) / 60;
            } else {
                // Default: 1 ora per lezione
                $hoursTotal += 1;
            }
        }
        
        // Calcola tariffa oraria (da configurazione docente o default)
        $hourlyRate = $this->getHourlyRate($teacher);
        
        // Calcola importo base
        $baseAmount = $hoursTotal * $hourlyRate;
        
        // Bonus e forfait (da configurazione)
        $bonusAmount = $this->calculateBonus($teacher, $lessonsCount, $hoursTotal);
        $forfaitAmount = $this->calculateForfait($teacher);
        
        $totalAmount = $baseAmount + $bonusAmount + $forfaitAmount;
        
        // Crea o aggiorna conto orario
        $teacherHour = TeacherHour::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'academic_year_id' => $academicYear->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ],
            [
                'lessons_count' => $lessonsCount,
                'hours_total' => round($hoursTotal, 2),
                'hourly_rate' => $hourlyRate,
                'base_amount' => round($baseAmount, 2),
                'bonus_amount' => round($bonusAmount, 2),
                'forfait_amount' => round($forfaitAmount, 2),
                'total_amount' => round($totalAmount, 2),
                'status' => 'calculated',
            ]
        );
        
        return $teacherHour;
    }
    
    /**
     * Ottiene tariffa oraria docente
     */
    protected function getHourlyRate(Teacher $teacher)
    {
        // TODO: Aggiungere campo hourly_rate al model Teacher
        // Per ora usa default o da configurazione
        return 25.00; // Default 25€/ora
    }
    
    /**
     * Calcola bonus a consuntivo
     */
    protected function calculateBonus(Teacher $teacher, $lessonsCount, $hoursTotal)
    {
        // TODO: Logica bonus personalizzata
        // Esempio: bonus se supera X lezioni
        if ($lessonsCount >= 100) {
            return 200.00; // Bonus 200€ per 100+ lezioni
        }
        return 0;
    }
    
    /**
     * Calcola voci forfettarie
     */
    protected function calculateForfait(Teacher $teacher)
    {
        // TODO: Voci forfettarie da configurazione
        return 0;
    }
    
    /**
     * Calcola conto orario per tutti i docenti
     */
    public function calculateAllTeachers(AcademicYear $academicYear, $periodStart = null, $periodEnd = null)
    {
        $teachers = Teacher::active()->get();
        $results = [];
        
        foreach ($teachers as $teacher) {
            $results[] = $this->calculateHours($teacher, $academicYear, $periodStart, $periodEnd);
        }
        
        return $results;
    }
}

