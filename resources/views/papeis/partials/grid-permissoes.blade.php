@php
    $selecionadas = collect($permissoesSelecionadas ?? []);
@endphp

<div class="space-y-4">
    @foreach ($grupos as $grupo)
        <fieldset class="rounded-md border border-gray-200 p-4">
            <legend class="px-1 text-sm font-semibold text-gray-700">{{ $grupo['label'] }}</legend>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($grupo['permissoes'] as $nome => $descricao)
                    <label class="flex items-start gap-2 text-sm text-gray-700">
                        <input
                            type="checkbox"
                            name="permissoes[]"
                            value="{{ $nome }}"
                            class="mt-0.5"
                            @checked($selecionadas->contains($nome))
                        >
                        <span>
                            <span class="block font-medium">{{ $nome }}</span>
                            <span class="block text-xs text-gray-500">{{ $descricao }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endforeach
</div>
