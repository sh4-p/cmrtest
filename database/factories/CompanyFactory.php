<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'industry' => fake()->randomElement([
                'Technology', 'Healthcare', 'Finance', 'Education', 'Manufacturing',
                'Retail', 'Real Estate', 'Consulting', 'Marketing', 'Legal'
            ]),
            'website' => fake()->optional(0.8)->url(),
            'phone_number' => fake()->optional(0.9)->phoneNumber(),
            'address' => fake()->optional(0.7)->address(),
            'owner_id' => \App\Models\User::factory(),
            'notes' => fake()->optional(0.3)->paragraph(),
        ];
    }
}
