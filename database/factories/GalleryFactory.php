<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk membuat {@see Gallery} palsu.
 */
class GalleryFactory extends Factory
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
            'image' => 'galleries/'.fake()->unique()->numberBetween(1, 99999).'.jpg',
        ];
    }
}
