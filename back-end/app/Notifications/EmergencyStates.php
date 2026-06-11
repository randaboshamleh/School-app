<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmergencyStates extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $state;

    protected string $message;

    protected string $date;

    /**
     * تمرير بيانات الحالة الطارئة عند الإنشاء.
     *
     * @param  string  $state  // مثل 'closed', 'delayed', 'evacuation'
     * @param  string  $message  // وصف مختصر للحالة
     * @param  string  $date  // تاريخ/وقت الحالة (ISO أو نص قابل للعرض)
     */
    public function __construct(string $state, string $message, string $date)
    {
        $this->state = $state;
        $this->message = $message;
        $this->date = $date;
    }

    /**
     * قنوات الإرسال: هنا database فقط.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * البيانات المُخزّنة في عمود `data`.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'emergency_state',
            'state' => $this->state,
            'message' => $this->message,
            'date' => $this->date,
            'recipient_id' => $notifiable->id,
        ];
    }
}
