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
        'course_id',
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
