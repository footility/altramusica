<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FirstContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'first_name',
        'last_name',
        'birth_date',
        'phone',
        'email',
        'notes',
        'status',
        'student_id',
        'converted_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'converted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($firstContact) {
            if (empty($firstContact->token)) {
                $firstContact->token = Str::random(32);
            }
        });
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    // Helper methods
    public function convertToStudent($academicYearId = null)
    {
        if ($this->status === 'converted' && $this->student_id) {
            return $this->student;
        }

        if (!$academicYearId) {
            $academicYear = AcademicYear::where('is_active', true)->first();
            $academicYearId = $academicYear ? $academicYear->id : null;
        }

        $student = Student::create([
            'academic_year_id' => $academicYearId,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birth_date' => $this->birth_date,
            'status' => 'prospect',
            'notes' => $this->notes,
            'last_contact_date' => now(),
        ]);

        $this->update([
            'status' => 'converted',
            'student_id' => $student->id,
            'converted_at' => now(),
        ]);

        return $student;
    }
}
