<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_type',
        'level',
        'subject',
        'exam_date',
        'registration_date',
        'registration_fee',
        'result',
        'grade',
        'certificate_number',
        'notes',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'registration_date' => 'date',
        'level' => 'integer',
        'registration_fee' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
