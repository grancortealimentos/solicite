<?php

namespace App\Repositories;

use App\Models\Solicitacao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SolicitacaoRepository
{
    /**
     * @param  array<int, array{codigo: string, descricao: string, unidade_medida: string, armazem: string, cta_contabil: string, grupo_produto: string, quantidade: string, data_prazo: string, observacao: string, centro_custo: string}>  $itens
     */
    public function criar(int $userId, string $filial, array $itens): Solicitacao
    {
        return DB::transaction(function () use ($userId, $filial, $itens) {
            $solicitacao = Solicitacao::create([
                'user_id' => $userId,
                'filial' => $filial,
                'status' => Solicitacao::STATUS_PENDENTE,
            ]);

            $solicitacao->itens()->createMany(
                array_map(fn (array $item): array => Arr::except($item, 'estoque_filial'), $itens)
            );

            return $solicitacao;
        });
    }

    public function paginar(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        $busca = $filtros['search'] ?? null;
        $status = $filtros['status'] ?? null;
        $emitidaDe = $filtros['emitida_de'] ?? null;
        $emitidaAte = $filtros['emitida_ate'] ?? null;
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
            ->when(
                filled($emitidaAte),
                fn ($query) => $query->whereDate('created_at', '<=', $emitidaAte)
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
