<x-layouts.app :title="'Permissões de ' . $usuario->name">

    <div class="max-w-2xl space-y-6">

        <h1 class="text-xl font-semibold text-ink">Permissões de {{ $usuario->name }}</h1>

        <form method="POST" action="{{ route('usuarios.permissoes.update', $usuario) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-surface border border-border rounded-xl p-6">
                <label for="role" class="block text-sm font-medium text-ink-muted mb-2">Papel</label>
                <select id="role" name="role"
                    class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink focus:ring-primary/20 focus:border-primary">
                    <option value="">— nenhum —</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @selected($papelAtual === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-ink-muted">O usuário tem no máximo um papel; ao trocar, o anterior é removido.</p>
            </div>

            <div class="space-y-4">
                @foreach ($grupos as $grupo)
                    <fieldset class="rounded-xl border border-border p-4">
                        <legend class="px-1 text-sm font-semibold text-ink">{{ $grupo['label'] }}</legend>
                        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @foreach ($grupo['permissoes'] as $nome => $descricao)
                                @php $viaRole = in_array($nome, $viaPapel, true); @endphp
                                <label class="flex items-start gap-2 text-sm text-ink-muted">
                                    <input
                                        type="checkbox"
                                        name="permissoes[]"
                                        value="{{ $nome }}"
                                        class="mt-0.5 rounded border-border bg-canvas text-primary focus:ring-primary/20 checked:bg-primary checked:border-primary disabled:opacity-50"
                                        @checked($viaRole || in_array($nome, $diretas, true))
                                        @disabled($viaRole)
                                    >
                                    <span>
                                        <span class="block font-medium text-ink">
                                            {{ $nome }}
                                            @if ($viaRole)
                                                <span class="text-xs font-normal text-ink-muted">(via papel)</span>
                                            @endif
                                        </span>
                                        <span class="block text-xs text-ink-muted">{{ $descricao }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </div>

            <button type="submit"
                class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                Salvar
            </button>
        </form>

    </div>

</x-layouts.app>