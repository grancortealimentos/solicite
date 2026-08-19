<?php

use App\Livewire\Solicitacao\Create;
use App\Models\Solicitacao;
use App\Models\User;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/**
 * Linhas cruas no formato retornado pela view VW_SOLICITE_FILIAL do Protheus,
 * usadas para dublar a conexão 'protheus' nos testes sem bater na rede.
 *
 * @return Collection<int, object>
 */
function catalogoFiliaisDeTeste(): Collection
{
    return collect([
        (object) [
            'Cod_filial' => '010101',
            'Nome_filial' => '010101-GRAN CORTE - INDUSTRIA',
            'Cpf_Cgc' => '17098519000157',
            'Cidade' => 'CERQUEIRA CESAR',
            'Endereco' => 'RODOVIA SALIM CURIATI, S/N',
            'Bairro' => 'MACUQUINHO',
            'UF' => 'SP',
            'Email' => '',
            'fone' => '55-14-37142911',
        ],
        (object) [
            'Cod_filial' => '020101',
            'Nome_filial' => '020101-FRISS - INDUSTRIA',
            'Cpf_Cgc' => '10649422000146',
            'Cidade' => 'ITU',
            'Endereco' => 'AV PLAZA,06',
            'Bairro' => 'JARDIM PARAISO',
            'UF' => 'SP',
            'Email' => '',
            'fone' => '55-11-40231450',
        ],
    ]);
}

/**
 * Substitui a conexão 'protheus' real por um dublê que responde às mesmas
 * queries do FilialProtheusRepository sobre o catálogo de teste em memória.
 */
function mockarConexaoProtheusFiliais(): void
{
    $filiais = catalogoFiliaisDeTeste();

    $conexao = Mockery::mock(ConnectionInterface::class);

    $conexao->shouldReceive('select')
        ->andReturnUsing(function (string $query, array $bindings = []) use ($filiais) {
            if (empty($bindings)) {
                return $filiais->values()->all();
            }

            $termo = mb_strtolower(trim($bindings[0], '%'));

            return $filiais
                ->filter(fn (object $f) => str_contains(mb_strtolower($f->Cod_filial), $termo)
                    || str_contains(mb_strtolower($f->Nome_filial), $termo)
                    || str_contains(mb_strtolower($f->Cidade), $termo))
                ->values()
                ->all();
        });

    $conexao->shouldReceive('selectOne')
        ->andReturnUsing(fn (string $query, array $bindings = []) => $filiais->first(
            fn (object $f) => $f->Cod_filial === ($bindings[0] ?? null)
        ));

    DB::shouldReceive('connection')->with('protheus')->andReturn($conexao);

    // SolicitacaoRepository::criar() envolve a persistência em DB::transaction()
    // na conexão padrão; como o facade DB inteiro fica mockado a partir daqui,
    // repassamos a chamada pra rodar a closure normalmente.
    DB::shouldReceive('transaction')->andReturnUsing(fn (Closure $callback) => $callback());
}

beforeEach(function () {
    $this->seed(PermissaoSeeder::class);
    mockarConexaoProtheusFiliais();
});

test('usuário sem permissão não acessa a criação de solicitação', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('solicitacao.create'))->assertForbidden();
});

test('usuário com permissão acessa a criação de solicitação', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('solicitacao.criar');

    $this->actingAs($user)->get(route('solicitacao.create'))->assertOk();
});

test('inicia sem filial selecionada e com a busca fechada', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->assertSet('filial', null)
        ->assertSet('filialBuscaAberta', false);
});

test('abre e fecha a busca de filial', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('abrirBuscaFilial')
        ->assertSet('filialBuscaAberta', true)
        ->call('fecharBuscaFilial')
        ->assertSet('filialBuscaAberta', false);
});

test('lista todas as filiais quando a busca está vazia', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(Create::class);

    expect($component->instance()->resultadoBuscaFilial())->toHaveCount(2);
});

test('filtra filiais por código, nome ou cidade', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(Create::class)
        ->set('termoBuscaFilial', 'ITU');

    expect($component->instance()->resultadoBuscaFilial())->toHaveCount(1);
    expect($component->instance()->resultadoBuscaFilial()[0]->code)->toBe('020101');
});

test('selecionar uma filial fecha a busca e preenche a filial escolhida', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('abrirBuscaFilial')
        ->call('selecionarFilial', '010101')
        ->assertSet('filialBuscaAberta', false)
        ->assertSet('filial.code', '010101')
        ->assertSet('filial.name', '010101-GRAN CORTE - INDUSTRIA');
});

test('sincroniza os itens recebidos do componente filho', function () {
    $user = User::factory()->create();

    $item = [
        'codigo' => '88946',
        'descricao' => 'MINI PC',
        'unidade_medida' => 'UN',
        'armazem' => '50',
        'cta_contabil' => '123456789',
        'grupo_produto' => 'INFORMATICA',
        'quantidade' => 2,
        'data_prazo' => now()->addDays(5)->format('Y-m-d'),
        'observacao' => 'Uso interno.',
        'centro_custo' => 'TI',
        'estoque_filial' => 0.0,
    ];

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('sincronizarItens', [$item])
        ->assertCount('itens', 1)
        ->assertSet('itens.0.codigo', '88946');
});

test('não salva sem filial selecionada', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('salvar')
        ->assertDispatched('toast', tipo: 'error', mensagem: 'Selecione a filial antes de salvar.');

    expect(Solicitacao::count())->toBe(0);
});

test('não salva sem nenhum item adicionado', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('selecionarFilial', '010101')
        ->call('salvar')
        ->assertDispatched('toast', tipo: 'error', mensagem: 'Adicione ao menos um item antes de salvar.');

    expect(Solicitacao::count())->toBe(0);
});

test('salva a solicitação com a filial e os itens informados', function () {
    $user = User::factory()->create();

    $item = [
        'codigo' => '88946',
        'descricao' => 'MINI PC',
        'unidade_medida' => 'UN',
        'armazem' => '50',
        'cta_contabil' => '123456789',
        'grupo_produto' => 'INFORMATICA',
        'quantidade' => 2,
        'data_prazo' => now()->addDays(5)->format('Y-m-d'),
        'observacao' => 'Uso interno.',
        'centro_custo' => 'TI',
        'estoque_filial' => 0.0,
    ];

    Livewire::actingAs($user)
        ->test(Create::class)
        ->call('selecionarFilial', '010101')
        ->call('sincronizarItens', [$item])
        ->call('salvar')
        ->assertRedirect(route('solicitacao.index'));

    expect(Solicitacao::count())->toBe(1);

    $solicitacao = Solicitacao::first();
    expect($solicitacao->user_id)->toBe($user->id);
    expect($solicitacao->filial)->toBe('010101');
    expect($solicitacao->status)->toBe(Solicitacao::STATUS_PENDENTE);
    expect($solicitacao->itens)->toHaveCount(1);
    expect($solicitacao->itens->first()->codigo)->toBe('88946');
});
