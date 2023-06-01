<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 15; $i < 3000; $i++) {
            \App\Models\User::factory()->create([
                "name" => "Nace$i",
                "surname" => "Tavcer$i",
                "email" => "nace.tavcer+$i@gmail.com",
                "role_id" => 2
            ]);
        }
    }
}
