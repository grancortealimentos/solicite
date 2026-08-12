<x-layouts.app :title="'Novo papel'">

    <div class="max-w-2xl space-y-6">

        <h1 class="text-xl font-semibold text-ink">Novo papel</h1>

        <form method="POST" action="{{ route('papeis.store') }}" class="space-y-6">
            @csrf

            <div class="bg-surface border border-border rounded-xl p-6">
                <label for="name" class="block text-sm font-medium text-ink-muted mb-2">Nome</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('name') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            @include('papeis.partials.grid-permissoes')

            <button type="submit"
                class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                Criar papel
            </button>
        </form>

    </div>

</x-layouts.app>