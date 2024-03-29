<?php

namespace App\Policies;

use App\Models\Gradebook;
use App\Models\SubjectTeacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GradebookPolicy
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
        return ($user->role->permissions->find('gradebook.view') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function create(User $user): Response
    {
        return ($user->role->permissions->find('gradebook.create') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function edit(User $user): Response
    {
        return ($user->role->permissions->find('gradebook.edit') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function delete(User $user): Response
    {
        return ($user->role->permissions->find('gradebook.delete') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function canForSubject(User $user, $subjectId): Response
    {
        if ($user->role->permissions->find('gradebook.bypass') || $user->is_account_owner)
            return Response::allow();

        if (SubjectTeacher::find($subjectId)->user_id != $user->id)
            return Response::denyWithStatus(403);

        return Response::allow();
    }
}
