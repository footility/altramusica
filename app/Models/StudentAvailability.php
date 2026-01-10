<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAvailability extends Model
{
    use HasFactory;

    protected $table = 'student_availability';

    protected $fillable = [
        'student_id',
        'day_of_week',
        'time_start',
        'time_end',
        'available',
        'notes',
    ];

    protected $casts = [
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'available' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
