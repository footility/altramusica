<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'instrument_id',
        'start_date',
        'end_date',
        'monthly_fee',
        'deposit',
        'status',
        'return_date',
        'return_condition',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'return_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'deposit' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
