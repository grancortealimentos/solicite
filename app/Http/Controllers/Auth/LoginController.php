<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Autentique\AutentiqueApiException;
use App\Services\Autentique\AutentiqueClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, AutentiqueClient $autentique): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $result = $autentique->login($credentials['email'], $credentials['password']);
        } catch (AutentiqueApiException $e) {
            throw ValidationException::withMessages([
                'email' => $e->isValidationError()
                    ? ($e->errors['email'][0] ?? $e->getMessage())
                    : 'Não foi possível autenticar no momento. Tente novamente em instantes.',
            ]);
        }

        $user = User::syncFromAutentique($result['user']);

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Este usuário está inativo.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->put('autentique_token', $result['token']);

        if ($user->force_password_change) {
            return redirect()->route('password.change.create')
                ->with('status', 'Por segurança, defina uma nova senha para continuar.');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request, AutentiqueClient $autentique): RedirectResponse
    {
        $token = $request->session()->get('autentique_token');

        if ($token) {
            try {
                $autentique->logout($token);
            } catch (AutentiqueApiException) {
                // Sessão local é encerrada de qualquer forma; o token na API
                // expira/torna-se órfão, mas isso não deve travar o logout local.
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
