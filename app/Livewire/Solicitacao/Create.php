<?php

namespace App\Livewire\Solicitacao;

use App\DTOs\Protheus\FilialProtheusData;
use App\Services\Protheus\FilialProtheusService;
use App\Services\SolicitacaoService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Nova solicitação')]
class Create extends Component
{
    /** Limite máximo de anos à frente para a data de uso. */
    private const MAX_ANOS_PREVISAO = 2;

    /**
     * @var array{code: string, name: string, document: string, city: string, address: string, district: string, state: string, email: string, phone: string}|null
     */
    public ?array $filial = null;

    public bool $filialBuscaAberta = false;

    public string $termoBuscaFilial = '';

    /**
     * Data de uso da solicitação, aplicada a todos os itens. Reativa: qualquer
     * alteração aqui é refletida pelo SolicitacaoItens (ver seu updated()).
     */
    public string $dataUso = '';

    /**
     * Cópia dos itens mantida pelo componente filho (SolicitacaoItens) via
     * evento, só pra ter o que persistir quando o usuário clicar em Salvar.
     *
     * @var array<int, array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string, estoque_filial: float}>
     */
    public array $itens = [];

    public bool $confirmacaoAberta = false;

    /**
     * @var array<int, string>
     */
    public array $problemas = [];

    private FilialProtheusService $filialProtheusService;

    public function boot(FilialProtheusService $filialProtheusService): void
    {
        $this->filialProtheusService = $filialProtheusService;
    }

    /**
     * @return array<int, FilialProtheusData>
     */
    #[Computed]
    public function resultadoBuscaFilial(): array
    {
        return $this->filialProtheusService->search(
            $this->termoBuscaFilial !== '' ? $this->termoBuscaFilial : null
        );
    }

    public function abrirBuscaFilial(): void
    {
        $this->termoBuscaFilial = '';
        $this->filialBuscaAberta = true;
    }

    public function fecharBuscaFilial(): void
    {
        $this->filialBuscaAberta = false;
    }

    public function selecionarFilial(string $codigo): void
    {
        $filial = $this->filialProtheusService->findByCode($codigo);

        if (! $filial) {
            return;
        }

        $this->filial = $filial->toArray();
        $this->filialBuscaAberta = false;
    }

    /**
     * @param  array<int, array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string, estoque_filial: float}>  $itens
     */
    #[On('itens-atualizados')]
    public function sincronizarItens(array $itens): void
    {
        $this->itens = $itens;
    }

    /**
     * Revalida filial e itens; se estiver tudo certo, abre a tela de revisão
     * antes de gravar de fato (evita salvar sem o usuário conferir os dados).
     */
    public function abrirConfirmacao(): void
    {
        $this->problemas = [];

        if (! $this->filial) {
            $this->problemas[] = 'Escolha a filial da solicitação.';
        }

        if (! $this->dataUso) {
            $this->problemas[] = 'Informe a data de uso da solicitação.';
        } elseif (! $this->dataUsoValida()) {
            $this->problemas[] = 'A data de uso deve estar entre hoje e '.self::MAX_ANOS_PREVISAO.' anos à frente.';
        }

        if (empty($this->itens)) {
            $this->problemas[] = 'Adicione pelo menos um item antes de salvar.';
        }

        if (! empty($this->problemas)) {
            return;
        }

        $this->confirmacaoAberta = true;
    }

    public function voltarParaEdicao(): void
    {
        $this->confirmacaoAberta = false;
    }

    private function dataUsoValida(): bool
    {
        try {
            $data = Carbon::createFromFormat('Y-m-d', $this->dataUso)->startOfDay();
        } catch (\Exception) {
            return false;
        }

        return $data->greaterThanOrEqualTo(today()) && $data->lessThanOrEqualTo(now()->addYears(self::MAX_ANOS_PREVISAO));
    }

    public function salvar(SolicitacaoService $solicitacaoService): void
    {
        if (! $this->filial) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Selecione a filial antes de salvar.');

            return;
        }

        if (! $this->dataUso) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Informe a data de uso antes de salvar.');

            return;
        }

        if (empty($this->itens)) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Adicione ao menos um item antes de salvar.');

            return;
        }

        $solicitacao = $solicitacaoService->criar(auth()->id(), $this->filial['code'], $this->itens);

        session()->flash('status', "Solicitação #{$solicitacao->id} criada com sucesso.");

        $this->redirectRoute('solicitacao.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.solicitacao.create');
    }
}
