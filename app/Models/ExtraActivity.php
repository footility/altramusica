<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'teacher_id',
        'start_date',
        'end_date',
        'day_of_week',
        'time_start',
        'time_end',
        'price',
        'max_participants',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'price' => 'decimal:2',
        'max_participants' => 'integer',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments()
    {
        return $this->hasMany(ExtraActivityEnrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
