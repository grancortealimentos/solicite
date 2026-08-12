@php
    $selecionadas = collect($permissoesSelecionadas ?? []);
@endphp

<div class="space-y-4">
    @foreach ($grupos as $grupo)
        <fieldset class="rounded-xl border border-border p-4">
            <legend class="px-1 text-sm font-semibold text-ink">{{ $grupo['label'] }}</legend>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($grupo['permissoes'] as $nome => $descricao)
                    <label class="flex items-start gap-2 text-sm text-ink-muted">
                        <input
                            type="checkbox"
                            name="permissoes[]"
                            value="{{ $nome }}"
                            class="mt-0.5 rounded border-border bg-canvas text-primary focus:ring-primary/20 checked:bg-primary checked:border-primary"
                            @checked($selecionadas->contains($nome))
                        >
                        <span>
                            <span class="block font-medium text-ink">{{ $nome }}</span>
                            <span class="block text-xs text-ink-muted">{{ $descricao }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endforeach
</div>