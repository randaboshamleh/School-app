<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
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
            'code' => $this->code,
            'description' => $this->description,
            'weekly_session' => $this->weekly_session,
            'academic_year_id' => $this->academic_year_id,
            'teacher_id' => $this->teacher_id,
            'is_mandatory' => $this->is_mandatory,
            'semester' => $this->semester,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
