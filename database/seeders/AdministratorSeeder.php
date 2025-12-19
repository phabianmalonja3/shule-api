<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@test.com',
            'password'=>'password'
        ]);
        $user->is_verified = true;
        $user->update();
        $user->assignRole('administrator');
        $this->command->info('Administrator created been successfully created.');
    }
}
