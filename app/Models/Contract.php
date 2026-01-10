<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'contract_number',
        'type',
        'start_date',
        'end_date',
        'status',
        'sent_date',
        'signed_date',
        'terms',
        'notes',
        'token', // Per link precompilati
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sent_date' => 'date',
        'signed_date' => 'date',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function($q) {
                $q->where('end_date', '<', now())
                  ->where('status', '!=', 'cancelled');
            });
    }
}
