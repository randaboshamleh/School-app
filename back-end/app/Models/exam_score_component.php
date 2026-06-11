<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class exam_score_component extends Model
{
    protected $fillable = [
        'obtained_marks',
        'max_marks',
    ];

    public function Student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function exam_component()
    {
        return $this->belongsTo(exam_component::class);
    }
}
