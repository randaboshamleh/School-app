<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamScoreResource extends JsonResource
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
            'exam_id' => $this->exam_id,
            'student_id' => $this->student_id,
            'subject_name' => $this->examComponent?->subject?->name,
            'component_name' => $this->examComponent?->component_name,
            'marks_obtained' => $this->marks_obtained,
            'max_marks' => $this->examComponent?->max_marks,
            'min_marks' => $this->examComponent?->min_marks,
            'passed' => $this->marks_obtained >= ($this->examComponent?->min_marks ?? 0),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
