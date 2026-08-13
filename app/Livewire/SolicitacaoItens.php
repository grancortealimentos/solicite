<?php

namespace App\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SolicitacaoItens extends Component
{
    /**
     * @var array<int, array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, importacao: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string}>
     */
    public array $itens = [];

    public bool $buscaModalAberta = false;

    public string $termoBusca = '';

    public bool $detalheModalAberta = false;

    /**
     * @var array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, importacao: string, grupo_produto: string}|null
     */
    public ?array $produtoSelecionado = null;

    public string $quantidade = '';

    public string $dataPrazo = '';

    public string $observacao = '';

    public string $centroCusto = '';

    /**
     * Catálogo de produtos somente leitura, usado até existir uma fonte de dados real (tabela local ou integração com ERP).
     *
     * @return array<int, array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, importacao: string, grupo_produto: string}>
     */
    protected function catalogoProdutos(): array
    {
        return [
            [
                'codigo' => '88946',
                'descricao' => 'MINI PC BLUE 2K INTEL® CORE I3, 8GB RAM, 240 GB SSD, SERIAL ON BOARD WIND 10 PRO',
                'unidade_medida' => 'UN',
                'armazem' => '50',
                'cta_contabil' => '123456789',
                'importacao' => 'Não',
                'grupo_produto' => '4909',
            ],
            [
                'codigo' => '90211',
                'descricao' => 'NOTEBOOK DELL VOSTRO 3520 INTEL CORE I5, 16GB RAM, 512 GB SSD, WINDOWS 11 PRO',
                'unidade_medida' => 'UN',
                'armazem' => '50',
                'cta_contabil' => '123456789',
                'importacao' => 'Sim',
                'grupo_produto' => '4909',
            ],
            [
                'codigo' => '77213',
                'descricao' => 'MONITOR LED 24" FULL HD HDMI/VGA',
                'unidade_medida' => 'UN',
                'armazem' => '10',
                'cta_contabil' => '123456790',
                'importacao' => 'Sim',
                'grupo_produto' => '4910',
            ],
            [
                'codigo' => '65402',
                'descricao' => 'TECLADO E MOUSE SEM FIO USB',
                'unidade_medida' => 'KIT',
                'armazem' => '10',
                'cta_contabil' => '123456790',
                'importacao' => 'Não',
                'grupo_produto' => '4910',
            ],
            [
                'codigo' => '34120',
                'descricao' => 'PAPEL SULFITE A4 75G/M² 500 FOLHAS',
                'unidade_medida' => 'RESMA',
                'armazem' => '20',
                'cta_contabil' => '123456791',
                'importacao' => 'Não',
                'grupo_produto' => '3102',
            ],
            [
                'codigo' => '54098',
                'descricao' => 'CADEIRA DE ESCRITÓRIO GIRATÓRIA COM APOIO DE BRAÇO',
                'unidade_medida' => 'UN',
                'armazem' => '20',
                'cta_contabil' => '123456792',
                'importacao' => 'Não',
                'grupo_produto' => '2205',
            ],
        ];
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

    #[Computed]
    public function produtosEncontrados(): array
    {
        $termo = mb_strtolower(trim($this->termoBusca));

        if ($termo === '') {
            return $this->catalogoProdutos();
        }

        return array_values(array_filter(
            $this->catalogoProdutos(),
            fn (array $produto): bool => str_contains(mb_strtolower($produto['codigo']), $termo)
                || str_contains(mb_strtolower($produto['descricao']), $termo)
        ));
    }

    public function abrirModalBusca(): void
    {
        $this->termoBusca = '';
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

        $produto = collect($this->catalogoProdutos())->firstWhere('codigo', $codigo);

        if (! $produto) {
            $this->fecharModalBusca();

            return;
        }

        $this->produtoSelecionado = $produto;
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

        $this->itens[] = array_merge($this->produtoSelecionado, [
            'quantidade' => $dados['quantidade'],
            'data_prazo' => $dados['dataPrazo'],
            'observacao' => $dados['observacao'],
            'centro_custo' => $this->centrosCusto()[$dados['centroCusto']],
        ]);

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
