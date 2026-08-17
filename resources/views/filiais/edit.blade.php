<x-layouts.app :title="'Editar filial'">

    <div class="max-w-2xl space-y-6">

        <h1 class="text-xl font-semibold text-ink">Editar filial</h1>

        <form method="POST" action="{{ route('filiais.update', $company) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('filiais.partials.form', ['company' => $company])

            <button type="submit"
                class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                Salvar
            </button>
        </form>

    </div>

</x-layouts.app>
