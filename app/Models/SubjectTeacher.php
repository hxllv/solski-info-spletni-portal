<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectTeacher extends Model
{
    use HasFactory;

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
}

