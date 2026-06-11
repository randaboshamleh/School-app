<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    public function determinePassing(Student $student): bool
    {
        $rule = passing_Rules::where('subject_id', $this->subject_id)
            ->where(function ($q) use ($student) {
                $q->where('classrooms_id', $student->class_room_id)
                    ->orWhereNull('classrooms_id');
            })->latest()->first();

        if (! $rule) {
            return false;
        }

        $this->min_marks = intval(($this->max_marks * $rule->passing_percentage) / 100);

        return ($this->marks_obtained ?? 0) >= $this->min_marks;
    }

    protected $fillable = [
        'title',
        'exam_code',
        'subject_id',
        'classrooms_id',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'instructions',
        'syllabus',
        'is_active',
        'exam_type',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    protected $appends = ['duration'];

    public function getDurationAttribute()
    {
        // إذا start_time أو end_time فارغة، يعرض "-"
        $start = $this->start_time ?? '-';
        $end = $this->end_time ?? '-';

        return "$start → $end";
    }

    public function components()
    {
        return $this->hasMany(exam_component::class);
    }
}
