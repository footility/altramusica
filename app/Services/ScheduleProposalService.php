<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentAvailability;
use App\Models\TeacherAvailability;
use App\Models\ScheduleProposal;
use App\Models\Course;
use Carbon\Carbon;

class ScheduleProposalService
{
    /**
     * Genera proposte orarie per uno o più studenti
     * 
     * @param array $studentIds Array di ID studenti
     * @param array $options Opzioni (course_id, teacher_id, etc.)
     * @return array Array di ScheduleProposal creati
     */
    public function generateProposals(array $studentIds, array $options = [])
    {
        $proposals = [];
        
        foreach ($studentIds as $studentId) {
            $student = Student::findOrFail($studentId);
            
            // Ottieni disponibilità studente
            $studentAvailabilities = StudentAvailability::where('student_id', $student->id)
                ->where('available', true)
                ->get();
            
            if ($studentAvailabilities->isEmpty()) {
                continue;
            }
            
            // Per ogni disponibilità studente, trova docenti disponibili
            foreach ($studentAvailabilities as $studentAvail) {
                $dayOfWeek = $studentAvail->day_of_week;
                $timeStart = $studentAvail->time_start;
                $timeEnd = $studentAvail->time_end;
                
                // Se non c'è orario specifico, usa range default (14:00-20:00)
                if (!$timeStart) {
                    $timeStart = Carbon::parse('14:00:00')->toTimeString();
                    $timeEnd = Carbon::parse('20:00:00')->toTimeString();
                } else {
                    $timeStart = $timeStart instanceof Carbon ? $timeStart->toTimeString() : $timeStart;
                    $timeEnd = $timeEnd instanceof Carbon ? $timeEnd->toTimeString() : $timeEnd;
                }
                
                // Trova docenti disponibili in questo giorno/orario
                $availableTeachers = $this->findAvailableTeachers($dayOfWeek, $timeStart, $timeEnd, $options);
                
                foreach ($availableTeachers as $teacher) {
                    // Verifica conflitti
                    if ($this->hasConflict($teacher->id, $dayOfWeek, $timeStart, $timeEnd)) {
                        continue;
                    }
                    
                    // Crea proposta
                    $proposal = ScheduleProposal::create([
                        'student_id' => $student->id,
                        'course_id' => $options['course_id'] ?? null,
                        'teacher_id' => $teacher->id,
                        'day_of_week' => $dayOfWeek,
                        'time_start' => $timeStart,
                        'time_end' => $timeEnd,
                        'status' => 'draft',
                        'notes' => $options['notes'] ?? null,
                    ]);
                    
                    $proposals[] = $proposal;
                }
            }
        }
        
        return $proposals;
    }
    
    /**
     * Trova docenti disponibili per giorno/orario
     */
    protected function findAvailableTeachers($dayOfWeek, $timeStart, $timeEnd, $options = [])
    {
        $query = Teacher::query();
        
        // Filtro per docente specifico se richiesto
        if (!empty($options['teacher_id'])) {
            $query->where('id', $options['teacher_id']);
        }
        
        $teachers = $query->active()->get();
        $availableTeachers = [];
        
        foreach ($teachers as $teacher) {
            $teacherAvail = TeacherAvailability::where('teacher_id', $teacher->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('available', true)
                ->first();
            
            if (!$teacherAvail) {
                continue;
            }
            
            // Verifica che l'orario studente sia compatibile con disponibilità docente
            $teacherTimeStart = $teacherAvail->time_start ? Carbon::parse($teacherAvail->time_start) : Carbon::parse('08:00:00');
            $teacherTimeEnd = $teacherAvail->time_end ? Carbon::parse($teacherAvail->time_end) : Carbon::parse('22:00:00');
            
            $studentTimeStart = is_string($timeStart) ? Carbon::parse($timeStart) : $timeStart;
            $studentTimeEnd = is_string($timeEnd) ? Carbon::parse($timeEnd) : $timeEnd;
            
            if (!$studentTimeStart instanceof Carbon) {
                $studentTimeStart = Carbon::parse($studentTimeStart);
            }
            if (!$studentTimeEnd instanceof Carbon) {
                $studentTimeEnd = Carbon::parse($studentTimeEnd);
            }
            
            // Verifica sovrapposizione orari
            if ($studentTimeStart->gte($teacherTimeStart) && $studentTimeEnd->lte($teacherTimeEnd)) {
                $availableTeachers[] = $teacher;
            }
        }
        
        return $availableTeachers;
    }
    
    /**
     * Verifica conflitti (docente già impegnato in questo orario)
     */
    protected function hasConflict($teacherId, $dayOfWeek, $timeStart, $timeEnd)
    {
        // Verifica proposte già accettate
        $conflict = ScheduleProposal::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('status', 'accepted')
            ->where(function($q) use ($timeStart, $timeEnd) {
                $q->whereBetween('time_start', [$timeStart, $timeEnd])
                  ->orWhereBetween('time_end', [$timeStart, $timeEnd])
                  ->orWhere(function($q2) use ($timeStart, $timeEnd) {
                      $q2->where('time_start', '<=', $timeStart)
                        ->where('time_end', '>=', $timeEnd);
                  });
            })
            ->exists();
        
        if ($conflict) {
            return true;
        }
        
        // Verifica corsi esistenti del docente
        $courseConflict = Course::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where(function($q) use ($timeStart, $timeEnd) {
                $courseTimeStart = $q->getModel()->getTable() . '.time_start';
                $courseTimeEnd = $q->getModel()->getTable() . '.time_end';
                $q->whereBetween($courseTimeStart, [$timeStart, $timeEnd])
                  ->orWhereBetween($courseTimeEnd, [$timeStart, $timeEnd]);
            })
            ->exists();
        
        return $courseConflict;
    }
}

