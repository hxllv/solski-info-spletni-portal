<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\User;
use Inertia\Response;
use App\Actions\Jetstream\DeleteUser;
use App\Models\Permission;
use App\Models\SchoolClass;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): Response
    {   
        $this->authorize('view', User::class);

        $data = request()->validate([
            'term' => 'string|nullable',
            'role' => 'numeric|nullable',
        ]); 

        $mQuery = User::query();

        $data['term'] = $data['term'] ?? '';
        $data['role'] = $data['role'] ?? '';

        $mQuery->where(function ($query) use ($data) {
            $query->where('surname', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('email', 'LIKE', '%'.$data['term'].'%')
                ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
        });

        if ($data['role'] !== '') 
            $mQuery->where('role_id', $data['role']);

        $permissions = auth()->user()->role->permissions->pluck('name')->toArray();

        if (auth()->user()->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        //dd($mQuery->paginate(10));
        return Inertia::render('Admin/Users', 
            [
                'roles' => Role::all(),
                'classes' => SchoolClass::all(),
                'users' => $mQuery->with(['role', 'studentOf'])->paginate(10),
                'params' => [
                    'term' => $data['term'],
                    'role' => $data['role'],
                ],
                'permission' => $permissions
            ]
        );
    }

    public function update(User $user, Request $request, UpdatesUserProfileInformation $updater)
    {
        $this->authorize('edit', User::class);

        if (Role::where('id', $request->input('role'))->get()->count() === 0)
            return redirect()->back()->withErrors([
                'role' => 'Neveljavna skupina pravic.'
            ]);

        $updater->update($user, $request->all());

        return app(ProfileInformationUpdatedResponse::class);
    }

    public function multiupdate()
    {
        $this->authorize('edit', User::class);

        $data = request()->validate([
            'class' => [Rule::excludeIf(!empty(request()->input('role'))), 'numeric', 'required'],
            'role' => [Rule::excludeIf(!empty(request()->input('class'))), 'numeric', 'required'],
            'userIds' => 'required|array',
        ]); 

        $users = User::find($data['userIds']);

        if (!empty($data['class'])) {
            if ($data['class'] != -1 && Role::find($data['class'])->get()->count() === 0)
                return redirect()->back()->withErrors([
                    'class' => 'Neveljavni razred.'
                ]);

            foreach ($users as $user) {
                if ($data['class'] == -1) {
                    $user->studentOf()->dissociate($data['class'])->save();
                    continue;
                }
                $user->studentOf()->associate($data['class'])->save();
            }
        }
        else if (!empty($data['role'])) {
            if (Role::find($data['role'])->get()->count() === 0)
                return redirect()->back()->withErrors([
                    'role' => 'Neveljavna skupina pravic.'
                ]);

            foreach ($users as $user) {
                $user->role()->associate($data['role'])->save();
            }
        }
    }

    public function multidelete()
    {
        $this->authorize('edit', User::class);

        $data = request()->validate([
            'userIds' => 'required|array',
        ]); 

        $users = User::find($data['userIds']);

        foreach ($users as $user) {
            $user->delete();
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', User::class);

        if (auth()->user()->id === $user->id)
            return redirect()->back()->withErrors([
                'delete' => 'Prijavljeni uporabnik se ne more izbrisati.'
            ]);

        if ($user->is_account_owner)
            return redirect()->back()->withErrors([
                'delete' => 'Glavnega administratorja se ne more izbrisati.'
            ]);

        app(DeleteUser::class)->delete($user);
    }
}
