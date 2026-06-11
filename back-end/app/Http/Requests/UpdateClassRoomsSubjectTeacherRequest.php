<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRoomsSubjectTeacherRequest extends FormRequest
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
            'class_rooms_id' => 'nullable|exists:class_rooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'periods_per_week' => 'nullable|integer|min:1',
            'minutes_per_period' => 'nullable|integer|min:1',
            'room' => 'nullable|string|max:255',
            'day_of_week' => 'nullable|in:sunday,monday,tuesday,wednesday,thursday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'semester' => 'nullable|string|max:255',
        ];
    }
}
