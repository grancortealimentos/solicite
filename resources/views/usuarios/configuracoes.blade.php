<x-layouts.app :title="'Configurações de ' . $usuario->name">

    <div class="max-w-2xl space-y-6">

        <h1 class="text-xl font-semibold text-ink">Configurações de {{ $usuario->name }}</h1>

        <form method="POST" action="{{ route('usuarios.configuracoes.update', $usuario) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-surface border border-border rounded-xl p-6">
                <label for="delivery_lead_days" class="block text-sm font-medium text-ink-muted mb-2">
                    Prazo de entrega (dias)
                </label>
                <input
                    type="number"
                    id="delivery_lead_days"
                    name="delivery_lead_days"
                    min="0"
                    value="{{ old('delivery_lead_days', $setting?->delivery_lead_days) }}"
                    class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink focus:ring-primary/20 focus:border-primary"
                >
                @error('delivery_lead_days')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                Salvar
            </button>
        </form>

    </div>

</x-layouts.app>
