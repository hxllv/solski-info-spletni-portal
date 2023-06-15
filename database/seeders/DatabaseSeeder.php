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

        \App\Models\Permission::factory()->hasAttached([$admin, $teacher])->createMany([
            ['name' => 'class.teacher'],
            ['name' => 'teacher'],
            ['name' => 'admin.panel.view'],
            ['name' => 'users.view'],
            ['name' => 'users.invite']
        ]);
        \App\Models\Permission::factory()->hasAttached($admin)->createMany([
            ['name' => 'users.edit'],
            ['name' => 'users.delete'],
            ['name' => 'roles.view'],
            ['name' => 'roles.create'],
            ['name' => 'roles.edit'],
            ['name' => 'roles.delete'],
            ['name' => 'classes.view'],
            ['name' => 'classes.create'],
            ['name' => 'classes.edit'],
            ['name' => 'classes.delete'],
            ['name' => 'subjects.view'],
            ['name' => 'subjects.create'],
            ['name' => 'subjects.edit'],
            ['name' => 'subjects.delete']
        ]);

        $adminUser = \App\Models\User::factory()->for($admin)->create([
            'name' => 'Nace',
            'surname' => 'Tavcer',
            'email' => 'nace.tavcer20@gmail.com',
        ]);

        $class = \App\Models\SchoolClass::factory()->for($adminUser, 'classTeacher')->create([
            'name' => 'R1a',
        ]);

        $subject = \App\Models\Subject::factory()->hasAttached($adminUser, [], 'teachedBy')->create([
            'name' => 'Matematika',
        ]);

        \App\Models\User::factory()->for($teacher)->create([
            'name' => 'NaceT',
            'surname' => 'TavcerT',
            'email' => 'nace.tavcer@gmail.com',
            'is_account_owner' => false
        ]);

        \App\Models\User::factory()->for($teacher)->create([
            'name' => 'NaceT1',
            'surname' => 'TavcerT1',
            'email' => 'nace.tavcer+t@gmail.com',
            'is_account_owner' => false
        ]);

        $studentUser = \App\Models\User::factory()->for($student)->create([
            'name' => 'NaceS',
            'surname' => 'TavcerS',
            'email' => 'nace.tavcer+s@gmail.com',
            'is_account_owner' => false
        ]);

        $studentUser->studentOf()->associate($class)->save();
    }
}
