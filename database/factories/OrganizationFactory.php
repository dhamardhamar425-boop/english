<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::upper(fake()->unique()->words(2, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'logo' => null,
            'description' => fake()->paragraph(),
            'vision' => fake()->paragraph(),
            'mission' => fake()->paragraph(),
            'program_work' => fake()->paragraph(),
            'activities' => fake()->paragraph(),
            'requirements' => fake()->paragraph(),
            'whatsapp_group_link' => 'https://chat.whatsapp.com/'.Str::random(12),
            'contact_link' => 'https://wa.me/628'.fake()->numerify('##########'),
            'is_active' => true,
        ];
    }

    /**
     * Organisasi yang dinonaktifkan.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
