<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'tax_code',
        'relationship',
        'phone_home',
        'phone_work',
        'cell_1',
        'cell_2',
        'cell_3',
        'cell_4',
        'email_1',
        'email_2',
        'email_3',
        'address',
        'city',
        'postal_code',
        'country',
        'privacy_consent',
    ];

    protected $casts = [
        'privacy_consent' => 'boolean',
    ];

    // Relationships
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_guardian')
            ->withPivot('relationship_type', 'is_primary', 'is_billing_contact')
            ->withTimestamps();
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getPrimaryEmailAttribute()
    {
        return $this->email_1 ?? $this->email_2 ?? $this->email_3;
    }

    public function getPrimaryPhoneAttribute()
    {
        return $this->cell_1 ?? $this->cell_2 ?? $this->phone_home ?? $this->phone_work;
    }
}
