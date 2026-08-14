<?php

declare(strict_types=1);

namespace App\DTOs\Protheus;

final class ItemProtheusData
{
    private function __construct(
        public readonly string $code,
        public readonly string $description,
        public readonly string $type,
        public readonly string $location,
        public readonly string $unitMeasurement,
        public readonly string $account,
        public readonly string $group,
        public readonly string $groupDescription,
    ) {}

    public static function forRead(array $line): self
    {
        return self::map($line);
    }

    private static function map(array $line): self
    {
        return new self(
            code: trim($line['B1_COD'] ?? ''),
            description: trim($line['B1_DESC'] ?? ''),
            type: trim($line['B1_TIPO'] ?? ''),
            location: trim($line['B1_LOCPAD'] ?? ''),
            unitMeasurement: trim($line['B1_UM'] ?? ''),
            account: trim($line['B1_CONTA'] ?? ''),
            group: trim($line['BM_GRUPO'] ?? ''),
            groupDescription: trim($line['BM_DESC'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type,
            'location' => $this->location,
            'unitMeasurement' => $this->unitMeasurement,
            'account' => $this->account,
            'group' => $this->group,
            'groupDescription' => $this->groupDescription,
        ];
    }
}
