<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AssignmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * قناة الإشعار – تخزين في قاعدة البيانات فقط.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * تحويل البيانات إلى مصفوفة تُخزن في جدول notifications
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'assignment_reminder',
            'assignment_id' => $this->data['id'] ?? null,
            'assignment_title' => $this->data['title'] ?? 'غير معروف',
            'due_date' => $this->data['due_date'] ?? null,
            'student_id' => $notifiable->id,
            'url' => $this->data['url'] ?? null,
        ];
    }
}
