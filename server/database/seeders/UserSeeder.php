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
        //
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => '123',
            'role' => 1
        ]);
        User::factory()->create([
            'name' => 'Felhasználó',
            'email' => 'felhasznalo@example.com',
            'password' => '123',
            'role' => 2
        ]);
    }
}
