<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Subject;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index(): Response
    {   
        $this->authorize('view', Subject::class);

        $data = request()->validate([
            'term' => 'string|nullable',
        ]); 

        $mQuery = Subject::query();

        $data['term'] = $data['term'] ?? '';

        $mQuery->where('name', 'LIKE', '%'.$data['term'].'%');

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        //dd($mQuery->paginate(10));
        return Inertia::render('Admin/Subjects', 
            [
                'subjects' => $mQuery->paginate(10),
                'params' => [
                    'term' => $data['term'],
                ],
                'permission' => $permissions
            ]
        );
    }

    public function view(Subject $subject): Response
    {
        $this->authorize('view', Subject::class);

        $data = request()->validate([
            'term' => 'string|nullable',
            'role' => 'numeric|nullable',
            'term_adding' => 'string|nullable',
            'role_adding' => 'numeric|nullable',
        ]);

        // teachers

        $mQuery = $subject->teachedBy();

        $data['term'] = $data['term'] ?? '';
        $data['role'] = $data['role'] ?? '';

        $mQuery->where(function ($query) use ($data) {
            $query->where('surname', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('email', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
        });

        if ($data['role'] !== '') 
            $mQuery->where('role_id', $data['role']);

        // potential teachers

        // not a great way to find users 

        $rolesWithTeacher = Permission::find('teacher.panel.view')->roles->pluck('id')->toArray();
        $nQuery = User::query();

        $data['term_adding'] = $data['term_adding'] ?? '';
        $data['role_adding'] = $data['role_adding'] ?? '';

        $nQuery->where(function ($query) use ($data) {
            $query->where('surname', 'LIKE', '%'.$data['term_adding'].'%')
                ->orWhere('email', 'LIKE', '%'.$data['term_adding'].'%')
                ->orWhere('name', 'LIKE', '%'.$data['term_adding'].'%');
        });

        if ($data['role_adding'] !== '') 
            $nQuery->where('role_id', $data['role_adding']);

        $allUsers = $nQuery->whereHas('role', function($query) use ($rolesWithTeacher){
            $query->whereIn('id', $rolesWithTeacher);
        })->get()->pluck('id')->toArray();

        $subjectUsers = $subject->teachedBy->pluck('id')->toArray();
        $potentialUsers = User::whereIn('id', array_diff($allUsers, $subjectUsers));

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        //dd($mQuery->with(['teacherOf'])->get());

        return Inertia::render('Admin/Subject', 
        [
            'subject' => $subject,
            'users' => $mQuery->with(['role', 'teacherOf'])->paginate(10),
            'potentialTeachers' => $potentialUsers->with(['role'])->paginate(10, ['*'], 'ps_page'),
            'roles' => Role::all(),
            'params' => [
                'page' => request()->input('page'),
                'term' => $data['term'],
                'role' => $data['role'],
            ],
            'permission' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Subject::class);

        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('subjects')],
        ])->validate();

        DB::transaction(function () use ($input) {
            return tap(Subject::create([
                'name' => $input['name'],
            ]), function (Subject $role) {
                return $role;
            });
        });
    }

    public function update(Subject $subject, Request $request)
    {
        $this->authorize('edit', Subject::class);

        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('subjects')->ignore($subject->id)],
        ])->validate();

        $subject->forceFill([
            'name' => $input['name'],
        ])->save();
    }

    public function destroy(Subject $subject)
    {
        $this->authorize('delete', Subject::class);

        if ($subject->teachedBy->count() !== 0)
            return redirect()->back()->withErrors([
                'delete' => 'Predmet ima nosilce.'
            ]);

        $subject->delete();
    }

    public function toggle(Subject $subject, Request $request)
    {
        $this->authorize('edit', Subject::class);

        $input = $request->all();

        Validator::make($input, [
            'custom_name' => ['string', 'nullable', 'max:255', Rule::unique('subjects', 'name')->ignore($subject->id)],
            'userIds' => ['required', 'array'],
            'detaching' => ['required', 'boolean'],
        ])->validate();

        foreach ($input['userIds'] as $user) {
            if (User::find($user)->get()->count() === 0)
                return redirect()->back()->withErrors([
                    'userIds' => 'Neveljavni uporabnik med izbranimi.'
                ]);

            $permArr = User::find($user)->role()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('name')->toArray();
            if (!in_array('teacher.panel.view', $permArr))
                return redirect()->back()->withErrors([
                    'teacher' => 'Uporabnik ne more biti učitelj.'
                ]);
        }

        if ($input['detaching']) {
            $subject->teachedBy()->detach($input['userIds']);
            return;
        }

        $subject->teachedBy()->syncWithPivotValues($input['userIds'], ['custom_name' => $input['custom_name']], false);
    }
}
