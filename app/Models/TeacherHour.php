<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'academic_year_id',
        'period_start',
        'period_end',
        'lessons_count',
        'hours_total',
        'hourly_rate',
        'base_amount',
        'bonus_amount',
        'forfait_amount',
        'total_amount',
        'status',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'hours_total' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'forfait_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCalculated($query)
    {
        return $query->where('status', 'calculated');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Helper methods
    public function approve($userId = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }
}
