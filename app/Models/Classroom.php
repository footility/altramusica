<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'capacity',
        'equipment',
        'available',
        'notes',
    ];

    protected $casts = [
        'equipment' => 'array',
        'available' => 'boolean',
    ];

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }

    // Helper methods
    public function isAvailableAt($date, $timeStart, $timeEnd)
    {
        // TODO: Verifica conflitti con lezioni esistenti
        return $this->available;
    }
}
