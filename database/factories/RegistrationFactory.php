<?php

namespace Database\Factories;

use App\Enums\FinalResult;
use App\Enums\RegistrationStatus;
use App\Models\Organization;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sequence = $this->faker->unique()->numberBetween(100000, 999999);

        return [
            'registration_number' => 'REG-'.$sequence,
            'full_name' => fake()->name(),
            'nis' => (string) $sequence,
            'nik' => (string) $this->faker->unique()->numerify('32##############00'),
            'gender' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'class' => fake()->randomElement(['X-1', 'X-2', 'XI-1', 'XI-2']),
            'phone' => '08'.$this->faker->numerify('##########'),
            'address' => fake()->address(),
            'reason' => fake()->sentence(12),
            'organization_id' => Organization::factory(),
            'status' => RegistrationStatus::Menunggu,
            'final_result' => null,
            'admin_notes' => null,
            'registered_at' => now(),
        ];
    }

    /**
     * Pendaftar yang sudah memasuki tahap seleksi offline.
     */
    public function seleksi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RegistrationStatus::Seleksi,
        ]);
    }

    /**
     * Pendaftar yang sudah memiliki hasil akhir diterima.
     */
    public function diterima(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RegistrationStatus::Diterima,
            'final_result' => FinalResult::Diterima,
        ]);
    }

    /**
     * Pendaftar yang sudah memiliki hasil akhir ditolak.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RegistrationStatus::Ditolak,
            'final_result' => FinalResult::Ditolak,
        ]);
    }
}
