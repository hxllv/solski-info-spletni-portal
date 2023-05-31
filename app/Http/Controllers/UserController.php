<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\User;
use Inertia\Response;
use App\Actions\Jetstream\DeleteUser;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Users', 
            [
                'roles' => Role::all(),
                'users' => User::all(),
                'can' => [
                    'invite' => auth()->user()->can('invite', User::class)
                ]
            ]
        );
    }

    public function update(User $user, Request $request, UpdatesUserProfileInformation $updater)
    {
        if (Role::where('id', $request->input('role'))->get()->count() === 0)
            return redirect()->back()->withErrors([
                'role' => 'Neveljavna skupina pravic.'
            ]);

        $updater->update($user, $request->all());

        return app(ProfileInformationUpdatedResponse::class);
    }

    public function destroy(User $user)
    {
        if (auth()->user()->id === $user->id)
            abort(403, 'Cannot delete yourself');

        app(DeleteUser::class)->delete($user);
    }
}
