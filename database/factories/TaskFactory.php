<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['Pending', 'In Progress', 'Completed']);

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional(0.7)->paragraph(),
            'due_date' => fake()->optional(0.8)->dateTimeBetween('now', '+2 months'),
            'status' => $status,
            'priority' => fake()->randomElement(['Low', 'Medium', 'High', 'Urgent']),
            'assigned_to_id' => \App\Models\User::factory(),
            'related_to_type' => fake()->randomElement([
                \App\Models\Contact::class,
                \App\Models\Deal::class,
                \App\Models\Lead::class,
            ]),
            'related_to_id' => 1, // Will be overridden when creating with specific relation
            'completed_at' => $status === 'Completed' ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
