<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);

        $permissions = [
            'dashboard',
            'articles',
            'roles',
            'menus',
            'access-control',
            'users',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $superAdmin->syncPermissions(['dashboard', 'articles', 'roles', 'menus', 'access-control', 'users']);
    }
}