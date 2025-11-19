<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->optional(0.8)->phoneNumber(),
            'source' => fake()->randomElement(['Website', 'Referral', 'Cold Call', 'Social Media', 'Email Campaign']),
            'status' => fake()->randomElement(['New', 'Contacted', 'Qualified', 'Unqualified']),
            'assigned_to_id' => fake()->optional(0.8)->randomElement([\App\Models\User::factory()]),
            'converted_to_contact_id' => null,
            'converted_at' => null,
            'notes' => fake()->optional(0.4)->paragraph(),
        ];
    }
}
