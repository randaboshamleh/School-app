<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomResource extends JsonResource
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
            'name' => $this->name,
            'academic_year_id' => $this->academic_year_id,
            'teacher_id' => $this->teacher_id,
            'capacity' => $this->capacity,
            'location' => $this->location,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->diffForHumans(),
        ];

    }
}
