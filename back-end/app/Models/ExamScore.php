<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamScore extends Model
{
    protected $fillable = [
        'student_id',
        'marks_obtained',
        'exam_component_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function examComponent()
    {
        return $this->belongsTo(exam_component::class);
    }
}
