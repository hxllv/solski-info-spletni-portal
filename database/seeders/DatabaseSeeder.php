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

        $admin = \App\Models\Role::factory()->create([
            'name' => 'Administrator',
        ]);

        $teacher = \App\Models\Role::factory()->create([
            'name' => 'Učitelj',
        ]);

        $student = \App\Models\Role::factory()->create([
            'name' => 'Učenec',
        ]);

        \App\Models\Middleware::factory()->hasAttached([$admin, $teacher])->create(['name' => 'admin.panel.view']);
        \App\Models\Middleware::factory()->hasAttached([$admin, $teacher])->create(['name' => 'users.view']);
        \App\Models\Middleware::factory()->hasAttached([$admin, $teacher])->create(['name' => 'users.invite']);
        \App\Models\Middleware::factory()->hasAttached($admin)->create(['name' => 'users.edit']);
        \App\Models\Middleware::factory()->hasAttached($admin)->create(['name' => 'users.delete']);
        \App\Models\Middleware::factory()->hasAttached($admin)->create(['name' => 'roles.view']);
        \App\Models\Middleware::factory()->hasAttached($admin)->create(['name' => 'roles.create']);
        \App\Models\Middleware::factory()->hasAttached($admin)->create(['name' => 'roles.edit']);
        \App\Models\Middleware::factory()->hasAttached($admin)->create(['name' => 'roles.delete']);

        /* ['name' => 'users.invite'],
        ['name' => 'users.edit'],
        ['name' => 'users.delete'],
        ['name' => 'roles.view'],
        ['name' => 'roles.create'],
        ['name' => 'roles.edit'],
        ['name' => 'roles.delete'], */

        /* \App\Models\User::factory()->create([
            'name' => 'Nace',
            'surname' => 'Tavcer',
            'email' => 'nace.tavcer20@gmail.com',
        ]); */
    }
}
