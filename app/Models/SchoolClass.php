<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    public function students()
    {
        return $this->hasMany(User::class, 'school_class_id', 'id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function timetable_entries()
    {
        return $this->hasMany(TimetableEntry::class, 'school_class_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'school_class_id');
    }

    public function overrides()
    {
        return $this->hasMany(TimetableEntryOverride::class, 'school_class_id');
    }
}
