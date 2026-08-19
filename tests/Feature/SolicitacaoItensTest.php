<?php

use App\Livewire\SolicitacaoItens;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

/**
 * Linhas cruas no formato retornado pela view VW_SOLICITE_ITENS do Protheus,
 * usadas para dublar a conexão 'protheus' nos testes sem bater na rede.
 *
 * @return Collection<int, object>
 */
function catalogoProtheusDeTeste(): Collection
{
    return collect([
        (object) [
            'B1_COD' => '88946',
            'B1_DESC' => 'MINI PC BLUE 2K INTEL CORE I3, 8GB RAM, 240 GB SSD',
            'B1_TIPO' => 'PA',
            'B1_LOCPAD' => '50',
            'B1_UM' => 'UN',
            'B1_CONTA' => '123456789',
            'BM_GRUPO' => '4909',
            'BM_DESC' => 'INFORMATICA',
        ],
        (object) [
            'B1_COD' => '90211',
            'B1_DESC' => 'NOTEBOOK DELL VOSTRO 3520 INTEL CORE I5, 16GB RAM, 512 GB SSD',
            'B1_TIPO' => 'PA',
            'B1_LOCPAD' => '50',
            'B1_UM' => 'UN',
            'B1_CONTA' => '123456789',
            'BM_GRUPO' => '4909',
            'BM_DESC' => 'INFORMATICA',
        ],
        (object) [
            'B1_COD' => '77213',
            'B1_DESC' => 'MONITOR LED 24" FULL HD HDMI/VGA',
            'B1_TIPO' => 'PA',
            'B1_LOCPAD' => '10',
            'B1_UM' => 'UN',
            'B1_CONTA' => '123456790',
            'BM_GRUPO' => '4910',
            'BM_DESC' => 'PERIFERICOS',
        ],
        (object) [
            'B1_COD' => '99999',
            'B1_DESC' => '.',
            'B1_TIPO' => 'PA',
            'B1_LOCPAD' => '10',
            'B1_UM' => 'UN',
            'B1_CONTA' => '123456790',
            'BM_GRUPO' => '4910',
            'BM_DESC' => 'PERIFERICOS',
        ],
    ]);
}

/**
 * Filtra o catálogo de teste pelo termo de busca (%termo%) presente nas
 * bindings, replicando o WHERE RTRIM(B1_DESC) LIKE ? OR RTRIM(B1_COD) LIKE ?
 * do repositório real.
 *
 * @param  Collection<int, object>  $itens
 * @param  array<int, mixed>  $bindings
 * @return Collection<int, object>
 */
function filtrarItensProtheus(Collection $itens, array $bindings): Collection
{
    $itens = $itens->reject(fn (object $item) => in_array(trim($item->B1_DESC), ['.', ','], true));

    $termo = collect($bindings)->first(fn ($valor) => is_string($valor) && str_starts_with($valor, '%'));

    if ($termo === null) {
        return $itens->values();
    }

    $termo = mb_strtolower(trim($termo, '%'));

    return $itens
        ->filter(fn (object $item) => str_contains(mb_strtolower($item->B1_DESC), $termo)
            || str_contains(mb_strtolower($item->B1_COD), $termo))
        ->values();
}

/**
 * Filial de teste no formato devolvido por FilialProtheusData::toArray(),
 * usada como prop reativa do componente nos testes que dependem dela.
 *
 * @return array{code: string, name: string, document: string, city: string, address: string, district: string, state: string, email: string, phone: string}
 */
function filialDeTeste(): array
{
    return [
        'code' => '010101',
        'name' => '010101-GRAN CORTE - INDUSTRIA',
        'document' => '17098519000157',
        'city' => 'CERQUEIRA CESAR',
        'address' => 'RODOVIA SALIM CURIATI, S/N',
        'district' => 'MACUQUINHO',
        'state' => 'SP',
        'email' => '',
        'phone' => '55-14-37142911',
    ];
}

/**
 * Estoque de teste lido pelo dublê da conexão 'protheus' a cada chamada.
 * mockarConexaoProtheus() só registra o dublê uma vez, no beforeEach; os
 * testes que precisam de um saldo específico usam definirEstoqueDeTeste()
 * pra sobrescrever o que o dublê já registrado vai ler na hora da consulta
 * (reatribuir o mock no meio do teste não funciona: o Mockery mantém a
 * primeira expectativa sem limite de chamadas como a válida).
 *
 * @return array<string, array<string, float>>
 */
function &estoqueDeTesteStore(): array
{
    static $estoque = [];

    return $estoque;
}

/**
 * @param  array<string, array<string, float>>  $estoque  [codigo_filial => [codigo_produto => saldo]]
 */
function definirEstoqueDeTeste(array $estoque): void
{
    $store = &estoqueDeTesteStore();
    $store = $estoque;
}

/**
 * Substitui a conexão 'protheus' real por um dublê que responde às mesmas
 * queries do ItemProtheusRepository e do EstoqueProtheusRepository sobre o
 * catálogo/estoque de teste em memória.
 */
function mockarConexaoProtheus(): void
{
    $itens = catalogoProtheusDeTeste();
    definirEstoqueDeTeste([]);

    $conexao = Mockery::mock(ConnectionInterface::class);

    $conexao->shouldReceive('selectOne')
        ->andReturnUsing(function (string $query, array $bindings = []) use ($itens) {
            if (str_contains($query, 'SB2010')) {
                [, $codigo, $filial] = $bindings;

                return (object) ['saldo' => estoqueDeTesteStore()[$filial][$codigo] ?? 0];
            }

            if (str_contains($query, 'COUNT(*)')) {
                return (object) ['total' => filtrarItensProtheus($itens, $bindings)->count()];
            }

            return $itens->first(fn (object $item) => $item->B1_COD === ($bindings[0] ?? null));
        });

    $conexao->shouldReceive('select')
        ->andReturnUsing(function (string $query, array $bindings = []) use ($itens) {
            [$offset, $perPage] = array_slice($bindings, -2);

            return filtrarItensProtheus($itens, $bindings)->slice($offset, $perPage)->values()->all();
        });

    DB::shouldReceive('connection')->with('protheus')->andReturn($conexao);
}

beforeEach(function () {
    mockarConexaoProtheus();
});

test('inicia sem itens e com os modais fechados', function () {
    Livewire::test(SolicitacaoItens::class)
        ->assertCount('itens', 0)
        ->assertSet('buscaModalAberta', false)
        ->assertSet('detalheModalAberta', false);
});

test('abre e fecha o modal de busca quando há filial selecionada', function () {
    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('abrirModalBusca')
        ->assertSet('buscaModalAberta', true)
        ->call('fecharModalBusca')
        ->assertSet('buscaModalAberta', false);
});

test('não abre o modal de busca sem filial selecionada', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('abrirModalBusca')
        ->assertSet('buscaModalAberta', false)
        ->assertDispatched('toast', tipo: 'error', mensagem: 'Selecione a filial antes de adicionar itens.');
});

test('lista todo o catálogo quando o termo de busca está vazio', function () {
    $component = Livewire::test(SolicitacaoItens::class);

    expect($component->instance()->resultadoBusca()['data'])->toHaveCount(3);
});

test('não lista produtos com descrição inválida (. ou ,)', function () {
    $component = Livewire::test(SolicitacaoItens::class);

    $codigos = collect($component->instance()->resultadoBusca()['data'])->pluck('code');

    expect($codigos)->not->toContain('99999');

    $component->set('termoBusca', '99999');
    expect($component->instance()->resultadoBusca()['data'])->toHaveCount(0);
});

test('filtra produtos por descrição', function () {
    $component = Livewire::test(SolicitacaoItens::class)
        ->set('termoBusca', 'notebook');

    expect($component->instance()->resultadoBusca()['data'])->toHaveCount(1);

    $component->set('termoBusca', 'produto inexistente');
    expect($component->instance()->resultadoBusca()['data'])->toHaveCount(0);
});

test('filtra produtos por código', function () {
    $component = Livewire::test(SolicitacaoItens::class)
        ->set('termoBusca', '88946');

    $resultado = $component->instance()->resultadoBusca()['data'];

    expect($resultado)->toHaveCount(1);
    expect($resultado[0]->code)->toBe('88946');
});

test('selecionar um produto fecha a busca e abre o modal de detalhes', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('abrirModalBusca')
        ->call('selecionarProduto', '88946')
        ->assertSet('buscaModalAberta', false)
        ->assertSet('detalheModalAberta', true)
        ->assertSet('produtoSelecionado.code', '88946')
        ->assertCount('itens', 0);
});

test('cancelar os detalhes fecha o modal sem adicionar o item', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('selecionarProduto', '88946')
        ->call('cancelarDetalhe')
        ->assertSet('detalheModalAberta', false)
        ->assertSet('produtoSelecionado', null)
        ->assertCount('itens', 0);
});

test('exige quantidade, data prazo, centro de custo e observação para confirmar o item', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('selecionarProduto', '88946')
        ->set('quantidade', '')
        ->set('dataPrazo', '')
        ->set('centroCusto', '')
        ->set('observacao', '')
        ->call('confirmarItem')
        ->assertHasErrors(['quantidade', 'dataPrazo', 'centroCusto', 'observacao'])
        ->assertCount('itens', 0);
});

test('confirma o item preenchido e ele volta pra tela principal', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('selecionarProduto', '88946')
        ->set('quantidade', 5)
        ->set('dataPrazo', now()->addDays(10)->format('Y-m-d'))
        ->set('centroCusto', 'ti')
        ->set('observacao', 'Uso interno do setor de TI.')
        ->call('confirmarItem')
        ->assertHasNoErrors()
        ->assertSet('detalheModalAberta', false)
        ->assertSet('produtoSelecionado', null)
        ->assertCount('itens', 1)
        ->assertSet('itens.0.codigo', '88946')
        ->assertSet('itens.0.quantidade', 5)
        ->assertSet('itens.0.centro_custo', 'TI');
});

test('não adiciona o mesmo produto duas vezes', function () {
    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('selecionarProduto', '88946')
        ->set('quantidade', 1)
        ->set('dataPrazo', now()->addDays(5)->format('Y-m-d'))
        ->set('centroCusto', 'comercial')
        ->set('observacao', 'Primeira compra.')
        ->call('confirmarItem');

    $component->call('abrirModalBusca')
        ->call('selecionarProduto', '88946')
        ->assertSet('detalheModalAberta', false)
        ->assertCount('itens', 1);
});

test('remove um item pelo código', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('selecionarProduto', '88946')
        ->set('quantidade', 2)
        ->set('dataPrazo', now()->addDays(3)->format('Y-m-d'))
        ->set('centroCusto', 'rh')
        ->set('observacao', 'Solicitação do RH.')
        ->call('confirmarItem')
        ->call('removerItem', '88946')
        ->assertCount('itens', 0);
});

test('consulta o saldo em estoque (SB2010) do produto ao selecioná-lo, quando há filial', function () {
    definirEstoqueDeTeste(['010101' => ['88946' => 3]]);

    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('selecionarProduto', '88946')
        ->assertSet('estoqueProdutoSelecionado', 3.0);
});

test('não consulta o saldo em estoque sem filial selecionada', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('selecionarProduto', '88946')
        ->assertSet('estoqueProdutoSelecionado', null);
});

test('pede confirmação antes de adicionar um produto com saldo na filial', function () {
    definirEstoqueDeTeste(['010101' => ['88946' => 3]]);

    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('selecionarProduto', '88946')
        ->set('quantidade', 2)
        ->set('dataPrazo', now()->addDays(5)->format('Y-m-d'))
        ->set('centroCusto', 'ti')
        ->set('observacao', 'Reposição de material.')
        ->call('confirmarItem')
        ->assertSet('avisoEstoqueAberto', true)
        ->assertCount('itens', 0)
        ->call('confirmarComEstoque')
        ->assertSet('avisoEstoqueAberto', false)
        ->assertCount('itens', 1)
        ->assertSet('itens.0.estoque_filial', 3.0);
});

test('cancelar o aviso de estoque não adiciona o item', function () {
    definirEstoqueDeTeste(['010101' => ['88946' => 3]]);

    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('selecionarProduto', '88946')
        ->set('quantidade', 2)
        ->set('dataPrazo', now()->addDays(5)->format('Y-m-d'))
        ->set('centroCusto', 'ti')
        ->set('observacao', 'Reposição de material.')
        ->call('confirmarItem')
        ->call('cancelarAvisoEstoque')
        ->assertSet('avisoEstoqueAberto', false)
        ->assertCount('itens', 0);
});

test('adiciona direto quando o produto não tem saldo na filial', function () {
    definirEstoqueDeTeste(['010101' => ['88946' => 0]]);

    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('selecionarProduto', '88946')
        ->set('quantidade', 2)
        ->set('dataPrazo', now()->addDays(5)->format('Y-m-d'))
        ->set('centroCusto', 'ti')
        ->set('observacao', 'Sem estoque disponível.')
        ->call('confirmarItem')
        ->assertSet('avisoEstoqueAberto', false)
        ->assertCount('itens', 1)
        ->assertSet('itens.0.estoque_filial', 0.0);
});
