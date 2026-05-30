<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * R13 (#8539) — Singolo messaggio nel thread di una richiesta famiglia.
 * `author_role` distingue famiglia e segreteria per il rendering e l'audit.
 */
class FamilyRequestMessage extends Model
{
    use HasFactory;

    public const ROLE_FAMILY = 'family';
    public const ROLE_STAFF = 'staff';

    protected $fillable = [
        'family_request_id',
        'user_id',
        'author_role',
        'body',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(FamilyRequest::class, 'family_request_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isFamily(): bool
    {
        return $this->author_role === self::ROLE_FAMILY;
    }
}
