<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Permission::create(['name' => 'edit personal details']);
        Permission::create(['name' => 'edit school details']);
        Permission::create(['name' => 'upload teacher details']);
        Permission::create(['name' => 'edit teacher details']);
        Permission::create(['name' => 'designate academic teacher']);
        Permission::create(['name' => 'activate deactivate teacher']);
        Permission::create(['name' => 'transfer teacher']);
        Permission::create(['name' => 'view students attendance']);
        Permission::create(['name' => 'view student results']);
        Permission::create(['name' => 'view print academic performance']);
        Permission::create(['name' => 'view print subscription status']);
    }
}
