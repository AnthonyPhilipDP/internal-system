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
    
        DB::table('customers')->insert([
            'name' => 'Samsung Electronics',
            'nickname' => 'smasnug',
            'address' => "South Korea",
            'mobile1' => '09123456789',
            'telephone1' => '0912345678',
            'email' => 'email@samsung.com',
            'vat' => 'VAT',
            'dateCertified' => now(),
            'payment' => 'Cash on Delivery',
            'status' => 'Active',
            'businessStyle' => 'Electronics',
            'businessNature' => 'Manufacturing',
            'tin' => '1234567890',
            'created_at' => now(),
        ]);
        
        /*
        DB::table('equipment')->insert([
            'transaction_id' => '1',
            'equipment_id' => 'Galaxy S25 Ultra',
            'ar_id' => '1',
            'customer_id' => '1',
            'make' => 'Samsung',
            'model' => 'Galaxy Series',
            'serial' => 'GS25U123456',
            'description' => 'Smartphone',
            'inspection' => json_encode(['no visible damage']),
            'laboratory' => 'electrical',
            'calibrationType' => 'Active',
            'category' => 'electrical',
            'inDate' => now(),
            'decisionRule' => 'simple',
            'created_at' => now(),
        ]);
        */
    }
}
