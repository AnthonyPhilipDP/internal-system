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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'level' => 1,
            'password' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Philip',
            'email' => 'philip@email.com',
            'level' => 2,
            'password' => 'philip',
        ]);
    }
}
