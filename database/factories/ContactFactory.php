<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
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
            'phone_number' => fake()->optional(0.9)->phoneNumber(),
            'company_id' => fake()->optional(0.7)->randomElement([\App\Models\Company::factory()]),
            'owner_id' => \App\Models\User::factory(),
            'notes' => fake()->optional(0.3)->paragraph(),
        ];
    }
}
