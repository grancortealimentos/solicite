<x-layouts.app :title="'Editar papel'">

    <div class="max-w-2xl space-y-6">

        <h1 class="text-xl font-semibold text-ink">Editar papel</h1>

        <form method="POST" action="{{ route('papeis.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-surface border border-border rounded-xl p-6">
                <label for="name" class="block text-sm font-medium text-ink-muted mb-2">Nome</label>
                <input id="name" type="text" name="name" value="{{ old('name', $role->name) }}" required autofocus
                    @disabled($role->isSystemRole())
                    class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 disabled:opacity-50 disabled:pointer-events-none @error('name') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            @if ($role->isSystemRole())
                <div class="flex items-start gap-3 rounded-xl border border-caution/30 bg-surface p-4 text-sm text-caution">
                    <svg class="shrink-0 size-5 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>O papel Admin tem acesso irrestrito a todo o sistema e não recebe permissões individuais.</span>
                </div>
            @else
                @include('papeis.partials.grid-permissoes')
            @endif

            @unless ($role->isSystemRole())
                <button type="submit"
                    class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                    Salvar
                </button>
            @endunless
        </form>

    </div>

</x-layouts.app>