<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'class_room_id',
        'academic_year_id',
        'enrolled_at',
        'scholarship_percentage',
        'status',
        'graduation_date',
        'is_current',
        'fees_paid',
        'notes',
    ];

    public function academicYear()
    {
        return $this->belongsTo(academicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(classRoom::class);
    }

    public function Payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function getDiscountedFee($amount)
    {
        $percentage = $this->scholarship_percentage ?? 0;

        return $amount - ($amount * $percentage / 100);
    }
}
