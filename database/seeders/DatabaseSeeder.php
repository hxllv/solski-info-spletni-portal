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
            ['name' => 'absences.view'],
            ['name' => 'absences.create'],
            ['name' => 'absences.delete'],
            ['name' => 'tests.view'],
            ['name' => 'tests.create'],
            ['name' => 'tests.edit'],
            ['name' => 'tests.delete'],
        ]);
        \App\Models\Permission::factory()->hasAttached([$admin, $classTeacher])->createMany([
            ['name' => 'class.teacher'],
            ['name' => 'users.invite'],
            ['name' => 'roles.view'],
            ['name' => 'absences.edit'],
            ['name' => 'absences.approval'],
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
            ['name' => 'gradebook.bypass'],
            ['name' => 'absences.bypass'],
            ['name' => 'tests.bypass'],
        ]);

        $adminUser = \App\Models\User::factory()->for($admin)->create([
            'name' => 'Nace',
            'surname' => 'Tavcer',
            'email' => 'nace.tavcer20@gmail.com',
        ]);

        $t1 = \App\Models\User::factory()->for($teacher)->create([
            'name' => 'Luka',
            'surname' => 'Tavcer',
            'email' => 'nace.tavcer@gmail.com',
            'is_account_owner' => false
        ]);

        $t2 = \App\Models\User::factory()->for($teacher)->create([
            'name' => 'Janez',
            'surname' => 'Tavcer',
            'email' => 'nace.tavcer+t@gmail.com',
            'is_account_owner' => false
        ]);

        $studentUsers = \App\Models\User::factory()->for($student)->createMany([
            [
                'email' => 'nace.tavcer+s@gmail.com',
                'is_account_owner' => false
            ],
            ['is_account_owner' => false],
            ['is_account_owner' => false],
            ['is_account_owner' => false],
            ['is_account_owner' => false],
            ['is_account_owner' => false],
            ['is_account_owner' => false],
            ['is_account_owner' => false],
        ]);

        $class = \App\Models\SchoolClass::factory()->for($adminUser, 'classTeacher')->create([
            'name' => 'R1a',
        ]);

        foreach ($studentUsers as $student) {
            $student->studentOf()->associate($class)->save();
        }

        $math = \App\Models\Subject::factory()->hasAttached($adminUser, [], 'teachedBy')->create([
            'name' => 'Matematika',
        ]);

        $eng = \App\Models\Subject::factory()->hasAttached([$adminUser, $t1], [], 'teachedBy')->create([
            'name' => 'Angleščina',
        ]);

        $slo = \App\Models\Subject::factory()->hasAttached([$t1, $t2], [], 'teachedBy')->create([
            'name' => 'Slovenščina',
        ]);

        $spo = \App\Models\Subject::factory()->hasAttached([$t1, $t2, $adminUser], [], 'teachedBy')->create([
            'name' => 'Športna',
        ]);

        $mathPivot = $math->teachedBy->pluck('pivot');
        $engPivot = $eng->teachedBy->pluck('pivot');
        $sloPivot = $slo->teachedBy->pluck('pivot');
        $spoPivot = $spo->teachedBy->pluck('pivot');

        \App\Models\TimetableEntry::factory()->createMany([
            [
                'day' => 0,
                'hour' => 0,
                'subject_teacher_id' => $mathPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 0,
                'hour' => 1,
                'subject_teacher_id' => $engPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 0,
                'hour' => 2,
                'subject_teacher_id' => $sloPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 0,
                'hour' => 3,
                'subject_teacher_id' => $spoPivot[0]->id,
                'school_class_id' => $class->id
            ],
            // torek
            [
                'day' => 1,
                'hour' => 0,
                'subject_teacher_id' => $mathPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 1,
                'hour' => 1,
                'subject_teacher_id' => $engPivot[1]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 1,
                'hour' => 2,
                'subject_teacher_id' => $sloPivot[1]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 1,
                'hour' => 3,
                'subject_teacher_id' => $spoPivot[1]->id,
                'school_class_id' => $class->id
            ],
            // sreda
            [
                'day' => 2,
                'hour' => 0,
                'subject_teacher_id' => $mathPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 2,
                'hour' => 1,
                'subject_teacher_id' => $engPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 2,
                'hour' => 2,
                'subject_teacher_id' => $sloPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 2,
                'hour' => 3,
                'subject_teacher_id' => $spoPivot[2]->id,
                'school_class_id' => $class->id
            ],
            // cetrtek
            [
                'day' => 3,
                'hour' => 0,
                'subject_teacher_id' => $mathPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 3,
                'hour' => 1,
                'subject_teacher_id' => $engPivot[1]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 3,
                'hour' => 2,
                'subject_teacher_id' => $sloPivot[1]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 3,
                'hour' => 3,
                'subject_teacher_id' => $spoPivot[0]->id,
                'school_class_id' => $class->id
            ],
            // petek
            [
                'day' => 4,
                'hour' => 0,
                'subject_teacher_id' => $mathPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 4,
                'hour' => 1,
                'subject_teacher_id' => $engPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 4,
                'hour' => 2,
                'subject_teacher_id' => $sloPivot[0]->id,
                'school_class_id' => $class->id
            ],
            [
                'day' => 4,
                'hour' => 3,
                'subject_teacher_id' => $spoPivot[1]->id,
                'school_class_id' => $class->id
            ],
        ]);

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
