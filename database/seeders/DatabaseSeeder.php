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

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Call TruncateTableSeeder to delete existing data
        $this->call(TruncateTableSeeder::class);

        // Call other seeders to populate data
        $this->call(ProductSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(EmployerSeeder::class);
        $this->call(BudgetSeeder::class);
        $this->call(OrderSeeder::class);
        // Add more seeders if needed
    }
}
