<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamScoreRequest extends FormRequest
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
            'exam_id' => 'nullable|exists:exams,id',
            'exam_component_id' => 'required|exists:exam_components,id',
            'student_id' => 'required|exists:students,student_id',
            'marks_obtained' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ];
    }
}
