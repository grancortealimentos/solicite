<?php

namespace App\Livewire;

use App\DTOs\Protheus\ItemProtheusData;
use App\Services\Protheus\EstoqueProtheusService;
use App\Services\Protheus\ItemProtheusService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class SolicitacaoItens extends Component
{
    /**
     * @var array<int, array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string, estoque_filial: float}>
     */
    public array $itens = [];

    /**
     * Filial selecionada na tela de "Nova solicitação" (componente pai), usada
     * pra consultar o saldo em estoque do produto antes de confirmar o item.
     *
     * @var array{code: string, name: string, document: string, city: string, address: string, district: string, state: string, email: string, phone: string}|null
     */
    #[Reactive]
    public ?array $filial = null;

    public bool $buscaModalAberta = false;

    public string $termoBusca = '';

    /**
     * Página atual da busca de produtos, exibida dentro da modal.
     */
    public int $pagina = 1;

    public int $porPagina = 20;

    public bool $detalheModalAberta = false;

    /**
     * @var array{code: string, description: string, type: string, location: string, unitMeasurement: string, account: string, group: string, groupDescription: string}|null
     */
    public ?array $produtoSelecionado = null;

    public string $quantidade = '';

    public string $dataPrazo = '';

    public string $observacao = '';

    public string $centroCusto = '';

    /**
     * Saldo em estoque (SB2010) do produto selecionado na filial atual.
     * Null quando ainda não há filial escolhida ou a consulta não rodou.
     */
    public ?float $estoqueProdutoSelecionado = null;

    public bool $avisoEstoqueAberto = false;

    /**
     * Item já validado, aguardando confirmação do usuário no aviso de
     * estoque antes de entrar de fato em $itens.
     *
     * @var array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string, estoque_filial: float}|null
     */
    public ?array $itemPendente = null;

    /**
     * Services injetados via boot(), não ficam expostos como propriedade
     * pública (não precisam ser serializados entre requisições do Livewire).
     */
    private ItemProtheusService $service;

    private EstoqueProtheusService $estoqueService;

    public function boot(ItemProtheusService $service, EstoqueProtheusService $estoqueService): void
    {
        $this->service = $service;
        $this->estoqueService = $estoqueService;
    }

    /**
     * Centros de custo somente leitura, usados até existir a integração com a tabela de centros de custo do Protheus.
     *
     * @return array<string, string>
     */
    protected function centrosCusto(): array
    {
        return [
            'comercial' => 'Comercial',
            'ti' => 'TI',
            'rh' => 'RH',
            'dp' => 'DP',
            'financeiro' => 'Financeiro',
        ];
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function centrosCustoDisponiveis(): array
    {
        return $this->centrosCusto();
    }

    /**
     * Resultado paginado da busca de produtos no Protheus, exibido na modal de busca.
     *
     * @return array{data: array<int, ItemProtheusData>, total: int, page: int, perPage: int}
     */
    #[Computed]
    public function resultadoBusca(): array
    {
        return $this->service->search(
            term: $this->termoBusca !== '' ? $this->termoBusca : null,
            page: $this->pagina,
            perPage: $this->porPagina,
        );
    }

    /**
     * Sempre que o usuário digita uma nova busca, força maiúsculas (padrão
     * Protheus) e volta pra primeira página — evita o usuário ficar "preso"
     * numa página que não existe mais pro novo filtro.
     */
    public function updatedTermoBusca(): void
    {
        $this->termoBusca = mb_strtoupper($this->termoBusca);
        $this->pagina = 1;
    }

    public function proximaPagina(): void
    {
        $this->pagina++;
    }

    public function paginaAnterior(): void
    {
        $this->pagina = max(1, $this->pagina - 1);
    }

    public function abrirModalBusca(): void
    {
        if (! $this->filial) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Selecione a filial antes de adicionar itens.');

            return;
        }

        $this->termoBusca = '';
        $this->pagina = 1;
        $this->buscaModalAberta = true;
    }

    public function fecharModalBusca(): void
    {
        $this->buscaModalAberta = false;
    }

    public function selecionarProduto(string $codigo): void
    {
        $jaAdicionado = collect($this->itens)->contains('codigo', $codigo);

        if ($jaAdicionado) {
            $this->fecharModalBusca();

            return;
        }

        $produto = $this->service->findByCode($codigo);

        if (! $produto) {
            $this->fecharModalBusca();

            return;
        }

        $this->produtoSelecionado = $produto->toArray();
        $this->quantidade = '';
        $this->dataPrazo = '';
        $this->observacao = '';
        $this->centroCusto = '';
        $this->resetValidation();

        $this->estoqueProdutoSelecionado = $this->filial
            ? $this->estoqueService->saldo($this->filial['code'], $codigo)
            : null;

        $this->buscaModalAberta = false;
        $this->detalheModalAberta = true;
    }

    public function cancelarDetalhe(): void
    {
        $this->detalheModalAberta = false;
        $this->produtoSelecionado = null;
        $this->estoqueProdutoSelecionado = null;
    }

    public function confirmarItem(): void
    {
        $dados = $this->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
            'dataPrazo' => ['required', 'date'],
            'observacao' => ['required', 'string', 'max:500'],
            'centroCusto' => ['required', Rule::in(array_keys($this->centrosCusto()))],
        ]);

        $item = [
            'codigo' => $this->produtoSelecionado['code'],
            'descricao' => $this->produtoSelecionado['description'],
            'unidade_medida' => $this->produtoSelecionado['unitMeasurement'],
            'armazem' => $this->produtoSelecionado['location'],
            'cta_contabil' => $this->produtoSelecionado['account'],
            'grupo_produto' => $this->produtoSelecionado['groupDescription'],
            'quantidade' => $dados['quantidade'],
            'data_prazo' => $dados['dataPrazo'],
            'observacao' => $dados['observacao'],
            'centro_custo' => $this->centrosCusto()[$dados['centroCusto']],
            'estoque_filial' => $this->estoqueProdutoSelecionado ?? 0,
        ];

        if ($this->filial && $this->estoqueProdutoSelecionado > 0) {
            $this->itemPendente = $item;
            $this->avisoEstoqueAberto = true;

            return;
        }

        $this->adicionarItem($item);
    }

    /**
     * Usuário confirmou que quer pedir mesmo o produto tendo saldo na filial.
     */
    public function confirmarComEstoque(): void
    {
        if (! $this->itemPendente) {
            return;
        }

        $this->adicionarItem($this->itemPendente);
    }

    public function cancelarAvisoEstoque(): void
    {
        $this->avisoEstoqueAberto = false;
        $this->itemPendente = null;
    }

    /**
     * @param  array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string, estoque_filial: float}  $item
     */
    private function adicionarItem(array $item): void
    {
        $this->itens[] = $item;

        $this->detalheModalAberta = false;
        $this->produtoSelecionado = null;
        $this->estoqueProdutoSelecionado = null;
        $this->avisoEstoqueAberto = false;
        $this->itemPendente = null;

        $this->dispatch('itens-atualizados', itens: $this->itens);
    }

    public function removerItem(string $codigo): void
    {
        $this->itens = array_values(
            array_filter($this->itens, fn (array $item): bool => $item['codigo'] !== $codigo)
        );

        $this->dispatch('itens-atualizados', itens: $this->itens);
    }

    public function render()
    {
        return view('livewire.solicitacao-itens');
    }
}
