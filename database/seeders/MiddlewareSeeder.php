<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MiddlewareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Middleware::factory()->createMany([
            ['name' => 'users.view'],
            ['name' => 'users.invite'],
            ['name' => 'users.edit'],
            ['name' => 'users.delete'],
            ['name' => 'roles.view'],
            ['name' => 'roles.create'],
            ['name' => 'roles.edit'],
            ['name' => 'roles.delete'],
        ]);
    }
}
