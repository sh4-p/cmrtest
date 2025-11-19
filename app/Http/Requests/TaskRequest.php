<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|in:Pending,In Progress,Completed',
            'priority' => 'nullable|string|in:Low,Medium,High,Urgent',
            'assigned_to_id' => 'nullable|exists:users,id',
            'related_to_type' => 'required|string',
            'related_to_id' => 'required|integer',
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
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title must not exceed 255 characters.',
            'due_date.date' => 'Please provide a valid due date.',
            'status.in' => 'Invalid status selected.',
            'priority.in' => 'Invalid priority selected.',
            'assigned_to_id.exists' => 'The selected user does not exist.',
            'related_to_type.required' => 'Related entity type is required.',
            'related_to_id.required' => 'Related entity ID is required.',
            'related_to_id.integer' => 'Related entity ID must be a number.',
        ];
    }
}
