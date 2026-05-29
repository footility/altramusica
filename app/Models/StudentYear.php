<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'code',
        'status',
        'school_origin',
        'how_know_us',
        'preferences',
        'notes',
        'admin_notes',
        'privacy_consent',
        'privacy_consent_at',
        'privacy_policy_version',
        'photo_consent',
        'photo_consent_at',
        'last_contact_date',
        'withdrawn_at',
    ];

    protected $casts = [
        'privacy_consent' => 'boolean',
        'privacy_consent_at' => 'datetime',
        'photo_consent' => 'boolean',
        'photo_consent_at' => 'datetime',
        'last_contact_date' => 'date',
        'withdrawn_at' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopeForYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }
}

