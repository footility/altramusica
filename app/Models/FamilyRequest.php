<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * R13 (#8539) — Richiesta/messaggio della famiglia verso il gestionale.
 * Gli stati sono gestiti dalla segreteria; la famiglia può solo aprire e
 * rispondere finché la richiesta non è chiusa.
 */
class FamilyRequest extends Model
{
    use HasFactory;

    /** Stati gestiti dalla segreteria. */
    public const STATUS_NEW = 'nuova';
    public const STATUS_IN_PROGRESS = 'in_lavorazione';
    public const STATUS_WAITING_FAMILY = 'in_attesa_famiglia';
    public const STATUS_RESOLVED = 'risolta';
    public const STATUS_CLOSED = 'chiusa';

    public const STATUSES = [
        self::STATUS_NEW => 'Nuova',
        self::STATUS_IN_PROGRESS => 'In lavorazione',
        self::STATUS_WAITING_FAMILY => 'In attesa della famiglia',
        self::STATUS_RESOLVED => 'Risolta',
        self::STATUS_CLOSED => 'Chiusa',
    ];

    /** Categorie selezionabili dalla famiglia all'apertura. */
    public const CATEGORIES = [
        'amministrativa' => 'Amministrativa / segreteria',
        'didattica' => 'Didattica / corsi',
        'pagamenti' => 'Pagamenti e fatture',
        'privacy' => 'Privacy / GDPR',
        'altro' => 'Altro',
    ];

    protected $fillable = [
        'guardian_id',
        'student_id',
        'category',
        'subject',
        'status',
        'assigned_to_user_id',
        'last_message_at',
        'last_message_role',
        'resolved_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(FamilyRequestMessage::class)->orderBy('created_at');
    }

    /** La famiglia può ancora scrivere finché la richiesta non è chiusa. */
    public function isOpenForFamily(): bool
    {
        return $this->status !== self::STATUS_CLOSED;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'bg-primary',
            self::STATUS_IN_PROGRESS => 'bg-info text-dark',
            self::STATUS_WAITING_FAMILY => 'bg-warning text-dark',
            self::STATUS_RESOLVED => 'bg-success',
            self::STATUS_CLOSED => 'bg-secondary',
            default => 'bg-light text-dark',
        };
    }
}
