<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'guardian_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /** R13 — tutore collegato a questo account (solo per gli utenti dell'area famiglie). */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    /** Account dell'area famiglie: ha il ruolo `family` ed è collegato a un tutore. */
    public function isFamily(): bool
    {
        return $this->hasRole('family');
    }

    /**
     * Retro-compatibile: privilegia i ruoli Spatie, fallback alla colonna legacy `role`.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher') || $this->role === 'teacher';
    }
}
