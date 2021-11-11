<?php

namespace Attla\SSO\Database\Seeds;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Attla\SSO\Models\Role;
use Attla\SSO\Models\Permission;
use Attla\SSO\Models\PermissionGroup;
use HaydenPierce\ClassFinder\ClassFinder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resources = ClassFinder::getClassesInNamespace('App\\Http\\Controllers\\', ClassFinder::RECURSIVE_MODE);
        foreach ($resources as $resource) {
            $this->generatePermissions($resource);
        }

        if ($role = Role::withoutCache()->whereName(AdmRoleSeeder::ADMIN_ROLE)->first()) {
            $role->permissions()->sync(Permission::all()->map(function (Permission $permission) {
                return $permission->id;
            }));
        }
    }

    private function generatePermissions($resource)
    {
        $groupName = $this->convertToName($resource);
        $insertName = $this->ucwords($this->convertToPermissionName(explode('.', $groupName)));
        $group = PermissionGroup::firstOrCreate([
            'name' => $insertName,
        ]);

        $methods = (new \ReflectionClass($resource))->getMethods();
        foreach ($methods as $method) {
            if (
                $method->class == $resource
                && !Str::startsWith($method->name, '__')
                && (new \ReflectionMethod($method->class, $method->name))->isPublic()
            ) {
                $this->createPermission($groupName, $method->name, $group->id);
            }
        }
    }

    private function createPermission($name, $method, $groupId)
    {
        $name = explode('.', $name);
        $namespace = count($name) > 1 ? join('.', array_map(function ($item) {
            return strtolower($item);
        }, array_slice($name, 0, -1))) . '.' : '';

        $method = Str::snake($method, '-');
        $permissionName = $this->ucwords($method) . ' - ' . $this->ucwords($this->convertToPermissionName($name));

        Permission::firstOrCreate([
            'name'                  => $permissionName,
        ], [
            'permission_group_id'   => $groupId,
            'identifier'            => $namespace . Str::snake(end($name), '-') . '.' . $method,
        ]);
    }

    private function convertToName($className)
    {
        $name = str_replace(['App\\Http\\Controllers\\', 'Controller'], '', $className);
        $exploded = explode('\\', $name);
        $name = end($exploded);
        return (count($exploded) > 1 ? join('.', array_slice($exploded, 0, -1)) . '.' : '') . Str::snake($name, '-');
    }

    private function convertToPermissionName($value)
    {
        return join(' ', array_map(function ($item) {
            return ucfirst($item);
        }, (array) $value));
        return ucwords(str_replace(['-', '_'], ' ', $value));
    }

    private function ucwords($value)
    {
        return ucwords(str_replace(['-', '_'], ' ', $value));
    }
}
