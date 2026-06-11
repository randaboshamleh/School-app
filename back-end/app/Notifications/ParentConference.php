<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ParentConference extends Notification implements ShouldQueue
{
    use Queueable;

    protected $conference;

    public function __construct($conference)
    {
        $this->conference = $conference;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'parent_conference',
            'conference_id' => $this->conference->id,
            'conference_date' => $this->conference->date->toDateString(),
            'location' => $this->conference->location,
            'message' => $this->conference->notes ?? '',
            'student_id' => $notifiable->id,
            'url' => url("/conferences/{$this->conference->id}"),
        ];
    }
}
