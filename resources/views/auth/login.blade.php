{{-- resources/views/auth/login.blade.php --}}
<x-layouts.guest :title="'Entrar · ' . config('app.name')">

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
            <h1 class="text-2xl font-bold text-ink">Bem-vindo de volta</h1>
            <p class="text-sm text-ink-muted mt-1">Entre com suas credenciais para continuar.</p>
        </div>

        <div class="p-8">

            {{-- Mensagens de session('status')/error são exibidas pelo
                 <x-toast-container> do layout guest, para manter a consistência. --}}

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
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

                {{-- Senha --}}
                <div x-data="{ show: false }">
                    <div class="flex flex-wrap justify-between items-center gap-2 mb-2">
                        <label for="password" class="block text-sm font-medium text-ink-muted">
                            Senha
                        </label>
                        <a href="{{ route('password.forgot') }}"
                            class="inline-flex items-center gap-x-1 text-sm text-primary-light decoration-2 hover:underline focus:outline-hidden focus:underline font-medium">
                            Esqueci minha senha
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3.5">
                            <svg class="size-4 text-ink-muted" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                            autocomplete="current-password" aria-describedby="password-error" placeholder="••••••••"
                            class="py-2.5 sm:py-3 ps-10 pe-10 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 disabled:opacity-50 disabled:pointer-events-none @error('password') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        <button type="button" @click="show = !show" tabindex="-1"
                            :aria-label="show ? 'Ocultar senha' : 'Mostrar senha'"
                            class="absolute inset-y-0 end-0 flex items-center z-20 px-3 cursor-pointer text-ink-muted hover:text-ink focus:outline-hidden focus:text-primary">
                            <svg class="shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <g x-show="!show">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                    <path
                                        d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                    <line x1="2" x2="22" y1="2" y2="22" />
                                </g>
                                <g x-show="show" x-cloak>
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </g>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-danger mt-2" id="password-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lembrar-me --}}
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))
                        class="shrink-0 size-4 bg-canvas border-border rounded text-primary focus:ring-primary/20 checked:bg-primary checked:border-primary">
                    <label for="remember" class="ms-2.5 text-sm text-ink-muted select-none cursor-pointer">
                        Manter-me conectado
                    </label>
                </div>

                {{-- Botão entrar --}}
                <button type="submit" x-data="{ enviando: false }" @click="enviando = true"
                    :class="enviando && 'opacity-70 pointer-events-none'"
                    class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40 transition-all">
                    <svg x-show="enviando" x-cloak class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z"></path>
                    </svg>
                    <span x-text="enviando ? 'Entrando...' : 'Entrar'">Entrar</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Rodapé --}}
    <p class="text-center text-xs text-ink-muted mt-6">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
    </p>

</x-layouts.guest>