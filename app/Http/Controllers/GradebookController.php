<?php

namespace App\Http\Controllers;

use App\Models\Gradebook;
use App\Models\Permission;
use App\Models\SchoolClass;
use App\Models\SubjectTeacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GradebookController extends Controller
{
    public function index()
    {
        $this->authorize('view', Gradebook::class);

        $data = request()->validate([
            'userId' => 'numeric|nullable',
            'classId' => 'numeric|nullable',
        ]);

        $data['classId'] = $data['classId'] ?? '';
        $data['userId'] = $data['userId'] ?? '';

        if (!$data['classId']) {
            return redirect('/teacher/');
        }

        $students = SchoolClass::with('students')->find($data['classId'])->students;

        $classSubjectIds = SchoolClass::with('timetable_entries')->find($data['classId'])->timetable_entries->pluck('subject_teacher_id')->flatten();
        $teachersSubjects = auth()->user()->teacherOfPivot->pluck('id')->flatten();

        $subjects = SubjectTeacher::with('user')->find($classSubjectIds);
        $grades = null;

        if ($data['userId']) {
            $grades = SubjectTeacher::whereHas('grades', function($query) use ($data){
                $query->where('user_id', $data['userId']);
            })->with('grades')->find($classSubjectIds);
        }

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        if (in_array('gradebook.bypass', $permissions))
            $teachersSubjects = $classSubjectIds;

        return Inertia::render('Teacher/Gradebook', 
        [
            'subjects' => $subjects,
            'grades' => $grades,
            'students' => $students,
            'teachersSubjects' => $teachersSubjects,
            'userId' => $data['userId'],
            'classId' => $data['classId'],
            'classes' => SchoolClass::all(),
            'permission' => $permissions
        ]);
    }

    public function store()
    {
        $this->authorize('create', Gradebook::class);

        $data = request()->validate([
            'user' => 'numeric|required|exists:users,id',
            'subject' => 'numeric|required|exists:subject_teachers,id',
            'grade' => 'numeric|required|max:5|min:1',
            'note' => 'string|nullable',
        ]);

        $this->authorize('canForSubject', [Gradebook::class, $data['subject']]);

        DB::transaction(function () use ($data) {
            return tap(User::find($data['user'])->grades()->create([
                'grade' => $data['grade'],
                'note' => $data['note'],
                'subject_teacher_id' => $data['subject'],
            ]), function (Gradebook $grade) {
                return $grade;
            });
        });
    }

    public function update(Gradebook $grade)
    {
        $this->authorize('edit', Gradebook::class);

        $data = request()->validate([
            'grade' => 'numeric|required|max:5|min:1',
            'note' => 'string|nullable',
        ]);

        $this->authorize('canForSubject', [Gradebook::class, $grade->subject_teacher->id]);

        $grade->forceFill([
            'grade' => $data['grade'],
            'note' => $data['note'],
        ])->save();
    }

    public function destroy(Gradebook $grade)
    {
        $this->authorize('delete', Gradebook::class);

        $this->authorize('canForSubject', [Gradebook::class, $grade->subject_teacher->id]);

        $grade->delete();
    }
}
