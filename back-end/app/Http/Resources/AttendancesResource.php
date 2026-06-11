<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendancesResource extends JsonResource
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
            'student_id' => $this->student_id,
            'class_room_id' => $this->class_room_id,
            'date' => $this->date->format('Y-m-d'),
            'status' => $this->status,
            'time_in' => $this->time_in,
            'time_out' => $this->time_out,
            'notes' => $this->notes,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
