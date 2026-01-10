<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'instrument_type',
        'level_abrsm_instrument',
        'level_abrsm_theory',
        'abrsm_id',
        'lcm_id',
        'notes',
    ];

    protected $casts = [
        'level_abrsm_instrument' => 'integer',
        'level_abrsm_theory' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
