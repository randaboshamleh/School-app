<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
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
            'student_id' => 'nullable|exists:students,id',
            'class_room_id' => 'nullable|exists:class_rooms,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'enrolled_at' => 'nullable|date',
            'status' => 'nullable|in:active,graduated,suspended,withdrawn',
            'graduation_date' => 'nullable|date',
            'is_current' => 'nullable|boolean',
            'fees_paid' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ];
    }
}
