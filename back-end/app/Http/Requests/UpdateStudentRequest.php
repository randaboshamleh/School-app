<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
            'address' => 'nullable|string|max:255',
            'parent_contact' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|string',
            'enrollment_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'status' => 'nullable|in:active,graduated,suspended,withdrawn',
        ];
    }
}
