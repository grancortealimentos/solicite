{{-- resources/views/components/layouts/sidebar.blade.php --}}
{{-- Backdrop escuro no mobile quando a sidebar abre --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-50 bg-black/60 lg:hidden">
</div>

{{-- Botão de menu (só mobile) --}}
<button type="button" @click="sidebarOpen = true"
    class="lg:hidden fixed top-3 start-3 z-40 size-9 flex justify-center items-center rounded-lg bg-surface border border-border text-ink shadow-2xs hover:bg-surface-hover">
    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" x2="21" y1="6" y2="6" />
        <line x1="3" x2="21" y1="12" y2="12" />
        <line x1="3" x2="21" y1="18" y2="18" />
    </svg>
</button>

<aside x-show="sidebarOpen || window.innerWidth >= 1024" x-cloak
    class="fixed inset-y-0 start-0 z-60 w-64 bg-sidebar border-e border-border lg:block lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" style="transition: transform .3s">

    <div class="flex flex-col h-full">
        {{-- Logo --}}
        <div class="px-6 pt-3 pb-1 flex items-center justify-center border-b border-border">
            <a href="{{ route('dashboard') }}">
                <x-logo class="h-24 w-auto" />
            </a>
        </div>

        {{-- Navegação --}}
        <nav class="flex-1 overflow-y-auto p-3">
            <ul class="flex flex-col space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" @class([
                        'flex items-center gap-x-3 py-2 px-2.5 text-sm rounded-lg',
                        'bg-primary/15 text-primary-light font-medium' => request()->routeIs('dashboard'),
                        'text-ink-muted hover:bg-surface hover:text-ink' => !request()->routeIs('dashboard'),
                    ])>
                        <i class="bi bi-house {{ request()->routeIs('dashboard') ? 'hidden' : '' }}"></i>
                        <i class="bi bi-house-fill {{ request()->routeIs('dashboard') ? '' : 'hidden' }}"></i>
                        Página inicial
                    </a>
                </li>

                @can('pessoas.criar')
                    <li>
                        <a href="{{ route('pessoas.create') }}" @class([
                            'flex items-center gap-x-3 py-2 px-2.5 text-sm rounded-lg',
                            'bg-primary/15 text-primary-light font-medium' => request()->routeIs('pessoas.*'),
                            'text-ink-muted hover:bg-surface hover:text-ink' => !request()->routeIs('pessoas.*'),
                        ])>
                            <i class="bi bi-person-plus {{ request()->routeIs('pessoas.*') ? 'hidden' : '' }}"></i>
                            <i class="bi bi-person-plus-fill {{ request()->routeIs('pessoas.*') ? '' : 'hidden' }}"></i>
                            Nova pessoa
                        </a>
                    </li>
                @endcan

                @can('solicitacao.visualizar')
                    <li>
                        <a href="{{ route('solicitacao.index') }}" @class([
                            'flex items-center gap-x-3 py-2 px-2.5 text-sm rounded-lg',
                            'bg-primary/15 text-primary-light font-medium' => request()->routeIs('solicitacao.*'),
                            'text-ink-muted hover:bg-surface hover:text-ink' => !request()->routeIs('solicitacao.*'),
                        ])>
                            <i class="bi bi-cart-plus {{ request()->routeIs('solicitacao.*') ? 'hidden' : '' }}"></i>
                            <i class="bi bi-cart-plus-fill {{ request()->routeIs('solicitacao.*') ? '' : 'hidden' }}"></i>
                            Solicitações de compras
                        </a>
                    </li>
                @endcan

                @canany(['papeis.visualizar', 'usuarios.visualizar'])
                    {{-- Item com submenu (accordion em Alpine) --}}
                    <li x-data="{ open: {{ request()->routeIs('papeis.*') || request()->routeIs('usuarios.*') ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-ink-muted rounded-lg hover:bg-surface hover:text-ink">

                            <i class="bi bi-gear" :class="open && 'hidden'"></i>
                            <i class="bi bi-gear-fill" :class="!open && 'hidden'"></i>

                            Configurações
                            <svg class="ms-auto size-4 transition-transform" :class="open && 'rotate-180'"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <ul x-show="open" x-collapse class="ps-8 pt-1 space-y-1">
                            @can('papeis.visualizar')
                                <li>
                                    <a href="{{ route('papeis.index') }}" @class([
                                        'flex py-2 px-2.5 text-sm rounded-lg',
                                        'bg-primary/15 text-primary-light font-medium' => request()->routeIs('papeis.*'),
                                        'text-ink-muted hover:bg-surface hover:text-ink' => !request()->routeIs('papeis.*'),
                                    ])>
                                        Papéis
                                    </a>
                                </li>
                            @endcan

                            @can('usuarios.visualizar')
                                <li>
                                    <a href="{{ route('usuarios.index') }}" @class([
                                        'flex py-2 px-2.5 text-sm rounded-lg',
                                        'bg-primary/15 text-primary-light font-medium' => request()->routeIs('usuarios.*'),
                                        'text-ink-muted hover:bg-surface hover:text-ink' => !request()->routeIs('usuarios.*'),
                                    ])>
                                        Usuários
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            </ul>
        </nav>

        {{-- Usuário (fixo na base da sidebar) --}}
        <div class="relative mt-auto border-t border-border p-3" x-data="{ open: false }">
            <button @click="open = !open" type="button"
                class="w-full flex items-center gap-x-3 py-2 px-2.5 rounded-lg hover:bg-surface">
                <span
                    class="size-9 shrink-0 inline-flex justify-center items-center rounded-full bg-primary text-sm font-medium text-white">
                    {{ Str::of(auth()->user()?->name)->substr(0, 1)->upper() }}
                </span>
                <span class="min-w-0 text-start">
                    <span class="block text-sm font-medium text-ink truncate">
                        {{ auth()->user()?->name }}
                    </span>
                    <span class="block text-xs text-ink-muted truncate">
                        {{ auth()->user()?->email }}
                    </span>
                </span>
                <svg class="ms-auto shrink-0 size-4 text-ink-muted transition-transform" :class="open && 'rotate-180'"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m18 15-6-6-6 6" />
                </svg>
            </button>

            <div x-show="open" x-cloak @click.outside="open = false"
                class="absolute start-3 end-3 bottom-full mb-2 bg-surface border border-border shadow-xl rounded-lg">

                <div class="p-1.5">
                    <a href="{{ route('password.change.create') }}"
                        class="w-full text-start flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-ink hover:bg-surface-hover">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        Alterar senha
                    </a>
                </div>

                <div class="border-t border-border p-1.5">
                    <form method="POST" action="{{ route('logout') }}" x-data="{ saindo: false }"
                        @submit="saindo = true">
                        @csrf
                        <button type="submit" :disabled="saindo"
                            class="w-full text-start flex items-center gap-x-3 py-2 px-3 rounded-lg text-sm text-danger hover:bg-danger/10 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{-- Ícone de sair (some quando está saindo) --}}
                            <svg x-show="!saindo" class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" x2="9" y1="12" y2="12" />
                            </svg>
                            {{-- Spinner enquanto sai --}}
                            <svg x-show="saindo" x-cloak class="shrink-0 size-4 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z"></path>
                            </svg>
                            <span x-text="saindo ? 'Saindo...' : 'Sair'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        {{-- Fim usuário --}}
    </div>
</aside>