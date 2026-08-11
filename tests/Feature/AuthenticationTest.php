<?php

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function fakeAutentiqueUser(array $overrides = []): array
{
    return array_merge([
        'id' => 42,
        'person_id' => 7,
        'name' => 'Ana Silva',
        'email' => 'ana@example.com',
        'is_active' => true,
        'force_password_change' => false,
    ], $overrides);
}

test('login autentica via api, espelha o usuário e cria sessão', function () {
    Http::fake([
        '*/login' => Http::response([
            'token' => 'plain-text-token',
            'token_type' => 'Bearer',
            'user' => fakeAutentiqueUser(),
            'force_password_change' => false,
        ], 200),
    ]);

    $response = $this->post('/login', [
        'email' => 'ana@example.com',
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $user = User::where('autentique_user_id', 42)->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Ana Silva');
    expect($user->email)->toBe('ana@example.com');

    Http::assertSent(fn ($request) => $request->hasHeader('X-API-KEY', 'testing-api-key')
        && $request->url() === 'http://autentique.test/api/v1/login');
});

test('login com credenciais inválidas não autentica', function () {
    Http::fake([
        '*/login' => Http::response([
            'message' => 'The given data was invalid.',
            'errors' => ['email' => ['Credenciais inválidas.']],
        ], 422),
    ]);

    $response = $this->post('/login', [
        'email' => 'ana@example.com',
        'password' => 'wrong',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login com force_password_change redireciona para troca de senha', function () {
    Http::fake([
        '*/login' => Http::response([
            'token' => 'plain-text-token',
            'token_type' => 'Bearer',
            'user' => fakeAutentiqueUser(['force_password_change' => true]),
            'force_password_change' => true,
        ], 200),
    ]);

    $response = $this->post('/login', [
        'email' => 'ana@example.com',
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('password.change.create'));
});

test('usuário autenticado com senha expirada é bloqueado em outras rotas', function () {
    $user = User::factory()->mustChangePassword()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('password.change.create'));
});

test('logout chama a api e encerra a sessão local', function () {
    Http::fake(['*/logout' => Http::response([], 200)]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['autentique_token' => 'plain-text-token'])
        ->post('/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('esqueci minha senha envia e-mail sem revelar se o usuário existe', function () {
    Mail::fake();

    Http::fake([
        '*/password/forgot' => Http::response([
            'reset_token' => 'abc123',
            'expires_in_minutes' => 60,
        ], 200),
    ]);

    $this->post('/password/forgot', ['email' => 'ana@example.com'])
        ->assertRedirect(route('login'));

    Mail::assertSent(PasswordResetMail::class);
});
