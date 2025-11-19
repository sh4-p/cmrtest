<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller via policies
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
            'description' => 'required|string',
            'type' => 'required|string|in:Call,Meeting,Email,Note',
            'subject_type' => 'required|string',
            'subject_id' => 'required|integer',
            'activity_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'description.required' => 'Activity description is required.',
            'type.required' => 'Activity type is required.',
            'type.in' => 'Invalid activity type selected.',
            'subject_type.required' => 'Related entity type is required.',
            'subject_id.required' => 'Related entity ID is required.',
            'subject_id.integer' => 'Related entity ID must be a number.',
            'activity_date.date' => 'Please provide a valid activity date.',
        ];
    }
}
