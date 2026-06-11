<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'weekly_session',
        'academic_year_id',
        'teacher_id',
        'is_mandatory',
        'semester',
    ];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function TimeTable()
    {
        return $this->hasMany(TimeTable::class);
    }

    public function classRoomsSubjectTeacher()
    {
        return $this->hasMany(classRoomsSubjectTeacher::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(teacher::class);
    }

    public function AcademicYears()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function exam_component()
    {
        return $this->HasMany(exam_component::class);
    }

    public function homeworks()
    {
        return $this->hasMany(Homework::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_subject_teachers')
            ->withPivot(['class_room_id', 'periods_per_week', 'minutes_per_period']);
    }
}
