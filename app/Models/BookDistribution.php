<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'book_id',
        'academic_year_id',
        'course_offering_id',
        'distribution_date',
        'quantity',
        'price_paid',
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'quantity' => 'integer',
        'price_paid' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function course()
    {
        // Compatibilità: accesso al catalogo corso tramite offering
        return $this->hasOneThrough(
            Course::class,
            CourseOffering::class,
            'id',                 // course_offerings.id
            'id',                 // courses.id
            'course_offering_id', // book_distributions.course_offering_id
            'course_id'           // course_offerings.course_id
        );
    }
}
