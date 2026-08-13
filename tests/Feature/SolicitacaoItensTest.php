<?php

use App\Livewire\SolicitacaoItens;
use Livewire\Livewire;

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

    expect($component->instance()->produtosEncontrados())->toHaveCount(6);
});

test('filtra produtos por código ou descrição', function () {
    $component = Livewire::test(SolicitacaoItens::class)
        ->set('termoBusca', '88946');

    expect($component->instance()->produtosEncontrados())->toHaveCount(1);

    $component->set('termoBusca', 'notebook');
    expect($component->instance()->produtosEncontrados())->toHaveCount(1);

    $component->set('termoBusca', 'produto inexistente');
    expect($component->instance()->produtosEncontrados())->toHaveCount(0);
});

test('selecionar um produto fecha a busca e abre o modal de detalhes', function () {
    Livewire::test(SolicitacaoItens::class)
        ->call('abrirModalBusca')
        ->call('selecionarProduto', '88946')
        ->assertSet('buscaModalAberta', false)
        ->assertSet('detalheModalAberta', true)
        ->assertSet('produtoSelecionado.codigo', '88946')
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
