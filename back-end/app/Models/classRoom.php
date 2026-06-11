<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class classRoom extends Model
{
    protected $table = 'classrooms'; // Laravel رح يعرف الجدول الصحيح

    protected $fillable = [
        'name',
        'academic_year_id',
        'teacher_id',
        'capacity',
        'location',
        'is_active',
    ];

    public function exam()
    {
        return $this->HasMany(Exam::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(academicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(teacher::class);
    }

    public function enrollment()
    {
        return $this->HasMany(enrollment::class);
    }

    public function TimeTable()
    {
        return $this->hasMany(TimeTable::class);
    }

    public function Attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function classRoomsSubjectTeacher()
    {
        return $this->hasMany(classRoomsSubjectTeacher::class);
    }

    public function homeworks()
    {
        return $this->hasMany(Homework::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
