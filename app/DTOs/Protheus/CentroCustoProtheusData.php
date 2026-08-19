<?php

declare(strict_types=1);

namespace App\DTOs\Protheus;

final class CentroCustoProtheusData
{
    private function __construct(
        public readonly string $filial,
        public readonly string $filialNome,
        public readonly string $codigo,
        public readonly string $descricao,
        public readonly string $conta,
    ) {}

    public static function forRead(array $line): self
    {
        return self::map($line);
    }

    private static function map(array $line): self
    {
        return new self(
            filial: trim($line['CTT_FILIAL'] ?? ''),
            filialNome: trim($line['NOME_FILIAL'] ?? ''),
            codigo: trim($line['CTT_CUSTO'] ?? ''),
            descricao: trim($line['CTT_DESC01'] ?? ''),
            conta: trim($line['CTT_X_CTA'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'filial' => $this->filial,
            'filialNome' => $this->filialNome,
            'codigo' => $this->codigo,
            'descricao' => $this->descricao,
            'conta' => $this->conta,
        ];
    }
}
