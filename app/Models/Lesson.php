<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
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
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'completed' => 'boolean',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
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

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

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
        
        return $this->time_start->diffInMinutes($this->time_end);
    }

    public function markAsCompleted()
    {
        $this->update(['completed' => true]);
    }
}
