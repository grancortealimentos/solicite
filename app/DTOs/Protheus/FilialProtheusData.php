<?php

declare(strict_types=1);

namespace App\DTOs\Protheus;

final class FilialProtheusData
{
    private function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $document,
        public readonly string $city,
        public readonly string $address,
        public readonly string $district,
        public readonly string $state,
        public readonly string $email,
        public readonly string $phone,
    ) {}

    public static function forRead(array $line): self
    {
        return self::map($line);
    }

    private static function map(array $line): self
    {
        return new self(
            code: trim($line['Cod_filial'] ?? ''),
            name: trim($line['Nome_filial'] ?? ''),
            document: trim($line['Cpf_Cgc'] ?? ''),
            city: trim($line['Cidade'] ?? ''),
            address: trim($line['Endereco'] ?? ''),
            district: trim($line['Bairro'] ?? ''),
            state: trim($line['UF'] ?? ''),
            email: trim($line['Email'] ?? ''),
            phone: trim($line['fone'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'document' => $this->document,
            'city' => $this->city,
            'address' => $this->address,
            'district' => $this->district,
            'state' => $this->state,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
