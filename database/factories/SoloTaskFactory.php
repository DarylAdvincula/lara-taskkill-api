<?php

namespace Database\Factories;

use App\Models\SoloTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SoloTask>
 */
class SoloTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'done']),
            'due_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d')
        ];
    }
}
