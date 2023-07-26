<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Permission;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\SubjectTeacher;
use App\Models\TimetableEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AbsenceController extends Controller
{
    public function index()
    {
        $this->authorize('view', Absence::class);

        $data = request()->validate([
            'userId' => 'numeric|nullable',
            'classId' => 'numeric|nullable',
            'forDate' => 'date|nullable',
            'hourAndSubject' => 'string|nullable',
            'term' => 'string|nullable',
        ]);

        $data['classId'] = $data['classId'] ?? '';
        $data['userId'] = $data['userId'] ?? '';
        $data['forDate'] = $data['forDate'] ?? date('Y-m-d');
        $data['hourAndSubject'] = $data['hourAndSubject'] ?? '';
        $data['term'] = $data['term'] ?? '';

        if (!$data['classId']) {
            return redirect('/teacher/');
        }

        $dayOfWeek = date('w', strtotime($data['forDate'])) - 1;

        $teachersSubjects = auth()->user()->teacherOfPivot->pluck('id')->flatten();

        $todaysEntries = TimetableEntry::where('day', $dayOfWeek)->whereHas('subject_teacher', function($query) {
            $query->where('user_id', auth()->user()->id);
        })->whereHas('school_class', function($query) use ($data) {
            $query->where('id', $data['classId']);
        })->with('subject_teacher')->get();

        $students = SchoolClass::with('students')->find($data['classId'])->students;

        $studentsPaginated = SchoolClass::with('students')->find($data['classId'])->students()->where(function ($query) use ($data) {
            $query->where('surname', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('email', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
        });

        if ($data['hourAndSubject']) {
            $data['hourAndSubject'] = json_decode($data['hourAndSubject']);

            $studentsPaginated->doesntHave('absences')->orWhereHas('absences', function($query) use ($data) {
                $query->where('date', '!=', $data['forDate'])
                    ->orWhere('hour', '!=', $data['hourAndSubject'][0])
                    ->orWhere('subject_teacher_id', '!=', $data['hourAndSubject'][1]);
            });
        }

        $absences = null;

        if ($data['userId']) {
            $absences = Absence::where('user_id', $data['userId'])->with('subject_teacher')->orderBy('date', 'desc')->get()->groupBy('date');
        }

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        if (in_array('gradebook.bypass', $permissions) || auth()->user()->is_account_owner) {
            $teachersSubjects = SchoolClass::with('timetable_entries')->find($data['classId'])->timetable_entries->pluck('subject_teacher_id')->flatten();
            $todaysEntries = TimetableEntry::where('day', $dayOfWeek)->whereHas('school_class', function($query) use ($data) {
                $query->where('id', $data['classId']);
            })->with('subject_teacher')->get();
        }

        return Inertia::render('Teacher/Absences', 
        [
            'availableAbsences' => $todaysEntries,
            'absences' => $absences,
            'students' => $students,
            'studentsPaginated' => $studentsPaginated->paginate(10),
            'teachersSubjects' => $teachersSubjects,
            'userId' => $data['userId'],
            'classId' => $data['classId'],
            'classTeacherClassIds' => auth()->user()->classTeacherOf->pluck('id')->flatten(),
            'forDate' => $data['forDate'],
            'classes' => SchoolClass::all(),
            'permission' => $permissions,
            'params' => [
                'term' => $data['term'],
            ],
        ]);
    }

    public function store()
    {
        $this->authorize('create', Absence::class);

        $data = request()->validate([
            'userIds' => 'array|required|exists:users,id',
            'subject' => 'numeric|required|exists:subject_teachers,id',
            'date' => 'date|required',
            'hour' => 'numeric|required',
        ]);

        $this->authorize('canFor', [Absence::class, $data['subject']]);

        DB::transaction(function () use ($data) {
            foreach ($data['userIds'] as $userId) {
                return tap(User::find($userId)->absences()->create([
                    'date' => $data['date'],
                    'hour' => $data['hour'],
                    'subject_teacher_id' => $data['subject'],
                    'status' => 0
                ]), function (Absence $absence) {
                    return $absence;
                });
            }
        });
    }

    public function approval(Absence $absence)
    {
        $this->authorize('approval', Absence::class);

        $data = request()->validate([
            'status' => 'numeric|required|max:3|min:0',
            'classId' => 'numeric|required|exists:school_classes,id',
        ]);

        $this->authorize('canForClass', [Absence::class, $data['classId']]);

        $absence->forceFill([
            'status' => $data['status'],
        ])->save();
    }

    public function excuse(Absence $absence)
    {
        $this->authorize('approval', Absence::class);

        $data = request()->validate([
            'excuse' => 'string|required',
            'classId' => 'numeric|required|exists:school_classes,id',
        ]);

        $this->authorize('canForClass', [Absence::class, $data['classId']]);

        $absence->forceFill([
            'excuse' => $data['excuse'],
        ])->save();
    }

    public function destroy(Absence $absence)
    {
        $this->authorize('delete', Absence::class);

        $this->authorize('canFor', [Absence::class, $absence->subject_teacher->id]);

        $absence->delete();
    }
}
