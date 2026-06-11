<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BusNotBoarded extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $bus_number;

    protected string $date;

    protected ?string $notes;

    /**
     * Create a new notification instance.
     *
     * @param  string  $bus_number  رقم الحافلة
     * @param  string  $date  تاريخ الحجز أو التسجيل
     * @param  string|null  $notes  إضافات (مثلاً: "لم تصعد" أو ملاحظات)
     */
    public function __construct(string $bus_number, string $date, ?string $notes = null)
    {
        $this->bus_number = $bus_number;
        $this->date = $date;
        $this->notes = $notes;

    }

    /**
     * قنوات الإرسال: database فقط
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * بنية البيانات المراد تخزينها ضمن عمود `data`
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'bus_not_boarded',
            'bus_number' => $this->bus_number,
            'date' => $this->date,
            'student_id' => $notifiable->id,
            'notes' => $this->notes,
        ];
    }
}
