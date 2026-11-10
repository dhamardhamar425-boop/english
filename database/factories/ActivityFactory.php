<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk membuat {@see Activity} palsu.
 */
class ActivityFactory extends Factory
{
    /**
     * Definisi state bawaan model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'occurred_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'image' => null,
        ];
    }
}
