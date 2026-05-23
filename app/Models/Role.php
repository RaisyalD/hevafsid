<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Role name constants
    const SUPER_ADMIN     = 'super_admin';
    const ADMIN_GUDANG    = 'admin_gudang';
    const ADMIN_KEUANGAN  = 'admin_keuangan';
    const OWNER           = 'owner';
}
