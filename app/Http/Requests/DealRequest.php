<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DealRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'deal_stage_id' => 'required|exists:deal_stages,id',
            'amount' => 'required|numeric|min:0',
            'closing_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'assigned_to_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
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
            'name.required' => 'Deal name is required.',
            'contact_id.required' => 'Contact is required.',
            'contact_id.exists' => 'The selected contact does not exist.',
            'deal_stage_id.required' => 'Deal stage is required.',
            'deal_stage_id.exists' => 'The selected deal stage does not exist.',
            'amount.required' => 'Deal amount is required.',
            'amount.numeric' => 'Deal amount must be a number.',
            'amount.min' => 'Deal amount must be at least 0.',
            'closing_date.date' => 'Please provide a valid closing date.',
            'probability.integer' => 'Probability must be a whole number.',
            'probability.min' => 'Probability must be at least 0.',
            'probability.max' => 'Probability must not exceed 100.',
            'assigned_to_id.exists' => 'The selected user does not exist.',
        ];
    }
}
