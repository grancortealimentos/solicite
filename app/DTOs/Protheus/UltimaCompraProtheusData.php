<?php

declare(strict_types=1);

namespace App\DTOs\Protheus;

final class UltimaCompraProtheusData
{
    private function __construct(
        public readonly string $fornecedor,
        public readonly ?string $dataCompra,
        public readonly float $valorUnitario,
    ) {}

    public static function forRead(array $line): self
    {
        return self::map($line);
    }

    private static function map(array $line): self
    {
        $data = $line['DATA_COMPRA'] ?? null;

        return new self(
            fornecedor: trim($line['FORNECEDOR'] ?? ''),
            dataCompra: $data instanceof \DateTimeInterface ? $data->format('Y-m-d') : ($data ? (string) $data : null),
            valorUnitario: (float) ($line['VALOR_UNITARIO'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'fornecedor' => $this->fornecedor,
            'dataCompra' => $this->dataCompra,
            'valorUnitario' => $this->valorUnitario,
        ];
    }
}
