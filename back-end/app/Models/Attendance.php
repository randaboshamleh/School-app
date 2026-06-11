<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'class_room_id',
        'date',
        'status',
        'time_in',
        'time_out',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(classRoom::class);
    }
}
