<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'enrollment_id',
        'subscription_id',
        'payment_date',
        'amount',
        'method',
        'reference',
        'due_date',
        'is_paid',
        'late_fee',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function getLateFeeAttribute($value)
    {
        if (! is_null($value) && $value > 0) {
            return $value;
        }

        if ($this->is_paid || ! $this->due_date) {
            return 0;
        }

        $today = Carbon::now();
        $dueDate = Carbon::parse($this->due_date);

        if ($today->greaterThan($dueDate)) {
            return round($this->amount * 0.10, 2);
        }

        return 0;
    }

    public function isOverdue()
    {
        return $this->status === 'overdue' ||
               ($this->due_date && $this->due_date < now() && $this->status !== 'paid');
    }

    public function Enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function Student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function transportSubscription()
    {
        return $this->belongsTo(transport_subscribtions::class, 'subscription_id');
    }
}
