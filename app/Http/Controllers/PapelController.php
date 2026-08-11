<?php

namespace App\Http\Controllers;

use App\Http\Requests\PapelRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class PapelController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('name')->paginate(15);

        return view('papeis.index', ['roles' => $roles]);
    }

    public function create(): View
    {
        return view('papeis.create', [
            'role' => new Role,
            'grupos' => config('permissoes.grupos'),
            'permissoesSelecionadas' => [],
        ]);
    }

    public function store(PapelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $role = DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
            $role->syncPermissions($data['permissoes'] ?? []);

            return $role;
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('papeis.index')->with('status', "Papel \"{$role->name}\" criado com sucesso.");
    }

    public function edit(Role $role): View
    {
        return view('papeis.edit', [
            'role' => $role,
            'grupos' => config('permissoes.grupos'),
            'permissoesSelecionadas' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(PapelRequest $request, Role $role): RedirectResponse
    {
        if ($role->isSystemRole()) {
            return back()->with('status', 'O papel Admin tem acesso irrestrito e não recebe permissões diretamente.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($role, $data) {
            $role->update(['name' => $data['name']]);
            $role->syncPermissions($data['permissoes'] ?? []);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('papeis.index')->with('status', "Papel \"{$role->name}\" atualizado com sucesso.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->isSystemRole()) {
            return back()->with('status', 'O papel Admin não pode ser excluído.');
        }

        if ($role->users()->exists()) {
            return back()->with('status', 'Não é possível excluir um papel que ainda possui usuários vinculados.');
        }

        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('papeis.index')->with('status', "Papel \"{$role->name}\" excluído com sucesso.");
    }
}
