<?php

use App\Models\Company;
use App\Models\User;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissaoSeeder::class);
});

function dadosFilial(array $overrides = []): array
{
    return array_merge([
        'is_active' => '1',
        'code' => 'FIL001',
        'name' => 'Filial Centro',
        'trade_name' => 'Centro',
        'cnpj' => '12345678000199',
        'ie' => '123456789',
        'zip' => '01310100',
        'street' => 'Av. Paulista',
        'number' => '1000',
        'district' => 'Bela Vista',
        'city' => 'São Paulo',
        'state' => 'SP',
        'complement' => 'Sala 1',
    ], $overrides);
}

test('usuário sem permissão não acessa a listagem de filiais', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('filiais.index'))->assertForbidden();
});

test('usuário com permissão lista filiais', function () {
    Company::factory()->count(2)->create();

    $user = User::factory()->create();
    $user->givePermissionTo('filiais.visualizar');

    $this->actingAs($user)->get(route('filiais.index'))->assertOk();
});

test('usuário com permissão cadastra filial', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('filiais.criar');

    $response = $this->actingAs($user)->post(route('filiais.store'), dadosFilial());

    $response->assertRedirect(route('filiais.index'));
    $this->assertDatabaseHas('companies', [
        'code' => 'FIL001',
        'name' => 'Filial Centro',
        'is_active' => true,
    ]);
});

test('não é possível cadastrar filial com código duplicado', function () {
    Company::factory()->create(['code' => 'FIL001']);

    $user = User::factory()->create();
    $user->givePermissionTo('filiais.criar');

    $response = $this->actingAs($user)->post(route('filiais.store'), dadosFilial());

    $response->assertSessionHasErrors('code');
});

test('usuário com permissão edita filial', function () {
    $company = Company::factory()->create(['name' => 'Filial Antiga']);

    $user = User::factory()->create();
    $user->givePermissionTo('filiais.editar');

    $response = $this->actingAs($user)->put(
        route('filiais.update', $company),
        dadosFilial(['code' => $company->code, 'name' => 'Filial Nova'])
    );

    $response->assertRedirect(route('filiais.index'));
    expect($company->refresh()->name)->toBe('Filial Nova');
});

test('usuário com permissão exclui filial', function () {
    $company = Company::factory()->create();

    $user = User::factory()->create();
    $user->givePermissionTo('filiais.excluir');

    $this->actingAs($user)->delete(route('filiais.destroy', $company))
        ->assertRedirect(route('filiais.index'));

    expect(Company::find($company->id))->toBeNull();
});
