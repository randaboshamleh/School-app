<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomSubjectTeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'class_rooms_id' => $this->class_rooms_id,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'periods_per_week' => $this->periods_per_week,
            'minutes_per_period' => $this->minutes_per_period,
            'room' => $this->room,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'semester' => $this->semester,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
