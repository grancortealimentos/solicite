<?php

declare(strict_types=1);

namespace App\Repositories\Protheus;

use App\DTOs\Protheus\FilialProtheusData;
use Illuminate\Support\Facades\DB;

final class FilialProtheusRepository
{
    /**
     * @return array<int, FilialProtheusData>
     */
    public function search(?string $term): array
    {
        $hasTerm = $term !== null && $term !== '';

        $whereClause = '';
        $bindings = [];

        if ($hasTerm) {
            $termo = '%'.$term.'%';
            $whereClause = 'WHERE RTRIM(Cod_filial) LIKE ? OR RTRIM(Nome_filial) LIKE ? OR RTRIM(Cidade) LIKE ?';
            $bindings = [$termo, $termo, $termo];
        }

        $rows = DB::connection('protheus')->select(
            "SELECT * FROM VW_SOLICITE_FILIAL {$whereClause} ORDER BY Cod_filial",
            $bindings
        );

        return array_map(
            fn (object $row): FilialProtheusData => FilialProtheusData::forRead((array) $row),
            $rows
        );
    }

    public function findByCode(string $code): ?FilialProtheusData
    {
        $row = DB::connection('protheus')
            ->selectOne(
                'SELECT TOP 1 * FROM VW_SOLICITE_FILIAL WHERE RTRIM(Cod_filial) = ?',
                [$code]
            );

        return $row ? FilialProtheusData::forRead((array) $row) : null;
    }
}
