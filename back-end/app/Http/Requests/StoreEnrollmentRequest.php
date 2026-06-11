<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
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
            'student_id' => 'required|exists:students,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrolled_at' => 'nullable|date',
            'status' => 'required|in:active,graduated,suspended,withdrawn',
            'graduation_date' => 'nullable|date',
            'is_current' => 'required|boolean',
            'fees_paid' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ];
    }
}
