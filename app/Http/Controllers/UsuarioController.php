<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = User::with('roles')->orderBy('name')->paginate(15);

        return view('usuarios.index', ['usuarios' => $usuarios]);
    }

    public function permissoes(User $usuario): View
    {
        $viaPapel = $usuario->getPermissionsViaRoles()->pluck('name')->all();

        return view('usuarios.permissoes', [
            'usuario' => $usuario,
            'roles' => Role::orderBy('name')->get(),
            'grupos' => config('permissoes.grupos'),
            'papelAtual' => $usuario->roles->first()?->name,
            'viaPapel' => $viaPapel,
            'diretas' => $usuario->getDirectPermissions()->pluck('name')->all(),
        ]);
    }

    public function atualizarPermissoes(Request $request, User $usuario): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'permissoes' => ['array'],
            'permissoes.*' => ['string', 'exists:permissions,name'],
        ]);

        DB::transaction(function () use ($usuario, $data) {
            if ($data['role'] ?? null) {
                $usuario->assignSingleRole($data['role']);
            } else {
                $usuario->syncRoles([]);
            }

            $viaPapel = $usuario->getPermissionsViaRoles()->pluck('name')->all();
            $desejadas = collect($data['permissoes'] ?? [])->diff($viaPapel)->unique()->values()->all();

            $usuario->syncPermissions($desejadas);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('usuarios.permissoes', $usuario)
            ->with('status', 'Permissões atualizadas com sucesso.');
    }
}
