@extends('layouts.app', ['title' => 'Esqueci minha senha'])

@section('content')
    <p class="mb-4 text-sm text-gray-600">
        Informe seu e-mail e enviaremos instruções para redefinir sua senha.
    </p>

    <form method="POST" action="{{ route('password.forgot.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Enviar instruções
        </button>

        <div class="text-center text-sm">
            <a href="{{ route('login') }}" class="text-gray-500 underline">Voltar ao login</a>
        </div>
    </form>
@endsection
