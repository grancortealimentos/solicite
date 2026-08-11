<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Services\Autentique\AutentiqueApiException;
use App\Services\Autentique\AutentiqueClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request, AutentiqueClient $autentique): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        try {
            $result = $autentique->forgotPassword($data['email']);

            Mail::to($data['email'])->send(
                new PasswordResetMail($result['reset_token'], $result['expires_in_minutes'])
            );
        } catch (AutentiqueApiException $e) {
            // Não expõe se o e-mail existe ou não: registra e segue com a
            // mesma mensagem genérica, evitando enumeração de usuários.
            Log::info('Solicitação de redefinição de senha não concluída.', [
                'email' => $data['email'],
                'status' => $e->status,
            ]);
        }

        return redirect()->route('login')->with(
            'status',
            'Se o e-mail informado estiver cadastrado, enviaremos instruções para redefinir a senha.'
        );
    }
}
