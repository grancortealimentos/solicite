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
    ]);
}

/**
 * Filtra o catálogo de teste pelo termo de busca (%termo%) presente nas
 * bindings, replicando o WHERE RTRIM(B1_DESC) LIKE ? do repositório real.
 *
 * @param  Collection<int, object>  $itens
 * @param  array<int, mixed>  $bindings
 * @return Collection<int, object>
 */
function filtrarItensProtheus(Collection $itens, array $bindings): Collection
{
    $termo = collect($bindings)->first(fn ($valor) => is_string($valor) && str_starts_with($valor, '%'));

    if ($termo === null) {
        return $itens->values();
    }

    $termo = mb_strtolower(trim($termo, '%'));

    return $itens
        ->filter(fn (object $item) => str_contains(mb_strtolower($item->B1_DESC), $termo))
        ->values();
}

/**
 * Substitui a conexão 'protheus' real por um dublê que responde às mesmas
 * queries do ItemProtheusRepository sobre o catálogo de teste em memória.
 */
function mockarConexaoProtheus(): void
{
    $itens = catalogoProtheusDeTeste();

    $conexao = Mockery::mock(ConnectionInterface::class);

    $conexao->shouldReceive('selectOne')
        ->andReturnUsing(function (string $query, array $bindings = []) use ($itens) {
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

test('abre e fecha o modal de busca', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('abrirModalBusca')
        ->assertSet('buscaModalAberta', true)
        ->call('fecharModalBusca')
        ->assertSet('buscaModalAberta', false);
});

test('lista todo o catálogo quando o termo de busca está vazio', function () {
    $component = Livewire::test(SolicitacaoItens::class);

    expect($component->instance()->resultadoBusca()['data'])->toHaveCount(3);
});

test('filtra produtos por descrição', function () {
    $component = Livewire::test(SolicitacaoItens::class)
        ->set('termoBusca', 'notebook');

    expect($component->instance()->resultadoBusca()['data'])->toHaveCount(1);

    $component->set('termoBusca', 'produto inexistente');
    expect($component->instance()->resultadoBusca()['data'])->toHaveCount(0);
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
    $component = Livewire::test(SolicitacaoItens::class)
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
