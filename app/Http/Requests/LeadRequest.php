<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
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
        $leadId = $this->route('lead')?->id;

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $leadId . '|max:255',
            'phone_number' => 'nullable|string|max:20',
            'source' => 'required|string|in:Website,Referral,Cold Call,Social Media',
            'status' => 'nullable|string|in:New,Contacted,Qualified,Unqualified,Converted',
            'assigned_to_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
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
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'source.required' => 'Lead source is required.',
            'source.in' => 'Invalid lead source selected.',
            'status.in' => 'Invalid status selected.',
            'assigned_to_id.exists' => 'The selected user does not exist.',
        ];
    }
}
