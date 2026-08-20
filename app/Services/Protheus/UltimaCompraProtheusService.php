<?php

declare(strict_types=1);

namespace App\Services\Protheus;

use App\DTOs\Protheus\UltimaCompraProtheusData;
use App\Repositories\Protheus\UltimaCompraProtheusRepository;

final class UltimaCompraProtheusService
{
    public function __construct(
        private readonly UltimaCompraProtheusRepository $ultimaCompraProtheusRepository
    ) {}

    public function porProduto(string $codigo): ?UltimaCompraProtheusData
    {
        return $this->ultimaCompraProtheusRepository->porProduto($codigo);
    }
}
