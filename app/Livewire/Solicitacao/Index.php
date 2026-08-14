<?php

namespace App\Livewire\Solicitacao;

use App\Models\Solicitacao;
use App\Services\SolicitacaoService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Solicitações')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $busca = '';

    /**
     * '' => todos os status (padrão); caso contrário, um dos valores de status.
     */
    #[Url(as: 'status', except: '')]
    public string $filtroStatus = '';

    #[Url(as: 'emitida_de', except: '')]
    public string $emitidaDe = '';

    public bool $filtrosAbertos = false;

    public function mount(): void
    {
        $this->filtrosAbertos = $this->temFiltroAvancado();
    }

    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function updatedEmitidaDe(): void
    {
        $this->resetPage();
    }

    public function temFiltroAvancado(): bool
    {
        return $this->filtroStatus !== '' || $this->emitidaDe !== '';
    }

    public function temQualquerFiltro(): bool
    {
        return $this->temFiltroAvancado() || $this->busca !== '';
    }

    public function limparFiltros(): void
    {
        $this->reset(['busca', 'filtroStatus', 'emitidaDe']);
        $this->resetPage();
    }

    /**
     * Cancela uma solicitação pendente, checando permissão e que o usuário
     * é o próprio solicitante antes de delegar ao service.
     */
    public function cancelar(int $solicitacaoId, SolicitacaoService $solicitacaoService): void
    {
        $solicitacao = Solicitacao::findOrFail($solicitacaoId);

        $podeCancelar = auth()->user()->can('solicitacao.cancelar')
            && $solicitacao->user_id === auth()->id()
            && $solicitacao->status === Solicitacao::STATUS_PENDENTE;

        if (! $podeCancelar) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Você não tem permissão para cancelar esta solicitação.');

            return;
        }

        $solicitacaoService->cancelar($solicitacao);

        $this->dispatch('toast', tipo: 'success', mensagem: 'Solicitação cancelada.');
    }

    public function render(SolicitacaoService $solicitacaoService)
    {
        $solicitacoes = $solicitacaoService->listar([
            'search' => $this->busca,
            'status' => $this->filtroStatus,
            'emitida_de' => $this->emitidaDe,
        ]);

        return view('livewire.solicitacao.index', compact('solicitacoes'));
    }
}
