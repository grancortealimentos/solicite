<?php

use App\Livewire\Solicitacao\Index;
use App\Models\Solicitacao;
use App\Models\User;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissaoSeeder::class);
});

test('usuário sem permissão não acessa a listagem de solicitações', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('solicitacao.index'))->assertForbidden();
});

test('usuário com permissão lista as solicitações', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('solicitacao.visualizar');

    Solicitacao::factory()->count(3)->for($user, 'solicitante')->create();

    $this->actingAs($user)->get(route('solicitacao.index'))->assertOk();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertViewHas('solicitacoes', fn ($solicitacoes) => $solicitacoes->total() === 3);
});

test('filtra solicitações por nome do solicitante', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('solicitacao.visualizar');

    $joao = User::factory()->create(['name' => 'João da Silva']);
    $maria = User::factory()->create(['name' => 'Maria Souza']);

    Solicitacao::factory()->for($joao, 'solicitante')->create();
    Solicitacao::factory()->for($maria, 'solicitante')->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('busca', 'joão')
        ->assertViewHas('solicitacoes', fn ($solicitacoes) => $solicitacoes->total() === 1);
});

test('filtra solicitações por status', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('solicitacao.visualizar');

    Solicitacao::factory()->for($user, 'solicitante')->pendente()->create();
    Solicitacao::factory()->for($user, 'solicitante')->create(['status' => Solicitacao::STATUS_APROVADA]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('filtroStatus', 'pendente')
        ->assertViewHas('solicitacoes', fn ($solicitacoes) => $solicitacoes->total() === 1);
});

test('filtra solicitações por período de emissão (de/até)', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('solicitacao.visualizar');

    $dentroPeriodo = Solicitacao::factory()->for($user, 'solicitante')->create(['created_at' => '2026-08-10']);
    Solicitacao::factory()->for($user, 'solicitante')->create(['created_at' => '2026-08-01']);
    Solicitacao::factory()->for($user, 'solicitante')->create(['created_at' => '2026-08-20']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('emitidaDe', '2026-08-05')
        ->set('emitidaAte', '2026-08-15')
        ->assertViewHas(
            'solicitacoes',
            fn ($solicitacoes) => $solicitacoes->total() === 1
                && $solicitacoes->first()->is($dentroPeriodo)
        );
});

test('troca a quantidade de itens por página', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('solicitacao.visualizar');

    Solicitacao::factory()->count(6)->for($user, 'solicitante')->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('porPagina', 5)
        ->assertViewHas('solicitacoes', fn ($solicitacoes) => $solicitacoes->perPage() === 5
            && $solicitacoes->count() === 5);
});

test('limpar filtros reseta busca e filtros avançados', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('solicitacao.visualizar');

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('busca', 'algo')
        ->set('filtroStatus', 'pendente')
        ->set('emitidaAte', '2026-08-15')
        ->assertSet('busca', 'algo')
        ->call('limparFiltros')
        ->assertSet('busca', '')
        ->assertSet('filtroStatus', '')
        ->assertSet('emitidaAte', '');
});

test('solicitante cancela a própria solicitação pendente', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['solicitacao.visualizar', 'solicitacao.cancelar']);

    $solicitacao = Solicitacao::factory()->for($user, 'solicitante')->pendente()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('cancelar', $solicitacao->id);

    expect($solicitacao->refresh()->status)->toBe(Solicitacao::STATUS_CANCELADA);
});

test('usuário não consegue cancelar solicitação de outra pessoa', function () {
    $dono = User::factory()->create();
    $outroUsuario = User::factory()->create();
    $outroUsuario->givePermissionTo(['solicitacao.visualizar', 'solicitacao.cancelar']);

    $solicitacao = Solicitacao::factory()->for($dono, 'solicitante')->pendente()->create();

    Livewire::actingAs($outroUsuario)
        ->test(Index::class)
        ->call('cancelar', $solicitacao->id);

    expect($solicitacao->refresh()->status)->toBe(Solicitacao::STATUS_PENDENTE);
});

test('não é possível cancelar uma solicitação que já não está mais pendente', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['solicitacao.visualizar', 'solicitacao.cancelar']);

    $solicitacao = Solicitacao::factory()->for($user, 'solicitante')->create([
        'status' => Solicitacao::STATUS_APROVADA,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('cancelar', $solicitacao->id);

    expect($solicitacao->refresh()->status)->toBe(Solicitacao::STATUS_APROVADA);
});
