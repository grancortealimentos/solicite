<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfPasswordChangeRequired
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->force_password_change && ! $request->routeIs('password.change.*', 'logout')) {
            return redirect()->route('password.change.create')
                ->with('status', 'Por segurança, defina uma nova senha para continuar.');
        }

        return $next($request);
    }
}
