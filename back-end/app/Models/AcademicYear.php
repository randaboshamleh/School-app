<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $casts = [
        'term_start_dates' => 'array',
    ];

    public function classRoom()
    {
        return $this->hasMany(classRoom::class);
    }

    public function enrollment()
    {
        return $this->HasMany(enrollment::class);
    }

    public function subject()
    {
        return $this->HasMany(subject::class);
    }

    public function exam_component()
    {
        return $this->HasMany(exam_component::class);
    }
}
