<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Admin',
            'slug' => 'admin',

        ]);
        Role::create([
            'name' => 'Hospital',
            'slug' => 'hospital',

        ]);
        Role::create([
            'name' => 'Radiologist',
            'slug' => 'radiologist',

        ]);
    }
}
