<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class exam_component extends Model
{
    protected $fillable = [
        'exam_id',
        'class_room_id',
        'component_name',
        'max_marks',
        'subject_id',
        'weight_percentage',
    ];

    public function Subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function AcademicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function Examscore()
    {
        return $this->hasMany(ExamScore::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
