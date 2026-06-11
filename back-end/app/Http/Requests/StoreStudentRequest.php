<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'address' => 'required|string|max:255',
            'parent_contact' => 'required|string|max:20',
            'profile_photo' => 'required|string',
            'enrollment_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'status' => 'in:active,graduated,suspended,withdrawn',
        ];
    }
}
