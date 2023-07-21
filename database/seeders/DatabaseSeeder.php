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

        $classTeacher = \App\Models\Role::factory()->create([
            'name' => 'Razrednik',
        ]);

        $teacher = \App\Models\Role::factory()->create([
            'name' => 'Učitelj',
        ]);

        $student = \App\Models\Role::factory()->create([
            'name' => 'Učenec',
        ]);

        $parent = \App\Models\Role::factory()->create([
            'name' => 'Starš',
        ]);

        \App\Models\Permission::factory()->hasAttached([$admin, $classTeacher, $teacher])->createMany([
            ['name' => 'teacher.panel.view'],
            ['name' => 'admin.panel.view'],
            ['name' => 'users.view'],
            ['name' => 'classes.view'],
            ['name' => 'subjects.view'],
            ['name' => 'gradebook.view'],
            ['name' => 'gradebook.create'],
            ['name' => 'gradebook.edit'],
            ['name' => 'gradebook.delete'],
            ['name' => 'missing.view'],
            ['name' => 'missing.create'],
            ['name' => 'missing.delete'],
            ['name' => 'test.view'],
            ['name' => 'test.create'],
            ['name' => 'test.edit'],
            ['name' => 'test.delete'],
        ]);
        \App\Models\Permission::factory()->hasAttached([$admin, $classTeacher])->createMany([
            ['name' => 'class.teacher'],
            ['name' => 'users.invite'],
            ['name' => 'roles.view'],
            ['name' => 'missing.edit'],
        ]);
        \App\Models\Permission::factory()->hasAttached($admin)->createMany([
            ['name' => 'all.classes.view'],
            ['name' => 'users.edit'],
            ['name' => 'users.delete'],
            ['name' => 'roles.create'],
            ['name' => 'roles.edit'],
            ['name' => 'roles.delete'], 
            ['name' => 'classes.create'],
            ['name' => 'classes.edit'],
            ['name' => 'classes.delete'],
            ['name' => 'subjects.create'],
            ['name' => 'subjects.edit'],
            ['name' => 'subjects.delete'],
            ['name' => 'timetable.edit'],
            ['name' => 'settings.edit'],
            ['name' => 'settings.view'],
        ]);

        $adminUser = \App\Models\User::factory()->for($admin)->create([
            'name' => 'Nace',
            'surname' => 'Tavcer',
            'email' => 'nace.tavcer20@gmail.com',
        ]);

        $class = \App\Models\SchoolClass::factory()->for($adminUser, 'classTeacher')->create([
            'name' => 'R1a',
        ]);

        for ($i=2; $i < 100; $i++) { 
            \App\Models\SchoolClass::factory()->for($adminUser, 'classTeacher')->create([
                'name' => "R$i",
            ]);
        }

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

        \App\Models\Setting::factory()->create([
            'name' => 'timetable.hours',
            'value' => json_encode([
                0 => [
                    'index' => -1,
                    'name' => '0. ura',
                    'from' => '7:10',
                    'to' => '7:55',
                ],
                1 => [
                    'index' => 0,
                    'name' => '1. ura',
                    'from' => '8:00',
                    'to' => '8:45',
                ],
                2 => [
                    'index' => 1,
                    'name' => '2. ura',
                    'from' => '8:50',
                    'to' => '9:35',
                ],
                3 => [
                    'index' => 2,
                    'name' => 'Malica',
                    'from' => '9:40',
                    'to' => '10:00',
                ],
                4 => [
                    'index' => 3,
                    'name' => '3. ura',
                    'from' => '10:05',
                    'to' => '10:50',
                ],
            ])
        ]);
    }
}
