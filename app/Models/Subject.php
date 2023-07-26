<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function teachedBy()
    {
        return $this->belongsToMany(User::class, 'subject_teachers', 'subject_id')->withPivot(['custom_name', 'id'])->withTimestamps();
    }
}
