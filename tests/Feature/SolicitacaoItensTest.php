<?php

use App\Livewire\SolicitacaoItens;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
 * Linhas cruas no formato retornado pela view VW_SOLICITE_CENTROCUSTOS do
 * Protheus, pra filial de teste (010101) — mantém os mesmos códigos/labels
 * da lista fixa antiga pra não precisar reescrever todos os testes.
 *
 * @return Collection<int, object>
 */
function catalogoCentrosCustoDeTeste(): Collection
{
    return collect([
        ['comercial', 'Comercial'],
        ['ti', 'TI'],
        ['rh', 'RH'],
        ['dp', 'DP'],
        ['financeiro', 'Financeiro'],
    ])->map(fn (array $c) => (object) [
        'CTT_FILIAL' => '010101',
        'NOME_FILIAL' => '010101-GRAN CORTE - INDUSTRIA',
        'CTT_CUSTO' => $c[0],
        'CTT_DESC01' => $c[1],
        'CTT_X_CTA' => '',
    ]);
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
            if (str_contains($query, 'VW_SOLICITE_CENTROCUSTOS')) {
                $filial = $bindings[0] ?? null;

                return catalogoCentrosCustoDeTeste()->where('CTT_FILIAL', $filial)->values()->all();
            }

            [$offset, $perPage] = array_slice($bindings, -2);

            return filtrarItensProtheus($itens, $bindings)->slice($offset, $perPage)->values()->all();
        });

    DB::shouldReceive('connection')->with('protheus')->andReturn($conexao);
}

/**
 * Preenche e confirma o item corrente (já com produto selecionado), sem
 * disparar nenhum aviso — atalho usado pelos testes que só precisam de um
 * item na lista pra testar outra coisa (editar, remover, etc).
 */
function preencherEConfirmarItem($component, array $dados = []): void
{
    $component
        ->set('quantidade', $dados['quantidade'] ?? 2)
        ->set('dataPrazo', $dados['dataPrazo'] ?? now()->addDays(5)->format('Y-m-d'))
        ->set('centroCusto', $dados['centroCusto'] ?? 'ti')
        ->set('observacao', $dados['observacao'] ?? 'Observação de teste.')
        ->call('confirmarItem');
}

beforeEach(function () {
    mockarConexaoProtheus();
});

test('inicia sem itens e com o painel e a busca fechados', function () {
    Livewire::test(SolicitacaoItens::class)
        ->assertCount('itens', 0)
        ->assertSet('buscaModalAberta', false)
        ->assertSet('formAberto', false);
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

test('lista os centros de custo (VW_SOLICITE_CENTROCUSTOS) da filial selecionada', function () {
    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()]);

    $centros = $component->instance()->centrosCustoDisponiveis();

    expect($centros)->toBe([
        'comercial' => 'comercial - Comercial',
        'ti' => 'ti - TI',
        'rh' => 'rh - RH',
        'dp' => 'dp - DP',
        'financeiro' => 'financeiro - Financeiro',
    ]);
});

test('não lista centros de custo sem filial selecionada', function () {
    $component = Livewire::test(SolicitacaoItens::class);

    expect($component->instance()->centrosCustoDisponiveis())->toBe([]);
});

test('iniciar novo item abre o painel e a busca de produto quando há filial', function () {
    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->assertSet('formAberto', true)
        ->assertSet('buscaModalAberta', true)
        ->assertSet('itemEmEdicao', null)
        ->assertSet('quantidade', '1');
});

test('não inicia novo item sem filial selecionada', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('iniciarNovoItem')
        ->assertSet('formAberto', false)
        ->assertDispatched('toast', tipo: 'error', mensagem: 'Selecione a filial antes de adicionar itens.');
});

test('selecionar um produto fecha a busca mantendo o painel do item aberto', function () {
    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->assertSet('buscaModalAberta', false)
        ->assertSet('formAberto', true)
        ->assertSet('produtoSelecionado.code', '88946')
        ->assertCount('itens', 0);
});

test('cancelar o painel fecha sem adicionar o item', function () {
    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->call('cancelarFormulario')
        ->assertSet('formAberto', false)
        ->assertSet('produtoSelecionado', null)
        ->assertCount('itens', 0);
});

test('exige quantidade, data prazo, centro de custo e observação para confirmar o item', function () {
    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('quantidade', '')
        ->set('dataPrazo', '')
        ->set('centroCusto', '')
        ->set('observacao', '')
        ->call('confirmarItem')
        ->assertHasErrors(['quantidade', 'dataPrazo', 'centroCusto', 'observacao'])
        ->assertCount('itens', 0);
});

test('rejeita data prazo no passado', function () {
    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('dataPrazo', now()->subDay()->format('Y-m-d'))
        ->call('confirmarItem')
        ->assertHasErrors(['dataPrazo']);
});

test('rejeita data prazo mais de 2 anos à frente', function () {
    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('dataPrazo', now()->addYears(2)->addDay()->format('Y-m-d'))
        ->call('confirmarItem')
        ->assertHasErrors(['dataPrazo']);
});

test('confirma o item preenchido e ele volta pra tela principal', function () {
    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946');

    preencherEConfirmarItem($component, ['quantidade' => 5, 'centroCusto' => 'ti', 'observacao' => 'Uso interno do setor de TI.']);

    $component
        ->assertHasNoErrors()
        ->assertSet('formAberto', false)
        ->assertSet('produtoSelecionado', null)
        ->assertCount('itens', 1)
        ->assertSet('itens.0.item', 1)
        ->assertSet('itens.0.codigo', '88946')
        ->assertSet('itens.0.quantidade', 5)
        ->assertSet('itens.0.centro_custo', 'ti - TI')
        ->assertSet('itens.0.imagens', []);
});

test('avisa e pede confirmação ao adicionar o mesmo produto duas vezes', function () {
    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946');
    preencherEConfirmarItem($component);

    $component->call('iniciarNovoItem')->call('selecionarProduto', '88946');
    preencherEConfirmarItem($component);

    $component
        ->assertSet('avisosAberto', true)
        ->assertCount('itens', 1)
        ->call('confirmarComAvisos')
        ->assertCount('itens', 2)
        ->assertSet('itens.1.item', 2);
});

test('avisa quando a quantidade é muito alta', function () {
    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946');

    preencherEConfirmarItem($component, ['quantidade' => 1500]);

    $component
        ->assertSet('avisosAberto', true)
        ->assertCount('itens', 0);

    expect($component->get('avisos'))->toContain('Quantidade alta: 1500 (acima de 1000). Confira o valor digitado.');
});

test('edita um item já adicionado', function () {
    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946');
    preencherEConfirmarItem($component, ['quantidade' => 2]);

    $component
        ->call('iniciarEdicao', 1)
        ->assertSet('formAberto', true)
        ->assertSet('itemEmEdicao', 1)
        ->assertSet('produtoSelecionado.code', '88946')
        ->assertSet('quantidade', '2')
        ->set('quantidade', 9)
        ->call('confirmarItem')
        ->assertCount('itens', 1)
        ->assertSet('itens.0.item', 1)
        ->assertSet('itens.0.quantidade', 9);
});

test('remove um item pelo número e renumera os restantes', function () {
    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()]);

    $component->call('iniciarNovoItem')->call('selecionarProduto', '88946');
    preencherEConfirmarItem($component);

    $component->call('iniciarNovoItem')->call('selecionarProduto', '90211');
    preencherEConfirmarItem($component);

    $component
        ->assertCount('itens', 2)
        ->call('removerItem', 1)
        ->assertCount('itens', 1)
        ->assertSet('itens.0.codigo', '90211')
        ->assertSet('itens.0.item', 1);
});

test('consulta o saldo em estoque (SB2010) do produto ao selecioná-lo, quando há filial', function () {
    definirEstoqueDeTeste(['010101' => ['88946' => 3]]);

    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
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

    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946');

    preencherEConfirmarItem($component, ['centroCusto' => 'ti', 'observacao' => 'Reposição de material.']);

    $component
        ->assertSet('avisosAberto', true)
        ->assertCount('itens', 0)
        ->call('confirmarComAvisos')
        ->assertSet('avisosAberto', false)
        ->assertCount('itens', 1)
        ->assertSet('itens.0.estoque_filial', 3.0);
});

test('cancelar os avisos não adiciona o item', function () {
    definirEstoqueDeTeste(['010101' => ['88946' => 3]]);

    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946');

    preencherEConfirmarItem($component, ['centroCusto' => 'ti', 'observacao' => 'Reposição de material.']);

    $component
        ->call('cancelarAvisos')
        ->assertSet('avisosAberto', false)
        ->assertCount('itens', 0);
});

test('adiciona direto quando o produto não tem saldo na filial', function () {
    definirEstoqueDeTeste(['010101' => ['88946' => 0]]);

    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946');

    preencherEConfirmarItem($component, ['centroCusto' => 'ti', 'observacao' => 'Sem estoque disponível.']);

    $component
        ->assertSet('avisosAberto', false)
        ->assertCount('itens', 1)
        ->assertSet('itens.0.estoque_filial', 0.0);
});

test('envia uma imagem válida do item', function () {
    Storage::fake('public');

    $arquivo = UploadedFile::fake()->image('foto.jpg', 100, 100)->size(500);

    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('novaImagem', $arquivo)
        ->assertCount('imagens', 1)
        ->tap(function ($component) {
            Storage::disk('public')->assertExists($component->get('imagens')[0]);
        });
})->skip(! extension_loaded('gd'), 'Requer a extensão GD pra gerar imagens falsas no teste.');

test('rejeita arquivo que não é imagem', function () {
    Storage::fake('public');

    $arquivo = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('novaImagem', $arquivo)
        ->assertHasErrors(['novaImagem'])
        ->assertCount('imagens', 0);
});

test('rejeita imagem maior que 5 MB', function () {
    Storage::fake('public');

    $arquivo = UploadedFile::fake()->image('foto.jpg')->size(6000);

    Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('novaImagem', $arquivo)
        ->assertHasErrors(['novaImagem'])
        ->assertCount('imagens', 0);
})->skip(! extension_loaded('gd'), 'Requer a extensão GD pra gerar imagens falsas no teste.');

test('remove uma imagem já enviada', function () {
    Storage::fake('public');

    $arquivo = UploadedFile::fake()->image('foto.jpg')->size(500);

    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('novaImagem', $arquivo);

    $caminho = $component->get('imagens')[0];

    $component->call('removerImagem', $caminho)
        ->assertCount('imagens', 0);

    Storage::disk('public')->assertMissing($caminho);
})->skip(! extension_loaded('gd'), 'Requer a extensão GD pra gerar imagens falsas no teste.');

test('não envia uma quinta imagem além do limite', function () {
    Storage::fake('public');

    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('imagens', ['a.jpg', 'b.jpg', 'c.jpg']);

    $component
        ->set('novaImagem', UploadedFile::fake()->image('foto.jpg')->size(100))
        ->assertCount('imagens', 3)
        ->assertDispatched('toast', tipo: 'error', mensagem: 'Limite de 3 imagens atingido.');
})->skip(! extension_loaded('gd'), 'Requer a extensão GD pra gerar imagens falsas no teste.');

test('as imagens do item ficam salvas junto com o item confirmado', function () {
    Storage::fake('public');

    $component = Livewire::test(SolicitacaoItens::class, ['filial' => filialDeTeste()])
        ->call('iniciarNovoItem')
        ->call('selecionarProduto', '88946')
        ->set('novaImagem', UploadedFile::fake()->image('foto.jpg')->size(500));

    preencherEConfirmarItem($component);

    expect($component->get('itens.0.imagens'))->toHaveCount(1);
})->skip(! extension_loaded('gd'), 'Requer a extensão GD pra gerar imagens falsas no teste.');
