<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'weekly_session' => 'required|integer|min:1',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'is_mandatory' => 'boolean',
            'semester' => 'required|in:first,second,summer',
        ];
    }
}
