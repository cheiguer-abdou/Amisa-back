<?php

namespace Database\Seeders;

use App\Models\Employer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EmployerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        // Create the first record with the role ADMIN
        Employer::create([
            'name' => $faker->name,
            'email' => $faker->email,
            'phone' => $faker->phoneNumber(),
            'password' => $faker->password,
            'role' => 'ADMIN',
        ]);

        for ($i = 1; $i < 20; $i++) {

            Employer::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber(),
                'password' =>  $faker->password,
                'role' => 'EMPLOYEE'
            ]);
        }
    }
}
