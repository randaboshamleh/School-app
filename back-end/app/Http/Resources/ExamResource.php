<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
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
            'title' => $this->title,
            'exam_code' => $this->exam_code,
            'subject_id' => $this->subject_id,
            'class_room_id' => $this->class_room_id,
            'exam_date' => optional($this->exam_date)->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration' => $this->duration,
            'duration_minutes' => $this->duration_minutes,
            'instructions' => $this->instructions,
            'syllabus' => $this->syllabus,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
