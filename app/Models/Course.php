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
        'code',
        'name',
        'description',
    ];

    protected $casts = [
    ];

    // Relationships
    public function courseType()
    {
        return $this->belongsTo(CourseType::class);
    }

    public function offerings()
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function enrollments()
    {
        return $this->hasManyThrough(
            Enrollment::class,
            CourseOffering::class,
            'course_id',          // course_offerings.course_id
            'course_offering_id', // enrollments.course_offering_id
            'id',                 // courses.id
            'id'                  // course_offerings.id
        );
    }

    // Scopes
    // NOTE: lo status è sull'offerta annuale (CourseOffering), non sul catalogo (Course).
    public function scopeActive($query)
    {
        return $query->whereHas('offerings', function ($q) {
            $q->where('status', 'active');
        });
    }
}
