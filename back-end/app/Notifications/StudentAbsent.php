<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StudentAbsent extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $classRoom;

    protected string $date;

    protected ?string $reason;

    /**
     * إنشاء الإشعار مع تمرير بياناته.
     *
     * @param  string  $classRoom  // اسم الفصل الذي غاب فيه الطالب
     * @param  string  $date  // تاريخ الغياب بالصيغة YYYY-MM-DD
     * @param  string|null  $reason  // ملاحظات اختيارية حول الغياب
     */
    public function __construct(string $classRoom, string $date, ?string $reason = null)
    {
        $this->classRoom = $classRoom;
        $this->date = $date;
        $this->reason = $reason;
    }

    /**
     * قنوات الإشعار – هنا نستخدم تخزين الإشعار في قاعدة البيانات.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * البيانات التي يتم تخزينها في عمود `data` بجدول `notifications`.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'student_absent',
            'class_room' => $this->classRoom,
            'date' => $this->date,
            'reason' => $this->reason,
            'student_id' => $notifiable->id,
        ];
    }
}
