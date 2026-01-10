<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'duration_minutes',
        'max_students',
        'price_full',
        'price_reduced',
        'price_annual',
        'price_monthly',
        'active',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'max_students' => 'integer',
        'price_full' => 'decimal:2',
        'price_reduced' => 'decimal:2',
        'price_annual' => 'decimal:2',
        'price_monthly' => 'decimal:2',
        'active' => 'boolean',
    ];

    // Relationships
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
