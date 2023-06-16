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

        $allUsers = Permission::find('teacher')->roles()->with('users')->get()->pluck('users')->flatten()->pluck('id')->toArray();
        $subjectUsers = $subject->teachedBy->pluck('id')->toArray();

        $potentialUsers = User::find(array_diff($allUsers, $subjectUsers));

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        return Inertia::render('Admin/Subject', 
        [
            'subject' => $subject,
            'users' => $mQuery->with(['role', 'teacherOf'])->paginate(10),
            'potentialTeachers' => $potentialUsers,
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
            'teacher' => ['required', 'numeric'],
        ])->validate();

        $permArr = User::find($input['teacher'])->role()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('name')->toArray();

        if (!in_array('teacher', $permArr))
            return redirect()->back()->withErrors([
                'teacher' => 'Uporabnik ne more biti učitelj.'
            ]);

        if ($subject->teachedBy()->find($input['teacher'])) {
            $subject->teachedBy()->detach($input['teacher']);
            return;
        }

        $subject->teachedBy()->sync([$input['teacher'] => ['custom_name' => $input['custom_name']]], false);
    }
}
