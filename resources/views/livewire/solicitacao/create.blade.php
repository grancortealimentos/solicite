{{-- resources/views/livewire/solicitacao/create.blade.php --}}
<div class="space-y-6">
    <h1 class="text-xl font-semibold text-ink">Nova solicitação</h1>

    <div class="bg-surface border border-border rounded-xl p-6 space-y-4">
        <h2 class="text-sm font-semibold text-ink">Dados da solicitação</h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-ink-muted mb-2">ID da solicitação</label>
                <input type="text" value="Gerado ao salvar" disabled readonly
                    class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink-muted cursor-not-allowed">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-muted mb-2">Solicitante</label>
                <input type="text" value="{{ auth()->user()->name }}" disabled readonly
                    class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink-muted cursor-not-allowed">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-muted mb-2">Data de emissão</label>
                <input type="text" value="{{ now()->format('d/m/Y') }}" disabled readonly
                    class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink-muted cursor-not-allowed">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-ink-muted mb-2">Filial</label>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <button type="button" wire:click="abrirBuscaFilial"
                    class="group flex min-w-0 flex-1 items-center gap-4 rounded-xl border-2 border-dashed border-border bg-canvas p-4 text-left transition-colors hover:border-primary hover:bg-primary/5">
                    <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-primary/10 text-primary">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        @if ($filial)
                            <p class="truncate text-base font-bold text-ink">{{ $filial['name'] }}</p>
                            <p class="mt-0.5 truncate font-mono text-xs text-ink-muted">
                                {{ $filial['code'] }} — {{ $filial['city'] }}/{{ $filial['state'] }}
                            </p>
                        @else
                            <p class="text-sm font-semibold text-ink">Clique para escolher a filial</p>
                            <p class="mt-0.5 text-xs text-ink-muted">A filial é obrigatória para adicionar itens</p>
                        @endif
                    </div>
                </button>

                <button type="button" wire:click="abrirBuscaFilial"
                    class="py-2.5 px-4 shrink-0 inline-flex items-center gap-2 text-sm font-medium rounded-lg bg-accent text-white hover:bg-accent-hover focus:outline-hidden focus:ring-2 focus:ring-accent/40">
                    <i class="bi bi-search"></i>
                    Buscar filial
                </button>
            </div>

            @unless ($filial)
                <p class="mt-2 flex items-center gap-1.5 text-xs text-caution">
                    <span class="inline-block size-1.5 rounded-full bg-caution"></span>
                    Nenhuma filial selecionada. Selecione uma filial antes de adicionar itens.
                </p>
            @endunless
        </div>
    </div>

    <livewire:solicitacao-itens :filial="$filial" wire:key="itens-{{ $filial['code'] ?? 'sem-filial' }}" />

    <div class="flex flex-wrap gap-2">
        <button type="button" wire:click="salvar" wire:loading.attr="disabled" wire:target="salvar"
            class="py-2.5 px-5 inline-flex items-center gap-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40 disabled:opacity-70 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="salvar">Salvar</span>
            <span wire:loading wire:target="salvar">Salvando...</span>
        </button>
        <a href="{{ route('solicitacao.index') }}" wire:navigate
            class="py-2.5 px-5 inline-flex items-center text-sm font-medium rounded-lg border border-border text-ink hover:bg-surface-hover">
            Cancelar
        </a>
    </div>

    {{-- Modal de busca de filial --}}
    @if ($filialBuscaAberta)
        <div class="fixed inset-0 z-[70] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60" wire:click="fecharBuscaFilial"></div>

            <div
                class="relative bg-surface border border-border rounded-xl shadow-xl w-full max-w-2xl max-h-[80vh] flex flex-col">
                <div class="p-7 border-b border-border flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-ink">Buscar filial</h3>
                    <button type="button" wire:click="fecharBuscaFilial" class="text-ink-muted hover:text-ink">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="p-7 border-b border-border">
                    <input type="text" wire:model.live.debounce.300ms="termoBuscaFilial" autofocus
                        placeholder="Buscar por código, nome ou cidade..."
                        class="py-3 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:border-primary focus:ring-primary/20">

                    <div wire:loading wire:target="termoBuscaFilial" class="text-xs text-ink-muted mt-2">
                        Buscando...
                    </div>
                </div>

                <div class="flex-1 min-h-0 overflow-y-auto">
                    <table class="min-w-full divide-y divide-border text-sm">
                        <thead class="bg-surface-hover sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-start font-medium text-ink-muted">Código</th>
                                <th class="px-4 py-3 text-start font-medium text-ink-muted">Nome</th>
                                <th class="px-4 py-3 text-start font-medium text-ink-muted">Cidade</th>
                                <th class="px-4 py-3 text-start font-medium text-ink-muted">UF</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($this->resultadoBuscaFilial as $f)
                                <tr wire:key="filial-{{ $f->code }}" wire:dblclick="selecionarFilial('{{ $f->code }}')"
                                    class="hover:bg-surface-hover cursor-pointer {{ $filial && $filial['code'] === $f->code ? 'bg-primary/10' : '' }}">
                                    <td class="px-4 py-3 font-mono text-ink">{{ $f->code }}</td>
                                    <td class="px-4 py-3 text-ink">{{ $f->name }}</td>
                                    <td class="px-4 py-3 text-ink">{{ $f->city }}</td>
                                    <td class="px-4 py-3 text-ink">{{ $f->state }}</td>
                                    <td class="px-4 py-3 text-end">
                                        <button type="button" wire:click="selecionarFilial('{{ $f->code }}')"
                                            class="py-1.5 px-3 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover">
                                            Selecionar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-ink-muted">
                                        Nenhuma filial encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
