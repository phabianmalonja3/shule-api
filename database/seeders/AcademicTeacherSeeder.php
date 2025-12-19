<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AcademicTeacherSeeder extends Seeder
{
    public function run()
    {
        // Check if the role already exists, otherwise create it
        $role = Role::firstOrCreate(['name' => 'academic teacher']);

        // Define the permissions for the academic teacher
        $permissions = [

            'create class streams',
            'manage student allocations',
            'assign class teachers',
            'assign subject teachers',
            'add edit subjects',
            'add edit student marks',
            'upload student marks',
            'manage school announcements',
            'view attendance report',
            'view student results',
            'edit pass marks grades',
            'view subscription status',
            'manage students',
            'transfer student',
            'view print academic performance'
        ];

        // Assign each permission to the academic teacher role
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }

        $this->command->info('Academic Teacher role and permissions have been successfully created.');
    }
}
