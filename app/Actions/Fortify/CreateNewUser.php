<?php

namespace App\Actions\Fortify;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(Role::find(1)->users()->create([
                'name' => $input['name'],
                'surname' => $input['surname'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'is_account_owner' => true,
                'is_registered' => true,
            ]), function (User $user) {
                return $user;
            });
        });
    }

    /**
     * Create a newly registered user (via invite).
     *
     * @param  array<string, string>  $input
     */
    public function invite(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'numeric'],
            'class' => ['required', 'numeric'],
        ])->validate();

        $role = Role::find($input['role']);

        return DB::transaction(function () use ($input, $role) {
            return tap($role->users()->create([
                'name' => $input['name'],
                'surname' => $input['surname'],
                'email' => $input['email'],
                'password' => Hash::make(Str::uuid()->toString()),
                'is_account_owner' => false,
                'is_registered' => false,
            ]), function (User $user) use ($input) {
                if (!empty($input['class']) && $input['class'] != -1)
                    $user->studentOf()->associate($input['class'])->save();
                return $user;
            });
        });
    }

    public function inviteFinal(array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
            'userId' => ['required', 'numeric'],
        ])->validate();

        User::find($input['userId'])->forceFill([
            'password' => Hash::make($input['password']),
            'is_registered' => true,
        ])->save();
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
