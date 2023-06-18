<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'day', 'hour'
    ];

    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }

    public function school_class()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
