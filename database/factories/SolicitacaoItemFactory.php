<?php

namespace Database\Factories;

use App\Models\Solicitacao;
use App\Models\SolicitacaoItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SolicitacaoItem>
 */
class SolicitacaoItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'solicitacao_id' => Solicitacao::factory(),
            'item' => 1,
            'codigo' => fake()->unique()->numerify('#####'),
            'descricao' => fake()->words(3, true),
            'unidade_medida' => 'UN',
            'armazem' => '10',
            'cta_contabil' => fake()->numerify('#########'),
            'grupo_produto' => fake()->randomElement(['INFORMATICA', 'PERIFERICOS', 'ESCRITORIO', 'MANUTENCAO']),
            'quantidade' => fake()->numberBetween(1, 20),
            'data_prazo' => now()->addDays(fake()->numberBetween(5, 30)),
            'observacao' => fake()->sentence(),
            'centro_custo' => fake()->randomElement(['Comercial', 'TI', 'RH', 'DP', 'Financeiro']),
        ];
    }
}
