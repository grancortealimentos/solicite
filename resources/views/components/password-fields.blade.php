{{-- resources/views/components/password-fields.blade.php --}}
@props([
    'label' => 'Nova senha',
    'confirmLabel' => 'Confirmar nova senha',
])

<div class="space-y-5" x-data="passwordValidator">
    {{-- Nova senha --}}
    <div x-data="{ show: false }">
        <label for="password" class="block text-sm font-medium text-ink-muted mb-2">{{ $label }}</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3.5">
                <svg class="size-4 text-ink-muted" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </div>
            <input :type="show ? 'text' : 'password'" id="password" name="password" required x-model="password"
                autocomplete="new-password"
                class="py-2.5 sm:py-3 ps-10 pe-10 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('password') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            <button type="button" @click="show = !show" tabindex="-1"
                :aria-label="show ? 'Ocultar senha' : 'Mostrar senha'"
                class="absolute inset-y-0 end-0 flex items-center z-20 px-3 cursor-pointer text-ink-muted hover:text-ink focus:outline-hidden focus:text-primary">
                <svg class="shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <g x-show="!show">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
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
            <p class="text-xs text-danger mt-2">{{ $message }}</p>
        @enderror

        {{-- Checklist em tempo real --}}
        <ul class="mt-3 space-y-1.5 text-xs">
            <template x-for="rule in rules" :key="rule.label">
                <li class="flex items-center gap-x-2 transition-colors"
                    :class="rule.valid ? 'text-success' : 'text-ink-muted'">
                    {{-- Ícone de check quando válido, círculo quando pendente --}}
                    <svg x-show="rule.valid" class="size-3.5 shrink-0" fill="none" stroke="currentColor"
                        stroke-width="3" viewBox="0 0 24 24">
                        <path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span x-show="!rule.valid"
                        class="size-3.5 shrink-0 inline-flex items-center justify-center">
                        <span class="size-1.5 rounded-full bg-border"></span>
                    </span>
                    <span x-text="rule.label"></span>
                </li>
            </template>
        </ul>
    </div>

    {{-- Confirmar --}}
    <div x-data="{ show: false }">
        <label for="password_confirmation"
            class="block text-sm font-medium text-ink-muted mb-2">{{ $confirmLabel }}</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3.5">
                <svg class="size-4 text-ink-muted" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </div>
            <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                x-model="confirmation" autocomplete="new-password"
                class="py-2.5 sm:py-3 ps-10 pe-10 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 transition-colors"
                :class="confirmation.length > 0 && !passwordsMatch ? 'border-danger focus:border-danger' : 'border-border focus:border-primary'">
            <button type="button" @click="show = !show" tabindex="-1"
                :aria-label="show ? 'Ocultar senha' : 'Mostrar senha'"
                class="absolute inset-y-0 end-0 flex items-center z-20 px-3 cursor-pointer text-ink-muted hover:text-ink focus:outline-hidden focus:text-primary">
                <svg class="shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <g x-show="!show">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
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

        <p x-show="confirmation.length > 0 && !passwordsMatch" x-cloak class="text-xs text-danger mt-2">
            As senhas não coincidem.
        </p>
    </div>

    {{-- Botão: travado até validar. O slot deixa cada tela definir o texto --}}
    <button type="submit" :disabled="!isValid"
        class="w-full py-3 px-4 inline-flex justify-center items-center text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40 transition-all disabled:opacity-50 disabled:pointer-events-none">
        {{ $slot }}
    </button>
</div>