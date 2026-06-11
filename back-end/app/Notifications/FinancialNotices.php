<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FinancialNotices extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $noticeType;

    protected float $amountDue;

    protected string $dueDate;

    protected ?string $message;

    /**
     * إعداد بيانات الإشعار المالي.
     *
     * @param  string  $noticeType  // نوع التنبيه (مثل 'fee_due', 'payment_confirmation')
     * @param  float  $amountDue  // المبلغ المطلوب أو المدفوع
     * @param  string  $dueDate  // تاريخ الاستحقاق أو السداد (YYYY‑MM‑DD)
     * @param  string|null  $message  // رسالة إضافية (اختياري)
     */
    public function __construct(string $noticeType, float $amountDue, string $dueDate, ?string $message = null)
    {
        $this->noticeType = $noticeType;
        $this->amountDue = $amountDue;
        $this->dueDate = $dueDate;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'financial_notice',
            'notice_type' => $this->noticeType,
            'amount' => $this->amountDue,
            'due_date' => $this->dueDate,
            'message' => $this->message,
            'student_id' => $notifiable->id,
        ];
    }
}
