<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'academic_year_id' => $this->academic_year_id,
            'enrolled_at' => optional($this->enrolled_at)->format('Y-m-d'),
            'status' => $this->status,
            'graduation_date' => optional($this->graduation_date)->format('Y-m-d'),
            'is_current' => (bool) $this->is_current,
            'fees_paid' => $this->fees_paid,
            'notes' => $this->notes,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
