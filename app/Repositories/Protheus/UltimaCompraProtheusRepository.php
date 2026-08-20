<?php

declare(strict_types=1);

namespace App\Repositories\Protheus;

use App\DTOs\Protheus\UltimaCompraProtheusData;
use Illuminate\Support\Facades\DB;

final class UltimaCompraProtheusRepository
{
    public function porProduto(string $codigo): ?UltimaCompraProtheusData
    {
        $row = DB::connection('protheus')->selectOne(
            "SELECT TOP 1
                TRIM(F1_FORNECE) + ' - ' + TRIM(A2_NREDUZ) AS FORNECEDOR,
                CONVERT(DATE, F1_EMISSAO, 112) AS DATA_COMPRA,
                D1_VUNIT AS VALOR_UNITARIO
            FROM SF1010 F1
            INNER JOIN SD1010 D1 ON D1_DOC = F1_DOC AND D1_FILIAL = F1_FILIAL AND D1_SERIE = F1_SERIE AND D1.D_E_L_E_T_ = ''
            INNER JOIN SB1010 B1 ON B1_COD = D1_COD AND B1.D_E_L_E_T_ = ''
            INNER JOIN SA2010 A2 ON A2_COD = F1_FORNECE AND A2_LOJA = F1_LOJA
            WHERE F1.D_E_L_E_T_ = ''
            AND D1_COD = ?
            ORDER BY F1_EMISSAO DESC",
            [$codigo]
        );

        return $row ? UltimaCompraProtheusData::forRead((array) $row) : null;
    }
}
