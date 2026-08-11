<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">
            <h1 class="mb-6 text-center text-xl font-semibold">{{ config('app.name') }}</h1>

            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
