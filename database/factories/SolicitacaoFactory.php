<?php

namespace Database\Factories;

use App\Models\Solicitacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Solicitacao>
 */
class SolicitacaoFactory extends Factory
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
            'filial' => '010101',
            'status' => fake()->randomElement([
                Solicitacao::STATUS_PENDENTE,
                Solicitacao::STATUS_APROVADA,
                Solicitacao::STATUS_REJEITADA,
                Solicitacao::STATUS_CANCELADA,
            ]),
        ];
    }

    /**
     * Indicate that the request is still pending approval.
     */
    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Solicitacao::STATUS_PENDENTE,
        ]);
    }
}
