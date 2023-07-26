<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'hour', 'excuse', 'status', 'subject_teacher_id', 'user_id'
    ];

    protected $appends = [
        'hour_name'
    ];

    public function getHourNameAttribute()
    {
        $hours = json_decode(Setting::find('timetable.hours')->value);

        $filtered = array_filter($hours, function ($var) {
            return $var->index == (float)$this->hour;
        });

        return array_shift($filtered)->name;
    }

    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
