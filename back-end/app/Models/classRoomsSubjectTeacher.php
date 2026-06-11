<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class classRoomsSubjectTeacher extends Model
{
    protected $fillable = [
        'class_rooms_id',
        'subject_id',
        'teacher_id',
        'periods_per_week',
        'minutes_per_period',
        'room',
        'day_of_week',
        'start_time',
        'end_time',
        'semester',
    ];

    public function classRoom()
    {
        return $this->belongsTo(classRoom::class);
    }

    public function subject()
    {
        return $this->belongsTo(subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(teacher::class);
    }
}
