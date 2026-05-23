<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id', 'name', 'email', 'phone', 'avatar', 'is_active', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(IncomingTransaction::class);
    }

    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(OutgoingTransaction::class);
    }

    // Role helpers
    public function isSuperAdmin(): bool
    {
        return $this->role?->name === Role::SUPER_ADMIN;
    }

    public function isAdminGudang(): bool
    {
        return $this->role?->name === Role::ADMIN_GUDANG;
    }

    public function isAdminKeuangan(): bool
    {
        return $this->role?->name === Role::ADMIN_KEUANGAN;
    }

    public function isOwner(): bool
    {
        return $this->role?->name === Role::OWNER;
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    public function canManageStock(): bool
    {
        return in_array($this->role?->name, [Role::SUPER_ADMIN, Role::ADMIN_GUDANG]);
    }

    public function canManageFinance(): bool
    {
        return in_array($this->role?->name, [Role::SUPER_ADMIN, Role::ADMIN_KEUANGAN]);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=f43f5e&color=fff';
    }
}
