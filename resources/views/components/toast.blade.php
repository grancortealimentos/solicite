{{-- resources/views/components/toast.blade.php --}}
@props([
    'type' => 'success', // success | error | warning | info
    'duracao' => 5000,   // ms; use 0 para não fechar sozinho
])

@php
    $config = [
        'success' => [
            'classes' => 'bg-surface border-success/30 text-ink',
            'icone'   => 'text-success',
            'path'    => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'error' => [
            'classes' => 'bg-surface border-danger/30 text-ink',
            'icone'   => 'text-danger',
            'path'    => 'M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
        ],
        'warning' => [
            'classes' => 'bg-surface border-caution/30 text-ink',
            'icone'   => 'text-caution',
            'path'    => 'M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
        ],
        'info' => [
            'classes' => 'bg-surface border-primary/30 text-ink',
            'icone'   => 'text-primary-light',
            'path'    => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ][$type];
@endphp

<div x-data="{ show: false }"
     x-init="
        $nextTick(() => show = true);
        @if ($duracao > 0) setTimeout(() => show = false, {{ $duracao }}); @endif
     "
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-x-8"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 translate-x-8"
     x-cloak
     class="pointer-events-auto max-w-sm w-full border shadow-lg text-sm rounded-xl {{ $config['classes'] }}"
     role="alert"
     aria-live="polite">
    <div class="flex items-start gap-3 p-4">
        <svg class="shrink-0 size-5 mt-0.5 {{ $config['icone'] }}" fill="none" stroke="currentColor"
             stroke-width="2" viewBox="0 0 24 24">
            <path d="{{ $config['path'] }}" stroke-linecap="round" stroke-linejoin="round" />
        </svg>

        <div class="flex-1 min-w-0">
            {{ $slot }}
        </div>

        <button type="button" @click="show = false"
                class="shrink-0 flex justify-center items-center size-5 text-ink-muted hover:text-ink focus:outline-hidden">
            <span class="sr-only">Fechar</span>
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
            </svg>
        </button>
    </div>
</div>