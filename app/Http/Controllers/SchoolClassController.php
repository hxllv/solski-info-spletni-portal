<?php

namespace App\Http\Controllers;

use App\Models\Middleware;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SchoolClassController extends Controller
{
    public function index(): Response
    {
        $this->authorize('view', SchoolClass::class);

        $data = request()->validate([
            'term' => 'string|nullable',
        ]);

        $mQuery = SchoolClass::query();

        $data['term'] = $data['term'] ?? '';

        $mQuery->where('class_name', 'LIKE', '%'.$data['term'].'%');

        $middlewares = auth()->user()->role->middlewares->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $middlewares = Middleware::all()->pluck('name')->toArray();

        $potentialTeachers = Middleware::find('class.teacher')->roles()->with('users')->get()->pluck('users')->flatten();

        return Inertia::render('Admin/Classes', 
        [
            'classes' => $mQuery->with(['classTeacher', 'students'])->paginate(10),
            'users' => $potentialTeachers,
            'params' => [
                'term' => $data['term'],
            ],
            'middleware' => $middlewares
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', SchoolClass::class);

        $input = $request->all();

        Validator::make($input, [
            'class_name' => ['required', 'string', 'max:255', Rule::unique('school_classes')],
            'class_teacher' => ['required', 'numeric'],
        ])->validate();

        $user = User::find($input['class_teacher']);

        DB::transaction(function () use ($input, $user) {
            return tap($user->classTeacherOf()->create([
                'class_name' => $input['class_name'],
            ]), function (SchoolClass $class) {
                return $class;
            });
        });
    }

    public function destroy(SchoolClass $class)
    {
        $this->authorize('delete', SchoolClass::class);

        foreach ($class->students as $student) {
            $student->studentOf()->dissociate()->save();
        }

        $class->delete();
    }
}
