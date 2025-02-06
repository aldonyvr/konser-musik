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
        // Create Roles
        Role::create([
            'name' => 'admin',
            'guard_name' => 'api',
            'full_name' => 'Administrator'
        ]);

        Role::create([
            'name' => 'user',
            'guard_name' => 'api',
            'full_name' => 'User'
        ]);

        Role::create([
            'name' => 'scanner',
            'guard_name' => 'api',
            'full_name' => 'Scanner Admin'  // Add full_name for scanner role
        ]);

        // Create Permissions
        Permission::create(['name' => 'scan_tickets', 'guard_name' => 'api']);
        Permission::create(['name' => 'view_scan_history', 'guard_name' => 'api']);

        // Assign Permissions to Scanner Role
        $scannerRole = Role::findByName('scanner', 'api');
        $scannerRole->givePermissionTo(['scan_tickets', 'view_scan_history']);
    }
}
