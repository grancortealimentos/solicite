<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SolicitacaoController extends Controller
{
    public function __construct(

    ) {}

    public function create(): View
    {
        return view('solicitacao.create');
    }
}
