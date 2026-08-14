<?php

namespace App\Repositories;

use App\Models\Solicitacao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SolicitacaoRepository
{
    public function paginar(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        $busca = $filtros['search'] ?? null;
        $status = $filtros['status'] ?? null;
        $emitidaDe = $filtros['emitida_de'] ?? null;
        $buscaNumerica = $busca ? preg_replace('/\D/', '', $busca) : null;

        return Solicitacao::query()
            ->with('solicitante')
            ->withCount('itens')
            ->when(
                filled($busca),
                function ($query) use ($busca, $buscaNumerica) {
                    $termo = '%'.mb_strtolower($busca).'%';

                    $query->where(function ($q) use ($termo, $buscaNumerica) {
                        $q->whereHas(
                            'solicitante',
                            fn ($sub) => $sub->whereRaw('LOWER(name) LIKE ?', [$termo])
                                ->orWhereRaw('LOWER(email) LIKE ?', [$termo])
                        );

                        if ($buscaNumerica) {
                            $q->orWhere('id', $buscaNumerica);
                        }
                    });
                }
            )
            ->when(
                filled($status),
                fn ($query) => $query->where('status', $status)
            )
            ->when(
                filled($emitidaDe),
                fn ($query) => $query->whereDate('created_at', '>=', $emitidaDe)
            )
            ->latest()
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function atualizarStatus(Solicitacao $solicitacao, string $status): Solicitacao
    {
        $solicitacao->update(['status' => $status]);

        return $solicitacao->refresh();
    }
}
