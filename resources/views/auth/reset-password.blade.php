{{-- resources/views/auth/reset-password.blade.php --}}
<x-layouts.guest :title="'Redefinir senha · ' . config('app.name')">

    {{-- Logo --}}
    <div class="flex justify-center mb-6">
        <a href="{{ route('login') }}">
            <x-logo class="h-20 w-auto" />
        </a>
    </div>

    {{-- Card --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xl overflow-hidden">

        {{-- Cabeçalho do card --}}
        <div class="px-8 pt-8 pb-6 text-center border-b border-border">
            <h1 class="text-2xl font-bold text-ink">Redefinir senha</h1>
            <p class="text-sm text-ink-muted mt-1">Escolha uma nova senha para sua conta.</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('password.reset.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <x-password-fields>
                    Redefinir senha
                </x-password-fields>

                @error('password')
                    <p class="text-xs text-danger mt-3">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </div>

    {{-- Rodapé --}}
    <p class="text-center text-xs text-ink-muted mt-6">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
    </p>

</x-layouts.guest>