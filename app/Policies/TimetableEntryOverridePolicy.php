<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\SubjectTeacher;
use App\Models\TimetableEntryOverride;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TimetableEntryOverridePolicy
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
        return ($user->role->permissions->find('timetable.override.view') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function create(User $user): Response
    {
        return ($user->role->permissions->find('timetable.override.create') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function edit(User $user): Response
    {
        return ($user->role->permissions->find('timetable.override.edit') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function delete(User $user): Response
    {
        return ($user->role->permissions->find('timetable.override.delete') || $user->is_account_owner) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function canForClass(User $user, $classId): Response
    {
        if ($user->role->permissions->find('timetable.override.bypass') || $user->is_account_owner)
            return Response::allow();

        if (SchoolClass::find($classId)->user_id != $user->id)
            return Response::denyWithStatus(403);

        return Response::allow();
    }
}
