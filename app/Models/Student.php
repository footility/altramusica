<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'code',
        'first_name',
        'last_name',
        'birth_date',
        'age',
        'tax_code',
        'status',
        'school_origin',
        'how_know_us',
        'preferences',
        'notes',
        'admin_notes',
        'privacy_consent',
        'photo_consent',
        'last_contact_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_contact_date' => 'date',
        'privacy_consent' => 'boolean',
        'photo_consent' => 'boolean',
    ];

    // Relationships
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
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

    public function extraActivityEnrollments()
    {
        return $this->hasMany(ExtraActivityEnrollment::class);
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

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

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

    // Scopes
    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['enrolled', 'interested']);
    }
}
