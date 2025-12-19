<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Check if the role already exists, otherwise create it
        $role = Role::firstOrCreate(['name' => 'teacher']);

        // Define the permissions for the teacher role
        $permissions = [

            'edit personal details',
            'view assigned subjects',
            'add edit student marks',
            'upload student marks',
            'comment on student performance',
            'view class announcements',
            'view attendance report',
            'manage homework assignments',
            'indicate homework completion',
            'manage notes past papers',
            'manage videos audios',
            'manage useful links'
        ];

        // Assign each permission to the teacher role
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }

        $this->command->info('Teacher role and permissions have been successfully created.');
    }
}
