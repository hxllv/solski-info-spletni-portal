<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user): Response
    {
        return ($user->role->permissions->find('users.view') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function invite(User $user): Response
    {
        return ($user->role->permissions->find('users.invite') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function edit(User $user): Response
    {
        return ($user->role->permissions->find('users.edit') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function delete(User $user): Response
    {
        return ($user->role->permissions->find('users.delete') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function canViewUser(User $user, $userId): Response
    {
        $permissions = $user->role->permissions->pluck('name')->toArray();

        if ($user->is_account_owner) 
            $permissions = Permission::all()->pluck('name')->toArray();

        $userIds = collect();

        if (in_array('dashboard.children.view', $permissions))
            $userIds = auth()->user()->children->pluck('id');

        if (in_array('dashboard.me.view', $permissions))
            $userIds->push(auth()->user()->id);

        if (in_array('dashboard.all.view', $permissions))
            $userIds = User::all()->pluck('id');

        return $userIds->contains($userId) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }
}
