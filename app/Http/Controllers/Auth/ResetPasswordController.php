<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Autentique\AutentiqueApiException;
use App\Services\Autentique\AutentiqueClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function create(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function store(Request $request, AutentiqueClient $autentique): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $autentique->resetPassword($data['token'], $data['password']);
        } catch (AutentiqueApiException $e) {
            throw ValidationException::withMessages([
                'password' => $e->isValidationError()
                    ? ($e->errors['password'][0] ?? $e->errors['token'][0] ?? $e->getMessage())
                    : 'Link de redefinição inválido ou expirado. Solicite um novo.',
            ]);
        }

        return redirect()->route('login')
            ->with('status', 'Senha redefinida com sucesso. Faça login com a nova senha.');
    }
}
