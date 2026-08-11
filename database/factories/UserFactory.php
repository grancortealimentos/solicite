<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'autentique_user_id' => fake()->unique()->numberBetween(1, 1_000_000),
            'autentique_person_id' => fake()->numberBetween(1, 1_000_000),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'is_active' => true,
            'force_password_change' => false,
            'email_verified_at' => now(),
            'autentique_synced_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user must change their password on next login.
     */
    public function mustChangePassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'force_password_change' => true,
        ]);
    }
}
