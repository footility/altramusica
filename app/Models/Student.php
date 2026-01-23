<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'age',
        'tax_code',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // Relationships
    public function years()
    {
        return $this->hasMany(StudentYear::class);
    }

    public function currentYear()
    {
        return $this->hasOne(StudentYear::class)->whereHas('academicYear', function ($q) {
            $q->where('is_active', true);
        });
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'student_guardian')
            ->withPivot('relationship_type', 'is_primary', 'is_billing_contact')
            ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function instrumentRentals()
    {
        return $this->hasMany(InstrumentRental::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function availability()
    {
        return $this->hasMany(StudentAvailability::class);
    }

    public function levels()
    {
        return $this->hasMany(StudentLevel::class);
    }

    // NOTE (Fase 1): rimosse relazioni verso moduli extra non AS-IS (comunicazioni/presenze).

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function bookDistributions()
    {
        return $this->hasMany(BookDistribution::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getStatusAttribute()
    {
        return $this->currentYear?->status;
    }

    public function getCodeAttribute()
    {
        return $this->currentYear?->code;
    }
}
