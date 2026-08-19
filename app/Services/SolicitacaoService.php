<?php

namespace App\Services;

use App\Models\Solicitacao;
use App\Repositories\SolicitacaoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SolicitacaoService
{
    public function __construct(
        private readonly SolicitacaoRepository $solicitacaoRepository,
    ) {}

    public function listar(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        return $this->solicitacaoRepository->paginar($filtros, $porPagina);
    }

    /**
     * @param  array<int, array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string}>  $itens
     */
    public function criar(int $userId, string $filial, array $itens): Solicitacao
    {
        return $this->solicitacaoRepository->criar($userId, $filial, $itens);
    }

    public function cancelar(Solicitacao $solicitacao): Solicitacao
    {
        return $this->solicitacaoRepository->atualizarStatus($solicitacao, Solicitacao::STATUS_CANCELADA);
    }
}
