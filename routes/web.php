<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PapelController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\SolicitacaoController;
use App\Http\Controllers\UsuarioController;
use App\Livewire\Solicitacao\Index as SolicitacaoIndex;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::get('password/forgot', [ForgotPasswordController::class, 'create'])->name('password.forgot');
    Route::post('password/forgot', [ForgotPasswordController::class, 'store'])->name('password.forgot.store');

    Route::get('password/reset/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'store'])->name('password.reset.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('password/change', [ChangePasswordController::class, 'create'])->name('password.change.create');
    Route::put('password/change', [ChangePasswordController::class, 'update'])->name('password.change.update');

    Route::middleware('password.fresh')->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');

        Route::prefix('papeis')->name('papeis.')->group(function () {
            Route::get('/', [PapelController::class, 'index'])->middleware('can:papeis.visualizar')->name('index');
            Route::get('/criar', [PapelController::class, 'create'])->middleware('can:papeis.criar')->name('create');
            Route::post('/', [PapelController::class, 'store'])->middleware('can:papeis.criar')->name('store');
            Route::get('/{role}/editar', [PapelController::class, 'edit'])->middleware('can:papeis.editar')->name('edit');
            Route::put('/{role}', [PapelController::class, 'update'])->middleware('can:papeis.editar')->name('update');
            Route::delete('/{role}', [PapelController::class, 'destroy'])->middleware('can:papeis.excluir')->name('destroy');
        });

        Route::prefix('pessoas')->name('pessoas.')->group(function () {
            Route::get('/criar', [PessoaController::class, 'create'])->middleware('can:pessoas.criar')->name('create');
            Route::post('/', [PessoaController::class, 'store'])->middleware('can:pessoas.criar')->name('store');
        });

        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->middleware('can:usuarios.visualizar')->name('index');
            Route::get('/{usuario}/permissoes', [UsuarioController::class, 'permissoes'])
                ->middleware('can:usuarios.gerenciar_permissoes')->name('permissoes');
            Route::put('/{usuario}/permissoes', [UsuarioController::class, 'atualizarPermissoes'])
                ->middleware('can:usuarios.gerenciar_permissoes')->name('permissoes.update');
        });

        Route::prefix('solicitacao')->name('solicitacao.')->group(function () {
            Route::get('/', SolicitacaoIndex::class)->middleware('can:solicitacao.visualizar')->name('index');

            Route::get('/criar', [SolicitacaoController::class, 'create'])
                ->middleware('can:solicitacao.criar')
                ->name('create');
        });
    });
});

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});
