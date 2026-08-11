<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Autentique\AutentiqueApiException;
use App\Services\Autentique\AutentiqueClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request, AutentiqueClient $autentique): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $token = $request->session()->get('autentique_token');

        if (! $token) {
            return redirect()->route('login')
                ->with('status', 'Sua sessão expirou. Faça login novamente para trocar a senha.');
        }

        try {
            $autentique->changePassword($token, $data['current_password'], $data['password']);
        } catch (AutentiqueApiException $e) {
            throw ValidationException::withMessages([
                'current_password' => $e->isValidationError()
                    ? ($e->errors['current_password'][0] ?? $e->getMessage())
                    : 'Não foi possível alterar a senha no momento.',
            ]);
        }

        $request->user()->update(['force_password_change' => false]);

        return redirect()->route('dashboard')->with('status', 'Senha alterada com sucesso.');
    }
}
