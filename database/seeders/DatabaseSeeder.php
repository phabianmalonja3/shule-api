<?php

namespace Database\Seeders;

use App\Models\User;

use AcademicTeacherSeeder;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\HeadTeacherSeeder;
use Database\Seeders\AdministratorSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call([
        RolesSeeder::class,
        PermisionSeeder::class,
        HeadTeacherSeeder::class,
        // AcademicTeacherSeeder::class,
        AdministratorSeeder::class,
        TeacherSeeder::class,
        AdminSeeder::class

       ]);
    }
}
