{{-- resources/views/components/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-canvas">
    {{-- Fundo com o gradiente da identidade, bem sutil --}}
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -end-40 size-96 rounded-full blur-3xl opacity-20"
            style="background: radial-gradient(circle, #5CBEFF 0%, transparent 70%);"></div>
        <div class="absolute -bottom-40 -start-40 size-96 rounded-full blur-3xl opacity-10"
            style="background: radial-gradient(circle, #7C3AED 0%, transparent 70%);"></div>
    </div>

    <main class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>

    <x-toast-container />
</body>

</html>