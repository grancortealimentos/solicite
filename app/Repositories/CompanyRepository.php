<?php

namespace App\Repositories;

use App\DTOs\CompanyData;
use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyRepository
{
    public function paginar(int $porPagina = 15): LengthAwarePaginator
    {
        return Company::query()
            ->orderBy('name')
            ->paginate($porPagina);
    }

    public function criar(CompanyData $data): Company
    {
        return Company::create($data->toArray());
    }

    public function atualizar(Company $company, CompanyData $data): Company
    {
        $company->update($data->toArray());

        return $company->refresh();
    }

    public function excluir(Company $company): bool
    {
        return (bool) $company->delete();
    }
}
