<?php

namespace App\Http\Controllers;

use App\Models\Gradebook;
use App\Models\Permission;
use App\Models\SchoolClass;
use App\Models\SubjectTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradebookController extends Controller
{
    public function index()
    {
        $this->authorize('view', Gradebook::class);

        $data = request()->validate([
            'user' => 'numeric|nullable',
            'classId' => 'numeric|nullable',
        ]);

        if ((array_key_exists('classId', $data) && !$data['classId']) || !array_key_exists('classId', $data)) {
            return redirect('/teacher/');
        }

        $students = SchoolClass::with('students')->find($data['classId'])->students;

        $classSubjectIds = SchoolClass::with('timetable_entries')->find($data['classId'])->timetable_entries->pluck('subject_teacher_id')->flatten();
        $teachersSubjects = auth()->user()->teacherOfPivot->pluck('id')->flatten();

        $subjects = SubjectTeacher::find($classSubjectIds);
        $grades = null;

        if (array_key_exists('user', $data) && $data['user']) {
            $grades = SubjectTeacher::whereHas('grades', function($query) use ($data){
                $query->where('user_id', $data['user']);
            })->find($classSubjectIds);
        }

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        return Inertia::render('Teacher/Gradebook', 
        [
            'subjects' => $subjects,
            'grades' => $grades,
            'students' => $students,
            'teachersSubjects' => $teachersSubjects,
            'user' => $data['user'] ?? '',
            'classId' => $data['classId'],
            'classes' => SchoolClass::all(),
            'permission' => $permissions
        ]);
    }
}
