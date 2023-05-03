<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*DB::table('customers')->insert([
            'name' => Str::random(15),
            'address' => Str::random(150)
        ]);*/

        Customer::factory()->count(50)->create();
    }
}
