<?php

declare(strict_types=1);

namespace App\Services\Protheus;

use App\DTOs\Protheus\FilialProtheusData;
use App\Repositories\Protheus\FilialProtheusRepository;

final class FilialProtheusService
{
    public function __construct(
        private readonly FilialProtheusRepository $filialProtheusRepository
    ) {}

    /**
     * @return array<int, FilialProtheusData>
     */
    public function search(?string $term): array
    {
        return $this->filialProtheusRepository->search($term);
    }

    public function findByCode(string $code): ?FilialProtheusData
    {
        return $this->filialProtheusRepository->findByCode($code);
    }
}
