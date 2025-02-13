<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic permissions
        $permissions = [
            'master-user',
            'master-role',
            'setting',
            'view-dashboard',
            'view-stats',
            'manage-own-konser'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'api',
            'full_name' => 'Administrator'
        ]);

        $userRole = Role::create([
            'name' => 'user',
            'guard_name' => 'api',
            'full_name' => 'User'
        ]);

        $mitraRole = Role::create([
            'name' => 'mitra',
            'guard_name' => 'api',
            'full_name' => 'Mitra Konser'
        ]);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to mitra
        $mitraRole->givePermissionTo([
            'view-dashboard',
            'view-stats',
            'manage-own-konser'
        ]);
    }
}
