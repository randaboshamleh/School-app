<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transport_subscribtions extends Model
{
    protected $fillable = [
        'student_id',
        'user_id',
        'academic_year',
        'academic_year_id',
        'route_id',
        'status',
        'is_active',
    ];

    public function route()
    {

        return $this->belongsTo(TransportRoutes::class, 'route_id');
    }

    public function Student()
    {

        return $this->belongsTo(TransportRoutes::class, 'student_id');
    }

    public function AcademicYear()
    {

        return $this->belongsTo(TransportRoutes::class, 'academic_year_id');
    }

    public function user()
    {

        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }
}
