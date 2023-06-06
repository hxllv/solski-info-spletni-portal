<?php

namespace App\Http\Controllers;

use App\Models\Middleware;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    readonly array $adminPanelSubMiddlewares;

    public function __construct() {
        $this->adminPanelSubMiddlewares = [
            'users.view',
            'users.invite',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
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

        $middlewares = auth()->user()->role->middlewares->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $middlewares = Middleware::all()->pluck('name')->toArray();

        return Inertia::render('Admin/Roles', 
        [
            'roles' => $mQuery->with('middlewares')->paginate(10),
            'params' => [
                'term' => $data['term'],
            ],
            'middleware' => $middlewares
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')],
        ]);

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
            'middlewares' => ['exclude_if:middlewares,false', 'array']
        ]);

        $role->forceFill([
            'name' => $input['name'],
        ]);

        $middlewares = $input['middlewares'];

        if (!$middlewares)
            return;

        if (!$middlewares['admin.panel.view']) {
            $role->middlewares()->detach('admin.panel.view');
            $role->middlewares()->detach($this->adminPanelSubMiddlewares);
        }
        else {
            $role->middlewares()->attach('admin.panel.view');
            $role->middlewares()->attach(
                // DEMON HACK, thank god for php helpers
                array_flip(array_intersect_key(
                    // get keys where true + flip
                    array_flip(array_keys($middlewares, true)), 
                    // flip the array so the values become the keys
                    array_flip($this->adminPanelSubMiddlewares)
                ))
            );
        }

        $role->save();
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', Role::class);

        if ($role->users->count() !== 0)
            return redirect()->back()->withErrors([
                'delete' => 'Role has users assigned to it.'
            ]);

        $role->delete();
    }
}
