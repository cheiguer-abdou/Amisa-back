<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruncateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /// Truncate or delete the data from the tables
        DB::table('products')->delete();
        DB::table('clients')->delete();
        DB::table('orders')->delete();
        DB::table('employers')->delete();
        DB::table('budgets')->delete();
    }
}
