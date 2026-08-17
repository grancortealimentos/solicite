<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $companyService,
    ) {}

    public function index(): View
    {
        return view('filiais.index', ['companies' => $this->companyService->listar()]);
    }

    public function create(): View
    {
        return view('filiais.create', ['company' => new Company]);
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $company = $this->companyService->criar($request->validated());

        return redirect()->route('filiais.index')->with('status', "Filial \"{$company->name}\" criada com sucesso.");
    }

    public function edit(Company $company): View
    {
        return view('filiais.edit', ['company' => $company]);
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $company = $this->companyService->atualizar($company, $request->validated());

        return redirect()->route('filiais.index')->with('status', "Filial \"{$company->name}\" atualizada com sucesso.");
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->companyService->excluir($company);

        return redirect()->route('filiais.index')->with('status', "Filial \"{$company->name}\" excluída com sucesso.");
    }
}
