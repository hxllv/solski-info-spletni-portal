<?php

namespace App\Http\Controllers;

use App\Models\Permission;
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

        $mQuery->where('name', 'LIKE', '%'.$data['term'].'%');

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        $potentialTeachers = Permission::find('class.teacher')->roles()->with('users')->get()->pluck('users')->flatten();

        return Inertia::render('Admin/Classes', 
        [
            'classes' => $mQuery->with('classTeacher')->paginate(10),
            'users' => $potentialTeachers,
            'params' => [
                'term' => $data['term'],
            ],
            'permission' => $permissions
        ]);
    }

    public function view(SchoolClass $class): Response
    {
        $this->authorize('view', SchoolClass::class);

        $data = request()->validate([
            'term' => 'string|nullable',
            'role' => 'numeric|nullable',
            'term_adding' => 'string|nullable',
            'role_adding' => 'numeric|nullable',
        ]);

        // students

        $mQuery = $class->students();

        $data['term'] = $data['term'] ?? '';
        $data['role'] = $data['role'] ?? '';

        $mQuery->where(function ($query) use ($data) {
            $query->where('surname', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('email', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
        });

        if ($data['role'] !== '') 
            $mQuery->where('role_id', $data['role']);

        // potential students

        $nQuery = User::query();

        $data['term_adding'] = $data['term_adding'] ?? '';
        $data['role_adding'] = $data['role_adding'] ?? '';

        $nQuery->where('school_class_id', '!=' , $class->id)->orWhereNull('school_class_id');
        $nQuery->where(function ($query) use ($data) {
            $query->where('surname', 'LIKE', '%'.$data['term_adding'].'%')
                ->orWhere('email', 'LIKE', '%'.$data['term_adding'].'%')
                ->orWhere('name', 'LIKE', '%'.$data['term_adding'].'%');
        });

        if ($data['role_adding'] !== '') 
            $nQuery->where('role_id', $data['role_adding']);

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        return Inertia::render('Admin/Class', 
        [
            'sClass' => $class,
            'classTeacher' => $class->classTeacher,
            'students' => $mQuery->with('role')->paginate(10),
            'potentialStudents' => $nQuery->with(['role', 'studentOf'])->paginate(10, ['*'], 'ps_page'),
            'roles' => Role::all(),
            'params' => [
                'page' => request()->input('page'),
                'term' => $data['term'],
                'term_adding' => $data['term_adding'],
                'role' => $data['role'],
                'role_adding' => $data['role_adding'],
            ],
            'permission' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', SchoolClass::class);

        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('school_classes')],
            'class_teacher' => ['required', 'numeric'],
        ])->validate();

        $user = User::find($input['class_teacher']);

        DB::transaction(function () use ($input, $user) {
            return tap($user->classTeacherOf()->create([
                'name' => $input['name'],
            ]), function (SchoolClass $class) {
                return $class;
            });
        });
    }

    public function update(SchoolClass $class, Request $request)
    {
        $this->authorize('edit', SchoolClass::class);

        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('school_classes')->ignore($class->id)],
            'class_teacher' => ['required', 'numeric'],
        ])->validate();

        $class->forceFill([
            'name' => $input['name'],
        ]);

        $class->classTeacher()->associate($input['class_teacher'])->save();
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
