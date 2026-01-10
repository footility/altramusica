<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'guardian_id',
        'type',
        'template_name',
        'subject',
        'message',
        'recipients',
        'sent_at',
        'sent_by_user_id',
        'status',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'recipients' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }
}
