<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->catchPhrase(),
            'contact_id' => \App\Models\Contact::factory(),
            'deal_stage_id' => \App\Models\DealStage::factory(),
            'amount' => fake()->randomFloat(2, 1000, 500000),
            'closing_date' => fake()->optional(0.8)->dateTimeBetween('now', '+6 months'),
            'probability' => fake()->randomElement([10, 25, 50, 75, 90, 100]),
            'assigned_to_id' => \App\Models\User::factory(),
            'description' => fake()->optional(0.6)->paragraph(),
        ];
    }
}
