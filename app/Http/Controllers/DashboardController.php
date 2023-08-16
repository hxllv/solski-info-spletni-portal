<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Permission;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\SubjectTeacher;
use App\Models\Test;
use App\Models\TimetableEntry;
use App\Models\TimetableEntryOverride;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response {
        $data = request()->validate([
            'userId' => 'numeric|nullable',
        ]);

        $data['userId'] = $data['userId'] ?? null;

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        // users

        $users = collect();

        //dd(auth()->user());

        if (in_array('dashboard.children.view', $permissions))
            $users = auth()->user()->children;

        if (in_array('dashboard.me.view', $permissions))
            $users->push(User::find(auth()->user()->id));

        if (in_array('dashboard.all.view', $permissions))
            $users = User::all();

        $user = User::find($data['userId']);

        if (is_null($user))
            $user = User::find($users[0]->id);

        $this->authorize('canViewUser', [User::class, $user->id]);

        $userPerm = $user->role->permissions->pluck('name')->toArray();

        if ($user->is_account_owner) 
            $userPerm = Permission::all()->pluck('name')->toArray();

        $class = $user->studentOf ?? null;
        $start = date('Y-m-d', strtotime('-' . date('w') . ' days'));
        $end = date('Y-m-d', strtotime('+' . (6-date('w')) . ' days'));

        // timetable

        $timetableEntries = null;
        $timetableOverrides = null;

        if (in_array('dashboard.timetable.view', $permissions)) {
            if (!is_null($class)) {
                $timetableEntries = $class->timetable_entries()->with('subject_teacher')->get();
                $timetableOverrides = $class->overrides()->whereBetween('date', [$start, $end])->with('subject_teacher')->get();
            }

            // for teachers

            if (in_array('teacher.panel.view', $userPerm)) {
                $teacherOfPivot = $user->teacherOfPivot->pluck('id')->toArray();
                $timetableEntries = TimetableEntry::whereIn('subject_teacher_id', $teacherOfPivot)->with(['subject_teacher', 'school_class'])->get();
                $timetableOverrides = TimetableEntryOverride::whereIn('subject_teacher_id', $teacherOfPivot)->whereBetween('date', [$start, $end])->with(['subject_teacher', 'school_class'])->get();

                $allOverrides = TimetableEntryOverride::whereBetween('date', [$start, $end])->with(['subject_teacher', 'school_class'])->get()->groupBy('hour');

                foreach ($timetableEntries as $key => $entry) {
                    if (
                        array_key_exists($entry->hour, $allOverrides->toArray()) && 
                        $allOverrides[$entry->hour]->count() &&
                        $allOverrides[$entry->hour][0]->day == $entry->day
                    ) {
                        $timetableEntries->pull($key);
                    }
                }
            }
        }

        // absences

        $absences = null;

        if (in_array('dashboard.absences.view', $permissions)) {
            $absences = Absence::where('user_id', $user->id)->with('subject_teacher')->orderBy('date', 'desc')->orderBy('hour', 'asc')->get()->groupBy('date');
        }

        // grades

        $grades = null;
        $subjects = null;

        if (in_array('dashboard.gradebook.view', $permissions) && !is_null($class)) {
            $classSubjectIds = SchoolClass::with('timetable_entries')->find($class->id)->timetable_entries->pluck('subject_teacher_id')->flatten();

            $subjects = SubjectTeacher::with('user')->find($classSubjectIds);
            $grades = SubjectTeacher::whereHas('grades', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('grades')->find($classSubjectIds);
        }

        // tests

        $tests = null;

        if (in_array('dashboard.tests.view', $permissions) && !is_null($class)) {
            $tests = Test::where('school_class_id', $class->id)->where('date', '>=', date('Y-m-d'))->
                with('subject_teacher')->orderBy('date', 'asc')->get()->groupBy('date');  
        }

        return Inertia::render('Dashboard', 
            [
                'permission' => $permissions,
                'timetableEntries' => $timetableEntries,
                'timetableOverrides' => $timetableOverrides,
                'absences' => $absences,
                'grades' => $grades,
                'tests' => $tests,
                'subjects' => $subjects,
                'sClass' => $class,
                'users' => $users,
                'hours' => Setting::find('timetable.hours'),
                'userId' => (string) $user->id,
            ] 
        );
    }

    public function excuse(Absence $absence)
    {
        $this->authorize('editOnDashboard', Absence::class);

        $data = request()->validate([
            'excuse' => 'string|required',
            'classId' => 'numeric|required|exists:school_classes,id',
        ]);

        $absence->forceFill([
            'excuse' => $data['excuse'],
        ])->save();
    }
}
