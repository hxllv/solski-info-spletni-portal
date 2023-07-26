<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectTeacher extends Model
{
    use HasFactory;

    protected $appends = [
        'name', 'users_full_name'
    ];

    public function getNameAttribute()
    {
        return $this->custom_name ? $this->custom_name : $this->subject->name;
    }

    public function getUsersFullNameAttribute()
    {
        return $this->user->full_name;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function timetable_entries()
    {
        return $this->hasMany(TimetableEntry::class, 'subject_teacher_id');
    }

    public function grades()
    {
        return $this->hasMany(Gradebook::class, 'subject_teacher_id');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'subject_teacher_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'subject_teacher_id');
    }
}

