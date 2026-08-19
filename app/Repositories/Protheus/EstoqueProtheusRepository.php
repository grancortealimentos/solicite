<?php

declare(strict_types=1);

namespace App\Repositories\Protheus;

use Illuminate\Support\Facades\DB;

final class EstoqueProtheusRepository
{
    /**
     * Armazém fixo consultado para a reserva de compras (B2_LOCAL = 50).
     */
    private const LOCAL = '50';

    /**
     * Soma o saldo em estoque (B2_QATU) do produto na filial informada.
     * D_E_L_E_T_ = '' é a marcação padrão do Protheus para registro não excluído.
     */
    public function saldo(string $filial, string $codigo): float
    {
        $row = DB::connection('protheus')->selectOne(
            "SELECT SUM(B2_QATU) AS saldo FROM SB2010
            WHERE RTRIM(B2_LOCAL) = ?
            AND D_E_L_E_T_ = ''
            AND RTRIM(B2_COD) = ?
            AND RTRIM(B2_FILIAL) = ?",
            [self::LOCAL, $codigo, $filial]
        );

        return (float) ($row->saldo ?? 0);
    }
}
