<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('seeder não cria usuário de teste em produção', function () {
    app()->detectEnvironment(fn () => 'production');

    app(DatabaseSeeder::class)->run();

    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
});

test('seeder cria usuário de teste fora de produção', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});
