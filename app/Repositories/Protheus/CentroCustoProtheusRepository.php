<?php

declare(strict_types=1);

namespace App\Repositories\Protheus;

use App\DTOs\Protheus\CentroCustoProtheusData;
use Illuminate\Support\Facades\DB;

final class CentroCustoProtheusRepository
{
    public function porFilial(string $filial): array
    {
        $rows = DB::connection('protheus')->select(
            'SELECT * FROM VW_SOLICITE_CENTROCUSTOS WHERE RTRIM(CTT_FILIAL) = ? ORDER BY CTT_DESC01',
            [$filial]
        );

        return array_map(
            fn (object $row): CentroCustoProtheusData => CentroCustoProtheusData::forRead((array) $row),
            $rows
        );
    }
}
