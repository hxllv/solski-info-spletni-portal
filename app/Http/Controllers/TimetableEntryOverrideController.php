<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\SchoolClass;
use App\Models\SubjectTeacher;
use App\Models\TimetableEntry;
use App\Models\TimetableEntryOverride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TimetableEntryOverrideController extends Controller
{
    public function index()
    {
        $this->authorize('view', TimetableEntryOverride::class);

        $data = request()->validate([
            'classId' => 'numeric|nullable',
            'forDate' => 'date|nullable',
        ]);

        $data['classId'] = $data['classId'] ?? '';
        $data['forDate'] = $data['forDate'] ?? date('Y-m-d');

        if (!$data['classId']) {
            return redirect('/teacher/');
        }

        $dayOfWeek = date('w', strtotime($data['forDate'])) - 1;

        $hoursAlreadyOverriden = TimetableEntryOverride::where('date', $data['forDate'])->get()->pluck('hour')->flatten();

        $todaysEntries = TimetableEntry::where('day', $dayOfWeek)->where('school_class_id', $data['classId'])
            ->whereNotIn('hour', $hoursAlreadyOverriden)
            ->with('subject_teacher')->orderBy('hour', 'asc')->get();

        $overridesInFuture = TimetableEntryOverride::where('school_class_id', $data['classId'])->where('date', '>=', date('Y-m-d'))->with('subject_teacher')->orderBy('date', 'asc')->orderBy('hour', 'asc')->get()->groupBy('date');
        $overridesInPast = TimetableEntryOverride::where('school_class_id', $data['classId'])->where('date', '<', date('Y-m-d'))->with('subject_teacher')->orderBy('date', 'desc')->orderBy('hour', 'asc')->get()->groupBy('date');

        $allTimetableEntries = TimetableEntry::where('school_class_id', $data['classId'])->with('subject_teacher')->get()->groupBy('hour');

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        $classTeacherClassIds = auth()->user()->classTeacherOf->pluck('id')->flatten();

        if (in_array('timetable.override.bypass', $permissions)) {
            $classTeacherClassIds = SchoolClass::all()->pluck('id')->flatten();
        }

        return Inertia::render('Teacher/TimetableOverrides', 
        [
            'todaysEntries' => $todaysEntries,
            'allSubjects' => SubjectTeacher::all(),
            'allTimetableEntries' => $allTimetableEntries,
            'overridesInFuture' => $overridesInFuture,
            'overridesInPast' => $overridesInPast,
            'classId' => $data['classId'],
            'classTeacherClassIds' => $classTeacherClassIds,
            'forDate' => $data['forDate'],
            'classes' => SchoolClass::all(),
            'permission' => $permissions,
        ]);
    }

    public function store()
    {
        $this->authorize('create', TimetableEntryOverride::class);

        $subjectValidator = '|exists:subject_teachers,id';

        if (request()->input('subject') == -1)
            $subjectValidator = "";

        $data = request()->validate([
            'subject' => "numeric|required$subjectValidator",
            'classId' => 'numeric|required|exists:school_classes,id',
            'date' => 'date|required',
            'hour' => 'numeric|required',
            'note' => 'string|nullable',
        ]);

        $data['subject'] = $data['subject'] > 0 ? $data['subject'] : null;

        $this->authorize('canForClass', [TimetableEntryOverride::class, $data['classId']]);

        if (
            TimetableEntryOverride::where('date', $data['date'])
                ->where('hour', $data['hour'])
                ->where('school_class_id', $data['classId'])->get()->count() != 0
        )
            return redirect()->back()->withErrors([
                'extra' => 'Nadomeščanje za dano uro in datum že obstaja.'
            ]);

        DB::transaction(function () use ($data) {
            return tap(SchoolClass::find($data['classId'])->overrides()->create([
                'date' => $data['date'],
                'hour' => $data['hour'],
                'note' => $data['note'],
                'subject_teacher_id' => $data['subject'],
            ]), function (TimetableEntryOverride $override) {
                return $override;
            });
        });
    }

    public function update(TimetableEntryOverride $override)
    {
        $this->authorize('edit', TimetableEntryOverride::class);

        $data = request()->validate([
            'note' => 'string|nullable',
        ]);

        $this->authorize('canForClass', [TimetableEntryOverride::class, $override->school_class_id]);

        $override->forceFill([
            'note' => $data['note'],
        ])->save();
    }

    public function destroy(TimetableEntryOverride $override)
    {
        $this->authorize('delete', TimetableEntryOverride::class);

        $this->authorize('canForClass', [TimetableEntryOverride::class, $override->school_class_id]);

        $override->delete();
    }
}
