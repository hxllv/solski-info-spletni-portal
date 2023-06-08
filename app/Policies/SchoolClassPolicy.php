<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class SchoolClassPolicy
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
        return ($user->role->middlewares->find('classes.view') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function create(User $user): Response
    {
        return ($user->role->middlewares->find('classes.create') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function edit(User $user): Response
    {
        return ($user->role->middlewares->find('classes.edit') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function delete(User $user): Response
    {
        return ($user->role->middlewares->find('classes.delete') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }
}
