<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableEntryOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'hour', 'note', 'subject_teacher_id'
    ];

    protected $appends = [
        'hour_name', 'day'
    ];

    public function getHourNameAttribute()
    {
        $hours = json_decode(Setting::find('timetable.hours')->value);

        $filtered = array_filter($hours, function ($var) {
            return $var->index == (float)$this->hour;
        });

        return array_shift($filtered)->name;
    }

    public function getDayAttribute()
    {
        return date('w', strtotime($this->date)) - 1;
    }


    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }

    public function school_class()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
