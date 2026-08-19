<?php

declare(strict_types=1);

namespace App\Services\Protheus;

use App\Repositories\Protheus\EstoqueProtheusRepository;

final class EstoqueProtheusService
{
    public function __construct(
        private readonly EstoqueProtheusRepository $estoqueProtheusRepository
    ) {}

    public function saldo(string $filial, string $codigo): float
    {
        return $this->estoqueProtheusRepository->saldo($filial, $codigo);
    }
}
