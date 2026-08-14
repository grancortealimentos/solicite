<div>
    <input
        type="text"
        wire:model.live.debounce.400ms="search"
        placeholder="Buscar item por descrição..."
        class="w-full rounded-md border-gray-300"
    >

    <div wire:loading class="text-sm text-gray-500 mt-1">
        Buscando...
    </div>

    <ul class="mt-2 divide-y divide-gray-200">
        @forelse ($items as $item)
            <li
                wire:click="selectItem('{{ $item->code }}')"
                class="cursor-pointer py-2 px-2 hover:bg-gray-50"
            >
                <span class="font-medium">{{ $item->code }}</span>
                — {{ $item->description }}
                <span class="text-xs text-gray-500">({{ $item->unitMeasurement }})</span>
            </li>
        @empty
            <li class="py-2 px-2 text-gray-500">Nenhum item encontrado.</li>
        @endforelse
    </ul>

    <div class="mt-3 flex items-center justify-between">
        <button
            type="button"
            wire:click="previousPage"
            @disabled($page <= 1)
            class="text-sm disabled:opacity-50"
        >
            Anterior
        </button>

        <span class="text-sm text-gray-500">{{ $total }} itens encontrados</span>

        <button
            type="button"
            wire:click="nextPage"
            @disabled(!$hasNextPage)
            class="text-sm disabled:opacity-50"
        >
            Próxima
        </button>
    </div>
</div>