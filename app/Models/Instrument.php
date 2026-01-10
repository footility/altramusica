<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'brand',
        'model',
        'size',
        'serial_number',
        'condition',
        'supplier',
        'purchase_date',
        'purchase_price',
        'current_value',
        'status',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function rentals()
    {
        return $this->hasMany(InstrumentRental::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
