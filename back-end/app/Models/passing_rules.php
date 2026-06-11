<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class passing_rules extends Model
{
    protected $casts = [
        'component_weights' => 'array',
    ];

    protected $fillable = [
        'academic_year_id',
        'class_room_id',
        'subject_id',
        'component_weights',
        'passing_percentage',
    ];

    public function AcademicYears()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(classRoom::class);
    }

    public function Subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
