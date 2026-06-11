<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'students';

    protected $primaryKey = 'student_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'student_id',
        'name',
        'password',
        'otp',
        'otp_expires_at',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function enrollment()
    {
        return $this->HasMany(enrollment::class);
    }

    public function Attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function Payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function ExamScore()
    {
        return $this->hasMany(ExamScore::class);
    }

    public function exam_component()
    {
        return $this->hasMany(exam_component::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
