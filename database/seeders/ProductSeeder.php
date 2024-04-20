<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            $initPrice = $faker->randomFloat(2, 10, 100);
            $price = $initPrice + 20; // Ensure price is greater than initPrice

            Product::create([
                'name' => $faker->name,
                'image' => $faker->imageUrl(),
                'description' => $faker->text,
                'initPrice' => $initPrice,
                'price' => $price,
                'quantity' => $faker->numberBetween(0, 100),
            ]);
        }
    }
}
