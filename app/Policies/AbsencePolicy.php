<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\SchoolClass;
use App\Models\SubjectTeacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsencePolicy
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
        return ($user->role->permissions->find('absences.view') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function create(User $user): Response
    {
        return ($user->role->permissions->find('absences.create') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function edit(User $user): Response
    {
        return ($user->role->permissions->find('absences.edit') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function delete(User $user): Response
    {
        return ($user->role->permissions->find('absences.delete') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function approval(User $user): Response
    {
        return ($user->role->permissions->find('absences.approval') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function canForSubject(User $user, $subjectId): Response
    {
        if ($user->role->permissions->find('absences.bypass.subject') || $user->is_account_owner)
            return Response::allow();

        if (SubjectTeacher::find($subjectId)->user_id != $user->id)
            return Response::denyWithStatus(403);

        return Response::allow();
    }

    public function canForClass(User $user, $classId): Response
    {
        if ($user->role->permissions->find('absences.bypass.class') || $user->is_account_owner)
            return Response::allow();

        if (SchoolClass::find($classId)->user_id != $user->id)
            return Response::denyWithStatus(403);

        return Response::allow();
    }
}
