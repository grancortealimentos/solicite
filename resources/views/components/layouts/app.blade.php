<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-canvas" x-data="{ sidebarOpen: false }">

        <x-layouts.sidebar />

        {{-- Toasts: fixos, fora do fluxo do documento --}}
        <x-toast-container />

        {{-- Toasts disparados via $this->dispatch('toast', ...) nos componentes Livewire
             (ações que não recarregam a página, então session() não serve) --}}
        <div x-data="{
                toasts: [],
                add(tipo, mensagem) {
                    const id = Date.now() + Math.random();
                    this.toasts.push({ id, tipo, mensagem });
                    setTimeout(() => this.remove(id), 5000);
                },
                remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
             }" @toast.window="add($event.detail.tipo, $event.detail.mensagem)"
            class="fixed top-5 end-5 z-[100] flex flex-col gap-y-2 pointer-events-none">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    class="pointer-events-auto max-w-sm w-full border shadow-lg text-sm rounded-xl bg-surface text-ink"
                    :class="toast.tipo === 'error' ? 'border-danger/30' : 'border-success/30'" role="alert">
                    <div class="flex items-start gap-3 p-4">
                        <svg class="shrink-0 size-5 mt-0.5" :class="toast.tipo === 'error' ? 'text-danger' : 'text-success'"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <span class="flex-1" x-text="toast.mensagem"></span>
                        <button type="button" @click="remove(toast.id)"
                            class="shrink-0 size-5 text-ink-muted hover:text-ink">
                            <span class="sr-only">Fechar</span>
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Conteúdo: empurrado pra direita no desktop pra dar espaço à sidebar --}}
        <div class="w-full lg:ps-64">
            <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>