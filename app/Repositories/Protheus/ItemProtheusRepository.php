<?php

declare(strict_types=1);

namespace App\Repositories\Protheus;

use App\DTOs\Protheus\ItemProtheusData;
use Illuminate\Support\Facades\DB;

final class ItemProtheusRepository
{
    public function search(?string $description, int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;

        $whereClause = $description !== null && $description !== ''
            ? 'WHERE RTRIM(B1_DESC) LIKE ?'
            : '';

        $bindings = $description !== null && $description !== ''
            ? ['%'.$description.'%']
            : [];

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
