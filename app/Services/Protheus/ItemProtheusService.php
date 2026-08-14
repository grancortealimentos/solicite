<?php

declare(strict_types=1);

namespace App\Services\Protheus;

use App\DTOs\Protheus\ItemProtheusData;
use App\Repositories\Protheus\ItemProtheusRepository;

final class ItemProtheusService
{
    private const MAX_PER_PAGE = 100;

    public function __construct(
        private readonly ItemProtheusRepository $itemProtheusRepository
    ) {}

    public function search(
        ?string $description,
        int $page = 1,
        int $perPage = 50
    ): array {
        $safePage = max(1, $page);
        $safePerPage = min($perPage, self::MAX_PER_PAGE);

        return $this->itemProtheusRepository->search(
            $description,
            $safePage,
            $safePerPage
        );
    }

    public function findByCode(string $code): ?ItemProtheusData
    {
        return $this->itemProtheusRepository->findByCode($code);
    }
}
