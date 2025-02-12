<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            'username' => 'Administrator',
            'email' => 'admin@email.com',
            'level' => 1,
            'password' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Philip',
            'email' => null,
            'level' => 1,
            'password' => 'admin',
        ]);

        DB::table('customers')->insert([
            'name' => 'Precision Measurement Specialists, Inc.',
            'address' => 'Carmona, Cavite',
            'phone' => '+639123123123',
            'email' => 'admin@email.com',
            'vat' => 'VAT',
            'certifyingBody' => 'Sample',
            'dateCertified' => '2025-01-30',
            'payment' => 'Cash on Delivery',
            'status' => 'Active',
            'businessStyle' => 'Measurement Services',
            'businessNature' => 'Metrology',
            'tin' => '123456',
        ]);
    }
}
