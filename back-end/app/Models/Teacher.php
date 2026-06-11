<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'gender',
        'hire_date',
        'specialization',
        'is_active',
    ];

    public function classRoom()
    {
        return $this->hasMany(classRoom::class);
    }

    public function subject()
    {
        return $this->HasMany(subject::class);
    }

    public function TimeTable()
    {
        return $this->hasMany(TimeTable::class);
    }

    public function classRoomsSubjectTeacher()
    {
        return $this->hasMany(classRoomsSubjectTeacher::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject_teachers')
            ->withPivot(['class_room_id', 'periods_per_week', 'minutes_per_period']);
    }
}
