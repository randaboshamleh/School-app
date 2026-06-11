<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transportRoutes extends Model
{
    protected $fillable = [
        'name',
        'code',
        'active',
    ];
}
