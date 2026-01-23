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
        'photo_consent',
        'last_contact_date',
    ];

    protected $casts = [
        'privacy_consent' => 'boolean',
        'photo_consent' => 'boolean',
        'last_contact_date' => 'date',
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

