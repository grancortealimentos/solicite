{{-- resources/views/auth/forgot-password.blade.php --}}
<x-layouts.guest :title="'Esqueci minha senha · ' . config('app.name')">

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
            <h1 class="text-2xl font-bold text-ink">Esqueci minha senha</h1>
            <p class="text-sm text-ink-muted mt-1">Informe seu e-mail e enviaremos instruções para redefinir sua senha.</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('password.forgot.store') }}" class="space-y-5">
                @csrf

                {{-- E-mail --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-ink-muted mb-2">
                        E-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3.5">
                            <svg class="size-4 text-ink-muted" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            autocomplete="username" aria-describedby="email-error" placeholder="voce@empresa.com.br"
                            class="py-2.5 sm:py-3 ps-10 pe-4 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 disabled:opacity-50 disabled:pointer-events-none @error('email') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                    </div>
                    @error('email')
                        <p class="text-xs text-danger mt-2" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botão enviar --}}
                <button type="submit" x-data="{ enviando: false }" @click="enviando = true"
                    :class="enviando && 'opacity-70 pointer-events-none'"
                    class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40 transition-all">
                    <svg x-show="enviando" x-cloak class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z"></path>
                    </svg>
                    <span x-text="enviando ? 'Enviando...' : 'Enviar instruções'">Enviar instruções</span>
                </button>

                <p class="text-center text-sm">
                    <a href="{{ route('login') }}"
                        class="text-primary-light decoration-2 hover:underline focus:outline-hidden focus:underline font-medium">
                        Voltar ao login
                    </a>
                </p>
            </form>
        </div>
    </div>

    {{-- Rodapé --}}
    <p class="text-center text-xs text-ink-muted mt-6">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
    </p>

</x-layouts.guest>