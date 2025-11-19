<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['Call', 'Meeting', 'Email', 'Note']);

        $descriptions = [
            'Call' => fake()->sentence() . ' - Phone call with client.',
            'Meeting' => 'Attended meeting to discuss ' . fake()->catchPhrase(),
            'Email' => 'Sent email regarding ' . fake()->words(3, true),
            'Note' => fake()->paragraph(),
        ];

        return [
            'description' => $descriptions[$type],
            'type' => $type,
            'user_id' => \App\Models\User::factory(),
            'subject_type' => fake()->randomElement([
                \App\Models\Contact::class,
                \App\Models\Deal::class,
                \App\Models\Lead::class,
            ]),
            'subject_id' => 1, // Will be overridden when creating with specific relation
            'activity_date' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
