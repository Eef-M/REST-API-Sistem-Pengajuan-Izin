<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'nama_lengkap' => 'Admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('admin'),
            'status' => 'admin',
            'verif' => true,
        ]);
    }
}
