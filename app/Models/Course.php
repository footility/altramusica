<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_type_id',
        'teacher_id',
        'code',
        'name',
        'description',
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
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'max_students' => 'integer',
        'current_students' => 'integer',
        'price_per_lesson' => 'decimal:2',
        'lessons_per_week' => 'integer',
        'weeks_per_year' => 'integer',
    ];

    // Relationships
    public function courseType()
    {
        return $this->belongsTo(CourseType::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }
}
