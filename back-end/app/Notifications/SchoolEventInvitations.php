<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SchoolEventInvitations extends Notification
{
    use Queueable;

    protected int $eventId;

    protected string $title;

    protected string $date;

    protected string $time;

    protected string $venue;

    protected ?string $description;

    public function __construct(int $eventId, string $title, string $date, string $time, string $venue, ?string $description = null)
    {
        $this->eventId = $eventId;
        $this->title = $title;
        $this->date = $date;
        $this->time = $time;
        $this->venue = $venue;
        $this->description = $description;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'school_event',
            'event_id' => $this->eventId,
            'title' => $this->title,
            'date' => $this->date,
            'time' => $this->time,
            'venue' => $this->venue,
            'description' => $this->description,
            'recipient_id' => $notifiable->id,
        ];
    }
}
