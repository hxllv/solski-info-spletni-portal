<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TimetableEntryController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('edit', TimetableEntry::class);

        $input = $request->all();

        Validator::make($input, [
            'hour' => ['required', 'numeric'],
            'day' => ['required', 'numeric'],
            'subject' => ['required', 'numeric'],
            'class' => ['required', 'numeric'],
        ])->validate();

        if (SubjectTeacher::find($request->input('subject'))->get()->count() === 0)
            return redirect()->back()->withErrors([
                'subject' => 'Neveljaven predmet.'
            ]);

        if (SchoolClass::find($request->input('class'))->get()->count() === 0)
            return redirect()->back()->withErrors([
                'class' => 'Neveljaven razred.'
            ]);

        $class = SchoolClass::find($input['class']);
        $subject = SubjectTeacher::find($input['subject']);

        DB::transaction(function () use ($input, $class, $subject) {
            return tap($class->timetable_entries()->create([
                'hour' => $input['hour'],
                'day' => $input['day'],
            ]), function (TimetableEntry $entry) use ($subject) {
                $entry->subject_teacher()->associate($subject)->save();
                return $entry;
            });
        });
    }

    public function destroy(TimetableEntry $entry)
    {
        $this->authorize('delete', TimetableEntry::class);

        $entry->delete();
    }
}
