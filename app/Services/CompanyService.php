<?php

namespace App\Services;

use App\DTOs\CompanyData;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService
{
    public function __construct(
        private readonly CompanyRepository $companyRepository,
    ) {}

    public function listar(int $porPagina = 15): LengthAwarePaginator
    {
        return $this->companyRepository->paginar($porPagina);
    }

    public function criar(array $dados): Company
    {
        return $this->companyRepository->criar(CompanyData::toCreate($dados));
    }

    public function atualizar(Company $company, array $dados): Company
    {
        return $this->companyRepository->atualizar($company, CompanyData::toUpdate($dados));
    }

    public function excluir(Company $company): bool
    {
        return $this->companyRepository->excluir($company);
    }
}
