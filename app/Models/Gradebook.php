<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gradebook extends Model
{
    use HasFactory;

    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
