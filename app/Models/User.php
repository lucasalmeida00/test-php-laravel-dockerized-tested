<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'cpf',
        'email',
        'amount',
        'password',
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

    public function cpf(): Attribute
    {
        return new Attribute(
            get: fn($value) => preg_replace('/[^0-9]/', '', $value),
            set: fn($value) => preg_replace('/[^0-9]/', '', $value),
        );
    }

    public function amount(): Attribute
    {
        return new Attribute(
            get: fn($value) => preg_replace('/[^0-9]/', '', $value),
            set: fn($value) => preg_replace('/[^0-9]/', '', $value),
        );
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles->contains('permissions', $permission);
    }

    public function assignRole(string $role): void
    {
        $this->roles()->attach($role);
    }

    public function permissions(): Collection
    {
        return $this->roles->pluck('permissions')->flatten()->unique();
    }
}
