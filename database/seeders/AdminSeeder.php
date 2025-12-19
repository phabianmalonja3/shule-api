<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Check if the role already exists, otherwise create it
        $role = Role::firstOrCreate(['name' => 'administrator']);

        // Define the permissions for the administrator
        $permissions = [
            'search school',
            'edit school details',
            'activate deactivate school',
            'activate deactivate user',
            'view print subscription status',
        ];

        // Assign each permission to the administrator role
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }

        $this->command->info('Administrator role and permissions have been successfully created.');
    }
}
