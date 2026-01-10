<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'teacher_id',
        'day_of_week',
        'time_start',
        'time_end',
        'status',
        'notes',
        'accepted_by',
        'accepted_at',
    ];

    protected $casts = [
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeProposed($query)
    {
        return $query->where('status', 'proposed');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    // Helper methods
    public function accept($userId = null)
    {
        $this->update([
            'status' => 'accepted',
            'accepted_by' => $userId ?? auth()->id(),
            'accepted_at' => now(),
        ]);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }
}
