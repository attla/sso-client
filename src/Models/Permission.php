<?php

namespace Attla\SSO\Models;

use Attla\Database\Eloquent;

class Permission extends Eloquent
{
    protected $fillable = [
        'permission_group_id',
        'name',
        'identifier'
    ];

    protected $hidden = [
        'pivot'
    ];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(PermissionGroup::class);
    }
}
