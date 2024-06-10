<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {





        $userData = [
            [  'username' => 'admin',
                'phone' => '123456789',
                'role_id' => 1,
                'slug'=>"admin",
                'email' => 'admin@shadowmatch.com',
                'password' => bcrypt('12345678'),
                'email_verified' => true,
                'phone_verified' => false,
                'user_verified' => true,
                'status' => "active",],
        ];

        foreach ($userData as $data) {
            User::create($data);
        }
    }
}
