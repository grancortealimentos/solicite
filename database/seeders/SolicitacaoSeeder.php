<?php

namespace Database\Seeders;

use App\Models\Solicitacao;
use App\Models\SolicitacaoItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class SolicitacaoSeeder extends Seeder
{
    /**
     * Gera solicitações de exemplo (com itens) para os usuários já
     * existentes, reaproveitando quem já foi criado antes deste seeder.
     */
    public function run(): void
    {
        $solicitantes = User::inRandomOrder()->take(3)->get();

        if ($solicitantes->isEmpty()) {
            $solicitantes = User::factory()->count(3)->create();
        }

        $solicitantes->each(function (User $solicitante) {
            Solicitacao::factory()
                ->count(random_int(2, 5))
                ->for($solicitante, 'solicitante')
                ->has(SolicitacaoItem::factory()->count(random_int(1, 4)), 'itens')
                ->create();
        });
    }
}
