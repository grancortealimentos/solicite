<x-layouts.app :title="'Nova filial'">

    <div class="max-w-2xl space-y-6">

        <h1 class="text-xl font-semibold text-ink">Nova filial</h1>

        <form method="POST" action="{{ route('filiais.store') }}" class="space-y-6">
            @csrf

            @include('filiais.partials.form', ['company' => $company])

            <button type="submit"
                class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                Criar filial
            </button>
        </form>

    </div>

</x-layouts.app>
