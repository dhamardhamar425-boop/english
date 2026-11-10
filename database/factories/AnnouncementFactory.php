<?php

namespace Database\Factories;

use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'type' => AnnouncementType::Info,
            'announcement_date' => now()->addDays(7)->toDateString(),
            'announcement_time' => '13:00:00',
            'is_published' => false,
            'published_at' => null,
            'created_by' => User::factory(),
        ];
    }

    /**
     * Jadwal pengumuman hasil seleksi (serentak semua organisasi).
     */
    public function result(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AnnouncementType::Result,
        ]);
    }

    /**
     * Pengumuman yang sudah diterbitkan.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now(),
        ]);
    }
}
