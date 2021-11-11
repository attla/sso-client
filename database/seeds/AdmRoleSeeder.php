<?php

namespace Attla\SSO\Database\Seeds;

use Attla\SSO\Models\Role;
use Illuminate\Database\Seeder;

class AdmRoleSeeder extends Seeder
{
    public const ADMIN_ROLE = 'admin';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate([
            'name'          => static::ADMIN_ROLE,
        ], [
            'description'   => static::ADMIN_ROLE,
        ]);
    }
}
