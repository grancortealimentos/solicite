<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissaoSeeder::class);
});

test('usuário sem papel nem permissões não acessa telas administrativas', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('papeis.index'))->assertForbidden();
});

test('papel Admin tem bypass total via Gate::before', function () {
    $admin = User::factory()->create();
    $admin->assignSingleRole('Admin');

    $this->actingAs($admin)->get(route('papeis.index'))->assertOk();
    $this->actingAs($admin)->get(route('usuarios.index'))->assertOk();
});

test('permissão concedida via papel libera a rota correspondente', function () {
    $role = Role::create(['name' => 'Gestor de Papéis', 'guard_name' => 'web']);
    $role->syncPermissions(['papeis.visualizar']);

    $user = User::factory()->create();
    $user->assignSingleRole($role);

    $this->actingAs($user)->get(route('papeis.index'))->assertOk();
    $this->actingAs($user)->get(route('usuarios.index'))->assertForbidden();
});

test('permissão direta soma com as permissões do papel', function () {
    $role = Role::create(['name' => 'Gestor de Papéis', 'guard_name' => 'web']);
    $role->syncPermissions(['papeis.visualizar']);

    $user = User::factory()->create();
    $user->assignSingleRole($role);
    $user->givePermissionTo('usuarios.visualizar');

    $this->actingAs($user)->get(route('papeis.index'))->assertOk();
    $this->actingAs($user)->get(route('usuarios.index'))->assertOk();

    expect($user->getPermissionsViaRoles()->pluck('name')->all())->toBe(['papeis.visualizar']);
    expect($user->getDirectPermissions()->pluck('name')->all())->toBe(['usuarios.visualizar']);
});

test('atribuir um novo papel substitui o anterior', function () {
    $papelA = Role::create(['name' => 'Papel A', 'guard_name' => 'web']);
    $papelB = Role::create(['name' => 'Papel B', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignSingleRole($papelA);
    $user->assignSingleRole($papelB);

    expect($user->roles->pluck('name')->all())->toBe(['Papel B']);
});

test('não é possível excluir papel com usuários vinculados', function () {
    $admin = User::factory()->create();
    $admin->assignSingleRole('Admin');

    $role = Role::create(['name' => 'Gestor de Papéis', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignSingleRole($role);

    $this->actingAs($admin)
        ->delete(route('papeis.destroy', $role))
        ->assertRedirect();

    expect(Role::find($role->id))->not->toBeNull();
});

test('não é possível excluir o papel Admin', function () {
    $admin = User::factory()->create();
    $admin->assignSingleRole('Admin');

    $adminRole = Role::where('name', 'Admin')->firstOrFail();

    $this->actingAs($admin)
        ->delete(route('papeis.destroy', $adminRole))
        ->assertRedirect();

    expect(Role::find($adminRole->id))->not->toBeNull();
});

test('tela de permissões do usuário atribui papel e permissões diretas', function () {
    $admin = User::factory()->create();
    $admin->assignSingleRole('Admin');

    $role = Role::create(['name' => 'Gestor de Papéis', 'guard_name' => 'web']);
    $role->syncPermissions(['papeis.visualizar']);

    $user = User::factory()->create();

    $this->actingAs($admin)->put(route('usuarios.permissoes.update', $user), [
        'role' => $role->name,
        'permissoes' => ['papeis.visualizar', 'usuarios.visualizar'],
    ])->assertRedirect(route('usuarios.permissoes', $user));

    $user->refresh();

    expect($user->roles->pluck('name')->all())->toBe(['Gestor de Papéis']);
    expect($user->getDirectPermissions()->pluck('name')->all())->toBe(['usuarios.visualizar']);
});
