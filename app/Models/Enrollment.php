<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'course_offering_id',
        'enrollment_date',
        'start_date',
        'end_date',
        'status',
        'notes',
        'discount_percentage',
        'discount_amount',
        'total_amount',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function course()
    {
        // Compatibilità: enrollment->course (catalogo) tramite offering
        return $this->hasOneThrough(
            Course::class,
            CourseOffering::class,
            'id',            // course_offerings.id
            'id',            // courses.id
            'course_offering_id', // enrollments.course_offering_id
            'course_id'      // course_offerings.course_id
        );
    }

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
