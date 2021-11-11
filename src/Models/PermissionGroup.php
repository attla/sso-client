<?php

namespace Attla\SSO\Models;

use Attla\Database\Eloquent;

class PermissionGroup extends Eloquent
{
    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
