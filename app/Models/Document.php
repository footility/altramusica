<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'contract_id',
        'type',
        'visible_to_family',
        'file_path',
        'file_name',
        'mime_type',
        'size',
        'uploaded_by_user_id',
    ];

    protected $casts = [
        'size' => 'integer',
        'visible_to_family' => 'boolean',
    ];

    /** R13 — documenti esplicitamente condivisi con l'area famiglie (fail-safe: default false). */
    public function scopeVisibleToFamily($query)
    {
        return $query->where('visible_to_family', true);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
