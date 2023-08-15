<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Init extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        \App\Models\Permission::factory()->hasAttached([$admin, $classTeacher, $teacher, $student, $parent])->createMany([
            ['name' => 'dashboard.view'],
            ['name' => 'dashboard.timetable.view'],
        ]);
        \App\Models\Permission::factory()->hasAttached([$admin, $classTeacher, $teacher, $student])->createMany([
            ['name' => 'dashboard.me.view'],
        ]);
        \App\Models\Permission::factory()->hasAttached([$parent])->createMany([
            ['name' => 'dashboard.children.view'],
            ['name' => 'dashboard.absences.edit'],
        ]);
        \App\Models\Permission::factory()->hasAttached([$parent, $student])->createMany([
            ['name' => 'dashboard.absences.view'],
            ['name' => 'dashboard.gradebook.view'],
            ['name' => 'dashboard.tests.view'],
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
            ['name' => 'timetable.override.view'],
            ['name' => 'timetable.override.create'],
            ['name' => 'timetable.override.edit'],
            ['name' => 'timetable.override.delete'],
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
            ['name' => 'absences.bypass.subject'],
            ['name' => 'absences.bypass.class'],
            ['name' => 'tests.bypass'],
            ['name' => 'timetable.override.bypass'],
            ['name' => 'dashboard.all.view'],
        ]);

        \App\Models\Setting::factory()->create([
            'name' => 'timetable.hours',
            'value' => '[]'
        ]);
    }
}
