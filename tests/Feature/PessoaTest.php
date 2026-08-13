<?php

use App\Models\User;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissaoSeeder::class);
});

function dadosPessoa(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ana Silva',
        'person_type' => 'PF',
        'document' => '12345678900',
        'email' => 'ana@example.com',
        'phone' => '11999999999',
        'zip' => '01310100',
        'street' => 'Av. Paulista',
        'number' => '1000',
        'district' => 'Bela Vista',
        'city' => 'São Paulo',
        'state' => 'SP',
        'complement' => 'Sala 1',
    ], $overrides);
}

test('usuário com permissão cadastra pessoa via api autentique', function () {
    Http::fake([
        '*/persons' => Http::response(['id' => 1, 'name' => 'Ana Silva'], 201),
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('pessoas.criar');

    $response = $this->actingAs($user)
        ->withSession(['autentique_token' => 'plain-text-token'])
        ->post(route('pessoas.store'), dadosPessoa());

    $response->assertRedirect(route('pessoas.create'));
    $response->assertSessionHas('status', 'Pessoa cadastrada com sucesso.');

    Http::assertSent(fn ($request) => $request->hasHeader('X-API-KEY')
        && $request->hasHeader('Authorization', 'Bearer plain-text-token')
        && $request->url() === 'http://autentique.test/api/v1/persons'
        && $request['document'] === '12345678900');
});

test('erro de validação da api é repassado para o formulário', function () {
    Http::fake([
        '*/persons' => Http::response([
            'message' => 'The given data was invalid.',
            'errors' => ['document' => ['CPF inválido.']],
        ], 422),
    ]);

    $user = User::factory()->create();
    $user->givePermissionTo('pessoas.criar');

    $response = $this->actingAs($user)
        ->withSession(['autentique_token' => 'plain-text-token'])
        ->post(route('pessoas.store'), dadosPessoa());

    $response->assertSessionHasErrors('document');
});

test('usuário sem permissão não acessa o cadastro de pessoa', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('pessoas.create'))->assertForbidden();
});
