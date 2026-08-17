<?php

namespace App\DTOs;

class CompanyData
{
    public function __construct(
        public readonly bool $isActive,
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $tradeName = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $ie = null,
        public readonly ?string $zip = null,
        public readonly ?string $street = null,
        public readonly ?string $number = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $complement = null,
        public readonly ?string $geolocation = null,
    ) {}

    public static function toCreate(array $data): self
    {
        return self::map($data);
    }

    public static function toUpdate(array $data): self
    {
        return self::map($data);
    }

    public static function map(array $data): self
    {
        return new self(
            isActive: $data['is_active'] ?? true,
            code: $data['code'],
            name: $data['name'],
            tradeName: $data['trade_name'] ?? null,
            cnpj: $data['cnpj'] ?? null,
            ie: $data['ie'] ?? null,
            zip: $data['zip'] ?? null,
            street: $data['street'] ?? null,
            number: $data['number'] ?? null,
            district: $data['district'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            complement: $data['complement'] ?? null,
            geolocation: $data['geolocation'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'is_active' => $this->isActive,
            'code' => $this->code,
            'name' => $this->name,
            'trade_name' => $this->tradeName,
            'cnpj' => $this->cnpj,
            'ie' => $this->ie,
            'zip' => $this->zip,
            'street' => $this->street,
            'number' => $this->number,
            'district' => $this->district,
            'city' => $this->city,
            'state' => $this->state,
            'complement' => $this->complement,
            'geolocation' => $this->geolocation,
        ];
    }
}
