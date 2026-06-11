<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'is_default',
        'is_protected',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
