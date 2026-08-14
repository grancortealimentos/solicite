<?php

namespace App\Livewire;

use App\DTOs\Protheus\ItemProtheusData;
use App\Services\Protheus\ItemProtheusService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SolicitacaoItens extends Component
{
    /**
     * @var array<int, array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string}>
     */
    public array $itens = [];

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
     * Service injetado via boot(), não fica exposto como propriedade pública
     * (não precisa ser serializado entre requisições do Livewire).
     */
    private ItemProtheusService $service;

    public function boot(ItemProtheusService $service): void
    {
        $this->service = $service;
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
            description: $this->termoBusca !== '' ? $this->termoBusca : null,
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

        $this->buscaModalAberta = false;
        $this->detalheModalAberta = true;
    }

    public function cancelarDetalhe(): void
    {
        $this->detalheModalAberta = false;
        $this->produtoSelecionado = null;
    }

    public function confirmarItem(): void
    {
        $dados = $this->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
            'dataPrazo' => ['required', 'date'],
            'observacao' => ['required', 'string', 'max:500'],
            'centroCusto' => ['required', Rule::in(array_keys($this->centrosCusto()))],
        ]);

        $this->itens[] = [
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
        ];

        $this->detalheModalAberta = false;
        $this->produtoSelecionado = null;
    }

    public function removerItem(string $codigo): void
    {
        $this->itens = array_values(
            array_filter($this->itens, fn (array $item): bool => $item['codigo'] !== $codigo)
        );
    }

    public function render()
    {
        return view('livewire.solicitacao-itens');
    }
}
