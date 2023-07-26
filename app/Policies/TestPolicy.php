<?php

namespace App\Policies;

use App\Models\SubjectTeacher;
use App\Models\Test;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TestPolicy
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
        return ($user->role->permissions->find('tests.view') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function create(User $user): Response
    {
        return ($user->role->permissions->find('tests.create') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function edit(User $user): Response
    {
        return ($user->role->permissions->find('tests.edit') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function delete(User $user): Response
    {
        return ($user->role->permissions->find('tests.delete') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function approval(User $user): Response
    {
        return ($user->role->permissions->find('tests.approval') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function canFor(User $user, $subjectId): Response
    {
        if ($user->role->permissions->find('tests.bypass') || $user->is_account_owner)
            return Response::allow();

        if (SubjectTeacher::find($subjectId)->user_id != $user->id)
            return Response::denyWithStatus(403);

        return Response::allow();
    }
}
