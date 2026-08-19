<?php

declare(strict_types=1);

namespace App\Services\Protheus;

use App\Repositories\Protheus\CentroCustoProtheusRepository;

final class CentroCustoProtheusService
{
    public function __construct(
        private readonly CentroCustoProtheusRepository $centroCustoProtheusRepository
    ) {}

    public function porFilial(string $filial): array
    {
        return $this->centroCustoProtheusRepository->porFilial($filial);
    }
}
