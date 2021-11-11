<?php

namespace Attla\SSO;

use Attla\SSO\Models\Role;

trait HasPermissions
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getRolesAttribute()
    {
        return $this->roles()->get(['id', 'name']);
    }

    /**
     * Get ids from roles
     *
     * @param array $roles
     * @return $this
     */
    protected function getIdRoles($roles)
    {
        return collect($roles)
        ->flatten()
        ->map(function ($role) {
            if (is_string($role)) {
                return Role::whereName($role)->first();
            } elseif (is_int($role)) {
                return Role::find($role);
            }

            return $role;
        })->filter(function ($role) {
            return $role instanceof Role;
        })->map(function (Role $role) {
            return $role->id;
        });
    }

    /**
     * Assign the given role
     *
     * @param string|int|string[]|int[] ...$roles
     * @return $this
     */
    public function assignRole(...$roles)
    {
        $this->roles()->syncWithoutDetaching($this->getIdRoles($roles));

        return $this;
    }

    /**
     * Revoke the given role
     *
     * @param string|int|string[]|int[] ...$roles
     */
    public function removeRole(...$roles)
    {
        foreach ($this->getIdRoles($roles) as $role) {
            $this->roles()->detach($role);
        }

        return $this;
    }

    /**
     * Check if has a role
     *
     * @param mixed ...$roles
     * @return bool
     */
    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if has a permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        $hasPermission = false;

        foreach ($this->roles as $role) {
            if ($hasPermission = $role->hasPermission($permission)) {
                break;
            }
        }

        return $hasPermission;
    }
}
