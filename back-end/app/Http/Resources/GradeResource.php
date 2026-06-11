<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'subject' => $this->first()?->examComponent?->subject?->name ?? '',
            'quiz' => $this->where('examComponent.component_name', 'مذاكرة')->first()->marks_obtained ?? 0,
            'final_exam' => $this->where('examComponent.component_name', 'امتحان')->first()->marks_obtained ?? 0,
        ];
    }
}
