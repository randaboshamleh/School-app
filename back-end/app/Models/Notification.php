<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // اسم الجدول
    protected $table = 'notifications';

    // المفتاح الأساسي UUID
    protected $keyType = 'string';

    public $incrementing = false;

    // الأعمدة المسموح تعبئتها
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    // نحدد نوع العمود data كـ JSON
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * علاقة الإشعار مع المستخدم أو أي موديل آخر
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}
