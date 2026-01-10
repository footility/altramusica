<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraActivityEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'extra_activity_id',
        'enrollment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function extraActivity()
    {
        return $this->belongsTo(ExtraActivity::class);
    }
}
