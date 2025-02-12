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
        // Create Permissions
        Permission::create(['name' => 'scan-ticket', 'guard_name' => 'api']);
        Permission::create(['name' => 'verify-ticket', 'guard_name' => 'api']);
        Permission::create(['name' => 'view-scan-history', 'guard_name' => 'api']);

        // Create Roles
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

        // Assign permissions to mitra role
        $mitraRole->givePermissionTo([
            'scan-ticket',
            'verify-ticket',
            'view-scan-history'
        ]);
    }
}
