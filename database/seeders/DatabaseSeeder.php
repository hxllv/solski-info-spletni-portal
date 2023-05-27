<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\Role::factory()->create([
            'name' => 'Administrator',
        ]);

        \App\Models\Role::factory()->create([
            'name' => 'Učitelj',
        ]);

        \App\Models\Role::factory()->create([
            'name' => 'Učenec',
        ]);

        /* \App\Models\User::factory()->create([
            'name' => 'Nace',
            'surname' => 'Tavcer',
            'email' => 'nace.tavcer20@gmail.com',
        ]); */
    }
}
