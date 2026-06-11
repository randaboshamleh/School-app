<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
    protected $fillable = [
        'subject_id',
        'teacher_id',
        'day',
        'subject',
        'start_time',
        'end_time',
        'room',
        'period_order',
        'class_room_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(classRoom::class, 'class_room_id');
    }

    public function teacher()
    {
        return $this->belongsTo(teacher::class);
    }
}
