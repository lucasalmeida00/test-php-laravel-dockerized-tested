<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use SoftDeletes, HasFactory;

    const ROLE_DEFAULT = 'default';
    const ROLE_SHOPMANAGER = 'shopmanager';

    const ROLES = [
        self::ROLE_DEFAULT => 'Default',
        self::ROLE_SHOPMANAGER => 'Shop Manager',
    ];

    protected $fillable = [
        'name',
        'description',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
