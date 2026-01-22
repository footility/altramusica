<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseOffering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'academic_year_id',
        'teacher_id',
        'classroom_id',
        'start_date',
        'end_date',
        'day_of_week',
        'time_start',
        'time_end',
        'max_students',
        'current_students',
        'status',
        'price_per_lesson',
        'lessons_per_week',
        'weeks_per_year',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        // TIME columns -> string
        'time_start' => 'string',
        'time_end' => 'string',
        'max_students' => 'integer',
        'current_students' => 'integer',
        'price_per_lesson' => 'decimal:2',
        'lessons_per_week' => 'integer',
        'weeks_per_year' => 'integer',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_offering_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

