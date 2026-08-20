<?php

namespace App\Livewire;

use App\DTOs\Protheus\ItemProtheusData;
use App\Services\Protheus\CentroCustoProtheusService;
use App\Services\Protheus\EstoqueProtheusService;
use App\Services\Protheus\ItemProtheusService;
use App\Services\Protheus\UltimaCompraProtheusService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithFileUploads;

class SolicitacaoItens extends Component
{
    use WithFileUploads;

    /** Acima disso pedimos confirmação (evita erro de digitação). */
    private const QUANTIDADE_ALTA = 1000;

    private const MAX_IMAGENS_POR_ITEM = 3;

    /**
     * @var array<int, array{item: int, codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string, estoque_filial: float, imagens: array<int, string>}>
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

    /**
     * Data de uso definida no cabeçalho da solicitação (componente pai),
     * aplicada a todos os itens.
     */
    #[Reactive]
    public string $dataUso = '';

    public bool $buscaModalAberta = false;

    public string $termoBusca = '';

    /**
     * Página atual da busca de produtos, exibida dentro da modal.
     */
    public int $pagina = 1;

    public int $porPagina = 20;

    /**
     * Painel inline (não modal) com o formulário do item — aberto tanto pra
     * incluir um item novo quanto pra editar um já existente.
     */
    public bool $formAberto = false;

    /**
     * Número do item (posição em $itens) em edição; null quando é um item novo.
     */
    public ?int $itemEmEdicao = null;

    /**
     * @var array{code: string, description: string, type: string, location: string, unitMeasurement: string, account: string, group: string, groupDescription: string}|null
     */
    public ?array $produtoSelecionado = null;

    public string $quantidade = '';

    public string $observacao = '';

    public string $centroCusto = '';

    /**
     * Saldo em estoque (SB2010) do produto selecionado na filial atual.
     * Null quando ainda não há filial escolhida ou a consulta não rodou.
     */
    public ?float $estoqueProdutoSelecionado = null;

    /**
     * Última compra (SF1010/SD1010/SA2010) do produto selecionado, pra ajudar
     * o solicitante a conferir fornecedor e preço de referência. Null quando
     * não há produto selecionado ou não existe histórico de compra dele.
     *
     * @var array{fornecedor: string, dataCompra: string|null, valorUnitario: float}|null
     */
    public ?array $ultimaCompraProduto = null;

    /**
     * Caminhos (disco 'public') das imagens já enviadas pro item em edição.
     *
     * @var array<int, string>
     */
    public array $imagens = [];

    /**
     * Upload temporário corrente, processado assim que chega (updatedNovaImagem).
     */
    public $novaImagem = null;

    /**
     * Avisos (não bloqueantes) pendentes de confirmação antes de gravar o item:
     * item duplicado, quantidade alta, produto com saldo na filial.
     *
     * @var array<int, string>
     */
    public array $avisos = [];

    public bool $avisosAberto = false;

    /**
     * @var array{item: int, codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string, estoque_filial: float, imagens: array<int, string>}|null
     */
    public ?array $itemPendente = null;

    /**
     * Services injetados via boot(), não ficam expostos como propriedade
     * pública (não precisam ser serializados entre requisições do Livewire).
     */
    private ItemProtheusService $service;

    private EstoqueProtheusService $estoqueService;

    private CentroCustoProtheusService $centroCustoService;

    private UltimaCompraProtheusService $ultimaCompraService;

    public function boot(
        ItemProtheusService $service,
        EstoqueProtheusService $estoqueService,
        CentroCustoProtheusService $centroCustoService,
        UltimaCompraProtheusService $ultimaCompraService,
    ): void {
        $this->service = $service;
        $this->estoqueService = $estoqueService;
        $this->centroCustoService = $centroCustoService;
        $this->ultimaCompraService = $ultimaCompraService;
    }

    /**
     * Centros de custo da filial atual (VW_SOLICITE_CENTROCUSTOS), no formato
     * código => descrição pra popular o select. Vazio sem filial selecionada.
     *
     * @return array<string, string>
     */
    #[Computed]
    public function centrosCustoDisponiveis(): array
    {
        if (! $this->filial) {
            return [];
        }

        return collect($this->centroCustoService->porFilial($this->filial['code']))
            ->mapWithKeys(fn ($centroCusto) => [$centroCusto->codigo => "{$centroCusto->codigo} - {$centroCusto->descricao}"])
            ->all();
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

    /**
     * Abre o painel do item já com a busca de produto em cima, pra incluir um
     * item novo. Trava se ainda não há filial selecionada.
     */
    public function iniciarNovoItem(): void
    {
        if (! $this->filial) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Selecione a filial antes de adicionar itens.');

            return;
        }

        if (! $this->dataUso) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Informe a data de uso antes de adicionar itens.');

            return;
        }

        $this->limparFormulario();
        $this->itemEmEdicao = null;
        $this->formAberto = true;

        $this->abrirModalBusca();
    }

    /**
     * Abre o painel já preenchido com os dados de um item existente, pronto
     * pra edição (sem reabrir a busca de produto).
     */
    public function iniciarEdicao(int $item): void
    {
        $dados = collect($this->itens)->firstWhere('item', $item);

        if (! $dados) {
            return;
        }

        $this->produtoSelecionado = [
            'code' => $dados['codigo'],
            'description' => $dados['descricao'],
            'type' => '',
            'location' => $dados['armazem'],
            'unitMeasurement' => $dados['unidade_medida'],
            'account' => $dados['cta_contabil'],
            'group' => '',
            'groupDescription' => $dados['grupo_produto'],
        ];
        $this->quantidade = (string) $dados['quantidade'];
        $this->observacao = (string) $dados['observacao'];
        $this->centroCusto = array_search($dados['centro_custo'], $this->centrosCustoDisponiveis(), true) ?: '';
        $this->imagens = $dados['imagens'] ?? [];
        $this->estoqueProdutoSelecionado = $dados['estoque_filial'] ?? null;
        $this->ultimaCompraProduto = $this->ultimaCompraService->porProduto($dados['codigo'])?->toArray();
        $this->resetValidation();

        $this->itemEmEdicao = $item;
        $this->formAberto = true;
        $this->buscaModalAberta = false;
    }

    public function cancelarFormulario(): void
    {
        $this->formAberto = false;
        $this->itemEmEdicao = null;
        $this->limparFormulario();
    }

    private function limparFormulario(): void
    {
        $this->produtoSelecionado = null;
        $this->quantidade = '1';
        $this->observacao = '';
        $this->centroCusto = '';
        $this->imagens = [];
        $this->estoqueProdutoSelecionado = null;
        $this->ultimaCompraProduto = null;
        $this->resetValidation();
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
        $produto = $this->service->findByCode($codigo);

        if (! $produto) {
            $this->fecharModalBusca();

            return;
        }

        $this->produtoSelecionado = $produto->toArray();
        $this->resetValidation();

        $this->estoqueProdutoSelecionado = $this->filial
            ? $this->estoqueService->saldo($this->filial['code'], $codigo)
            : null;

        $this->ultimaCompraProduto = $this->ultimaCompraService->porProduto($codigo)?->toArray();

        $this->buscaModalAberta = false;
    }

    /**
     * Processa o upload assim que o arquivo chega (input com wire:model).
     */
    public function updatedNovaImagem(): void
    {
        if (! $this->novaImagem) {
            return;
        }

        if (count($this->imagens) >= self::MAX_IMAGENS_POR_ITEM) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Limite de '.self::MAX_IMAGENS_POR_ITEM.' imagens atingido.');
            $this->novaImagem = null;

            return;
        }

        $this->validate([
            'novaImagem' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $this->imagens[] = $this->novaImagem->store('solicitacao-itens', 'public');
        $this->novaImagem = null;

        $this->dispatch('toast', tipo: 'success', mensagem: 'Imagem adicionada.');
    }

    public function removerImagem(string $caminho): void
    {
        Storage::disk('public')->delete($caminho);

        $this->imagens = array_values(array_filter($this->imagens, fn (string $i): bool => $i !== $caminho));
    }

    public function confirmarItem(): void
    {
        $dados = $this->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
            'observacao' => ['required', 'string', 'max:500'],
            'centroCusto' => ['required', Rule::in(array_keys($this->centrosCustoDisponiveis()))],
        ]);

        $numeroItem = $this->itemEmEdicao ?? (count($this->itens) + 1);

        $item = [
            'item' => $numeroItem,
            'codigo' => $this->produtoSelecionado['code'],
            'descricao' => $this->produtoSelecionado['description'],
            'unidade_medida' => $this->produtoSelecionado['unitMeasurement'],
            'armazem' => $this->produtoSelecionado['location'],
            'cta_contabil' => $this->produtoSelecionado['account'],
            'grupo_produto' => $this->produtoSelecionado['groupDescription'],
            'quantidade' => $dados['quantidade'],
            'data_prazo' => $this->dataUso,
            'observacao' => $dados['observacao'],
            'centro_custo' => $this->centrosCustoDisponiveis()[$dados['centroCusto']],
            'estoque_filial' => $this->estoqueProdutoSelecionado ?? 0,
            'imagens' => $this->imagens,
        ];

        $avisos = [];

        $duplicado = collect($this->itens)
            ->first(fn (array $i): bool => $i['codigo'] === $item['codigo'] && $i['item'] !== $numeroItem);

        if ($duplicado) {
            $avisos[] = "Este produto já foi adicionado no item {$duplicado['item']}. Deseja adicionar mesmo assim?";
        }

        if ($dados['quantidade'] > self::QUANTIDADE_ALTA) {
            $avisos[] = "Quantidade alta: {$dados['quantidade']} (acima de ".self::QUANTIDADE_ALTA.'). Confira o valor digitado.';
        }

        if ($this->filial && $this->estoqueProdutoSelecionado > 0) {
            $unidade = $this->estoqueProdutoSelecionado > 1 ? 'unidades' : 'unidade';
            $avisos[] = "O produto {$item['descricao']} tem {$this->estoqueProdutoSelecionado} {$unidade} no estoque da filial. Tem certeza que deseja pedir?";
        }

        if (! empty($avisos)) {
            $this->itemPendente = $item;
            $this->avisos = $avisos;
            $this->avisosAberto = true;

            return;
        }

        $this->adicionarOuAtualizarItem($item);
    }

    /**
     * Usuário confirmou que quer seguir mesmo com os avisos exibidos.
     */
    public function confirmarComAvisos(): void
    {
        if (! $this->itemPendente) {
            return;
        }

        $this->adicionarOuAtualizarItem($this->itemPendente);
    }

    public function cancelarAvisos(): void
    {
        $this->avisosAberto = false;
        $this->avisos = [];
        $this->itemPendente = null;
    }

    /**
     * @param  array{item: int, codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string, estoque_filial: float, imagens: array<int, string>}  $item
     */
    private function adicionarOuAtualizarItem(array $item): void
    {
        if ($this->itemEmEdicao !== null) {
            $this->itens = collect($this->itens)
                ->map(fn (array $i): array => $i['item'] === $item['item'] ? $item : $i)
                ->all();
        } else {
            $this->itens[] = $item;
        }

        $this->avisosAberto = false;
        $this->avisos = [];
        $this->itemPendente = null;

        $this->cancelarFormulario();

        $this->dispatch('itens-atualizados', itens: $this->itens);
    }

    /**
     * $dataUso é reativa (vem do cabeçalho, componente pai); o Livewire chama
     * este hook quando o valor muda.
     */
    public function updated(string $name): void
    {
        if ($name === 'dataUso') {
            $this->sincronizarDataUso();
        }
    }

    /**
     * Propaga a data de uso atual do cabeçalho pros itens já adicionados,
     * pra não ficar item com data desatualizada em relação ao cabeçalho.
     */
    public function sincronizarDataUso(): void
    {
        if (empty($this->itens)) {
            return;
        }

        $this->itens = collect($this->itens)
            ->map(fn (array $i): array => [...$i, 'data_prazo' => $this->dataUso])
            ->all();

        $this->dispatch('itens-atualizados', itens: $this->itens);
    }

    public function removerItem(int $item): void
    {
        $this->itens = collect($this->itens)
            ->reject(fn (array $i): bool => $i['item'] === $item)
            ->values()
            ->map(fn (array $i, int $indice): array => [...$i, 'item' => $indice + 1])
            ->all();

        $this->dispatch('itens-atualizados', itens: $this->itens);
    }

    public function render()
    {
        return view('livewire.solicitacao-itens');
    }
}
