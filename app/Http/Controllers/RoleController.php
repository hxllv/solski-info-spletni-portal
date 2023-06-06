<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(): Response
    {
        $this->authorize('view', Role::class);

        $data = request()->validate([
            'term' => 'string|nullable',
        ]);

        $mQuery = Role::query();

        $data['term'] = $data['term'] ?? '';

        $mQuery->where('name', 'LIKE', '%'.$data['term'].'%');

        return Inertia::render('Admin/Roles', 
        [
            'roles' => $mQuery->paginate(10),
            'params' => [
                'term' => $data['term'],
            ],
            'middleware' => auth()->user()->role->middlewares->pluck('name')->toArray()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        //dd($request);

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
        ]);

        $role->forceFill([
            'name' => $input['name'],
        ])->save();
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
