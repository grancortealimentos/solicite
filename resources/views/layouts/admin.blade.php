<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <nav class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
            <div class="flex items-center gap-4 text-sm font-medium text-gray-600">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-900">{{ config('app.name') }}</a>
                @can('usuarios.visualizar')
                    <a href="{{ route('usuarios.index') }}" class="hover:text-gray-900">Usuários</a>
                @endcan
                @can('papeis.visualizar')
                    <a href="{{ route('papeis.index') }}" class="hover:text-gray-900">Papéis</a>
                @endcan
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-900">Sair</button>
            </form>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-4 py-8">
        <h1 class="mb-6 text-lg font-semibold">{{ $title ?? '' }}</h1>

        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
