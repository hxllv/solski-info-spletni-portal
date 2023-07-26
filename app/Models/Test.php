<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'note', 'subject_teacher_id'
    ];

    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
