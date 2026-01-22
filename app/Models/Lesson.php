<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'teacher_id',
        'substitute_teacher_id',
        'date',
        'time_start',
        'time_end',
        'notes',
        'completed',
    ];

    protected $casts = [
        'date' => 'date',
        // TIME columns -> string
        'time_start' => 'string',
        'time_end' => 'string',
        'completed' => 'boolean',
    ];

    // Relationships
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    public function course()
    {
        // Compatibilità: lesson->course (catalogo) tramite offering
        return $this->hasOneThrough(
            Course::class,
            CourseOffering::class,
            'id',                 // course_offerings.id
            'id',                 // courses.id
            'course_offering_id', // lessons.course_offering_id
            'course_id'           // course_offerings.course_id
        );
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function substituteTeacher()
    {
        return $this->belongsTo(Teacher::class, 'substitute_teacher_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // NOTE (Fase 1): rimosse relazioni verso moduli extra non AS-IS (presenze).

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('completed', false);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId)
            ->orWhere('substitute_teacher_id', $teacherId);
    }

    // Helper methods
    public function getDurationAttribute()
    {
        if (!$this->time_start || !$this->time_end) {
            return null;
        }

        // time_start/time_end are strings like "17:00:00"
        try {
            $start = \Carbon\Carbon::createFromFormat('H:i:s', $this->time_start);
            $end = \Carbon\Carbon::createFromFormat('H:i:s', $this->time_end);
            return $start->diffInMinutes($end);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function markAsCompleted()
    {
        $this->update(['completed' => true]);
    }
}
