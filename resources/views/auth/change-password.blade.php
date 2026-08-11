@extends('layouts.app', ['title' => 'Alterar senha'])

@section('content')
    @if (auth()->user()->force_password_change)
        <p class="mb-4 text-sm text-amber-700">
            Por segurança, defina uma nova senha para continuar usando o sistema.
        </p>
    @endif

    <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">Senha atual</label>
            <input id="current_password" type="password" name="current_password" required autofocus
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none">
            @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Nova senha</label>
            <input id="password" type="password" name="password" required minlength="8"
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirme a nova senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none">
        </div>

        <button type="submit" class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Salvar nova senha
        </button>
    </form>
@endsection
