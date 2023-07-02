<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    readonly array $adminPanelSubPermissions;

    public function __construct() {
        $this->adminPanelSubPermissions = [
            'users.view',
            'users.invite',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'classes.view',
            'classes.invite',
            'classes.edit',
            'classes.delete',
            'subjects.view',
            'subjects.create',
            'subjects.edit',
            'subjects.delete',
            'timetable.edit',
            'settings.edit',
            'settings.view',
        ];
    }

    public function index(): Response
    {
        $this->authorize('view', Role::class);

        $data = request()->validate([
            'term' => 'string|nullable',
        ]);

        $mQuery = Role::query();

        $data['term'] = $data['term'] ?? '';

        $mQuery->where('name', 'LIKE', '%'.$data['term'].'%');

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        return Inertia::render('Admin/Roles', 
        [
            'roles' => $mQuery->with('permissions')->paginate(10),
            'params' => [
                'term' => $data['term'],
            ],
            'permission' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')],
        ])->validate();

        DB::transaction(function () use ($input) {
            return tap(Role::create([
                'name' => $input['name'],
            ]), function (Role $role) {
                return $role;
            });
        });
    }

    public function update(Role $role, Request $request)
    {
        $this->authorize('edit', Role::class);

        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'permissions' => ['exclude_if:permissions,false', 'array']
        ])->validate();

        $role->forceFill([
            'name' => $input['name'],
        ]);

        $permissions = $input['permissions'];

        if (!$permissions)
            return;

        $role->permissions()->syncWithoutDetaching(array_keys($permissions, true));
        $role->permissions()->detach(array_keys($permissions, false));

        if (!$permissions['admin.panel.view']) {
            $role->permissions()->detach('admin.panel.view');
            $role->permissions()->detach($this->adminPanelSubPermissions);
        }

        $role->save();
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', Role::class);

        if ($role->users->count() !== 0)
            return redirect()->back()->withErrors([
                'delete' => 'Skupina pravic ima uporabnike, ki jo uporabljajo.'
            ]);

        $role->delete();
    }
}
