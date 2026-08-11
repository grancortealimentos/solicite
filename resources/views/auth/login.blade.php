@extends('layouts.app', ['title' => 'Entrar'])

@section('content')
    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
            <input id="password" type="password" name="password" required
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember">
            Lembrar-me
        </label>

        <button type="submit" class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Entrar
        </button>

        <div class="text-center text-sm">
            <a href="{{ route('password.forgot') }}" class="text-gray-500 underline">Esqueci minha senha</a>
        </div>
    </form>
@endsection
