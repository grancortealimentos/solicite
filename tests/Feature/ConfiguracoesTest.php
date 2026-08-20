<?php

use App\Models\User;
use App\Models\UserSetting;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissaoSeeder::class);
});

test('usuário sem permissão não acessa configurações de outro usuário', function () {
    $user = User::factory()->create();
    $outro = User::factory()->create();

    $this->actingAs($user)->get(route('usuarios.configuracoes', $outro))->assertForbidden();
});

test('usuário com permissão acessa a tela de configurações', function () {
    $admin = User::factory()->create();
    $admin->assignSingleRole('Admin');

    $outro = User::factory()->create();

    $this->actingAs($admin)->get(route('usuarios.configuracoes', $outro))->assertOk();
});

test('salva as configurações do usuário quando ainda não existem', function () {
    $admin = User::factory()->create();
    $admin->assignSingleRole('Admin');

    $outro = User::factory()->create();

    $this->actingAs($admin)->put(route('usuarios.configuracoes.update', $outro), [
        'delivery_lead_days' => 5,
    ])->assertRedirect(route('usuarios.configuracoes', $outro));

    expect(UserSetting::where('user_id', $outro->id)->first()->delivery_lead_days)->toBe(5);
});

test('atualiza as configurações existentes do usuário', function () {
    $admin = User::factory()->create();
    $admin->assignSingleRole('Admin');

    $outro = User::factory()->create();
    UserSetting::create(['user_id' => $outro->id, 'delivery_lead_days' => 3]);

    $this->actingAs($admin)->put(route('usuarios.configuracoes.update', $outro), [
        'delivery_lead_days' => 10,
    ])->assertRedirect(route('usuarios.configuracoes', $outro));

    expect(UserSetting::where('user_id', $outro->id)->count())->toBe(1);
    expect($outro->fresh()->setting->delivery_lead_days)->toBe(10);
});

test('exige delivery_lead_days válido', function () {
    $admin = User::factory()->create();
    $admin->assignSingleRole('Admin');

    $outro = User::factory()->create();

    $this->actingAs($admin)->put(route('usuarios.configuracoes.update', $outro), [
        'delivery_lead_days' => -1,
    ])->assertSessionHasErrors('delivery_lead_days');
});
