<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GuardianInvitation extends Model
{
    use HasFactory;

    /** Validità di default del token di invito (giorni). */
    public const EXPIRES_DAYS = 7;

    protected $fillable = [
        'guardian_id',
        'email',
        'token',
        'expires_at',
        'accepted_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /** Invito ancora utilizzabile: non scaduto e non già accettato. */
    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isExpired();
    }

    public function scopePending($query)
    {
        return $query->whereNull('accepted_at')->where('expires_at', '>', now());
    }

    /** Genera un nuovo invito monouso per un tutore. */
    public static function generateFor(Guardian $guardian, string $email, ?int $createdByUserId = null): self
    {
        // Invalida eventuali inviti pendenti precedenti (un solo token vivo per volta).
        $guardian->invitations()->pending()->update(['expires_at' => now()]);

        return static::create([
            'guardian_id' => $guardian->id,
            'email' => $email,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(self::EXPIRES_DAYS),
            'created_by_user_id' => $createdByUserId,
        ]);
    }
}
