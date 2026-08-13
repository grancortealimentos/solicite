<?php

namespace App\Http\Controllers;

use App\Http\Requests\PessoaRequest;
use App\Services\Autentique\AutentiqueApiException;
use App\Services\Autentique\AutentiqueClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PessoaController extends Controller
{
    public function create(): View
    {
        return view('pessoas.create');
    }

    public function store(PessoaRequest $request, AutentiqueClient $autentique): RedirectResponse
    {
        try {
            $autentique->createPerson(session('autentique_token'), $request->validated());
        } catch (AutentiqueApiException $e) {
            throw ValidationException::withMessages(
                $e->isValidationError() ? $e->errors : ['name' => [$e->getMessage()]]
            );
        }

        return redirect()->route('pessoas.create')->with('status', 'Pessoa cadastrada com sucesso.');
    }
}
