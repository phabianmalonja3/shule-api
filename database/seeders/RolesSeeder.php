<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create roles
       $headmaster = Role::create(['name' => 'header teacher']);
        Role::create(['name' => 'administrator']);
        Role::create(['name' => 'academic teacher']);
        Role::create(['name' => 'class teacher']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'parent']);
        Role::create(['name' => 'student']);
    }
}
