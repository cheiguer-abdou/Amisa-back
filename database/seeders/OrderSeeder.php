<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Retrieve all products
        $products = Product::all();
        $clientIds = Client::pluck('id')->toArray();

        // Loop through products and create orders
        foreach ($products as $product) {
            for ($i = 0; $i < 20; $i++) { // Create 10 orders for each product
                Order::create([
                    'client_id' => $faker->randomElement($clientIds), // Assuming you have clients in your system
                    'product_id' => $product->id,
                    'quantity' => $faker->numberBetween(1, 3),
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }
    }
}
