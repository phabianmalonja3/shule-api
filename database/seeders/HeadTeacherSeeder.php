<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HeadTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Check if the role already exists, otherwise create it
        $role = Role::firstOrCreate(['name' => 'head teacher']);

        // Define the permissions for the head teacher
        $permissions = [

            'edit personal details',
            'edit school details',
            'upload teacher details',
            'edit teacher details',
            'designate academic teacher',
            'activate deactivate teacher',
            'transfer teacher',
            'view students attendance',
            'view student results',
            'view print academic performance',
            'view print subscription status'
        ];

        // Assign each permission to the head teacher role
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }

        $this->command->info('Head Teacher role and permissions have been successfully created.');
    }
}
