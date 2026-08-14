<?php

declare(strict_types=1);

namespace App\Repositories\Protheus;

use App\DTOs\Protheus\ItemProtheusData;
use Illuminate\Support\Facades\DB;

final class ItemProtheusRepository
{
    /**
     * Descrições sem valor cadastradas incorretamente no Protheus (ex.: "."
     * ou ","), que não devem aparecer na busca de itens.
     */
    private const DESCRICOES_INVALIDAS = ['.', ','];

    public function search(?string $term, int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;

        $hasTerm = $term !== null && $term !== '';

        $placeholders = implode(', ', array_fill(0, count(self::DESCRICOES_INVALIDAS), '?'));
        $conditions = ["RTRIM(B1_DESC) NOT IN ({$placeholders})"];
        $bindings = self::DESCRICOES_INVALIDAS;

        if ($hasTerm) {
            $conditions[] = '(RTRIM(B1_DESC) LIKE ? OR RTRIM(B1_COD) LIKE ?)';
            $bindings = [...$bindings, '%'.$term.'%', '%'.$term.'%'];
        }

        $whereClause = 'WHERE '.implode(' AND ', $conditions);

        $total = DB::connection('protheus')
            ->selectOne(
                "SELECT COUNT(*) AS total FROM VW_SOLICITE_ITENS {$whereClause}",
                $bindings
            )->total;

        $rows = DB::connection('protheus')->select(
            "SELECT * FROM VW_SOLICITE_ITENS
            {$whereClause}
            ORDER BY B1_DESC
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY",
            [...$bindings, $offset, $perPage]
        );

        return [
            'data' => array_map(
                fn (object $row): ItemProtheusData => ItemProtheusData::forRead((array) $row),
                $rows
            ),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ];
    }

    public function findByCode(string $code): ?ItemProtheusData
    {
        $row = DB::connection('protheus')
            ->selectOne(
                'SELECT TOP 1 * FROM VW_SOLICITE_ITENS WHERE RTRIM(B1_COD) = ?',
                [$code]
            );

        return $row ? ItemProtheusData::forRead((array) $row) : null;
    }
}
