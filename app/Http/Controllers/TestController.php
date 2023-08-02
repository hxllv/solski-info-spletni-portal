<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\SchoolClass;
use App\Models\SubjectTeacher;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index()
    {
        $this->authorize('view', Test::class);

        $data = request()->validate([
            'classId' => 'numeric|nullable',
        ]);

        $data['classId'] = $data['classId'] ?? '';

        if (!$data['classId']) {
            return redirect('/teacher/');
        }

        $classSubjectIds = SchoolClass::with('timetable_entries')->find($data['classId'])->timetable_entries->pluck('subject_teacher_id')->flatten();
        $teachersSubjectIds = auth()->user()->teacherOfPivot->pluck('id')->flatten();

        $subjects = SubjectTeacher::with('user')->find($classSubjectIds);
        $teachersSubjects = SubjectTeacher::with('user')->find($teachersSubjectIds);

        $testsInFuture = Test::where('school_class_id', $data['classId'])->where('date', '>=', date('Y-m-d'))->with('subject_teacher')->orderBy('date', 'asc')->get()->groupBy('date');
        $testsInPast = Test::where('school_class_id', $data['classId'])->where('date', '<', date('Y-m-d'))->with('subject_teacher')->orderBy('date', 'desc')->get()->groupBy('date');

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        if (in_array('tests.bypass', $permissions)) {
            $teachersSubjectIds = $classSubjectIds;
            $teachersSubjects = $subjects;
        }

        return Inertia::render('Teacher/Tests', 
        [
            'subjects' => $subjects,
            'testsInFuture' => $testsInFuture,
            'testsInPast' => $testsInPast,
            'teachersSubjectIds' => $teachersSubjectIds,
            'teachersSubjects' => $teachersSubjects,
            'classId' => $data['classId'],
            'classes' => SchoolClass::all(),
            'permission' => $permissions
        ]);
    }

    public function store()
    {
        $this->authorize('create', Test::class);

        $data = request()->validate([
            'subject' => 'numeric|required|exists:subject_teachers,id',
            'classId' => 'numeric|required|exists:school_classes,id',
            'date' => 'date|required',
            'note' => 'string|nullable',
        ]);

        $this->authorize('canForSubject', [Test::class, $data['subject']]);

        DB::transaction(function () use ($data) {
            return tap(SchoolClass::find($data['classId'])->tests()->create([
                'date' => $data['date'],
                'note' => $data['note'],
                'subject_teacher_id' => $data['subject'],
            ]), function (Test $test) {
                return $test;
            });
        });
    }

    public function update(Test $test)
    {
        $this->authorize('edit', Test::class);

        $data = request()->validate([
            'date' => 'date|required',
            'note' => 'string|nullable',
        ]);

        $this->authorize('canForSubject', [Test::class, $test->subject_teacher_id]);

        $test->forceFill([
            'date' => $data['date'],
            'note' => $data['note'],
        ])->save();
    }

    public function destroy(Test $test)
    {
        $this->authorize('delete', Test::class);

        $this->authorize('canForSubject', [Test::class, $test->subject_teacher_id]);

        $test->delete();
    }
}
