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
        $userData =  [
            [
                'name' => 'Rizky Admin',
                'username' => 'Admin123',
                'code' => 'ADM001',
                'role' => 'Admin',
                'password' => bcrypt('password123'),
                'email' => 'admin@example.com'
            ],
            [
                'name' => 'Chandra Karyawan',
                'username' => 'Karyawan123',
                'code' => 'KRY001',
                'role' => 'Karyawan',
                'password' => bcrypt('password123'),
                'email' => 'karyawan@example.com'
            ]
        ];

        foreach ($userData as $val) {
            User::create($val);
        }
    }
}
