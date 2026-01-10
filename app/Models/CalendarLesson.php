<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CalendarLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'date',
        'day_of_week',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeForDayOfWeek($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    // Helper methods
    public static function getActiveDaysForYear($academicYearId, $dayOfWeek = null)
    {
        $query = static::forYear($academicYearId)->active();
        
        if ($dayOfWeek) {
            $query->forDayOfWeek($dayOfWeek);
        }
        
        return $query->orderBy('date')->get();
    }

    public static function countActiveWeeks($academicYearId, $dayOfWeek, $startDate, $endDate = null)
    {
        $query = static::forYear($academicYearId)
            ->forDayOfWeek($dayOfWeek)
            ->active()
            ->where('date', '>=', $startDate);
        
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }
        
        return $query->count();
    }
}
