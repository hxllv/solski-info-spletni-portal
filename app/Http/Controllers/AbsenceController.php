<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Permission;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\SubjectTeacher;
use App\Models\TimetableEntry;
use App\Models\TimetableEntryOverride;
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

        $todaysEntries = TimetableEntry::where('day', $dayOfWeek)
            ->where('school_class_id', $data['classId'])->with('subject_teacher')->orderBy('hour', 'asc')->get();

        $todaysOverrides = TimetableEntryOverride::where('date', $data['forDate'])
            ->where('school_class_id', $data['classId'])->with('subject_teacher')->get()->groupBy('hour');

        $students = SchoolClass::with('students')->find($data['classId'])->students;

        $studentsPaginated = User::where(function ($query) use ($data) {
            $query->where('surname', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('email', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
        })->where('school_class_id', $data['classId']);

        if ($data['hourAndSubject']) {
            $data['hourAndSubject'] = json_decode($data['hourAndSubject']);

            $studentsPaginated->whereDoesntHave('absences', function($query) use ($data) {
                $query->where('date', $data['forDate'])
                    ->where('hour', $data['hourAndSubject'][0]);
            });
        }

        $absences = null;

        if ($data['userId']) {
            $absences = Absence::where('user_id', $data['userId'])->with('subject_teacher')->orderBy('date', 'desc')->orderBy('hour', 'asc')->get()->groupBy('date');
        }

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        foreach ($todaysEntries as $key => $todaysEntry) {
            if (array_key_exists($todaysEntry->hour, $todaysOverrides->toArray()) && $todaysOverrides[$todaysEntry->hour]->count()) {
                if (
                    $todaysOverrides[$todaysEntry->hour][0]->subject_teacher_id != null && 
                    (
                        $todaysOverrides[$todaysEntry->hour][0]->subject_teacher->user_id == auth()->user()->id ||
                        in_array('absences.bypass.subject', $permissions)
                    )
                )
                    $todaysEntry->override = $todaysOverrides[$todaysEntry->hour][0]->subject_teacher;
                else
                    $todaysEntries->pull($key);
            }
        }

        $shouldFilter = true;

        if (in_array('absences.bypass.subject', $permissions)) {
            $teachersSubjects = SchoolClass::with('timetable_entries')->find($data['classId'])->timetable_entries->pluck('subject_teacher_id')->flatten();
            $shouldFilter = false;
        }

        // filter the array to only have data of auth user

        if ($shouldFilter) {
            $todaysEntries = $todaysEntries->filter(function ($todaysEntry, $key) {
                if (array_key_exists('override', $todaysEntry->toArray()))
                    return $todaysEntry->override->user_id == auth()->user()->id;

                return $todaysEntry->subject_teacher->user_id == auth()->user()->id;
            });
        }

        $classTeacherClassIds = auth()->user()->classTeacherOf->pluck('id')->flatten();

        if (in_array('absences.bypass.class', $permissions)) {
            $classTeacherClassIds = SchoolClass::all()->pluck('id')->flatten();
        }

        return Inertia::render('Teacher/Absences', 
        [
            'availableAbsences' => $todaysEntries->values(),
            'absences' => $absences,
            'students' => $students,
            'studentsPaginated' => $studentsPaginated->paginate(10),
            'teachersSubjects' => $teachersSubjects,
            'userId' => $data['userId'],
            'classId' => $data['classId'],
            'classTeacherClassIds' => $classTeacherClassIds,
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

        $this->authorize('canForSubject', [Absence::class, $data['subject']]);

        if (
            Absence::where('date', $data['date'])
                ->where('hour', $data['hour'])
                ->whereIn('user_id', $data['userIds'])->get()->count() != 0
        )
            return redirect()->back()->withErrors([
                'extra' => 'Uporabnik že ima izostanek za dani datum in uro.'
            ]);

        DB::transaction(function () use ($data) {
            foreach ($data['userIds'] as $userId) {
                tap(User::find($userId)->absences()->create([
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
        $this->authorize('edit', Absence::class);

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

        $this->authorize('canForSubject', [Absence::class, $absence->subject_teacher_id]);

        $absence->delete();
    }
}
