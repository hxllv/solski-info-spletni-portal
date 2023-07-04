<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Setting;
use App\Models\SubjectTeacher;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): Response {
        $class = auth()->user()->studentOf;
        $timetableEntries = null;
        $teacherSubject = SubjectTeacher::with(['user', 'subject'])->get();
        
        if (!is_null($class))
            $timetableEntries = $class->timetable_entries()->with('subject_teacher')->get();

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        if (in_array('teacher', $permissions)) {
            $teacherOfPivot = auth()->user()->teacherOfPivot->pluck('id')->toArray();
            $timetableEntries = TimetableEntry::whereIn('subject_teacher_id', $teacherOfPivot)->with(['subject_teacher', 'school_class'])->get();
        }

        return Inertia::render('Dashboard', 
            [
                'permission' => $permissions,
                'timetableEntries' => $timetableEntries,
                'sClass' => $class,
                'subjects' => $teacherSubject,
                'hours' => Setting::find('timetable.hours'),
            ]
        );
    }
}
