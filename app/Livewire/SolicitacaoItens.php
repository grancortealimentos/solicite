<?php

namespace App\Livewire;

use App\Services\Protheus\ItemProtheusService;
use Livewire\Component;

class SolicitacaoItens extends Component
{
    /**
     * Termo de busca digitado pelo usuário, ligado ao input via wire:model.
     */
    public string $search = '';

    /**
     * Página atual da paginação.
     */
    public int $page = 1;

    /**
     * Quantidade de itens por página exibidos no seletor.
     */
    public int $perPage = 20;

    /**
     * Service injetado via boot(), não fica exposto como propriedade pública
     * (não precisa ser serializado entre requisições do Livewire).
     */
    private ItemProtheusService $service;

    /**
     * Hook do Livewire chamado a cada requisição do componente (inicial e nas
     * subsequentes), usado aqui só para resolver a dependência do Service.
     */
    public function boot(ItemProtheusService $service): void
    {
        $this->service = $service;
    }

    /**
     * Hook automático do Livewire, disparado depois que a propriedade $search muda.
     * Sempre que o usuário digita uma nova busca, volta pra primeira página —
     * evita o usuário ficar "preso" numa página 5 que não existe mais pro novo filtro.
     */
    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    /**
     * Avança para a próxima página, se houver mais resultados.
     */
    public function nextPage(): void
    {
        $this->page++;
    }

    /**
     * Volta para a página anterior, nunca abaixo de 1.
     */
    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);
    }

    /**
     * Chamado quando o usuário clica em um item da lista.
     * Emite um evento para o componente pai (formulário de solicitação)
     * com os dados do item selecionado, e limpa a busca.
     */
    public function selectItem(string $code): void
    {
        $item = $this->service->findByCode($code);

        if ($item === null) {
            return;
        }

        $this->dispatch('item-selected', item: $item->toArray());

        $this->search = '';
        $this->page = 1;
    }

    /**
     * Renderiza o componente, buscando os itens do Protheus conforme
     * o termo de busca e a página atual.
     */
    public function render()
    {
        $result = $this->service->search(
            description: $this->search !== '' ? $this->search : null,
            page: $this->page,
            perPage: $this->perPage,
        );

        return view('livewire.solicitacao-itens', [
            'items' => $result['data'],
            'total' => $result['total'],
            'hasNextPage' => ($this->page * $this->perPage) < $result['total'],
        ]);
    }
}
