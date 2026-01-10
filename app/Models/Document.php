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
        'file_path',
        'file_name',
        'mime_type',
        'size',
        'uploaded_by_user_id',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

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
