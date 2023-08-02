<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'is_account_owner', 'is_registered'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'full_name', 'role_name', 'class_name'
    ];

    public function getFullNameAttribute()
    {
        if (!$this->is_registered) 
            return "$this->name $this->surname (neregistriran)";

        return "$this->name $this->surname";
    }

    public function getRoleNameAttribute()
    {
        return $this->role->name;
    }

    public function getClassNameAttribute()
    {
        return $this->studentOf ? $this->studentOf->name : null;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function classTeacherOf()
    {
        return $this->hasMany(SchoolClass::class, 'user_id', 'id');
    }

    public function studentOf()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id', 'id');
    }

    public function teacherOf()
    {
        return $this->belongsToMany(Subject::class, 'subject_teachers', 'user_id')->withPivot(['custom_name', 'id'])->withTimestamps();
    }

    public function teacherOfPivot() {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function parents() {
        return $this->belongsToMany(User::class, 'parents', 'child_id', 'parent_id');
    }

    public function children() {
        return $this->belongsToMany(User::class, 'parents', 'parent_id', 'child_id');
    }

    public function grades() {
        return $this->hasMany(Gradebook::class, 'user_id');
    }

    public function absences() {
        return $this->hasMany(Absence::class, 'user_id');
    }
}
