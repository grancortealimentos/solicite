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

    public function cancelar(Solicitacao $solicitacao): Solicitacao
    {
        return $this->solicitacaoRepository->atualizarStatus($solicitacao, Solicitacao::STATUS_CANCELADA);
    }
}
