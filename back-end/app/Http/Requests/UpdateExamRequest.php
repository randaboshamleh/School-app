<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'exam_code' => 'nullable|string|unique:exams,exam_code,'.$this->route('exam'),
            'subject_id' => 'nullable|exists:subjects,id',
            'class_room_id' => 'nullable|exists:class_rooms,id',
            'exam_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer',
            'instructions' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
